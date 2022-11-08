<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

// NOTE: this is throw-away code
// TODO / CLEANUP: make this robust


$request = $_SERVER['REQUEST_URI'];
$request = preg_replace( '/^'.preg_quote(EH_BASEFOLDER, '/').'/', '', $request );
$request = explode( '/', $request );

if( isset($request[0]) && $request[0] == 'feed' ){
	if( isset($request[1]) && $request[1] == 'rss' ) {
		// invoke rss
		include_once( 'site/rss.php' );
		exit;
	}
}

// TODO: this should be /api/v1/micropub or something like this; see \Eigenheim\Micropub::getEndpoint()
if( isset($_POST['content']) ) {
	// invoke micropub

	\Eigenheim\Micropub::checkRequest();

	exit;
}




include_once( EH_ABSPATH.'site/index.php' );
