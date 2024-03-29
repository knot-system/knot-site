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

	if( ! empty($_GET['link_preview']) ) {

		// TODO: add a nonce we can check

		$id = $_GET['link_preview'];

		// we expect there to be a cachefile for this link, because it should have been created already
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
