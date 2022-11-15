<?php

namespace Eigenheim;

if( ! defined( 'EH_ABSPATH' ) ) exit;

class Files {

	static function read_dir( $folderpath_input, $recursive = false ){

		$folderpath = EH_ABSPATH.'content/'.$folderpath_input;

		if( ! is_dir( $folderpath ) ) return array(); // TODO: error handling: $folderpath is no directory

		$files = array();

		if( $handle = opendir($folderpath) ){
			while( false !== ($file = readdir($handle)) ){
				if( substr($file,0,1) == '.' ) continue; // skip hidden files, ./ and ../

				if( is_dir($folderpath.$file) ) {
					if( $recursive ){
						$files = array_merge( $files, Files::read_dir( $folderpath_input.$file.'/', true ) );
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

	static function read_file( $filepath ){

		$filepath = EH_ABSPATH.'content/'.$filepath;

		if( ! file_exists( $filepath) ) return false; // TODO: error handling: file not found

		$content = file_get_contents( $filepath );

		if( ! $content ) return false; // TODO: error handling: empty file

		$fields = explode( "\n\n----\n\n", $content );

		if( ! is_array($fields) || ! count($fields) ) return false; // TODO: error handling: no fields in file

		$return = array();

		foreach( $fields as $field ) {

			$pos = strpos( $field, ':' );

			if( $pos === false ) continue; // TODO: error handling: no fieldname for this field

			$field_name = substr( $field, 0, $pos );
			$field_content = substr( $field, $pos+1 );

			$field_name = strtolower(trim($field_name));
			$field_content = trim($field_content);

			$return[$field_name] = $field_content;

		}

		return $return;

	}

	static function write_file( $filename, $content ) {

		if( file_exists($filename) ) return false; // TODO: error handling: file exists already

		$return = file_put_contents( $filename, $content );

		if( $return !== false ) return true; // return true, even if 0 bytes were written

		return false;
	}

}
