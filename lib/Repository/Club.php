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

        return $members;
    }

    private function addDocuments(File $clubFile, Member $member) : array {
        try {
            $id = str_pad($member->getId(), 10, "0", STR_PAD_LEFT);

            $searchPath = $clubFile->getParent()->getPath() . '/archiv/' . $id;
            $archiveDirectories = $this->storage->search($id);

            foreach ($archiveDirectories as $directory) {
                if ($directory->getPath() === $searchPath) {
                    foreach ($directory->getDirectoryListing() as $archivedFile) {

                        error_log("mount: " . $archivedFile->getMountPoint()->getMountPoint() );

                        $member->addFile($archivedFile);
                    }
                }
            }
        } catch(\OCP\Files\NotFoundException $e) {
        }

        return array();
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
