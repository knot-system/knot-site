<?php

if( ! $eigenheim ) exit;


function database_get_pages() {

	global $eigenheim;
	$database = new Database();
	$files = $database->read_dir( '', true, 'page.txt' );

	if( ! count($files) ) return array();

	$pages = array();

	foreach( $files as $filename ) {
		$pages[] = database_get_page_by_filename( $filename )->get(); // TODO: use page object
	}

	return $pages;
}


function database_get_page( $page_id ) {

	global $eigenheim;
	$database = new Database();
	$files = $database->read_dir( '', true, 'page.txt' );

	if( ! count($files) ) return false;

	$page_name = false;
	foreach( $files as $filename ) {
		$file = new File( $filename );
		$file_id = $file->get_id();
		if( $file_id == $page_id ) {
			$page_name = $filename;
			break;
		}
	}

	if( ! $page_name ) return false;

	$page = database_get_page_by_filename( $page_name );

	return $page->get(); // TODO: return page object
}


function database_get_page_by_filename( $filename ) {
	$page = new Page( $filename );
	return $page;
}


function database_get_posts() {

	global $eigenheim;
	$database = new Database();
	$files = $database->read_dir( 'posts/', true, 'post.txt' );
	rsort($files);


	if( ! count($files) ) return array();

	$posts = array();

	foreach( $files as $filename ) {
		$posts[] = database_get_post_by_filename( $filename )->get(); // TODO: use post object
	}

	return $posts;
}


function database_get_post( $post_id ) {

	global $eigenheim;
	$database = new Database();
	$files = $database->read_dir( 'posts/', true, 'post.txt' );
	rsort($files);


	if( ! count($files) ) return false;

	$post_name = false;
	foreach( $files as $filename ) {
		$file = new File( $filename );
		$file_id = $file->get_id();
		if( $file_id == $post_id ) {
			$post_name = $filename;
			break;
		}
	}

	if( ! $post_name ) return false;

	$post = database_get_post_by_filename( $post_name );

	return $post->get();  // TODO: return post object
}


function database_get_post_by_filename( $filename ) {
	$post = new Post( $filename );
	return $post;
}


function database_create_post( $data, $photo = false ) {

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

	$file = new File( $file_target, $data_string );
	if( ! $file ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		$eigenheim->debug(  "File could not be written" );
		exit;
	}

	// when we get here, everything should have worked and the post was created.

	$permalink = url('post/'.$post_id); // TODO: this should be retreived from $file

	return $permalink;
}

