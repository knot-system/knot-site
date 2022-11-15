<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

$post = $args['post'];
if( ! $post ) return;

$title = $post['title'];
$text = $post['content_html'];
$tags = $post['tags'];

$date = false;
if( $post['timestamp'] > 0 ) $date = date( 'd.m.Y', $post['timestamp'] );

?>
<article class="h-entry">
	<a class="anchor" name="<?= $post['id'] ?>"></a>
	<?php

	if( $date ) echo '<time class="dt-published" datetime="'.$date.'"><a href="'.$post['permalink'].'">'.$date.'</a></time>';
	
	if( $title ) echo '<h2 class="p-name"><a href="'.$post['permalink'].'">'.$title.'</a></h2>';
	
	if( $text ) echo '<div class="e-content">'.$text.'</div>';

	if( count($tags) ) echo '<ul class="tags"><li class="p-category">'.implode( '</li><li>', $tags).'</li></ul>';

	?>
</article>