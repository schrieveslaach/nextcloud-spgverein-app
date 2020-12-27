<?php

namespace OCA\SPGVerein\Repository;

use OCA\SPGVerein\Model\Member;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\IServerContainer;
use Psr\Log\LoggerInterface;

class Club
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Folder */
    private $userFolder;

    /** @var IAppData */
    private $appData;

    const MITGL_DAT = "mitgl.dat";

    const PARSED_CLUBS_FOLDER_NAME = "ParsedClubs";

    function __construct(LoggerInterface $logger, Folder $userFolder, IAppData $appData)
    {
        $this->logger = $logger;
        $this->userFolder = $userFolder;
        $this->appData = $appData;
    }

    public function getAllMembers(string $club): array
    {
        $clubFile = $this->openClubFile($club);
        $json = $this->parseMembers($clubFile);

        $members = array();
        foreach($json as $jsonData) {
            $member = new Member($jsonData);
            array_push($members, $member);
        }

        foreach($members as $member) {
            $this->addDocuments($clubFile, $member);
        }

        return $members;
    }

    private function addDocuments(File $clubFile, Member $member) : array {
        try {
            $id = str_pad($member->getId(), 10, "0", STR_PAD_LEFT);

            $searchPath = "/archiv/" . $id;
            $archiveDirectories = $this->userFolder->search($id);

            foreach ($archiveDirectories as $directory) {
                if (self::endsWith($directory->getPath(), $searchPath)) {
                    foreach ($directory->getDirectoryListing() as $archivedFile) {
                        $member->addFile($archivedFile);
                    }
                }
            }
        } catch(NotFoundException $e) {
        }

        return array();
    }

    public static function isClubFile(File $clubFile): bool {
        return self::endsWith($clubFile->getName(), self::MITGL_DAT);
    }

    private static function endsWith( $str, $sub ) {
        return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
    }


    private function openClubFile(string $club): File
    {
        $clubMemberFiles = $this->userFolder->search($club . self::MITGL_DAT);

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
        $clubMemberFiles = $this->userFolder->search(self::MITGL_DAT);

        $clubs = array();

        foreach ($clubMemberFiles as $file) {
            $name = $file->getName();
            $name = substr($name, 0, strlen($name) - strlen(self::MITGL_DAT));
            array_push($clubs, $name);
        }

        return $clubs;
    }

    private function parseMembers(File $clubFile): array {
        try {
            $parsedClubs = $this->appData->getFolder(self::PARSED_CLUBS_FOLDER_NAME);
            $file = $parsedClubs->getFile($clubFile->getId() . ".json");
            return json_decode($file->getContent());
        } catch(NotFoundException $e) {
            $this->logger->debug("Cannot load prepaserd club", ["ex" => $e->getMessage()]);
        }

        return self::runParser($clubFile);
    }

    public static function runParser(File $clubFile): array {
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
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);
        if ($returnCode === 0) {
            return json_decode($stdout);
        }
        else {
            throw new ClubException($stderr);
        }
    }
}
