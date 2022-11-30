<?php

class Eigenheim {

	private const VERSION = 'alpha.8';

	public $abspath;
	public $basefolder;
	public $baseurl;

	public $config;
	public $log;

	function __construct() {

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

	}

	function include( $file_path, $args = array() ) {

		$eigenheim = $this;

		$full_file_path = $this->abspath.$file_path;

		if( ! file_exists($full_file_path) ) {

			if( $this->config->get('debug') ) echo '<p><strong>Error:</strong> include not found</p>';
			exit;
		}

		include( $full_file_path );

	}

	function version() {
		return self::VERSION;
	}

}
