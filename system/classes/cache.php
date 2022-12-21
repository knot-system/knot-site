<?php

// NOTE: in system/classes/core.php there is also the 'refresh_cache()' function
// that takes care of deleting old, obsolete cache files

class Cache {

	private $cache_folder = 'cache/';
	private $cache_file;

	public $type;
	public $name;
	public $hash;
	public $cache_file_name;
	public $filesize;
	
	function __construct( $type, $input, $use_hash = false ) {

		// TODO: add config option to disable cache

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

			$this->hash = get_hash( $this->name );

		}

		$cache_file = $this->get_hashfile_by_hash( $this->hash );

		if( ! $cache_file ) {
			// if no cachefile exists for this hash, generate a new hashfile with the current timestamp
			$cache_file = time().'_'.$this->hash;
		}

		$this->cache_file = $this->cache_folder.$cache_file;
		$this->cache_file_name = $cache_file;

	}


	function getData() {
		if( ! file_exists($this->cache_file) ) return false;

		$this->filesize = filesize($this->cache_file);

		$cache_content = file_get_contents($this->cache_file);

		return $cache_content;
	}


	function addData( $data ) {
		global $eigenheim;

		// remove old cache file (fail silently, if the file vanished or something ..):
		@unlink( $eigenheim->abspath.$this->cache_file);

		// create a new cachefile, with new timestamp:
		$new_filename = time().'_'.$this->hash;
		$this->cache_file = $this->cache_folder.$new_filename;
		$this->cache_file_name = $new_filename;

		if( ! file_put_contents( $eigenheim->abspath.$this->cache_file, $data ) ) {
			$eigenheim->debug( 'could not create cache file', $this->cache_file );
		}

		return $this;
	}


	function get_remote_file( $url ) {

		$url = html_entity_decode($url);

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


	function get_hashfile_by_hash( $hash ) {
		global $eigenheim;

		// TODO: maybe use Database class?

		$folderpath = $this->cache_folder;

		if( ! is_dir( $folderpath ) ) {
			$eigenheim->debug( $folderpath.' is no directory' );
			return array();
		}

		$filename = false;
		if( $handle = opendir($folderpath) ){
			while( false !== ($file = readdir($handle)) ){
				if( substr($file,0,1) == '.' ) continue; // skip hidden files, ./ and ../

				if( ! str_ends_with( $file, $hash ) ) continue; // not the file we want

				$filename = $file;
			}
			closedir($handle);
		} else {
			$eigenheim->debug( 'could not open dir', $folderpath );
			return array();
		}

		return $filename;
	}


	private function checkCacheFolder(){
		global $eigenheim;

		if( is_dir($eigenheim->abspath.$this->cache_folder) ) return;

		if( mkdir( $eigenheim->abspath.$this->cache_folder, 0777, true ) === false ) {
			$eigenheim->debug( 'could not create cache dir', $this->cache_folder );
		}

		return $this;
	}


};
