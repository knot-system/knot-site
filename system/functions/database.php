<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


// NOTE: this is a wrapper for the files.php, so we can easily change to a 'real' database instead of using text files


function database_get_pages() {
	// TODO

	return array();
}


function database_get_page( $page_id ) {
	// TODO

	return false;
}


function database_get_posts() {

	$files = dir_read( 'posts/', true );

	if( ! count($files) ) return array();

	$posts = array();

	foreach( $files as $filename ) {
		$posts[] = database_get_post_by_filename( $filename );
	}

	return $posts;
}


function database_get_post( $post_id ) {

	$files = dir_read( 'posts/', true );

	if( ! count($files) ) return false;

	$post_name = false;
	foreach( $files as $filename ) {
		$filename_exp = explode( '/', $filename );
		$id = $filename_exp[count($filename_exp)-2];
		$id_exp = explode('_', $id);
		if( end($id_exp) == $post_id ) {
			$post_name = $filename;
			break;
		}
	}

	if( ! $post_name ) return false;

	$post = database_get_post_by_filename( $post_name );

	return $post;
}


function database_get_post_by_filename( $filename ) {

	$file_content = file_read( $filename );

	if( ! $file_content ) return false;


	$fields = explode( "\n\n----\n\n", $file_content );

	if( ! is_array($fields) || ! count($fields) ) return false; // TODO: error handling: no fields in file
	

	$data = array();

	foreach( $fields as $field ) {

		$pos = strpos( $field, ':' );

		if( $pos === false ) continue; // TODO: error handling: no fieldname for this field

		$field_name = substr( $field, 0, $pos );
		$field_content = substr( $field, $pos+1 );

		$field_name = strtolower(trim($field_name));
		$field_content = trim($field_content);

		$data[$field_name] = $field_content;

	}


	$author_information = get_author_information();
	$author = false;
	if( ! empty( $author_information['display_name'] ) ) $author = $author_information['display_name'];

	$content_html = $data['content'];

	$content_text = strip_tags($content_html); // TODO: revisit this in the future

	$content_html = text_cleanup( $content_html );

	$image = false;
	if( ! empty( $data['photo']) ) {
		$post_folder = trailing_slash_it(pathinfo( $filename, PATHINFO_DIRNAME ));

		if( file_exists(EH_ABSPATH.'content/'.$post_folder.$data['photo']) ) {
			$image = $post_folder.$data['photo'];

			// TODO: we may want to get the image size on upload and cache this information instead of getting it on runtime
			// TODO: we also want to resize the image, if its too large. also at upload time.
			list( $width, $height ) = getimagesize( EH_ABSPATH.'content/'.$image );

			$content_html = '<p><img src="'.EH_BASEURL.'content/'.$image.'" width="'.$width.'" height="'.$height.'"></p>'.$content_html;

		}

	}

	$title = '';
	if( ! empty($data['name']) ) $title = $data['name'];

	$tags = array();
	if( ! empty($data['category']) ) $tags = json_decode( $data['category'] ); 
	if( ! is_array($tags) ) $tags = array();

	$timestamp = $data['timestamp'];
	$id = $data['id'];

	$permalink = EH_BASEURL.'post/'.$id.'/';

	$date_published = date( 'c', $timestamp );

	$date_modified = $date_published; // TODO: add modified date

	// this is the structure that the json feed wants for a post, see https://www.jsonfeed.org/version/1.1/ (with some additional fields we use elsewhere)
	$post = array(
		'id' => $id,
		'title' => $title,
		'author' => $author,
		'permalink' => $permalink,
		'content_html' => $content_html,
		'content_text' => $content_text,
		'tags' => $tags,
		'date_published' => $date_published,
		'date_modified' => $date_modified,
		'timestamp' => $timestamp,
		'image' => $image,
	);

	return $post;
}


function database_create_post( $data, $photo = false ) {

	$year = date('Y', $data['timestamp']);
	$month = date('m', $data['timestamp']);
	$target_folder = EH_ABSPATH.'content/posts/'.$year.'/'.$month.'/';
	if( ! is_dir($target_folder) ) {
		mkdir( $target_folder, 0777, true );
		if( ! is_dir($target_folder) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			echo "Folder could not be created";
			exit;
		}
	}

	$prefix = date('Y-m-d_H-i-s', $data['timestamp']);
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
	if( $data['post-status'] == 'draft' ) $filename = '_draft_'.$filename; // for now, we prefix drafts and don't show them in the front-end


	if( $photo ) {

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
		if( ! chmod( $photo_target, 0644) ) {
			header( "HTTP/1.1 500 Internal Server Error" );
			echo 'Photo was uploaded, but could not be set to readable';
			exit;	
		}

		$data['photo'] = $photo['name'];

	}


	$file_target = $target_folder.$filename;


	$data_string = '';
	foreach( $data as $key => $value ){
		$data_string .= $key.': '.$value."\n\n----\n\n";
	}

	if( ! file_write( $file_target, $data_string ) ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		echo "File could not be written";
		exit;
	}


	// when we get here, everything should have worked and the post was created.

	$permalink = EH_BASEURL.'post/'.$post_id;

	return $permalink;
}
