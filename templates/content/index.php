<center>
    <h1 id="app-heading"><?php p($l->t('SPG Verein')); ?></h1>
</center>

<div id="members">
</div>

<?php
script('spgverein', 'members');
script('spgverein', 'script');
script('spgverein', 'print');

