<?php

// Core Version: 0.1.0

class Theme {

	public $folder_path;
	public $path;
	public $url;
	public $config;

	public $stylesheets = array();
	public $scripts = array();
	public $metatags = array();


	function __construct( $core ) {

		$theme_name = $core->config->get('theme');

		if( ! file_exists( $core->abspath.'theme/'.$theme_name.'/theme.php' ) ) {
			$theme_name = 'default';
		}

		$file_path = $core->abspath.'theme/'.$theme_name.'/theme.php';
		$this->config = $this->load_theme_config_from_file( $file_path );

		$this->folder_name = $theme_name;
		$this->path = 'theme/'.$theme_name.'/';
		$this->url = url('theme/'.$theme_name.'/');

		$this->add_stylesheet( 'css/eigenheim.css', 'global' );

		$this->add_metatag( 'script_eigenheim', '<script type="text/javascript">const Eigenheim = { API: { url: "'.url(api_get_endpoint()).'" } };</script>', 'footer' );
		$this->add_script( 'js/eigenheim.js', 'global', 'async', true );


		$this->add_metatag( 'charset', '<meta charset="utf-8">' );
		$this->add_metatag( 'viewport', '<meta name="viewport" content="width=device-width,initial-scale=1.0">' );
		$this->add_metatag( 'title', '<title>'.$core->config->get('site_title').'</title>' );

		$author = get_author_information();
		if( ! empty( $author['display_name'] ) ) {
			$this->add_metatag( 'author', '<meta name="author" content="'.$author['display_name'].'">' );
		}

		$this->add_metatag( 'generator', '<meta tag="generator" content="Eigenheim v.'.$core->version().'">' );

		$this->add_metatag( 'auth_endpoint', '<link rel="authorization_endpoint" href="https://indieauth.com/auth">' );
		$this->add_metatag( 'token_endpoint', '<link rel="token_endpoint" href="https://tokens.indieauth.com/token">' );
		$this->add_metatag( 'auth_mail', '<link rel="me authn" href="mailto:'.$core->config->get('auth_mail').'">' );
		$this->add_metatag( 'micropub', '<link rel="micropub" href="'.micropub_get_endpoint( true ).'">' );
		$microsub_endpoint = $core->config->get('microsub');
		if( $microsub_endpoint ) {
			$this->add_metatag( 'microsub', '<link rel="microsub" href="'.$microsub_endpoint.'">' );
		}

		$this->add_metatag( 'feed_rss', '<link rel="alternate" type="application/rss+xml" title="'.$core->config->get('site_title').' RSS Feed" href="'.url('feed/rss').'">' );
		$this->add_metatag( 'feed_json', '<link rel="alternate" type="application/json" title="'.$core->config->get('site_title').' JSON Feed" href="'.url('feed/json').'">' );



		// expand eigenheim config options:
		$config_path = $core->abspath.'theme/'.$theme_name.'/config.php';
		if( file_exists( $config_path ) ) {
			$core->config->load_config_file( $config_path );
			// we need to overwrite it with the local user config again:
			$core->config->load_config_file( $core->abspath.'config.php' );
		}

	}


	function load(){
		global $core;
		$core->include( $this->path.'functions.php' );
	}


	function load_theme_config_from_file( $file_path ) {

		global $core;

		if( ! file_exists($file_path) ) {
			$core->debug( 'no config file found', $file_path );
			exit;
		}

		$config = include( $file_path );

		return $config;
	}


	function get( $key = false ) {

		if( ! $key ) return $this->config;

		if( array_key_exists($key, $this->config) ) return $this->config[$key];
		
		return false;
	}


	function add_stylesheet( $path, $type = 'theme' ) {

		global $core;

		$global_path = $core->abspath.'system/site/assets/';
		$global_url = $core->baseurl.'/system/site/assets/';

		if( $type == 'theme' && file_exists($this->path.$path) ) {
			$type = 'theme';
			$url = $this->url.$path;
		} elseif( $type == 'global' && file_exists($global_path.$path) ) {
			$type = 'global';
			$url = $global_url.$path;
		} else {
			return;
		}

		$this->stylesheets[$path] = [
			'path' => $path,
			'url' => $url,
			'type' => $type
		];
	}


