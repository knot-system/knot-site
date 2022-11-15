<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

$post = $args['post'];

$title = $post['title'];
$text = $post['content_html'];
$date = date( 'd.m.Y', $post['timestamp'] );
$tags = $post['tags'];

?>
<article class="h-entry">
	<a class="anchor" name="<?= $post['id'] ?>"></a>
	<time class="dt-published" datetime="<?= $date ?>"><?= $date ?></time>
	<?php if( $title ) echo '<h2 class="p-name">'.$title.'</h2>'; ?>
	<?php if( $text ) echo '<div class="e-content">'.$text.'</div>'; ?>
	<?php if( count($tags) ) echo '<ul class="tags"><li class="p-category">'.implode( '</li><li>', $tags).'</li></ul>'; ?>
</article>