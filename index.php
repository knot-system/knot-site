<?php

include_once( 'system/core.php' );

global $eigenheim;
$eigenheim = new Eigenheim();

$eigenheim->include( 'system/functions.php' );


if( ! file_exists($eigenheim->abspath.'config.php') || ! file_exists($eigenheim->abspath.'.htaccess') || isset($_GET['setup']) ) {
	$eigenheim->include( 'system/setup.php' );
	exit;
}


if( file_exists($eigenheim->abspath.'update') || file_exists($eigenheim->abspath.'update.txt') ) {
	$eigenheim->include( 'system/update.php' );
	exit;
}


$theme = get_theme();
$eigenheim->include( $theme['_path'].'functions.php' );


$route = get_route();

$template = $route['template'];
$args = false;
if( ! empty($route['args']) ) $args = $route['args'];

$eigenheim->include( 'system/site/'.$template.'.php', $args );
