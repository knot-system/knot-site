<?php

// TODO: move to posts class ?

function create_post_in_database( $data, $photo = false ) {

	global $eigenheim;

	$year = date('Y', $data['timestamp']);
	$month = date('m', $data['timestamp']);
	$target_folder = 'posts/'.$year.'/'.$month.'/';
	if( ! is_dir($eigenheim->abspath.'content/'.$target_folder) ) {
		mkdir( $eigenheim->abspath.'content/'.$target_folder, 0777, true );
		if( ! is_dir($eigenheim->abspath.'content/'.$target_folder) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			$eigenheim->debug( 'Folder could not be created' );
			exit;
		}
	}

	$prefix = date('Y-m-d_H-i-s', $data['timestamp']);
	do {
		$post_id = uniqid();
		$foldername = $prefix.'_'.$post_id.'/';
	} while( is_dir($eigenheim->abspath.'content/'.$target_folder.$foldername) );

	$target_folder .= $foldername;

	mkdir( $eigenheim->abspath.'content/'.$target_folder, 0777 );
	if( ! is_dir($eigenheim->abspath.'content/'.$target_folder) ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		$eigenheim->debug( "Folder could not be created" );
		exit;
	}

	$data['id'] = $post_id;

	$filename = 'post.txt';
	if( $data['post-status'] == 'draft' ) $filename = '_draft_'.$filename; // for now, we prefix drafts and don't show them in the front-end


	if( $photo ) {

		if( empty($photo['name']) || ! isset($photo['error']) || ! isset($photo['tmp_name']) || ! isset($photo['size']) || ! isset($photo['type']) ) {
			header( "HTTP/1.1 400 Bad Request" );
			$eigenheim->debug( "Photo could not be uploaded" );
			exit;
		} elseif( $photo['error'] > 0 ) {
			header( "HTTP/1.1 400 Bad Request" );
			$eigenheim->debug( 'Photo could not be uploaded (errorcode '.$photo['error'].')' );
			exit;
		} elseif( $photo['size'] <= 0 ) {
			header( "HTTP/1.1 400 Bad Request" );
			$eigenheim->debug( 'Photo could not be uploaded' );
			exit;
		} elseif( $photo['type'] != 'image/jpeg' && $photo['type'] != 'image/png' ) {
			header( "HTTP/1.1 400 Bad Request" );
			$eigenheim->debug( 'Photo could not be uploaded (only .jpg or .png is allowed)' );
			exit;
		}


		$photo_target = $eigenheim->abspath.'content/'.$target_folder.$photo['name'];

		if( file_exists($photo_target) ) {
			// TODO: rename photo name, if this already exists, instead of showing an error message and aborting here
			header( "HTTP/1.1 500 Internal Server Error" );
			$eigenheim->debug( "Photo could not be moved to the target location - this file already exists" );
			exit;
		}
		
		if( ! rename( $photo['tmp_name'], $photo_target ) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			$eigenheim->debug( 'Photo could not be moved to the target location' );
			exit;
		}
		if( ! chmod( $photo_target, 0644) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			$eigenheim->debug( 'Photo was uploaded, but could not be set to readable' );
			exit;	
		}

		$data['photo'] = $photo['name'];

	}


	$file_target = $target_folder.$filename;


	$data_string = '';
	foreach( $data as $key => $value ){
		$data_string .= $key.': '.$value."\r\n\r\n----\r\n\r\n";
	}

	$file = new File( $eigenheim, $file_target, $data_string );
	if( ! $file ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		$eigenheim->debug(  "File could not be written" );
		exit;
	}

	// when we get here, everything should have worked and the post was created.

	$permalink = url('post/'.$post_id); // TODO: this should be retreived from $file

	return $permalink;
}
