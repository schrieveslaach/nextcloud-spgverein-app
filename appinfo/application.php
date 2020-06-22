<?php

namespace OCA\SPGVerein\AppInfo;

use \OCP\AppFramework\App;
use \OCA\SPGVerein\Controller\ClubController;
use \OCA\SPGVerein\Controller\FileController;
use \OCA\SPGVerein\Controller\LabelController;
use \OCA\SPGVerein\Controller\PageController;
use OCA\SPGVerein\Repository\Club;

class Application extends App {

    public function __construct(array $urlParams = array()) {
        parent::__construct('spgverein', $urlParams);

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
            return new Club($c->query('UserFolder'), $c->query('ServerContainer'));
        });

        $container->registerService('UserFolder', function($c) {
            return $c->query('ServerContainer')->getUserFolder();
        });
    }

}
