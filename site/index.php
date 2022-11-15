<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

snippet( 'header' );

snippet( 'overview', array( 'posts' => $posts, 'tag' => $tag ) );

snippet( 'footer' );