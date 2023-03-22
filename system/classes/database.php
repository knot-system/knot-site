<?php


class Database {

	public $objects = array();

	function __construct( $folderpath_input, $recursive = false, $filename = false ) {

		global $core;

		$files = $this->read_dir( $folderpath_input, $recursive, $filename );

		if( ! count($files) ) return $this;

		$objects = array();
		foreach( $files as $files_filename ) {
			$file = new File( $files_filename );
			$objects[$file->sort] = $file;
		}

		ksort( $objects );

		$this->objects = $objects;

		return $this;
	}


	function read_dir( $folderpath_input, $recursive = false, $filename = false ) {

		global $core;

		$folderpath = $core->abspath.'content/'.$folderpath_input;

		if( ! is_dir( $folderpath ) ) {
			$core->debug( $folderpath.' is no directory' );
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
			$core->debug( 'could not open dir', $folderpath );
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
