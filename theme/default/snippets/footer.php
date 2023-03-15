<?php

// Version: 0.1.0

if( ! $core ) exit;

?>


</main>

<footer>
	<a href="https://github.com/maxhaesslein/eigenheim" target="_blank" rel="noopener">Eigenheim</a> v.<?= $core->version() ?> / <?= $core->theme->get('name') ?> v.<?= $core->theme->get('version') ?>

</footer>

<?php
foot_html();
