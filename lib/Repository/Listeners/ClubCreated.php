<?php

namespace OCA\SPGVerein\Repository\Listeners;

use OCA\SPGVerein\Repository\Club;
use OCA\SPGVerein\Repository\ClubException;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeCreatedEvent;
use OCP\Files\Events\Node\NodeWrittenEvent;
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
        if (Club::isClubFile($node)) {
            $this->logger->info("Parsing club file", ["file" => $node->getName()]);

            try {
                $members = Club::runParser($node);
                error_log("----members--->" . count($members));

                $folder = $this->getClubsFolder();
                $file = $folder->newFile($node->getId() . ".json");

                error_log("---file--->" . $file->getName());
                $file->putContent(json_encode($members));
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
}