<?php

//if( ! defined( 'EH_ABSPATH' ) ) exit; // this is not yet defined

class Eigenheim {

	private const VERSION = 'alpha.8';

	public $abspath;
	public $basefolder;
	public $baseurl;

	public $debug = false;

	function __construct() {

		$abspath = realpath(dirname(__FILE__)).'/';
		$abspath = preg_replace( '/system\/$/', '', $abspath );
		$this->abspath = $abspath;

		$basefolder = str_replace( 'index.php', '', $_SERVER['PHP_SELF']);
		$this->basefolder = $basefolder;

		if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
		else $baseurl = 'http://';
		$baseurl .= $_SERVER['HTTP_HOST'];
		$baseurl .= $basefolder;
		$this->baseurl = $baseurl;

		// TODO: get rid of these constants
		define( 'EH_ABSPATH', $this->abspath );
		define( 'EH_BASEFOLDER', $this->basefolder );
		define( 'EH_BASEURL', $this->baseurl );

		$this->debug = true; // TODO: get this from the config file

	}

	function include( $file_path ) {

		$full_file_path = $this->abspath.$file_path;

		if( ! file_exists($full_file_path) ) {

			if( $this->debug ) echo '<p><strong>Error:</strong> include not found</p>';
			exit;
		}

		include_once( $full_file_path );

	}

	function get_version() {
		return self::VERSION;
	}

}


// TODO: get rid of this function (use $eigenheim->get_version() instead)
function eigenheim_get_version(){
	global $eigenheim;

	return $eigenheim->get_version();
}
