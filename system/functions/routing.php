<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function get_route(){

	$request = $_SERVER['REQUEST_URI'];
	$request = preg_replace( '/^'.preg_quote(EH_BASEFOLDER, '/').'/', '', $request );
	$request = explode( '/', $request );

	$tag = false;

	if( ! empty($request[0]) && $request[0] == 'feed' && ! empty($request[1]) ){
		// feeds

		if( $request[1] == 'rss' ) {
			// rss
			return array(
				'template' => 'rss',
			);
		} elseif( $request[1] == 'json' ){
			// json
			return array(
				'template' => 'json',
			);
		}

	} elseif( ! empty($request[0]) && $request[0] == 'post' && ! empty($request[1]) ){
		// single post view

		$post_id = $request[1];
		$post = get_post( $post_id );

		if( $post ) {
			return array(
				'template' => 'post',
				'args' => array(
					'post_id' => $post_id,
					'post' => $post
				)
			);
		} else {
			return array(
				'template' => '404',
			);
		}

	} elseif( ! empty($request[0]) && $request[0] == 'tag' && ! empty($request[1]) ){
		// single tag view

		$tag = $request[1];
		$posts = get_posts_by_tag( $tag );

		if( ! count($posts) ) {
			return array(
				'template' => '404',
			);
		}

		return array(
			'template' => 'tag',
			'args' => array(
				'tag' => $tag,
				'posts' => $posts
			)
		);

	} elseif( ! empty($request[0]) && $request[0] == micropub_get_endpoint() ) {
		// micropub

		// TODO: return micropub template here? or maybe return a function instead of a template?
		// the 'exit' should maybe not happen here, but in the root index.php
		micropub_check_request();
		exit;

	} elseif( ! empty($request[0]) ) {
		// maybe static page

		$page_id = $request[0];
		$page = get_page( $page_id );
		if( $page ) {
			return array(
				'template' => 'page',
				'args' => array(
					'page_id' => $page_id,
					'page' => $page
				)
			);
		}

	}

	// default
	return array(
		'template' => 'index',
	);

}