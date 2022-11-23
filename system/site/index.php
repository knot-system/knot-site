<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

snippet( 'header' );

$posts = get_posts();
snippet( 'overview', array( 'posts' => $posts ) );

snippet( 'footer' );