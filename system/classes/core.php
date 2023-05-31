<?php


class Core {

	// TODO: check, if we want those variables to be public:

	public $version;

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

		global $core;
		$core = $this;

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


		$this->version = get_system_version( $abspath );


		$this->config = new Config();

		// overwrite the baseurl and/or the basefolder, because we are running in a 'transparent' subfolder; see #transparent-subfolder in the README.md for details
		if( $this->config->get('baseurl_overwrite') ) {
			$baseurl = $this->config->get('baseurl_overwrite');
			$this->baseurl = $baseurl;
		}
		if( $this->config->get('basefolder_overwrite') ) {
			$basefolder = $this->config->get('basefolder_overwrite');
			$this->basefolder = $basefolder;
		}


		
		if( ! $this->config->get('debug') ) {
			error_reporting(0);
		}


		$this->log = new Log();


		$this->theme = new Theme();

		$this->theme->add_stylesheet( 'css/eigenheim.css', 'global' );

		$this->theme->add_metatag( 'script_eigenheim', '<script type="text/javascript">const Eigenheim = { API: { url: "'.url(api_get_endpoint()).'" } };</script>', 'footer' );
		$this->theme->add_script( 'js/eigenheim.js', 'global', 'async', true );


		$this->theme->add_metatag( 'charset', '<meta charset="utf-8">' );
		$this->theme->add_metatag( 'viewport', '<meta name="viewport" content="width=device-width,initial-scale=1.0">' );
		$this->theme->add_metatag( 'title', '<title>'.get_config('site_title').'</title>' );

		$author = get_author_information();
		if( ! empty( $author['display_name'] ) ) {
			$this->theme->add_metatag( 'author', '<meta name="author" content="'.$author['display_name'].'">' );
		}

		$this->theme->add_metatag( 'generator', '<meta tag="generator" content="Eigenheim v.'.$core->version().'">' );


		// IndieAuth

		$indieauth_metadata = $core->config->get( 'indieauth-metadata' );
		if( $indieauth_metadata ) {

			if( $indieauth_metadata === true ) {
				// use internal link
				$indieauth_metadata = api_get_endpoint( true ).'indieauth-metadata'; // TODO: check endpoint, maybe move to own class
			}

			$this->add_endpoint( 'indieauth-metadata', $indieauth_metadata );

		} else {
			// old behaviour, fallback
			// TODO: remove this old behavior someday, and default to $indieauth_metadata

			$this->add_endpoint( 'authorization_endpoint' );
			$this->add_endpoint( 'token_endpoint' );

		}

		$me_authn = $this->config->get( 'me_authn' );
		if( $me_authn ) {
			
			// TODO: remove default behavior someday, but allow 'me_authn' config option
			if( $me_authn === true ) {
				// default behavior: auth via email
				$auth_email = $this->config->get( 'auth_mail' );
				if( $auth_email ) {
					$me_authn = 'mailto:'.$auth_email;
				}
			}
		
			$this->theme->add_metatag( 'me_authn', '<link rel="me authn" href="'.$me_authn.'">' );
		}
		

		// micropub endpoint
		$this->add_endpoint( 'micropub', micropub_get_endpoint( true ) );

		// microsub endpoint
		$this->add_endpoint( 'microsub' );


		// RSS / JSON feed
		$this->theme->add_metatag( 'feed_rss', '<link rel="alternate" type="application/rss+xml" title="'.get_config('site_title').' RSS Feed" href="'.url('feed/rss').'">' );
		$this->theme->add_metatag( 'feed_json', '<link rel="alternate" type="application/json" title="'.get_config('site_title').' JSON Feed" href="'.url('feed/json').'">' );


		$this->pages = new Pages();
		$this->posts = new Posts();

		$this->route = new Route();

		$this->refresh_cache();

	}

	function debug( ...$messages ) {

		if( $this->config->get('logging') ) {
			$this->log->message( ...$messages );
		}

		if( $this->config->get('debug') ) {
			echo '<div class="debugmessage"><strong class="debugmessage-head">DEBUGMESSAGE</strong><pre>';
			$first = true;
			foreach( $messages as $message ) {
				if( is_array($message) || is_object($message) ) $message = var_export($message, true);
				if( ! $first ) echo '<br>';
				echo $message;
				$first = false;
			}
			echo '</pre></div>';
		}

	}

	function include( $file_path, $args = array() ) {

		$core = $this;

		$full_file_path = $this->abspath.$file_path;

		if( ! file_exists($full_file_path) ) {
			$this->debug( 'include not found' );
			exit;
		}

		include( $full_file_path );

	}

	function version() {
		return $this->version;
	}

	function add_endpoint( $rel, $url = false ){

		if( ! $url ) {
			$url = $this->config->get( $rel );
		}

		if( ! $url ) {
			return $this;
		}

		// TODO: $url needs to encode all char codes greater than 255, see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Link#encoding_urls

		if( $this->config->get('endpoint-discovery-via-header') ) {

			$this->theme->add_header( $rel, 'Link: <'.$url.'>; rel="'.$rel.'"');

		} else {

			$this->theme->add_metatag( $rel, '<link rel="'.$rel.'" href="'.$url.'">' );

		}

		return $this;
	}


	function refresh_cache() {
		// NOTE: see system/classes/cache.php for general cache handling
		// this function clears out old cache files.

		$lifetime = $this->config->get( 'cache_lifetime' );

		$folderpath = $this->abspath.'cache/';

		$files = read_folder( $folderpath, true );

		foreach( $files as $file ) {
			$timestamp = filemtime($file);
			
			if( time()-$timestamp > $lifetime ) { // cachefile too old
				@unlink($file); // delete old cache file; fail silently
			}

		}

	}

}
