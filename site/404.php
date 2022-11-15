<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

http_response_code(404);

snippet( 'header' );

?>
<header>
	<h1><a href="<?= EH_BASEURL ?>">Eigenheim</a></h1>
</header>
<main class="error-404">

<section>
	<h2>Nothing here :(</h2>
	<p>This post does not exist. Maybe it was deleted, or did not even exist in the first place. Have a look at the <a href="<?= EH_BASEURL ?>">overview</a> instead!</p>
</section>

</main>
<?php
snippet( 'footer' );
