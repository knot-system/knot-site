<?php

// Version: alpha.10

if( ! $eigenheim ) exit;

$post = $args['post'];

if( ! $post ) return;

$title = $post->fields['title'];
$text = $post->fields['content_html'];
$tags = $post->fields['tags'];
$permalink = $post->fields['permalink'];

$date = false;
if( $post->fields['timestamp'] > 0 ) $date = date( 'd.m.Y', $post->fields['timestamp'] );

$image_html = $post->fields['image_html'];

?>
<article class="h-entry">
	<a class="anchor" name="<?= $post->fields['id'] ?>"></a>
	<?php

	if( $date ) echo '<time class="dt-published" datetime="'.$date.'"><a href="'.$post->fields['permalink'].'">'.$date.'</a></time>';
	
	if( $title ) echo '<h2 class="p-name"><a href="'.$permalink.'">'.$title.'</a></h2>';
	
	if( $image_html ) echo '<p>'.$image_html.'</p>';

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