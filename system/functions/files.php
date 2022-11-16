<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function dir_read( $folderpath_input, $recursive = false ){

	$folderpath = EH_ABSPATH.'content/'.$folderpath_input;

	if( ! is_dir( $folderpath ) ) return array(); // TODO: error handling: $folderpath is no directory

	$files = array();

	if( $handle = opendir($folderpath) ){
		while( false !== ($file = readdir($handle)) ){
			if( substr($file,0,1) == '.' ) continue; // skip hidden files, ./ and ../

			if( is_dir($folderpath.$file) ) {
				if( $recursive ){
					$files = array_merge( $files, dir_read( $folderpath_input.$file.'/', true ) );
				}
				continue;
			}

			if( $file != 'post.txt' ) continue;

			$files[] = $folderpath_input.$file;
		}
		closedir($handle);
	} else {
		return array(); // TODO: error handling: could not open dir
	}

	rsort($files);

	return $files;

}

function file_read( $filepath ){

	$filepath = EH_ABSPATH.'content/'.$filepath;

	if( ! file_exists( $filepath) ) return false; // TODO: error handling: file not found

	$content = file_get_contents( $filepath );

	if( ! $content ) return false; // TODO: error handling: empty file

	return $content;
}

function file_write( $filename, $content ) {

	if( file_exists($filename) ) return false; // TODO: error handling: file exists already

	$return = file_put_contents( $filename, $content );

	if( $return !== false ) return true; // return true, even if 0 bytes were written

	return false;
}
