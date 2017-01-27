<?php

namespace OCA\SPGVerein\AppInfo;

$application = new Application();
$application->registerRoutes($this, array(
    'routes' => array(
        array('name' => 'page#index', 'url' => '/', 'verb' => 'GET'),
        array('name' => 'club#listMembers', 'url' => '/members', 'verb' => 'GET')
    )
));
