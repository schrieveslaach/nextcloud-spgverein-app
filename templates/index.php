<?php
/** @var \OCP\IL10N $l */
style('spgverein', 'style');

?>

<div id="app">
    <div id="app-navigation">
        <?php print_unescaped($this->inc('navigation/index')); ?>
    </div>

    <div id="app-content">
        <div id="app-content-wrapper">
        </div>
    </div>
</div>

<?php
script('spgverein', 'spgverein');

