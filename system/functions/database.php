<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


// NOTE: this is a wrapper for the files.php, so we can easily change to a 'real' database instead of using text files

function database_get_posts(){

	$files = dir_read( '', true );

	if( ! count($files) ) return array();

	$posts = array();

	foreach( $files as $filename ) {
		$posts[] = database_get_post_by_filename( $filename );
	}

	return $posts;
}


function database_get_post( $post_id ) {

	$files = dir_read( '', true );

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

	$file_contents = file_read( $filename );

	if( ! $file_contents ) return false;

	$author_information = get_author_information();
	$author = false;
	if( ! empty( $author_information['display_name'] ) ) $author = $author_information['display_name'];

	$content_html = $file_contents['content'];

	$content_text = strip_tags($content_html); // TODO: revisit this in the future

	$content_html = text_cleanup( $content_html );

	$image = false;
	if( ! empty( $file_contents['photo']) ) {
		$post_folder = trailing_slash_it(pathinfo( $filename, PATHINFO_DIRNAME ));

		if( file_exists(EH_ABSPATH.'content/'.$post_folder.$file_contents['photo']) ) {
			$image = $post_folder.$file_contents['photo'];

			// TODO: we may want to get the image size on upload and cache this information instead of getting it on runtime
			// TODO: we also want to resize the image, if its too large. also at upload time.
			list( $width, $height ) = getimagesize( EH_ABSPATH.'content/'.$image );

			$content_html = '<p><img src="'.EH_BASEURL.'content/'.$image.'" width="'.$width.'" height="'.$height.'"></p>'.$content_html;

		}

	}

	$title = '';
	if( ! empty($file_contents['name']) ) $title = $file_contents['name'];

	$tags = array();
	if( ! empty($file_contents['category']) ) $tags = json_decode( $file_contents['category'] ); 
	if( ! is_array($tags) ) $tags = array();

	$timestamp = $file_contents['timestamp'];
	$id = $file_contents['id'];

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


// TODO: add database_create_post() instead of directly writing to a file in the system/functions/micropub.php
