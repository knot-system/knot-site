<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function snippet( $path, $return = false ) {
	
	$include_path = EH_ABSPATH.'site/snippets/'.$path.'.php';

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


function get_posts(){
	// TODO: get_posts() should work differently. don't know how exactly yet. but this function will very likely be replaced.

	$files = \Eigenheim\Files::read_dir( '' );

	if( ! count($files) ) return array();

	$posts = array();

	foreach( $files as $filename ){

		$file_contents = \Eigenheim\Files::read_file( $filename );

		$title = $file_contents['name'];
		$text = \Eigenheim\Text::auto_p($file_contents['content']);

		if( ! $text ) continue;

		// for now, the filename is the timestamp. THIS WILL CHANGE IN THE FUTURE.
		$timestamp = str_replace( '.txt', '', $filename );

		$posts[] = array(
			'title' => $title,
			'text' => $text,
			'timestamp' => $timestamp
		);

	}

	return $posts;

}


function trailingslashit( $string ){
	// add a slash at the end, if there isn't already one ..

	$string = preg_replace( '/\/$/', '', $string );
	$string .= '/';

	return $string;
}