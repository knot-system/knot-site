<?php

if( ! $eigenheim ) exit;

$args = $eigenheim->route->get('args');

snippet( 'header' );

snippet( 'single', array( 'post' => $args['post'] ) );

snippet( 'footer' );