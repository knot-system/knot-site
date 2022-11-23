<?php

// Version: alpha.7

if( ! defined( 'EH_ABSPATH' ) ) exit;

$post = $args['post'];
if( ! $post ) return;

$title = $post['title'];
$text = $post['content_html'];
$tags = $post['tags'];
$permalink = $post['permalink'];

$date = false;
if( $post['timestamp'] > 0 ) $date = date( 'd.m.Y', $post['timestamp'] );

?>
<article class="h-entry">
	<a class="anchor" name="<?= $post['id'] ?>"></a>
	<?php

	if( $date ) echo '<time class="dt-published" datetime="'.$date.'"><a href="'.$post['permalink'].'">'.$date.'</a></time>';
	
	if( $title ) echo '<h2 class="p-name"><a href="'.$permalink.'">'.$title.'</a></h2>';
	
	if( $text ) echo '<div class="e-content">'.$text.'</div>';

	if( count($tags) ) {
		echo '<ul class="tags">';
		foreach( $tags as $tag ) {
			echo '<li><a href="'.url('tag/'.$tag.'/').'" class="p-category tag">'.$tag.'</a></li>';
		}
		echo '</ul>';
	}

	?>
</article>