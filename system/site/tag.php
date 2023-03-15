<?php

if( ! $core ) exit;

$args = $core->route->get('args');

snippet( 'header' );

snippet( 'overview', $args );

snippet( 'footer' );