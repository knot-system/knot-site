<?php

// Version: 0.1.0

if( ! $core ) exit;

$post = $args['post'];

if( ! $post ) return;

$title = $post->fields['title'];
$text = $post->fields['content_html'];
$tags = $post->fields['tags'];
$permalink = $post->fields['permalink'];
$link_preview = $post->fields['link_preview'];

$date = false;
if( $post->fields['timestamp'] > 0 ) $date = date( 'd.m.Y', $post->fields['timestamp'] );

$image_html = $post->fields['image_html'];

?>

	<article class="h-entry">

		<a class="anchor" name="<?= $post->fields['id'] ?>"></a>
<?php
if( $date ) {
?>

		<time class="dt-published" datetime="<?= $date ?>"><a href="<?= $post->fields['permalink'] ?>"><?= $date ?></a></time>
<?php
}
		
if( $title ) {
?>

		<h2 class="p-name"><a href="<?= $permalink ?>"><?= $title ?></a></h2>
<?php
}
		
if( $image_html ) {
?>

		<?= $image_html ?>

<?php
}

if( $text ) {
?>

		<div class="e-content">
			<?= $text ?>
		</div>
<?php
}


if( $link_preview ) {
?>

		<div class="link-preview-container">
		<?= $link_preview ?>

		</div>
<?php
}


if( count($tags) ) {
?>

		<ul class="tags">
<?php foreach( $tags as $tag ) { ?>
			<li><a href="<?= url('tag/'.$tag.'/') ?>" class="p-category tag"><?= $tag ?></a></li>
<?php } ?>
		</ul>
<?php
}

?>

	</article>

