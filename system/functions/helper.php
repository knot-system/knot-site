<?php

// Core Version: 0.1.0


function get_eigenheim_version( $abspath ){
	return trim(file_get_contents($abspath.'system/version.txt'));
}


function url( $path = '', $trailing_slash = true ) {
	global $core;
	
	$path = $core->baseurl.$path;

	if( $trailing_slash ) {
		$path = trailing_slash_it($path);
	}
	
	return $path;
}


function trailing_slash_it( $string ){
	// add a slash at the end, if there isn't already one ..

	$string = preg_replace( '/\/*$/', '', $string );
	$string .= '/';

	return $string;
}


function un_trailing_slash_it( $string ) {
	// remove slash at the end

	$string = preg_replace( '/\/*$/', '', $string );

	return $string;
}


function add_stylesheet( $path, $type = 'theme' ) {
	global $core;
	$core->theme->add_stylesheet( $path, $type );
}

function remove_stylesheet( $path, $type = 'theme' ) {
	global $core;
	$core->theme->remove_stylesheet( $path, $type );
}


function add_script( $path, $type = 'theme', $loading = false, $footer = false ) {
	global $core;
	$core->theme->add_script( $path, $type, $loading, $footer );
}

function remove_script( $path, $type = 'theme' ) {
	global $core;
	$core->theme->remove_script( $path, $type );
}


function add_metatag( $name, $string, $position = false ) {
	global $core;
	$core->theme->add_metatag( $name, $string, $position );
}

function remove_metatag( $name, $position = false ) {
	global $core;
	$core->theme->remove_metatag( $name, $position );
}


function snippet( $path, $args = array(), $return = false ) {
	global $core;
	return $core->theme->snippet( $path, $args, $return );
}


function get_class_attribute( $classes ) {

	if( ! is_array( $classes ) ) $classes = explode( ' ', $classes );

	$classes = array_unique( $classes ); // remove double class names
	$classes = array_filter( $classes ); // remove empty class names

	if( ! count($classes) ) return '';

	return ' class="'.implode( ' ', $classes ).'"';
}


function sanitize_string_for_url( $string ) {

	// Entferne alle nicht druckbaren ASCII-Zeichen
	$string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);

	$string = mb_strtolower($string);

	$string = str_replace(array("ä", "ö", "ü", "ß"), array("ae", "oe", "ue", "ss"), $string);

	// Ersetze Sonderzeichen durch '-'
	$string = preg_replace('/[^\p{L}\p{N}]+/u', '-', $string);

	$string = trim($string, '-');
	
	return $string;
}


function get_post_id_from_slug( $slug ) {

	global $core;
	$posts = $core->posts->posts;
	foreach( $posts as $post_id => $post ) {

		if( ! isset($post->slug) ) continue;

		if( $post->slug == $slug ) {
			return $post_id;
		}
	}

	return false;
}



function get_navigation(){

	global $core;

	$pages = $core->pages->get();

	if( ! $pages ) return false;

	$route = $core->route;
	$current_page_id = false;
	if( $route->get('template') == 'page' && ! empty($route->get('args')['page_id']) ) {
		$current_page_id = $route->get('args')['page_id'];
	}

	$navigation = false;

	foreach( $pages as $page ) {

		$is_current_page = false;
		if( $current_page_id && $page->id == $current_page_id ) {
			$is_current_page = true;
		}

		$navigation[] = array(
			'title' => $page->fields['title'],
			'permalink' => $page->fields['permalink'],
			'is_current_page' => $is_current_page
		);
		
	}

	if( ! count($navigation) ) return false;

	return $navigation;
}


function get_hash( $input ) {
	// NOTE: this hash is for data validation, NOT cryptography!
	// DO NOT USE FOR CRYPTOGRAPHIC PURPOSES


	// TODO: check if we want to create the hash like this
	$hash = hash( 'tiger128,3', $input );

	return $hash;
}


function doing_feed(){
	// currently displaying rss or json feed
	
	global $core;

	if( empty($core->doing_feed) ) return false;

	return !! $core->doing_feed;
}


function read_folder( $folderpath, $recursive = false ) {

	global $core;

	$files = [];

	if( ! is_dir( $folderpath ) ) {
		$core->debug( $folderpath.' is no directory' );
		return array();
	}

	$filename = false;
	if( $handle = opendir($folderpath) ){
		while( false !== ($file = readdir($handle)) ){
			if( substr($file,0,1) == '.' ) continue; // skip hidden files, ./ and ../

			if( is_dir($folderpath.$file) ) {

				if( $recursive ) {
					$files = array_merge( $files, read_folder($folderpath.$file.'/', $recursive));
				}

				continue;
			}

			$files[] = $folderpath.$file;

		}
		closedir($handle);
	} else {
		$core->debug( 'could not open dir', $folderpath );
		return array();
	}

	return $files;
}


function head_html(){

	global $core;

	$body_classes = array();

	$color_scheme = $core->config->get('theme-color-scheme');
	if( $color_scheme ) $body_classes[] = 'theme-color-scheme-'.$color_scheme;

?><!DOCTYPE html>
<!--
___________.__                     .__           .__         
\_   _____/|__| ____   ____   ____ |  |__   ____ |__| _____  
 |    __)_ |  |/ ___\_/ __ \ /    \|  |  \_/ __ \|  |/     \ 
 |        \|  / /_/  >  ___/|   |  \   Y  \  ___/|  |  Y Y  \
/_______  /|__\___  / \___  >___|  /___|  /\___  >__|__|_|  /
		\/   /_____/      \/     \/     \/     \/         \/ 
-->
<html lang="en">
<head>
<?php
	$core->theme->print_metatags( 'header' );
?>


<?php
	$core->theme->print_stylesheets();
?>

<?php
	$core->theme->print_scripts();

	?>
	
</head>
<body<?= get_class_attribute($body_classes) ?>><?php

}

function foot_html(){

	global $core;

	$core->theme->print_metatags( 'footer' );
?>

<?php
	$core->theme->print_scripts( 'footer' );

?>


</body>
</html>
<?php
}
