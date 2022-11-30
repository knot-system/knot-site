<?php

if( ! $eigenheim ) exit;


function get_posts( $page = -1 ){

	$posts = database_get_posts();

	if( $page > -1 ) {
		$posts = paginate_posts( $posts, $page );
	}

	return $posts;
}


function get_posts_by_tag( $tag, $page = -1 ) {

	$all_posts = get_posts();

	$posts = array();

	foreach( $all_posts as $post ) {

		if( empty($post['tags']) || ! is_array($post['tags']) || ! count($post['tags']) ) continue;

		if( ! in_array( $tag, $post['tags']) ) continue;

		$posts[] = $post;

	}

	if( $page > -1 ) {
		$posts = paginate_posts( $posts, $page );
	}

	return $posts;
}


function paginate_posts( $posts, $page ) {

	global $eigenheim;

	$posts_per_page = $eigenheim->config->get( 'posts_per_page' );

	$offset = ($page-1)*$posts_per_page;

	$posts = array_slice( $posts, $offset, $posts_per_page );

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
