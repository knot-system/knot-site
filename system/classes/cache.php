<?php

class Cache {

	private $cache_folder = 'cache/';
	private $cache_file;

	public $type;
	public $name;
	public $hash;
	public $filesize;
	
	function __construct( $type, $input, $use_hash = false ) {

		// TODO: add config option to disable cache

		// TODO: check other cache files, if we maybe want to delete one
		// every cache file should have a max timestamp in their filename, so we can
		// easily filter and delete cache files we no longer need, or that need refreshing

		global $eigenheim;

		if( $type == 'image' || $type == 'image-preview' ) {
			$this->cache_folder .= 'images/';
		} elseif( $type == 'link' ) {
			$this->cache_folder .= 'link-previews/';
		} else {
			$this->cache_folder .= $type.'/';
		}

		$this->checkCacheFolder();

		$this->type = $type;

		if( $use_hash ) {

			$this->hash = $input;

		} else {

			$this->name = $input;

			// TODO: check if we want to create the hash like this
			$this->hash = hash( 'tiger128,3', $this->name );

		}

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



	function get_remote_file( $url ) {

		$ch = curl_init( $url );
		$fp = fopen( $this->cache_file, 'wb' );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_USERAGENT, get_user_agent() );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );

		return $this;
	}


	private function checkCacheFolder(){
		global $eigenheim;

		if( is_dir($eigenheim->abspath.$this->cache_folder) ) return;

		if( mkdir( $eigenheim->abspath.$this->cache_folder, 0777, true ) === false ) {
			$eigenheim->debug( 'could not create cache dir', $this->cache_folder );
		}

	}


};
