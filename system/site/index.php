<?php

if( ! $eigenheim ) exit;

snippet( 'header' );

$page = $args['page'];

snippet( 'overview', array( 'page' => $page ) );

snippet( 'footer' );