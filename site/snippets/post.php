<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

$post = $args['post'];

$title = $post['title'];
$text = $post['text'];
$date = date( 'd.m.Y', $post['timestamp'] );
$tags = $post['tags'];

?>
<section>
	<small><?= $date ?></small>
	<?php if( $title ) echo '<h2>'.$title.'</h2>'; ?>
	<?= $text; ?>
	<?php if( count($tags) ) echo '<ul class="tags"><li>'.implode( '</li><li>', $tags).'</li></ul>'; ?>
</section>