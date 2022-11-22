<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function get_posts(){

	$posts = database_get_posts();

	return $posts;

}


function get_posts_by_tag( $tag ) {

	$all_posts = get_posts();

	$posts = array();

	foreach( $all_posts as $post ) {

		if( empty($post['tags']) || ! is_array($post['tags']) || ! count($post['tags']) ) continue;

		if( ! in_array( $tag, $post['tags']) ) continue;

		$posts[] = $post;

	}

	return $posts;
}


function get_post( $post_id ) {

	$post = database_get_post( $post_id );

	return $post;
}


function get_categories(){
	
	$posts = get_posts();

	$categories = array();

	foreach( $posts as $post ){
		foreach( $post['tags'] as $tag ) {
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

	$conf = get_config('author');

	if( ! $raw ) {

		// "special" named fields:
		if( ! empty($conf['p-name']) ) $author['display_name'] = $conf['p-name'];
		if( ! empty($conf['p-given-name']) ) $author['given_name'] = $conf['p-given-name'];
		if( ! empty($conf['p-last-name']) ) $author['family_name'] = $conf['p-last-name'];
		if( ! empty($conf['p-note']) ) $author['description'] = $conf['p-note'];
		if( ! empty($conf['u-email']) ) $author['email'] = $conf['u-email'];
		if( ! empty($conf['u-url']) ) $author['url'] = $conf['u-url'];
		if( ! empty($conf['u-photo']) ) $author['avatar'] = $conf['u-photo'];

		if( ! empty($author['given_name']) && ! empty($author['family_name']) && empty($author['display_name']) ) $author['display_name'] = $author['given_name'].' '.$author['family_name'];

	}

	// see https://microformats.org/wiki/h-card
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
