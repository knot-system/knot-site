<?php

class Config {

	public $config = array(
		'posts_per_page' => 5,
		'allowed_html_elements' => [ 'p', 'br', 'i', 'b', 'em', 'strong', 'a', 'ul', 'ol', 'li', 'span' ],
		'image_cache_active' => true,
		'image_target_width' => 1200,
		'image_jpg_quality' => 70,
		'image_png_to_jpg' => true,
		'feed_limit_posts' => 20,
	);

	function __construct( $eigenheim ) {

		$config_file = $eigenheim->abspath.'config.php';

		if( ! file_exists($config_file) ) {
			echo '<p><strong>no config file found</strong></p>';
			exit;
		}

		$config = include( $config_file );

		$this->config = array_merge( $this->config, $config );

	}
	

	function get( $option = false, $fallback = false ) {

		if( $option ) {
			if( ! array_key_exists( $option, $this->config ) ) {
				return $fallback;
			}
			return $this->config[$option];
		}

		return $this->config;

	}

};
