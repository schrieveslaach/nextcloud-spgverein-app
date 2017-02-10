<?php

namespace OCA\SPGVerein\Repository;

use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\File;

class Club {

    const MEMBER_DATA_FIELD_LENGTH = 3200;
    const START_SYMBOLS = "\x00\x00\x4c\x80";

    private $storage;

    function __construct($storage) {
        $this->storage = $storage;
    }

    public function getAllMembers(): JSONResponse {
        $members = array();

        $clubFile = $this->openClubFile();
        $content = $clubFile->getContent();
        $length = strlen($content);

        $previous = "";
        for ($i = 0; $i < $length; $i += 4) {
            $buffer = substr($content, $i, 4);

            $symbolSearchBuffer = $previous . $buffer;
            $startSymbolPos = strpos($symbolSearchBuffer, self::START_SYMBOLS);
            if ($startSymbolPos !== false) {
                $memberData = substr($content, $i + $startSymbolPos, self::MEMBER_DATA_FIELD_LENGTH);

                $i += self::MEMBER_DATA_FIELD_LENGTH - 4;

                $member = $this->parseMember($memberData);
                array_push($members, $member);
            }

            $previous = $buffer;
        }

        return new JSONResponse($members);
    }

    private function openClubFile(): File {
        $clubMemberFiles = $this->storage->search("mitgl.dat");

        if (empty($clubMemberFiles)) {
            return false;
        }

        $clubFile = $clubMemberFiles[0];

        if ($clubFile instanceof File) {
            return $clubFile;
        } else {
            throw new StorageException('Could not open club file');
        }
    }

    private function parseMember(string $memberData): array {
        $memberDataUtf8 = mb_convert_encoding($memberData, 'UTF-8', 'ISO-8859-1');

        // These lines ensures that data after lastname is aligned correctly:
        // if lastname contains none ascii code following fields need to be shifted
        $zipCode = substr($memberDataUtf8, 200, 6);
        $shift = $zipCode[0] === ' ' ? 1 : 0;
        $shift += $zipCode[1] === ' ' ? 1 : 0;
        $shift += $zipCode[2] === ' ' ? 1 : 0;

        return array(
            "id" => (int) substr($memberDataUtf8, 0, 10),
            "salutation" => trim(substr($memberDataUtf8, 10, 15)),
            "title" => trim(substr($memberDataUtf8, 25, 35)),
            "firstname" => trim(substr($memberDataUtf8, 60, 35)),
            "lastname" => trim(substr($memberDataUtf8, 95, 70)),
            "street" => trim(substr($memberDataUtf8, 165, 35)),
            "zipcode" => substr($memberDataUtf8, 200 + $shift, 5),
            "city" => trim(substr($memberDataUtf8, 205 + $shift, 40)),
        );
    }

}
