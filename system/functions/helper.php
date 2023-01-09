<?php


function url( $path = '', $trailing_slash = true ) {
	global $eigenheim;
	
	$path = $eigenheim->baseurl.$path;

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
	global $eigenheim;
	$eigenheim->theme->add_stylesheet( $path, $type );
}


function remove_stylesheet( $path, $type = 'theme' ) {
	global $eigenheim;
	$eigenheim->theme->remove_stylesheet( $path );
}


function add_script( $path, $type = 'theme', $loading = false ) {
	global $eigenheim;
	$eigenheim->theme->add_script( $path, $type, $loading );
}


function remove_script( $path, $type = 'theme' ) {
	global $eigenheim;
	$eigenheim->theme->remove_script( $path );
}


function snippet( $path, $args = array(), $return = false ) {
	global $eigenheim;
	return $eigenheim->theme->snippet( $path, $args, $return );
}


function get_class_attribute( $classes ) {

	if( ! is_array( $classes ) ) $classes = explode( ' ', $classes );

	$classes = array_unique( $classes ); // remove double class names
	$classes = array_filter( $classes ); // remove empty class names

	if( ! count($classes) ) return '';

	return ' class="'.implode( ' ', $classes ).'"';
}


function get_navigation(){

	global $eigenheim;

	$pages = $eigenheim->pages->get();

	if( ! $pages ) return false;

	$route = $eigenheim->route;
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
	
	global $eigenheim;

	if( empty($eigenheim->doing_feed) ) return false;

	return !! $eigenheim->doing_feed;
}


function read_folder( $folderpath, $recursive = false ) {

	global $eigenheim;

	$files = [];

	if( ! is_dir( $folderpath ) ) {
		$eigenheim->debug( $folderpath.' is no directory' );
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
		$eigenheim->debug( 'could not open dir', $folderpath );
		return array();
	}

	return $files;
}


function head_html(){

	global $eigenheim;

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
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title><?= $eigenheim->config->get('site_title') ?></title>
<?php
	$author = get_author_information();
	if( ! empty( $author['display_name'] ) ) :
	?>
	<meta name="author" content="<?= $author['display_name'] ?>">
<?php
	endif;
	?>

<?php
	$eigenheim->theme->print_stylesheets();
	$eigenheim->theme->print_scripts();
	?>

	<link rel="authorization_endpoint" href="https://indieauth.com/auth">
	<link rel="token_endpoint" href="https://tokens.indieauth.com/token">
	<link rel="me authn" href="mailto:<?= $eigenheim->config->get('auth_mail') ?>">
	<link rel="micropub" href="<?= micropub_get_endpoint( true ).'/' ?>">
<?php
	$microsub_endpoint = $eigenheim->config->get('microsub');
	if( $microsub_endpoint ) :
	?>
	<link rel="microsub" href="<?= $microsub_endpoint ?>">
<?php
	endif;
	?>

	<link rel="alternate" type="application/rss+xml" title="<?= $eigenheim->config->get('site_title') ?> RSS Feed" href="<?= url('feed/rss') ?>">
	<link rel="alternate" type="application/json" title="<?= $eigenheim->config->get('site_title') ?> JSON Feed" href="<?= url('feed/json') ?>">
	
</head>
<body><?php

}

function foot_html(){
?></body>
</html>
<?php
}
