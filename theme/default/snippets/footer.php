<?php

// Version: alpha.15

if( ! $eigenheim ) exit;

?>


</main>

<footer>
	<a href="https://github.com/maxhaesslein/eigenheim" target="_blank" rel="noopener">Eigenheim</a> v.<?= $eigenheim->version() ?> / <?= $eigenheim->theme->get('name') ?> v.<?= $eigenheim->theme->get('version') ?>

</footer>

<?php
foot_html();
