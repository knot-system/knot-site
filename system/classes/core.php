<?php

class Eigenheim {

	private const VERSION = 'alpha.14';

	// TODO: check, if we want those variables to be public:

	public $abspath;
	public $basefolder;
	public $baseurl;

	public $config;
	public $log;
	public $theme;

	public $pages;
	public $posts;

	public $route;
	
	function __construct() {

		global $eigenheim;
		$eigenheim = $this;

		$abspath = realpath(dirname(__FILE__)).'/';
		$abspath = preg_replace( '/system\/classes\/$/', '', $abspath );
		$this->abspath = $abspath;

		$basefolder = str_replace( 'index.php', '', $_SERVER['PHP_SELF']);
		$this->basefolder = $basefolder;

		if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
		else $baseurl = 'http://';
		$baseurl .= $_SERVER['HTTP_HOST'];
		$baseurl .= $basefolder;
		$this->baseurl = $baseurl;

		$this->config = new Config( $this );
		$this->log = new Log( $this );
		$this->theme = new Theme( $this );

		$this->pages = new Pages( $this );
		$this->posts = new Posts( $this );

		$this->route = new Route( $this );

		$this->refresh_cache();

	}

	function debug( ...$messages ) {

		if( $this->config->get('logging') ) {
			$this->log->message( ...$messages );
		}

		if( $this->config->get('debug') ) {
			echo '<hr><strong>ERROR</strong>';
			foreach( $messages as $message ) {
				echo '<br>'.$message;
			}
		}

	}

	function include( $file_path, $args = array() ) {

		$eigenheim = $this;

		$full_file_path = $this->abspath.$file_path;

		if( ! file_exists($full_file_path) ) {
			$this->debug( 'include not found' );
			exit;
		}

		include( $full_file_path );

	}

	function version() {
		return self::VERSION;
	}


	function refresh_cache() {
		// NOTE: see system/classes/cache.php for general cache handling
		// this function clears out old cache files.

		$folderpath = $this->abspath.'cache/';

		$files = read_folder( $folderpath, true );

		foreach( $files as $file ) {
			$timestamp = filemtime($file);
			
			$lifetime = $this->config->get( 'cache_lifetime' );

			if( time()-$timestamp > $lifetime ) {
				// cachefile too old
				@unlink($file); // delete old cache file; fail silently
			}

		}

	}

}
