<?php

$abspath = realpath(dirname(__FILE__)).'/';
$abspath = preg_replace( '/system\/$/', '', $abspath );


// check if we have all required files, if not run the setup
if( ! file_exists($abspath.'config.php')
 || ! file_exists($abspath.'.htaccess')
 || isset($_GET['setup'])
) {
	include_once( $abspath.'system/setup.php' );
	exit;
}


include_once( $abspath.'system/functions.php' );
include_once( $abspath.'system/classes.php' );


$eigenheim = new Eigenheim();


// check if we want to run an update
if( isset($_GET['update'])
 && ( file_exists($eigenheim->abspath.'update') || file_exists($eigenheim->abspath.'update.txt') )
) {
	$eigenheim->include( 'system/update.php' );
	exit;
}


// here we gooo

$eigenheim->theme->load();


$template = $eigenheim->route->get('template');
if( ! file_exists( $eigenheim->abspath.'system/site/'.$template.'.php') ){
	$eigenheim->debug( 'template not found!', $template );
	exit;
}

$eigenheim->include( 'system/site/'.$template.'.php' );
