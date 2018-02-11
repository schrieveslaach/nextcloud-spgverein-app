<center>
    <h1 id="app-heading"><?php p($l->t('SPG Verein')); ?></h1>
    <br>
    <a id="printAll" class="pdf-link"><i class="fa fa-print" aria-hidden="true"></i> <?php p($l->t('Print All')); ?></a>
</center>

<div id="members">
</div>

<?php
script('spgverein', 'members');
script('spgverein', 'script');
script('spgverein', 'print');