	function remove_stylesheet( $path ) {
		if( ! array_key_exists($path, $this->stylesheets) ) return;

		unset($this->stylesheets[$path]);
	}


	function print_stylesheets() {

		global $core;

		foreach( $this->stylesheets as $stylesheet ) {
			if( $stylesheet['type'] == 'global' ) {
				$version = $core->version();
			} else {
				$version = $this->get('version');
			}

			if( $core->config->get('debug') ) {
				$version .= '.'.time();
			}

		?>
	<link rel="stylesheet" href="<?= $stylesheet['url'] ?>?v=<?= $version ?>">
<?php
		}

	}


	function add_script( $path, $type = 'theme', $loading = false, $footer = false ) {

		// $loading is meant for 'async' or 'defer' attributes

		global $core;

		$global_path = $core->abspath.'system/site/assets/';
		$global_url = $core->baseurl.'/system/site/assets/';

		if( $type == 'theme' && file_exists($this->path.$path) ) {
			$type = 'theme';
			$url = $this->url.$path;
		} elseif( $type == 'global' && file_exists($global_path.$path) ) {
			$type = 'global';
			$url = $global_url.$path;
		} else {
			return;
		}

		$this->scripts[$path] = [
			'path' => $path,
			'url' => $url,
			'type' => $type,
			'loading' => $loading,
			'footer' => $footer
		];
	}


	function remove_script( $path ) {
		if( ! array_key_exists($path, $this->scripts) ) return;

		unset($this->scripts[$path]);
	}


	function print_scripts( $position = false ) {

		global $core;

		foreach( $this->scripts as $script ) {

			if( $script['footer'] && $position != 'footer' ) continue;
			elseif( ! $script['footer'] && $position == 'footer' ) continue;

			if( $script['type'] == 'global' ) {
				$version = $core->version();
			} else {
				$version = $this->get('version');
			}

			if( $core->config->get('debug') ) {
				$version .= '.'.time();
			}

			$loading = '';
			if( ! empty($script['loading']) ) $loading = ' '.$script['loading'];

		?>
	<script<?= $loading ?> src="<?= $script['url'] ?>?v=<?= $version ?>"></script>
<?php
		}

	}


	function add_metatag( $name, $string, $position = false ) {

		if( ! $position ) $position = 'header';

		if( ! array_key_exists( $position, $this->metatags ) ) $this->metatags[$position] = array();

		if( array_key_exists($name, $this->metatags) ) {
			global $core;
			$core->debug('a metatag with this name already exists, it gets overwritten', $name, $string);
		}

		$this->metatags[$position][$name] = $string;
	}


	function remove_metatag( $name, $position ) {

		if( ! empty($this->metatags[$position]) && ! array_key_exists($name, $this->metatags[$position]) ) return;

		unset($this->metatags[$position][$name]);

	}


	function print_metatags( $position = false ) {

		if( ! $position ) $position = 'header';

		if( empty($this->metatags[$position]) ) return;

		foreach( $this->metatags[$position] as $name => $string ) {
			echo "\n	".$string;
		}

	}


	function snippet( $path, $args = array(), $return = false ) {
		
		global $core;

		$snippet_path = 'snippets/'.$path.'.php';

		if( file_exists($this->path.$snippet_path) ) {
			$include_path = $this->path.$snippet_path;
		} else {
			$include_path = 'system/site/'.$snippet_path;
		}

		if( ! file_exists( $core->abspath.$include_path) ) return;

		ob_start();

		$core->include( $include_path, $args );

		$snippet = ob_get_contents();
		ob_end_clean();

		if( $return === true ) {
			return $snippet;
		}

		echo $snippet;

	}


}
