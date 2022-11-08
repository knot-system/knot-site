<?php

namespace Eigenheim;

if( ! defined( 'EH_ABSPATH' ) ) exit;

// TODO / CLEANUP: make this more robust

class Config {
	
	static function getConfig( $option = false ){

		$config = Config::loadConfig();

		if( $option ) return $config[$option];

		return $config;

	}

	static function loadConfig(){
		$config_file = EH_ABSPATH.'site/config.php';

		if( ! file_exists($config_file) ) {
			echo '<strong>no config file found</strong>';
			die();
		}

		$config = include( $config_file );

		return $config;
	}

}

