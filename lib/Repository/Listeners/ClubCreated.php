<?php

namespace OCA\SPGVerein\Repository\Listeners;

use OCA\SPGVerein\Repository\Club;
use OCA\SPGVerein\Repository\ClubException;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeCreatedEvent;
use OCP\Files\Events\Node\NodeWrittenEvent;
use OCP\Files\File;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use Psr\Log\LoggerInterface;

class ClubCreated implements IEventListener {

    /** @var LoggerInterface */
    private $logger;

    /** @var IAppData */
    private $appData;

    public function __construct(LoggerInterface $logger, IAppData $appData) {
        $this->appData = $appData;
        $this->logger = $logger;
    }

    public function handle(Event $event): void {
        if (!($event instanceof NodeCreatedEvent) && !($event instanceof NodeWrittenEvent)) {
            return;
        }

        $node = $event->getNode();

        if (Club::isV3ClubFile($node->getName()) || Club::isV4ClubFile($node->getName())) {
            $this->logger->info("Parsing club file", ["file" => $node->getName()]);

            try {
                $folder = $this->getClubsFolder();
                $file = $folder->newFile($node->getId() . ".json");

                $version = "4";
                if (!Club::isV4ClubFile($node->getName())) {
                    $version = "3";
                }

                $this->runParserAndStore($version, $node->fopen("r"), $file->write());
            } catch(ClubException $e) {
                $this->logger->error("Cannot parse club file", [
                    "file" => $node->getName(), 
                    "msg" => $e->getMessage()
                ]);
            }
        }
    }

    private function getClubsFolder() {
        try {
            return $this->appData->getFolder(Club::PARSED_CLUBS_FOLDER_NAME);
        } catch (NotFoundException $e) {
            $this->logger->debug("Cannot find parsed clubs folder, creating one", [
                "msg" => $e->getMessage()
            ]);
            return $this->appData->newFolder(Club::PARSED_CLUBS_FOLDER_NAME);
        }
    }

    private function runParserAndStore(String $clubVersion, $clubFileStream, $cacheFileStream) {
        $temp = tmpfile();
        stream_copy_to_stream($clubFileStream, $temp);

        $descriptors = array(
            0 => array("pipe", "r"),  // STDIN
            1 => array("pipe", "w"),  // STDOUT
            2 => array("pipe", "w")   // STDERR
        );

        $cmd;
        if (php_uname("m") == "x86_64") {
            $cmd = __DIR__  . "/../parser-x86_64";
        }
        else if (php_uname("m") == "armv7l") {
            $cmd = __DIR__  . "/../parser-armv7l";
        }

        $path = stream_get_meta_data($temp)['uri'];
        $cmd = "sh -c 'chmod +x $cmd && $cmd -v $clubVersion -f \"$path\"'";

        $this->logger->info("Running SPG file parser…", ["cmd" => $cmd]);
        $process = proc_open($cmd, $descriptors, $pipes);

        stream_set_blocking($pipes[2], 0);
        
        if ($error = stream_get_contents($pipes[2])) {
            throw new ClubException($error);
        }

        $this->logger->info("SPG file parser finished…");
        try {
            stream_copy_to_stream($pipes[1], $cacheFileStream);
        } finally {
            fclose($temp);
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            fclose($clubFileStream);
        }
    }
}