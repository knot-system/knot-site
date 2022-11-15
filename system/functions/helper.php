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

	$author_information = get_author_information();
	$author = false;
	if( ! empty( $author_information['display_name'] ) ) $author = $author_information['display_name'];

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
			'author' => $author,
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


function get_author_information( $raw = false ){

	$author = array();

	$conf = \Eigenheim\Config::getConfig('author');

	if( ! $raw ) {

		// "special" named fields:
		if( ! empty($conf['p-name']) ) $author['display_name'] = $conf['p-name'];
		if( ! empty($conf['p-given-name']) ) $author['given_name'] = $conf['p-given-name'];
		if( ! empty($conf['p-last-name']) ) $author['family_name'] = $conf['p-last-name'];
		if( ! empty($conf['p-note']) ) $author['description'] = $conf['p-note'];
		if( ! empty($conf['u-email']) ) $author['email'] = $conf['u-email'];
		if( ! empty($conf['u-url']) ) $author['url'] = $conf['u-url'];

		if( ! empty($author['given_name']) && ! empty($author['family_name']) && empty($author['display_name']) ) $author['display_name'] = $author['given_name'].' '.$author['family_name'];

	}

// TODO: we need to test those fields
	$additional_hcard_properties = array(
		'p-name',
		'p-honorific-prefix',
		'p-given-name',
		'p-additional-name',
		'p-family-name',
		'p-sort-string',
		'p-honorific-suffix',
		'p-nickname',
		'u-email',
		'u-logo',
		'u-photo',
		'u-url',
		'u-uid',
		'p-category',
		'p-adr',
		//'h-adr',
		'p-post-office-box',
		'p-extended-address',
		'p-street-address',
		'p-locality',
		'p-region',
		'p-postal-code',
		'p-country-name',
		'p-label',
		'p-geo',
		'u-geo',
		//'h-geo',
		'p-latitude',
		'p-longitude',
		'p-altitude',
		'p-tel',
		'p-note',
		'dt-bday',
		'u-key',
		'p-org',
		'p-job-title',
		'p-role',
		'u-impp',
		'p-sex',
		'p-gender-identity',
		'dt-anniversary',
		//'u-sound'
	);

	foreach( $additional_hcard_properties as $additional_hcard_property ) {
		if( ! empty($conf[$additional_hcard_property]) ) $author[$additional_hcard_property] = $conf[$additional_hcard_property];
	}

	return $author;
}


function trailingslashit( $string ){
	// add a slash at the end, if there isn't already one ..

	$string = preg_replace( '/\/$/', '', $string );
	$string .= '/';

	return $string;
}
