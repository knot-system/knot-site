<?php

if( ! $eigenheim ) exit;

$dir = 'system/functions/';
// CLEANUP: include all the relevant files by name instead of including all files in the $dir to make the code a bit safer
if( $handle = opendir(EH_ABSPATH.$dir) ){
	while( false !== ($file = readdir($handle)) ){
		if( '.' === $file ) continue;
		if( '..' === $file ) continue;

		$file_extension = pathinfo( EH_ABSPATH.$dir.$file, PATHINFO_EXTENSION );
		if( strtolower($file_extension) != 'php' ) continue;

		$eigenheim->include( $dir.$file );
	}
	closedir($handle);
}
