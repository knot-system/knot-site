<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function get_theme() {

	$theme_name = get_config( 'theme' );

	if( ! file_exists( EH_ABSPATH.'theme/'.$theme_name.'/theme.php') ) {
		$theme_name = 'default';
	}

	global $theme_data;

	$theme = $theme_data;

	$theme['name'] = $theme_name;
	$theme['path'] = EH_ABSPATH.'theme/'.$theme_name.'/';
	$theme['url'] = url('theme/'.$theme_name.'/');

	return $theme;
}


function get_theme_data( $key = false ) {

	$theme_data = get_theme();

	unset($theme_data['path']);

	if( ! $key ) return $theme_data;

	if( array_key_exists($key, $theme_data) ) return $theme_data[$key];
	
	return false;
}


function add_stylesheet( $path ) {
	global $global_stylesheet_list;

	if( ! $global_stylesheet_list ) $global_stylesheet_list = array();

	$theme = get_theme();

	$global_stylesheet_list[] = $theme['url'].$path;

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

	if( file_exists($theme['path'].$snippet_path) ) {
		$include_path = $theme['path'].$snippet_path;
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
