<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

snippet( 'header' );

$posts = $args['posts'];
$page = $args['page'];
snippet( 'overview', array( 'posts' => $posts, 'page' => $page ) );

snippet( 'footer' );