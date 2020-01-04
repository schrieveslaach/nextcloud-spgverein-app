<?php
if ((@include_once __DIR__ . '/../vendor/autoload.php')===false) {
    throw new Exception('Cannot include autoload. Did you run install dependencies using composer?');
}

use OCP\AppFramework\App;

$app = new App('spgverein');
$container = $app->getContainer();

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
