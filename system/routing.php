<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

// NOTE: this is throw-away code
// TODO / CLEANUP: make this robust


$request = $_SERVER['REQUEST_URI'];
$request = preg_replace( '/^'.preg_quote(EH_BASEFOLDER, '/').'/', '', $request );
$request = explode( '/', $request );

if( isset($request[0]) && $request[0] == 'feed' && isset($request[1]) ){
	// feeds

	if( $request[1] == 'rss' ) {
		// rss
		include_once( 'site/rss.php' );
		exit;
	} elseif( $request[1] == 'json' ){
		// json
		include_once( 'site/json.php' );
		exit;
	}

} elseif( isset($request[0]) && $request[0] == 'post' && isset($request[1]) ){
	// single post view

	$post_id = $request[1];
	$post = get_post( $post_id );

	if( $post ) {
		include_once( EH_ABSPATH.'site/post.php' );
		exit;
	} else {
		include_once( EH_ABSPATH.'site/404.php' );
		exit;
	}

} elseif( isset($request[0]) && $request[0] == \Eigenheim\Micropub::getEndpoint() ) {
	// micropub

	\Eigenheim\Micropub::checkRequest();
	exit;

}

// default
include_once( EH_ABSPATH.'site/index.php' );
