<?php

namespace OCA\SPGVerein\Repository\Listeners;

use OCA\SPGVerein\Repository\Club;
use OCA\SPGVerein\Repository\ClubException;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use Psr\Log\LoggerInterface;

class ClubDeleted implements IEventListener {

    /** @var LoggerInterface */
    private $logger;

    /** @var IAppData */
    private $appData;

    public function __construct(LoggerInterface $logger, IAppData $appData) {
        $this->appData = $appData;
        $this->logger = $logger;
    }

    public function handle(Event $event): void {
        if (!($event instanceof NodeDeletedEvent)) {
            return;
        }

        $node = $event->getNode();
        if (Club::isClubFile($node)) {
            $this->logger->debug("Delete parsed club file", ["file" => $node->getName()]);

            try {
                $folder = $this->appData->getFolder(Club::PARSED_CLUBS_FOLDER_NAME);
                $file = $folder->getFile($node->getId() . ".json");
                $file->delete();
            } catch (NotFoundException $e) {
                $this->logger->debug("Cannot delete parsed file", [
                    "msg" => $e->getMessage()
                ]);
            }
        }
    }
}