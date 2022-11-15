<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

?>
<header>
	<h1><a href="<?= EH_BASEURL ?>">Eigenheim</a></h1>
</header>
<main>
<?php

snippet( 'post', $args );

snippet( 'author_info' );

?>
</main>
