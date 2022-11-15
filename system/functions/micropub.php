<?php

namespace Eigenheim;

if( ! defined( 'EH_ABSPATH' ) ) exit;

// micropub spec: https://www.w3.org/TR/micropub/

class Micropub {

	static function getEndpoint(){

		return 'micropub'; // TODO: revisit this in the future; if this uses '/' we need to fix routing.php as well

	}

	static function checkRequest(){

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

		$timestamp = time();

		// TODO: sanitize input. never trust anything we receive here. currently we just dump everything into a text file.
		$data = $_POST;

		$skip_fields = array( 'access_token', 'action' );
		foreach( $skip_fields as $key ) {
			if( ! array_key_exists( $key, $data) ) continue;
			unset($data[$key]);
		}

		$data['timestamp'] = $timestamp;
		$data['date'] = date('c', $timestamp);

		if( ! empty($data['category']) ) {
			// we assume for now, that 'category' is either an array or a comma separated string
			if( ! is_array($data['category']) ) {
				$data['category'] = explode( ',', $data['category'] );
				$data['category'] = array_map( 'trim', $data['category'] );
			}
			$data['category'] = json_encode($data['category']);
		}

		$post_status = 'published'; // possible values: published or draft
		if( ! empty($data['post-status']) ) {
			if( $data['post-status'] == 'publish' ) $data['post-status'] = 'published';

			$post_status = $data['post-status'];
		}

		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);
		$target_folder = EH_ABSPATH.'content/'.$year.'/'.$month.'/';
		if( ! is_dir($target_folder) ) {
			mkdir( $target_folder, 0777, true );
			if( ! is_dir($target_folder) ) {
				header( "HTTP/1.1 500 Internal Server Error" );
				echo "Folder could not be created";
				exit;
			}
		}

		$prefix = date('Y-m-d_H-i-s', $timestamp);
		do {
			$post_id = uniqid();
			$foldername = $prefix.'_'.$post_id.'/';
		} while( is_dir($target_folder.$foldername) );

		$target_folder .= $foldername;

		mkdir( $target_folder, 0777 );
		if( ! is_dir($target_folder) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			echo "Folder could not be created";
			exit;
		}

		$data['id'] = $post_id;

		$filename = 'post.txt';
		if( $post_status == 'draft' ) $filename = '_draft_'.$filename; // for now, we prefix drafts and don't show them in the front-end
		
		// NOTE: file upload - photo
		if( isset($_FILES['photo']) ) {
			$photo = $_FILES['photo'];

			if( empty($photo['name']) || ! isset($photo['error']) || ! isset($photo['tmp_name']) || ! isset($photo['size']) || ! isset($photo['type']) ) {
				header( "HTTP/1.1 400 Bad Request" );
				echo "Photo could not be uploaded";
				exit;
			} elseif( $photo['error'] > 0 ) {
				header( "HTTP/1.1 400 Bad Request" );
				echo 'Photo could not be uploaded (errorcode '.$photo['error'].')';
				exit;
			} elseif( $photo['size'] <= 0 ) {
				header( "HTTP/1.1 400 Bad Request" );
				echo 'Photo could not be uploaded';
				exit;
			} elseif( $photo['type'] != 'image/jpeg' ) {
				header( "HTTP/1.1 400 Bad Request" );
				echo 'Photo could not be uploaded (only .jpg is allowed for now)';
				exit;
			}
			
			$photo_target = $target_folder.$photo['name'];
			if( file_exists($photo_target) ) {
				// TODO: rename photo name, if this already exists, instead of showing an error message and aborting here
				header( "HTTP/1.1 500 Internal Server Error" );
				echo "Photo could not be moved to the target location - this file already exists";
				exit;
			}
			if( ! rename( $photo['tmp_name'], $photo_target ) ) {
				header( "HTTP/1.1 500 Internal Server Error" );
				echo 'Photo could not be moved to the target location';
				exit;
			}

			$data['photo'] = $photo['name'];

		}


		$file_target = $target_folder.$filename;

		$data_string = '';
		foreach( $data as $key => $value ){
			$data_string .= $key.': '.$value."\n\n----\n\n";
		}

		if( ! \Eigenheim\Files::write_file( $file_target, $data_string ) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			echo "File could not be written";
			exit;
		}


		// success !
		// Set headers, return location
		header( "HTTP/1.1 201 Created" );
		header( "Location: ".EH_BASEURL.'#'.$post_id );

	}

}
