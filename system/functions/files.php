<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


// TODO: cache these reads in a global variable (?) so we do not re-read the folder-structure and files multiple times in one page hit


function dir_read( $folderpath_input, $recursive = false, $filename = false, $reverse = false ){

	$folderpath = EH_ABSPATH.'content/'.$folderpath_input;

	if( ! is_dir( $folderpath ) ) return array(); // TODO: error handling: $folderpath is no directory

	$files = array();

	if( $handle = opendir($folderpath) ){
		while( false !== ($file = readdir($handle)) ){
			if( substr($file,0,1) == '.' ) continue; // skip hidden files, ./ and ../

			if( is_dir($folderpath.$file) ) {
				if( $recursive ){
					$files = array_merge( $files, dir_read( $folderpath_input.$file.'/', true, $filename, $reverse ) );
				}
				continue;
			}

			if( $filename && strtolower($file) != strtolower($filename) ) continue;

			$files[] = $folderpath_input.$file;
		}
		closedir($handle);
	} else {
		return array(); // TODO: error handling: could not open dir
	}

	if( $reverse ) {
		rsort($files);
	} else {
		asort($files);
	}

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


function file_get_fields( $filename ) {

	$file_content = file_read( $filename );

	if( ! $file_content ) return false;

	$file_content = str_replace("\r\n", "\n", $file_content);

	$fields = explode( "\n\n----\n\n", $file_content );

	if( ! is_array($fields) || ! count($fields) ) return false; // TODO: error handling: no fields in file
	

	$data = array();

	foreach( $fields as $field ) {

		$pos = strpos( $field, ':' );

		if( $pos === false ) continue; // TODO: error handling: no fieldname for this field

		$field_name = substr( $field, 0, $pos );
		$field_content = substr( $field, $pos+1 );

		$field_name = strtolower(trim($field_name));
		$field_content = trim($field_content);

		$data[$field_name] = $field_content;

	}

	return $data;
}


function file_get_id( $filename ) {
	$filename_exp = explode( '/', $filename );

	$id = $filename_exp[count($filename_exp)-2];
	$id_exp = explode('_', $id);

	$file_id = end($id_exp);

	return $file_id;
}
