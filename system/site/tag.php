<?php

if( ! $eigenheim ) exit;

$args = $eigenheim->route->get('args');

snippet( 'header' );

snippet( 'overview', $args );

snippet( 'footer' );