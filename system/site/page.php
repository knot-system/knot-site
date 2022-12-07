<?php

if( ! $eigenheim ) exit;

$args = $eigenheim->route->get('args');

snippet( 'header' );

snippet( 'page_content', array( 'page' => $args['page'] ) );

snippet( 'footer' );