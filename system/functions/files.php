<?php

namespace Eigenheim;

if( ! defined( 'EH_ABSPATH' ) ) exit;

class Files {

	static function read_dir( $folderpath ){

		$folderpath = EH_ABSPATH.'content/'.$folderpath;

		if( ! is_dir( $folderpath ) ) return array(); // TODO: error handling: $folderpath is no directory

		$files = array();

		if( $handle = opendir($folderpath) ){
			while( false !== ($file = readdir($handle)) ){
				if( '.' === $file ) continue;
				if( '..' === $file ) continue;
				if( is_dir($file) ) continue;
				if( substr($file,0,1) == '.' ) continue;

				$files[] = $file;
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

}
