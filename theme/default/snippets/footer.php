<?php

// Version: 0.2.1

if( ! $core ) exit;

?>


</main>

<footer>
	<a href="https://github.com/knot-system/knot-site" target="_blank" rel="noopener">Knot Site</a> v.<?= $core->version() ?> / <?= $core->theme->get('name') ?> v.<?= $core->theme->get('version') ?>

</footer>

<?php
foot_html();
