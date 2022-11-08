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

		$posts[] = array(
			'title' => $title,
			'text' => $text
		);

	}

	return $posts;

}
