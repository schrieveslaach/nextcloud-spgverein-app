<?php

namespace OCA\SPGVerein\AppInfo;

use \OCP\AppFramework\App;
use \OCA\SPGVerein\Controller\ClubController;
use \OCA\SPGVerein\Controller\PageController;
use OCA\SPGVerein\Repository\Club;

class Application extends App {

    public function __construct(array $urlParams = array()) {
        parent::__construct('spgverein', $urlParams);

        $container = $this->getContainer();

        /**
         * Controllers
         */
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

        /**
         * Repository Layer
         */
        $container->registerService('Club', function($c) {
            return new Club($c->query('UserFolder'));
        });

        $container->registerService('UserFolder', function($c) {
            return $c->query('ServerContainer')->getUserFolder();
        });
    }

}
