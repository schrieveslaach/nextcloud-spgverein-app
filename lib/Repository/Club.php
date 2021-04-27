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

    const V4_PREFIX = "spg_verein_";
    const V4_ENDING = ".mdf";

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

    public static function isV3ClubFile(String $clubFileName): bool {
        return self::endsWith($clubFileName, self::MITGL_DAT);
    }

    public static function isV4ClubFile(String $clubFileName): bool {
        return self::startsWith($clubFileName, self::V4_PREFIX) && 
            self::endsWith($clubFileName, self::V4_ENDING);
    }

    private static function endsWith( $str, $sub ) {
        return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
    }

    private static function startsWith($string, $startString) { 
        $len = strlen($startString); 
        return (substr($string, 0, $len) === $startString); 
    }

    private function openClubFile(string $clubFileId): File
    {
        $clubFile = $this->userFolder->getById($clubFileId);
        if ($clubFile[0] instanceof File) {
            return $clubFile[0];
        } else {
            throw new ClubException('Could not open club file');
        }
    }

    public function getAllClubs() {
        $clubMemberFiles = $this->userFolder->search(self::MITGL_DAT);

        $clubs = array();

        foreach ($clubMemberFiles as $file) {
            try {
                $this->getJsonFilePath($file);
            } catch(NotFoundException $e) {
                $this->logger->warning("No parsed data for club file", [
                    "file" => $file, 
                    "msg" => $e->getMessage()
                ]);
                continue;
            }

            $name = $file->getName();
            $name = substr($name, 0, strlen($name) - strlen(self::MITGL_DAT));
            array_push($clubs, array(
                "name" => $name,
                "id" => $file->getId(),
            ));
        }

        $clubMemberFiles = $this->userFolder->search(self::V4_ENDING);

        foreach ($clubMemberFiles as $file) {
            $name = $file->getName();
            if (!self::startsWith($name, self::V4_PREFIX)) {
                continue;
            }

            try {
                $this->getJsonFilePath($file);
            } catch(NotFoundException $e) {
                $this->logger->warning("No parsed data for club file", [
                    "file" => $file, 
                    "msg" => $e->getMessage()
                ]);
                continue;
            }

            $name = substr($name, 0, strlen($name) - strlen(self::V4_ENDING));
            $name = substr($name, strlen(self::V4_PREFIX));
            array_push($clubs, array(
                "name" => $name,
                "id" => $file->getId(),
            ));
        }

        return $clubs;
    }

    private function parseMembers(File $clubFile): array {
        try {
            $file = $this->getJsonFilePath($clubFile);
            return json_decode($file->getContent());
        } catch(NotFoundException $e) {
            $this->logger->debug("Cannot load prepaserd club", ["ex" => $e->getMessage()]);
            return array();
        }
    }

    private function getJsonFilePath(File $clubFile) {
        $parsedClubs = $this->appData->getFolder(self::PARSED_CLUBS_FOLDER_NAME);
        return $parsedClubs->getFile($clubFile->getId() . ".json");
    }
}
