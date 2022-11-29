<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function get_theme() {

	$theme_name = get_config( 'theme' );

	if( ! file_exists( EH_ABSPATH.'theme/'.$theme_name.'/config.php') ) {
		$theme_name = 'default';
	}

	$file_path = 'theme/'.$theme_name.'/config.php';
	$theme = load_theme_config_from_file( $file_path );

	$theme['_folder_name'] = $theme_name;
	$theme['_path'] = 'theme/'.$theme_name.'/';
	$theme['_url'] = url('theme/'.$theme_name.'/');

	return $theme;
}


function load_theme_config_from_file( $file_path ){

	if( ! file_exists(EH_ABSPATH.$file_path) ) {
		// TODO: add debug option to show or hide this message
		echo '<p><strong>no config file found</strong></p>';
		exit;
	}

	$config = include( EH_ABSPATH.$file_path );

	return $config;
}



function get_theme_data( $key = false ) {

	$theme_data = get_theme();

	unset($theme_data['_folder_name']);
	unset($theme_data['_path']);
	unset($theme_data['_url']);

	if( ! $key ) return $theme_data;

	if( array_key_exists($key, $theme_data) ) return $theme_data[$key];
	
	return false;
}


function add_stylesheet( $path ) {
	global $global_stylesheet_list;

	if( ! $global_stylesheet_list ) $global_stylesheet_list = array();

	$theme = get_theme();

	$global_stylesheet_list[] = $theme['_url'].$path;

}


function print_stylesheets() {
	global $global_stylesheet_list;

	if( ! is_array($global_stylesheet_list) || ! count($global_stylesheet_list) ) return;
	
	$version = get_theme_data('version');

	foreach( $global_stylesheet_list as $stylesheet ) {
	?>
	<link rel="stylesheet" href="<?= $stylesheet ?>?v=<?= $version ?>">
<?php
	}

}


function snippet( $path, $args = array(), $return = false ) {
	
	$snippet_path = 'snippets/'.$path.'.php';

	$theme = get_theme();

	if( file_exists($theme['_path'].$snippet_path) ) {
		$include_path = $theme['_path'].$snippet_path;
	} else {
		$include_path = EH_ABSPATH.'system/site/'.$snippet_path;
	}

	if( ! file_exists( $include_path) ) return;

	ob_start();

	include( $include_path );

	$snippet = ob_get_contents();
	ob_end_clean();

	if( $return === true ) {
		return $snippet;
	}

	echo $snippet;

}
