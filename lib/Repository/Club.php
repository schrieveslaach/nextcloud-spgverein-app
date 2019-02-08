<?php

namespace OCA\SPGVerein\Repository;

use OCA\SPGVerein\Model\Member;
use OCP\Files\File;

class Club
{

    const MEMBER_DATA_FIELD_LENGTH = 3200;
    const START_SYMBOLS = "\x00\x00\x4c\x80";

    private $storage;

    const MITGL_DAT = "mitgl.dat";

    function __construct($storage)
    {
        $this->storage = $storage;
    }

    public function getAllMembers(string $club): array
    {
        $members = array();

        $clubFile = $this->openClubFile($club);
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

                $this->addDocuments($clubFile, $member);

                array_push($members, $member);
            }

            $previous = $buffer;
        }

        usort($members, function ($a, $b) {
            $cmp = strcmp($a->getCity(), $b->getCity());
            if ($cmp !== 0) {
                return $cmp;
            }

            $m1 = array();
            $m2 = array();
            preg_match('/(.*)\s+((\d+)\s*([a-z])?)/', $a->getStreet(), $m1);
            preg_match('/(.*)\s+((\d+)\s*([a-z])?)/', $b->getStreet(), $m2);

            $cmp = strcmp($m1[1], $m2[1]);
            if ($cmp === 0) {
                $n1 = intval($m1[3]);
                $n2 = intval($m2[3]);

                if ($n1 < $n2)
                    $cmp = -1;
                else if ($n1 > $n2)
                    $cmp = 1;
                else
                    $cmp = 0;
            }

            return $cmp;
        });

        return $members;
    }

    private function addDocuments(File $clubFile, Member $member) : array {
        try {
            $id = str_pad($member->getId(), 10, "0", STR_PAD_LEFT);

            $searchPath = '/archiv/' . $id;
            $archiveDirectories = $this->storage->search($id);

            foreach ($archiveDirectories as $directory) {
                if ($this->endsWith($directory->getPath(), $searchPath)) {
                    foreach ($directory->getDirectoryListing() as $archivedFile) {
                        $member->addFile($archivedFile);
                    }
                }
            }
        } catch(\OCP\Files\NotFoundException $e) {
        }

        return array();
    }

    private function endsWith( $str, $sub ) {
        return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
    }


    private function openClubFile(string $club): File
    {
        $clubMemberFiles = $this->storage->search($club . self::MITGL_DAT);

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

    public function getAllClubs() {
        $clubMemberFiles = $this->storage->search(self::MITGL_DAT);

        $clubs = array();

        foreach ($clubMemberFiles as $file) {
            $name = $file->getName();
            $name = substr($name, 0, strlen($name) - strlen(self::MITGL_DAT));
            array_push($clubs, $name);
        }

        return $clubs;
    }

}
