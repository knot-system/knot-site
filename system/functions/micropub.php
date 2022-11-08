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
		$token = $headers['Authorization'];
		$ch = curl_init("https://tokens.indieauth.com/token");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/x-www-form-urlencoded",
			"Authorization: $token"
		));
		$response = array();
		parse_str(curl_exec($ch), $response);
		curl_close($ch);

		// Check for scope=post or scope=create
		// Check for me=basedomain
		$me = $response['me'];
		$iss = $response['issued_by'];
		$client = $response['client_id'];
		$scope = $response['scope'];

		if( empty($response) ){
			header("HTTP/1.1 401 Unauthorized");
			exit;
		}

		if( $me != EH_BASEURL ){
			header("HTTP/1.1 403 Forbidden");
			exit;
		}

		$possible_scopes = array( 'post', 'create' );
		$scopes = explode( ' ', $scope );
		$scope_found = false;
		foreach( array('post', 'create') as $possible_scope ){
			if( in_array($possible_scope, $scopes) ) {
				$scope_found = true;
				break;
			}
		}
		if( ! $scope_found ){
			header("HTTP/1.1 403 Forbidden");
			exit;
		}

		if(empty($_POST['content'])){
			header("HTTP/1.1 400 Bad Request");
			echo "Missing content";
			exit;
		}

		$fn = EH_ABSPATH."content/".time().".txt";

		// write file. TODO: fill this with relevant data so we can use it to display stuff
		$h = fopen($fn, 'w');
		foreach($_POST as $k => $v){
			$data .= "$k: $v\n\n----\n\n";
		}
		fwrite($h, $data); 
		fclose($h); 

		// Set headers, return location
		header("HTTP/1.1 201 Created");
		header("Location: ".EH_BASEURL);

	}

}
