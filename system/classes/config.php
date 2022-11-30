<?php

class Config {

	public $config = array(
		'posts_per_page' => 5
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
