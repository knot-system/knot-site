<?php

if( ! defined( 'EH_ABSPATH') ) exit;


function request_post( $url, $headers ){

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
	$response = array();
	parse_str( curl_exec($ch), $response );
	curl_close( $ch );

	return $response;
}
