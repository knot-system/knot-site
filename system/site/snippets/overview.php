<?php

// Version: alpha.11

if( ! $eigenheim ) exit;

$tag = false;
if( ! empty($args['tag']) ) $tag = $args['tag'];

if( $tag ) echo '<p><em>only showing posts with tag <strong>'.$tag.'</strong></em></p>';

foreach( $eigenheim->posts->get() as $post ) snippet( 'post', array( 'post' => $post ) );

snippet( 'pagination', array( 'tag' => $tag ) );

snippet( 'author_info' );
