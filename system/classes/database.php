<?php

class Database {



	function __construct() {

	}


	function read_dir( $folderpath_input, $recursive = false, $filename = false, $reverse = false ) {

		global $eigenheim;

		$folderpath = $eigenheim->abspath.'content/'.$folderpath_input;

		if( ! is_dir( $folderpath ) ) {
			$eigenheim->debug( $folderpath.' is no directory' );
			return array();
		}

		$files = array();

		if( $handle = opendir($folderpath) ){
			while( false !== ($file = readdir($handle)) ){
				if( substr($file,0,1) == '.' ) continue; // skip hidden files, ./ and ../

				if( is_dir($folderpath.$file) ) {
					if( $recursive ){
						$files = array_merge( $files, $this->read_dir( $folderpath_input.$file.'/', true, $filename ) );
					}
					continue;
				}

				if( $filename && strtolower($file) != strtolower($filename) ) continue;

				$files[] = $folderpath_input.$file;
			}
			closedir($handle);
		} else {
			$eigenheim->debug( 'could not open dir' );
			return array();
		}

		asort($files);

		return $files;
	}
	
}
