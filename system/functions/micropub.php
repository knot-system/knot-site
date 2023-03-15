<?php

// update: 2023-03-15


// micropub spec: https://www.w3.org/TR/micropub/

function micropub_get_endpoint( $complete_path = false ){

	$endpoint = 'micropub'; // TODO: revisit this in the future; if this uses '/' we need to fix routing.php as well

	if( ! $complete_path ) {
		return $endpoint;
	}

	return url($endpoint);
}

function micropub_check_request(){

	if( ! empty($_POST) ) {
		micropub_handle_post_request();
		return true;
	} elseif( ! empty($_GET) ) {
		micropub_handle_get_request();
		return true;
	}

	$json = json_decode(file_get_contents('php://input'), true);
	if( $json ) {
		micropub_handle_json_request( $json );
		return true;
	}

	return false;

}

function micropub_handle_get_request(){

	global $core;

	if( empty($_GET['q']) ) return;

	if( $_GET['q'] == 'config' ) {

		$categories = $core->posts->categories();

		$config = array(
			// 'media-endpoint' => '', // TODO: add media endpoint for multiple images
			'categories' => $categories
		);

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode( $config );
		exit;
	}

}

function micropub_handle_json_request( $json ) {

	micropub_check_authorization_bearer(); // this will exit with a error message if authorization is not allowed

	$data = array();

	$data['h'] = str_replace('h-', '', $json['type'][0]);
	foreach( $json['properties'] as $name => $property ) {

		if( is_array($property) ) $property = array_values($property);

		if( is_array($property) && count($property) == 1 ) $property = $property[0];

		// special case: content html
		if( $name == 'content' && ! empty($property['html']) ) $property = $property['html'];

		// special case: slug
		if( $name == 'mp-slug' ) $name = 'slug';

		$data[$name] = $property;
	}

	micropub_create_post( $data );

}

function micropub_handle_post_request() {

	micropub_check_authorization_bearer(); // this will exit with a error message if authorization is not allowed

	$data = $_POST;

	micropub_create_post( $data );

}

function micropub_create_post( $data ){

	$skip_fields = array( 'access_token', 'action' );
	foreach( $skip_fields as $key ) {
		if( ! array_key_exists( $key, $data) ) continue;
		unset($data[$key]);
	}

	// TODO: sanitize input. never trust anything we receive here. currently we just dump everything into a text file.

	$data['timestamp'] = time();
	$data['date'] = date('c', $data['timestamp']);

	if( ! empty($data['category']) ) {
		// we assume for now, that 'category' is either an array or a comma separated string
		if( ! is_array($data['category']) ) {
			$data['category'] = explode( ',', $data['category'] );
			$data['category'] = array_map( 'trim', $data['category'] );
		}
		$data['category'] = json_encode($data['category']);
	}

	if( empty($data['post-status']) ) $data['post-status'] = 'published'; // possible values: 'published' or 'draft'
	if( $data['post-status'] == 'publish' ) $data['post-status'] = 'published';

	if( empty($data['slug']) ) {
		$data['slug'] = micropub_create_post_slug( $data );
	}

	// make sure the slug is unique:
	$slug = $data['slug'];
	$suffix = 0;
	do {
		if( $suffix > 0 ) {
			$data['slug'] = $slug.'-'.$suffix;
		}
		$suffix++;
	} while( get_post_id_from_slug( $data['slug'] ) );


	$photo = false;
	if( ! empty($_FILES['photo']) ) {
		$photo = $_FILES['photo'];
	} elseif( ! empty($_FILES['image']) ) {
		$photo = $_FILES['image'];
	} elseif( ! empty($data['photo']) ) {
		$photo = $data['photo'];
	}


	$permalink = create_post_in_database( $data, $photo );
	// if something went wrong, create_post_in_database() will exit
	
	
	// success !
	// Set headers, return location
	header( "HTTP/1.1 201 Created" );
	header( "Location: ".$permalink );
	exit;

}

function micropub_create_post_slug( $data ) {

	$slug = uniqid(); // Fallback

	if( ! empty($data['name']) ) {
		$slug = sanitize_string_for_url( $data['name'] );
	} elseif( ! empty($data['content']) ) {
		$slug = sanitize_string_for_url( strip_tags($data['content']) );
	}

	global $core;
	$slug_max_length = $core->config->get('slug_max_length');
	$slug = substr( $slug, 0, $slug_max_length );

	return $slug;
}

function micropub_check_authorization_bearer() {

	global $core;

	$headers = apache_request_headers();

	$token = $headers['Authorization'];

	$headers = array(
		"Content-Type: application/x-www-form-urlencoded",
		"Authorization: $token"
	);
	$response = request_post( 'https://tokens.indieauth.com/token', $headers );

	if( empty($response) ){
		header( "HTTP/1.1 401 Unauthorized" );
		exit;
	}

	// Check for scope=post or scope=create
	// Check for me=basedomain
	$me = $response['me'];
	$iss = $response['issued_by'];
	$client = $response['client_id'];
	$scope = $response['scope'];
	
	if( trailing_slash_it($me) != trailing_slash_it($core->baseurl) ){
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

}
