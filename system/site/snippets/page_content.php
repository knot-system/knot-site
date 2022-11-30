<?php

// Version: alpha.9

if( ! $eigenheim ) exit;

$page = $args['page'];
if( ! $page ) return;

$title = $page->fields['title'];
$text = $page->fields['content_html'];

?>
<article class="h-entry">
	<?php
	
	if( $title ) echo '<h2 class="p-name">'.$title.'</h2>';
	
	if( $text ) echo '<div class="e-content">'.$text.'</div>';

	?>
</article>