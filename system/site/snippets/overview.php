<?php

// Version: 0.1.0

if( ! $core ) exit;

$tag = false;
if( ! empty($args['tag']) ) $tag = $args['tag'];

if( $tag ) {
?>
		
	<p class="tag-notice"><em>only showing posts with the tag <strong><?= $tag ?></strong></em> <a class="tag-notice-close" href="<?= url() ?>">close</a></p>

<?php
}


foreach( $core->posts->get() as $post ) snippet( 'post', array( 'post' => $post ) );

snippet( 'pagination', array( 'tag' => $tag ) );

snippet( 'author_info' );
