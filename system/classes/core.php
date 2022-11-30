<?php

class Eigenheim {

	private const VERSION = 'alpha.9';

	// TODO: check, if we want those variable to be public:

	public $abspath;
	public $basefolder;
	public $baseurl;

	public $log;
	public $config;
	public $theme;

	public $pages;
	public $posts;

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
		$this->theme = new Theme( $this );
		$this->pages = new Pages( $this );
		$this->posts = new Posts( $this );

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

	// TODO: move to helper class
	function url( $path = '' ) {
		$path = $this->baseurl.$path;
		$path = $this->trailing_slash_it($path);
		return $path;
	}

	// TODO: move to helper class
	function trailing_slash_it( $string ) {
		// add a slash at the end, if there isn't already one ..

		$string = preg_replace( '/\/*$/', '', $string );
		$string .= '/';

		return $string;
	}

	// TODO: move to image class
	function get_image_html( $image_path ) {

		$eigenheim = $this;

		$target_width = $eigenheim->config->get( 'image_target_width', 1200 );

		$image_meta = getimagesize( $eigenheim->abspath.'content/'.$image_path );
		$src_width = $image_meta[0];
		$src_height = $image_meta[1];

		$target_dimensions = $eigenheim->get_image_dimensions( $target_width, $src_width, $src_height );

		$width = $target_dimensions[0];
		$height = $target_dimensions[1];

		$html = '<img src="'.$eigenheim->baseurl.'content/'.$image_path.'" width="'.$width.'" height="'.$height.'" loading="lazy">';

		return $html;

	}

	// TODO: move to image class
	function get_image_dimensions( $target_width, $src_width, $src_height ) {

		if( $src_width <= $target_width ) {
			return array( $src_width, $src_height );
		}
		
		$width = $target_width;
		$height = (int) round($src_height/$src_width*$width);

		return array( $width, $height );
	}

	// TODO: move to own class
	function get_author_information( $raw = false ) {
		$eigenheim = $this;

		$author = array();

		$conf = $eigenheim->config->get('author');

		if( ! $raw ) {

			// "special" named fields:
			if( ! empty($conf['p-name']) ) $author['display_name'] = $conf['p-name'];
			if( ! empty($conf['p-given-name']) ) $author['given_name'] = $conf['p-given-name'];
			if( ! empty($conf['p-last-name']) ) $author['family_name'] = $conf['p-last-name'];
			if( ! empty($conf['p-note']) ) $author['description'] = $conf['p-note'];
			if( ! empty($conf['u-email']) ) $author['email'] = $conf['u-email'];
			if( ! empty($conf['u-url']) ) $author['url'] = $conf['u-url'];
			if( ! empty($conf['u-photo']) ) $author['avatar'] = $conf['u-photo'];

			if( ! empty($author['given_name']) && ! empty($author['family_name']) && empty($author['display_name']) ) $author['display_name'] = $author['given_name'].' '.$author['family_name'];

		}

		// see https://microformats.org/wiki/h-card
		// TODO: we need to test those fields
		$additional_hcard_properties = array(
			'p-name',
			'p-honorific-prefix',
			'p-given-name',
			'p-additional-name',
			'p-family-name',
			'p-sort-string',
			'p-honorific-suffix',
			'p-nickname',
			'u-email',
			'u-logo',
			'u-photo',
			'u-url',
			'u-uid',
			'p-category',
			'p-adr',
			//'h-adr',
			'p-post-office-box',
			'p-extended-address',
			'p-street-address',
			'p-locality',
			'p-region',
			'p-postal-code',
			'p-country-name',
			'p-label',
			'p-geo',
			'u-geo',
			//'h-geo',
			'p-latitude',
			'p-longitude',
			'p-altitude',
			'p-tel',
			'p-note',
			'dt-bday',
			'u-key',
			'p-org',
			'p-job-title',
			'p-role',
			'u-impp',
			'p-sex',
			'p-gender-identity',
			'dt-anniversary',
			//'u-sound'
		);

		foreach( $additional_hcard_properties as $additional_hcard_property ) {
			if( ! empty($conf[$additional_hcard_property]) ) $author[$additional_hcard_property] = $conf[$additional_hcard_property];
		}

		return $author;

	}

}
