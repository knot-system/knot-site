<?php

class Database {

	public $objects = array();

	function __construct( $eigenheim, $folderpath_input, $recursive = false, $filename = false ) {

		$files = $this->read_dir( $eigenheim, $folderpath_input, $recursive, $filename );

		if( ! count($files) ) return $this;

		$objects = array();
		foreach( $files as $filename ) {
			$file = new File( $eigenheim, $filename );
			$objects[$file->sort] = $file;
		}

		ksort( $objects );

		$this->objects = $objects;

		return $this;
	}


	function read_dir( $eigenheim, $folderpath_input, $recursive = false, $filename = false ) {

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
						$files = array_merge( $files, $this->read_dir( $eigenheim, $folderpath_input.$file.'/', true, $filename ) );
					}
					continue;
				}

				if( $filename && strtolower($file) != strtolower($filename) ) continue;

				$files[] = $folderpath_input.$file;
			}
			closedir($handle);
		} else {
			$eigenheim->debug( 'could not open dir', $folderpath );
			return array();
		}

		asort($files);

		return $files;
	}


	function get() {
		return $this->objects;
	}


	function reverse() {
		$this->objects = array_reverse($this->objects);

		return $this;
	}

}
