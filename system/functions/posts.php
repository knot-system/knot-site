<?php


// TODO: move to posts class ?

function create_post_in_database( $data, $photo = false ) {

	global $core;

	if( $photo ) {

		if( empty($photo['name']) || ! isset($photo['error']) || ! isset($photo['tmp_name']) || ! isset($photo['size']) || ! isset($photo['type']) ) {
			header( "HTTP/1.1 400 Bad Request" );
			$core->debug( "Photo could not be uploaded" );
			exit;
		} elseif( $photo['error'] > 0 ) {
			header( "HTTP/1.1 400 Bad Request" );
			$core->debug( 'Photo could not be uploaded (errorcode '.$photo['error'].')' );
			exit;
		} elseif( $photo['size'] <= 0 ) {
			header( "HTTP/1.1 400 Bad Request" );
			$core->debug( 'Photo could not be uploaded' );
			exit;
		} elseif( $photo['type'] != 'image/jpeg' && $photo['type'] != 'image/png' ) {
			header( "HTTP/1.1 400 Bad Request" );
			$core->debug( 'Photo could not be uploaded (only .jpg or .png is allowed)' );
			exit;
		}

	}


	// NOTE: we want to either have a title ('name'), content or a image; if all of them are empty, abort here.
	if( ! $data['content'] && ! $data['name'] && ! $photo ) {
		global $core;

		header( "HTTP/1.1 400 Bad Request" );
		$core->debug( 'we need at least content, or a title, or an image' );
		exit;
	}



	$year = date('Y', $data['timestamp']);
	$month = date('m', $data['timestamp']);
	$target_folder = 'posts/'.$year.'/'.$month.'/';
	if( ! is_dir($core->abspath.'content/'.$target_folder) ) {
		mkdir( $core->abspath.'content/'.$target_folder, 0777, true );
		if( ! is_dir($core->abspath.'content/'.$target_folder) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			$core->debug( 'Folder could not be created' );
			exit;
		}
	}


	$prefix = date('Y-m-d_H-i-s', $data['timestamp']);
	$target_folder .= $prefix.'_'.$data['slug'].'/';
	
	if( is_dir($core->abspath.'content/'.$target_folder) ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		$core->debug( 'Folder already exists' );
		exit;
	}

	mkdir( $core->abspath.'content/'.$target_folder, 0777 );
	if( ! is_dir($core->abspath.'content/'.$target_folder) ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		$core->debug( "Folder could not be created" );
		exit;
	}

	$data['id'] = uniqid();

	$filename = 'post.txt';
	if( $data['post-status'] == 'draft' ) $filename = '_draft_'.$filename; // for now, we prefix drafts and don't show them in the front-end


	$data['photo'] = false;
	if( $photo ) {

		$photo_name = explode('.', $photo['name']);
		$extension = array_pop($photo_name);
		$count = 0;
		do {
			$count_string = '';
			if( $count > 0 ) $count_string = '_'.$count.'_';

			$photo_name_string = implode('.', $photo_name).$count_string.'.'.$extension;
			$photo_target = $core->abspath.'content/'.$target_folder.$photo_name_string;

			$count++;

		} while( file_exists($photo_target) );

		
		if( ! rename( $photo['tmp_name'], $photo_target ) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			$core->debug( 'Photo could not be moved to the target location' );
			exit;
		}
		if( ! chmod( $photo_target, 0644) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			$core->debug( 'Photo was uploaded, but could not be set to readable' );
			exit;	
		}

		$data['photo'] = $photo_name_string;

	}


	$file_target = $target_folder.$filename;


	$data_string = '';
	foreach( $data as $key => $value ){
		$data_string .= $key.': '.$value."\r\n\r\n----\r\n\r\n";
	}

	$file = new File( $core, $file_target, $data_string );
	if( ! $file ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		$core->debug(  "File could not be written" );
		exit;
	}

	// when we get here, everything should have worked and the post was created.

	$permalink = url('post/'.$data['slug']); // TODO: this should be retreived from $file

	return $permalink;
}
