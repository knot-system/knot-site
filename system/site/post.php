<?php

if( ! $core ) exit;

$args = $core->route->get('args');

snippet( 'header' );

snippet( 'single', array( 'post' => $args['post'] ) );

snippet( 'footer' );