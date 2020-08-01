<?php

namespace OCA\SPGVerein\Repository;

use OCA\SPGVerein\Model\Member;
use OCP\Files\File;
use OCP\IServerContainer;

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
        $clubFile = $this->openClubFile($club);
        return $this->parseFile($clubFile);
    }

    private function addDocuments(File $clubFile, Member $member) : array {
        try {
            $id = str_pad($member->getId(), 10, "0", STR_PAD_LEFT);

            $searchPath = "/archiv/" . $id;
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

    private function parseFile(File $clubFile): array {
        $descriptors = array(
            0 => array("pipe", "r"),  // STDIN
            1 => array("pipe", "w"),  // STDOUT
            2 => array("pipe", "w")   // STDERR
        );

        $cmd;
        if (php_uname("m") == "x86_64") {
            $cmd = __DIR__  . "/parser-x86_64";
        }
        else if (php_uname("m") == "armv7l") {
            $cmd = __DIR__  . "/parser-armv7l";
        }

        $cmd = "$cmd  -v 3";
        $process = proc_open($cmd, $descriptors, $pipes);

        $fileStream = $clubFile->fopen("rb");
        stream_copy_to_stream($fileStream, $pipes[0]);
        fclose($pipes[0]);
        fclose($fileStream);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $members = array();
        $returnCode = proc_close($process);
        if ($returnCode === 0) {
            $json = json_decode($stdout);
            foreach($json as $jsonData) {
                $member = new Member($jsonData);
                $this->addDocuments($clubFile, $member);
                array_push($members, $member);
            }
        }

        return $members;
    }
}
