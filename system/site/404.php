<?php

if( ! $eigenheim ) exit;

http_response_code(404);

snippet( 'header' );

?>
<article>
	<h2>Nothing here :(</h2>
	<p>This post does not exist. Maybe it was deleted, or did not even exist in the first place. Have a look at the <a href="<?= url() ?>">overview</a> instead!</p>
</article>
<?php
snippet( 'footer' );
