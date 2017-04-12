<?php

namespace OCA\SPGVerein\Repository;

use OCA\SPGVerein\Model\Member;
use OCP\Files\File;

class Club {

    const MEMBER_DATA_FIELD_LENGTH = 3200;
    const START_SYMBOLS = "\x00\x00\x4c\x80";

    private $storage;

    function __construct($storage) {
        $this->storage = $storage;
    }

    public function getAllMembers(): array {
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

                $member = new Member($memberData);
                array_push($members, $member);
            }

            $previous = $buffer;
        }

        return $members;
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

}
