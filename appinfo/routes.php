<?php

namespace OCA\SPGVerein\AppInfo;

$application = new Application();
$application->registerRoutes($this, array(
    'routes' => array(
        array('name' => 'page#index', 'url' => '/', 'verb' => 'GET'),
        array('name' => 'club#listClubs', 'url' => '/clubs', 'verb' => 'GET'),
        array('name' => 'club#listMembers', 'url' => '/members/{club}', 'verb' => 'GET'),
        array('name' => 'club#listCities', 'url' => '/cities/{club}', 'verb' => 'GET'),
        array('name' => 'file#downloadFile', 'url' => '/files/{club}/{memberId}/{filename}', 'verb' => 'GET'),
        array('name' => 'file#exportClub', 'url' => '/files/{club}.ods', 'verb' => 'GET'),
        array('name' => 'label#formats', 'url' => '/labels/formats', 'verb' => 'GET'),
        array('name' => 'label#downloadLabels', 'url' => '/labels/{club}', 'verb' => 'GET')
    )
));
