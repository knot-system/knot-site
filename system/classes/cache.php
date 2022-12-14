<?php

class Cache {

	private $cache_folder = 'cache/';
	private $cache_file;

	public $type;
	public $name;
	public $hash;
	public $filesize;
	
	function __construct( $type, $name ) {

		// TODO: add config option to disable cache

		global $eigenheim;

		if( $type == 'image' || $type == 'image-preview' ) {
			$this->cache_folder .= 'images/';
		} elseif( $type == 'link' ) {
			$this->cache_folder .= 'link-previews/';
		}

		$this->checkCacheFolder();

		$this->type = $type;
		$this->name = $name;

		// TODO: check if we want to create the hash like this
		$this->hash = hash( 'tiger128,3', $this->name );

		$this->cache_file = $this->cache_folder.$this->hash;

	}


	function getData() {
		if( ! file_exists($this->cache_file) ) return false;

		$this->filesize = filesize($this->cache_file);

		$cache_content = file_get_contents($this->cache_file);

		return $cache_content;
	}


	function addData( $data ) {
		global $eigenheim;

		if( ! file_put_contents( $eigenheim->abspath.$this->cache_file, $data ) ) {
			$eigenheim->debug( 'could not create cache file', $this->cache_file );
		}
	}


	private function checkCacheFolder(){
		global $eigenheim;

		if( is_dir($eigenheim->abspath.$this->cache_folder) ) return;

		if( mkdir( $eigenheim->abspath.$this->cache_folder, 0777, true ) === false ) {
			$eigenheim->debug( 'could not create cache dir', $this->cache_folder );
		}

	}


};
