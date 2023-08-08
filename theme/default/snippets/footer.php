<?php

// Version: 0.1.0

if( ! $core ) exit;

?>


</main>

<footer>
	<a href="https://github.com/maxhaesslein/knot-site" target="_blank" rel="noopener">Knot Site</a> v.<?= $core->version() ?> / <?= $core->theme->get('name') ?> v.<?= $core->theme->get('version') ?>

</footer>

<?php
foot_html();
