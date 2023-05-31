<?php

// TODO: check, how we want to handle the api, and if it should be public

function api_get_endpoint( $complete_path = false ){

	$endpoint = 'api'; // TODO: revisit this in the future; if this uses '/' we need to fix routing.php as well

	if( ! $complete_path ) {
		return $endpoint;
	}

	return url($endpoint);
}

function api_check_request( $request ){

	array_shift($request); // remove first element, which is 'api' at the moment; TODO: check this, also check api_get_endpoint()

	if( $request[0] == 'indieauth-metadata' ) {

		global $core;

		// see https://indieauth.spec.indieweb.org/#indieauth-server-metadata
		$indieauth_metadata = array(
			'issuer' => url(),
			'authorization_endpoint' => '', // TODO
			'token_endpoint' => '', // TODO
			'introspection_endpoint' => '', // TODO
			//'introspection_endpoint_auth_methods_supported' => [], // TODO
			//'revocation_endpoint' => '', // TODO
			//'revocation_endpoint_auth_methods_supported' => [], // TODO
			//'scopes_supported' => [], // TODO
			//'response_types_supported' => [], // TODO
			//'grant_types_supported' => [], // TODO
			//'service_documentation' => '', // TODO
			'code_challenge_methods_supported' => [], // TODO
			//'authorization_response_iss_parameter_supported' => false, // TODO
			//'userinfo_endpoint' => '' // TODO
		);

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($indieauth_metadata);
		exit;

	} elseif( ! empty($_GET['link_preview']) ) {

		// TODO: add a nonce we can check

		$id = $_GET['link_preview'];

		// we expect to be a cachefile for this link, because it should have been created already
		try {
			$link = new Link( $id, true );
		} catch( Exception $e ) {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array(
				'success' => false,
				'message' => 'link does not exist'
			));
			exit;
		}

		$data = $link->get_info()->get_preview();

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array(
			'success' => true,
			'data' => $data
		));
		exit;
	}

}
