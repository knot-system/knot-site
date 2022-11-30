<?php

if( ! $eigenheim ) exit;

snippet( 'header' );

$tag = $args['tag'];
$posts = $args['posts'];
$page = $args['page'];
snippet( 'overview', array( 'posts' => $posts, 'tag' => $tag, 'page' => $page ) );

snippet( 'footer' );