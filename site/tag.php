<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

snippet( 'header' );

$tag = $args['tag'];
$posts = $args['posts'];
snippet( 'overview', array( 'posts' => $posts, 'tag' => $tag ) );

snippet( 'footer' );