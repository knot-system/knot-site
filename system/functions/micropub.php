<?php

namespace Eigenheim;

if( ! defined( 'EH_ABSPATH' ) ) exit;

class Micropub {

	static function getEndpoint(){

		return EH_BASEURL; // TODO: use /api/v1/micropub or something like this when we added routing

	}

	static function checkRequest(){

		// based on MVMP by rhiaro -- https://rhiaro.co.uk/2015/04/minimum-viable-micropub

		if( empty($_POST) ) return;

		$headers = apache_request_headers();// TODO: switch to $_SERVER ?

		// Check token is valid
		// TODO: make a custom wrapper for curl
		$token = $headers['Authorization'];
		$ch = curl_init( "https://tokens.indieauth.com/token" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/x-www-form-urlencoded",
			"Authorization: $token"
		));
		$response = array();
		parse_str( curl_exec($ch), $response );
		curl_close( $ch );

		// Check for scope=post or scope=create
		// Check for me=basedomain
		$me = $response['me'];
		$iss = $response['issued_by'];
		$client = $response['client_id'];
		$scope = $response['scope'];

		if( empty($response) ){
			header( "HTTP/1.1 401 Unauthorized" );
			exit;
		}

		if( trailingslashit($me) != trailingslashit(EH_BASEURL) ){
			header( "HTTP/1.1 403 Forbidden" );
			exit;
		}

		$scopes = explode( ' ', $scope );
		$scope_found = false;
		foreach( array('post', 'create') as $possible_scope ){
			if( in_array($possible_scope, $scopes) ) {
				$scope_found = true;
				break;
			}
		}
		if( ! $scope_found ){
			header( "HTTP/1.1 403 Forbidden" );
			exit;
		}

		if( empty($_POST['content']) ){
			header( "HTTP/1.1 400 Bad Request" );
			echo "Missing content";
			exit;
		}

		$filename = EH_ABSPATH."content/".time().".txt";
		if( file_exists($filename) ) {
			// TODO: error handling: we should use another name for the file (append '-2' to the filename or something) and not fail at this stage
			header( "HTTP/1.1 400 Bad Request" );
			echo "File exists";
			exit;
		}

		// write file.
		// TODO / CLEANUP: sanitize input. never trust anything we receive here. currently we just dump everything into a text file.
		$skip_fields = array( 'access_token' );
		foreach( $_POST as $key => $value ){

			if( in_array( $key, $skip_fields) ) continue;

			if( $key == 'category' ) {
				// we assume for now, that 'category' is either an array or a comma separated string
				if( ! is_array($value) ) {
					$value = explode( ',', $value );
					$value = array_map( 'trim', $value );
				}
				$value = json_encode($value);
			}

			$data .= $key.': '.$value."\n\n----\n\n";
		}
		if( ! \Eigenheim\Files::write_file( $filename, $data ) ) {
			// TODO: error handling: file could not be written
			header( "HTTP/1.1 400 Bad Request" );
			echo "File could not be written";
			exit;
		}

		// success !
		// Set headers, return location
		header( "HTTP/1.1 201 Created" );
		header( "Location: ".EH_BASEURL );

	}

}
