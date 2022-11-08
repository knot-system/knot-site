<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


$dir = EH_ABSPATH.'system/functions/';
// CLEANUP: include all the relevant files by name instead of including all files in the $dir to make the code a bit safer
if( $handle = opendir($dir) ){
	while( false !== ($file = readdir($handle)) ){
		if( '.' === $file ) continue;
		if( '..' === $file ) continue;

		include_once( $dir.$file );
	}
	closedir($handle);
}
