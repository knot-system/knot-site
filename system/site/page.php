<?php

if( ! $core ) exit;

$args = $core->route->get('args');

snippet( 'header' );

snippet( 'page_content', array( 'page' => $args['page'] ) );

snippet( 'footer' );