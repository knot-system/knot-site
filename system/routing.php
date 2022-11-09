<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

// NOTE: this is throw-away code
// TODO / CLEANUP: make this robust


$request = $_SERVER['REQUEST_URI'];
$request = preg_replace( '/^'.preg_quote(EH_BASEFOLDER, '/').'/', '', $request );
$request = explode( '/', $request );

if( isset($request[0]) && $request[0] == 'feed' && isset($request[1]) ){
	if( $request[1] == 'rss' ) {
		// rss feed
		include_once( 'site/rss.php' );
		exit;
	} elseif( $request[1] == 'json' ){
		// json feed
		include_once( 'site/json.php' );
		exit;
	}
}

if( isset($request[0]) && $request[0] == \Eigenheim\Micropub::getEndpoint() ) {
	// invoke micropub
	\Eigenheim\Micropub::checkRequest();
	exit;
}




include_once( EH_ABSPATH.'site/index.php' );
