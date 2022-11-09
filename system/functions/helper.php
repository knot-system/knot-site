<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function snippet( $path, $return = false, $args = array() ) {
	
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

		if( str_contains( $filename, '_draft_' ) ) continue; // skip drafts

		$file_contents = \Eigenheim\Files::read_file( $filename );

		$content_html = \Eigenheim\Text::auto_p($file_contents['content']);
		if( ! $content_html ) continue;

		$content_text = strip_tags($content_html); // TODO: revisit this in the future

		$title = '';
		if( ! empty($file_contents['name']) ) $title = $file_contents['name'];

		$tags = array();
		if( ! empty($file_contents['category']) ) $tags = json_decode( $file_contents['category'] ); 
		if( ! is_array($tags) ) $tags = array();

		// for now, the filename is the timestamp. THIS WILL CHANGE IN THE FUTURE.
		$timestamp = intval(str_replace( '.txt', '', $filename ));

		$id = $timestamp; // TODO: check how we want to handle the id. needs to be unique and should not change when editing this post.

		$permalink = EH_BASEURL.'#'.$id; // TODO: check how we want to handle permalinks to posts

		$date_published = date( 'c', $timestamp );

		$date_modified = $date_published; // TODO: add modified date

		$posts[] = array(
			'id' => $id,
			'title' => $title,
			'permalink' => $permalink,
			'content_html' => $content_html,
			'content_text' => $content_text,
			'tags' => $tags,
			'date_published' => $date_published,
			'date_modified' => $date_modified,
			'timestamp' => $timestamp // at the moment, this gets used by the rss feed and post.php
		);

	}

	return $posts;

}


function get_categories(){
	// TODO: revisit this in the future

	$files = \Eigenheim\Files::read_dir( '' );

	if( ! count($files) ) return array();

	$categories = array();

	foreach( $files as $filename ){

		$file_contents = \Eigenheim\Files::read_file( $filename );

		$text = \Eigenheim\Text::auto_p($file_contents['content']);
		if( ! $text ) continue;

		$tags = array();
		if( ! empty($file_contents['category']) ) $tags = json_decode( $file_contents['category'] ); 
		if( ! is_array($tags) ) $tags = array();

		foreach( $tags as $tag ) {
			$categories[] = $tag;
		}

	}

	$categories = array_unique( $categories );
	$categories = array_filter( $categories ); // remove empty entries
	$categories = array_values( $categories ); // get rid of keys

	return $categories;

}


function trailingslashit( $string ){
	// add a slash at the end, if there isn't already one ..

	$string = preg_replace( '/\/$/', '', $string );
	$string .= '/';

	return $string;
}
