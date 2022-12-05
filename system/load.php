<?php

$abspath = realpath(dirname(__FILE__)).'/';
$abspath = preg_replace( '/system\/$/', '', $abspath );


if( ! file_exists($abspath.'config.php')
 || ! file_exists($abspath.'.htaccess')
 || isset($_GET['setup'])
) {
	include_once( $abspath.'system/setup.php' );
	exit;
}


include( 'system/functions.php' );
include( 'system/classes.php' );

$eigenheim = new Eigenheim();



if( isset($_GET['update']) && (file_exists($eigenheim->abspath.'update')
 || file_exists($eigenheim->abspath.'update.txt'))
) {
	$eigenheim->include( 'system/update.php' );
	exit;
}


$eigenheim->theme->load();


$route = get_route();

$template = $route['template'];
$args = false;
if( ! empty($route['args']) ) $args = $route['args'];

$eigenheim->include( 'system/site/'.$template.'.php', $args );
