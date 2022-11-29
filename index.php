<?php

include_once( 'system/core.php' );

global $eigenheim;
$eigenheim = new Eigenheim();

$eigenheim->include( 'system/functions.php' );


if( ! file_exists(EH_ABSPATH.'config.php') || ! file_exists(EH_ABSPATH.'.htaccess') || isset($_GET['setup']) ) {
	include_once( EH_ABSPATH.'system/setup.php');
	exit;
}


if( file_exists(EH_ABSPATH.'update') || file_exists(EH_ABSPATH.'update.txt') ) {
	include_once( EH_ABSPATH.'system/update.php');
	exit;
}


$theme = get_theme();
include_once( $theme['path'].'theme.php' );


$route = get_route();

$template = $route['template'];
$args = false;
if( ! empty($route['args']) ) $args = $route['args'];

// TODO: check if template file exists before we include it and fall back to an error page if it doesn't
include_once( EH_ABSPATH.'system/site/'.$template.'.php' );
