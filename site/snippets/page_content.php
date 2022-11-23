<?php

// Version: alpha.7

if( ! defined( 'EH_ABSPATH' ) ) exit;

$page = $args['page'];
if( ! $page ) return;

$title = $page['title'];
$text = $page['content_html'];

?>
<article class="h-entry">
	<?php
	
	if( $title ) echo '<h2 class="p-name">'.$title.'</h2>';
	
	if( $text ) echo '<div class="e-content">'.$text.'</div>';

	?>
</article>