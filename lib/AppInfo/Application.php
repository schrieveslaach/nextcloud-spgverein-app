<?php

namespace OCA\SPGVerein\AppInfo;

use OCA\SPGVerein\Controller\ClubController;
use OCA\SPGVerein\Controller\FileController;
use OCA\SPGVerein\Controller\LabelController;
use OCA\SPGVerein\Controller\PageController;
use OCA\SPGVerein\Repository\Listeners\ClubCreated;
use OCA\SPGVerein\Repository\Listeners\ClubDeleted;
use OCA\SPGVerein\Repository\Club;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Files\Events\Node\NodeCreatedEvent;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\Events\Node\NodeWrittenEvent;
use OCP\Files\IAppData;
use Psr\Log\LoggerInterface;

class Application extends App implements IBootstrap {

    public function __construct(array $urlParams = array()) {
        parent::__construct('spgverein', $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        include_once __DIR__ . '/../../vendor/autoload.php';

        $container = $this->getContainer();

        $container->registerService('PageController', function($c) {
            return new PageController(
                $c->query('AppName'), $c->query('Request')
            );
        });

        $container->registerService('ClubController', function ($c) {
            return new ClubController(
                $c->query('AppName'), $c->query('Request'), $c->query('Club')
            );
        });

        $container->registerService('FileController', function ($c) {
            return new FileController(
                $c->query('AppName'), $c->query('Request'), $c->query('Club')
            );
        });

        $container->registerService('LabelController', function ($c) {
            return new LabelController(
                $c->query('AppName'), $c->query('Request'), $c->query('Club')
            );
        });

        $container->registerService('Club', function($c) {
            $logger = $c->query(LoggerInterface::class);
            $appData = $c->query(IAppData::class);
            return new Club($logger, $c->query('UserFolder'), $appData);
        });

        $container->registerService('UserFolder', function($c) {
            return $c->query('ServerContainer')->getUserFolder();
        });

        $container->query('OCP\INavigationManager')->add(function () use ($container) {
            $urlGenerator = $container->query('OCP\IURLGenerator');
            $l10n = $container->query('OCP\IL10N');
            return [
                'id' => 'spgverein',
                'order' => 10,
                'href' => $urlGenerator->linkToRoute('spgverein.page.index'),
                'icon' => $urlGenerator->imagePath('spgverein', 'app.svg'),
                'name' => $l10n->t('SPG Verein'),
            ];
        });

		$context->registerEventListener(NodeCreatedEvent::class, ClubCreated::class);
		$context->registerEventListener(NodeDeletedEvent::class, ClubDeleted::class);
		$context->registerEventListener(NodeWrittenEvent::class, ClubCreated::class);
    }

    public function boot(IBootContext $context): void {
    }
}
