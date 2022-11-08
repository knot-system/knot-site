<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

// NOTE: this is throw-away code
// TODO / CLEANUP: make this robust


// TODO: this should be /api/v1/micropub or something like this; see \Eigenheim\Micropub::getEndpoint()
if( isset($_POST['content']) ) {
	// invoke micropub

	\Eigenheim\Micropub::checkRequest();

	exit;
}




include_once( EH_ABSPATH.'site/index.php' );
