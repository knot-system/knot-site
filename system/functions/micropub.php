<?php

namespace Eigenheim;

if( ! defined( 'EH_ABSPATH' ) ) exit;

// micropub spec: https://www.w3.org/TR/micropub/

class Micropub {

	static function getEndpoint(){

		return 'micropub'; // TODO: revisit this in the future; if this uses '/' we need to fix routing.php as well

	}

	static function checkRequest(){

		// based on MVMP by rhiaro -- https://rhiaro.co.uk/2015/04/minimum-viable-micropub

		if( ! empty($_POST) ) {
			Micropub::postRequest();
			return;
		} elseif( ! empty($_GET) ) {
			Micropub::getRequest();
			return;
		}

	}

	static function getRequest(){

		if( empty($_GET['q']) ) return;

		if( $_GET['q'] == 'config' ) {

			$categories = get_categories();

			$config = array(
				// 'media-endpoint' => '',
				'categories' => $categories
			);

			echo json_encode( $config );
			exit;
		}

	}

	static function postRequest() {

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

		// TODO / CLEANUP: sanitize input. never trust anything we receive here. currently we just dump everything into a text file.
		$skip_fields = array( 'access_token', 'action' );
		$post_status = 'publish'; // possible values: publish or draft
		foreach( $_POST as $key => $value ){

			if( in_array( $key, $skip_fields) ) continue;

			if( $key == 'category' ) {
				// we assume for now, that 'category' is either an array or a comma separated string
				if( ! is_array($value) ) {
					$value = explode( ',', $value );
					$value = array_map( 'trim', $value );
				}
				$value = json_encode($value);
			} elseif( $key == 'post-status' ) {
				$post_status = $value;
				if( $post_status == 'published' ) $post_status = 'publish';
			}

			$data .= $key.': '.$value."\n\n----\n\n";
		}

		$filepath = EH_ABSPATH."content/";
		$filename = time().".txt";
		if( $post_status == 'draft' ) $filename = '_draft_'.$filename; // for now, we prefix drafts and don't show them in the front-end
		if( file_exists($filepath.$filename) ) {
			// TODO: error handling: we should use another name for the file (append '-2' to the filename or something) and not fail at this stage
			header( "HTTP/1.1 400 Bad Request" );
			echo "File exists";
			exit;
		}


		if( ! \Eigenheim\Files::write_file( $filepath.$filename, $data ) ) {
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
