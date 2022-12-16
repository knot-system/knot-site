<?php

// TODO: check, how we want to handle the api, and if it should be public

function api_get_endpoint( $complete_path = false ){

	$endpoint = 'api'; // TODO: revisit this in the future; if this uses '/' we need to fix routing.php as well

	if( ! $complete_path ) {
		return $endpoint;
	}

	return url($endpoint);
}

function api_check_request(){

	global $eigenheim;

	// TODO: add a nonce we can check

	if( ! empty($_GET['link_preview']) ) {

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


		$old_data = $link->getPreview();
		$data = $link->getLinkInfo()->getPreview();

		$changed = false;
		if( $data['title'] != $old_data['title'] ) $changed = true;
		elseif( $data['preview_image'] != $old_data['preview_image'] ) $changed = true;
		elseif( $data['description'] != $old_data['description'] ) $changed = true;

		if( ! $changed ) {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array(
				'success' => false,
				'message' => 'data did not change'
			));
			exit;
		}


		// TODO: this code is currently copied to text.php; we need one place for both
		$preview_title = '<span class="link-preview-title">'.$link->short_url.'</span>';
		$preview_image = '';
		$preview_description = '';
		if( ! empty($data['preview_image']) ) $preview_image = '<span class="link-preview-image">'.$data['preview_image'].'</span>';
		if( ! empty($data['title']) ) $preview_title = '<span class="link-preview-title">'.$data['title'].'</span>';
		if( ! empty($data['description']) ) $preview_description = '<span class="link-preview-description">'.$data['description'].'</span>';

		$inner_html = $preview_image.'<span class="link-preview-text">'.$preview_title.$preview_description;

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array(
			'success' => true,
			'data' => $data,
			'html' => $inner_html
		));
		exit;
	}

}
