<?php

if( ! $eigenheim ) exit;

snippet( 'header' );

$tag = $args['tag'];
$page = $args['page'];

snippet( 'overview', array( 'tag' => $tag, 'page' => $page ) );

snippet( 'footer' );