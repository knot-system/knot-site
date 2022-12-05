<?php


function get_image_html( $image_path ) {
	global $eigenheim;

	$target_width = $eigenheim->config->get( 'image_target_width', 1200 );

	$image_meta = getimagesize( $eigenheim->abspath.'content/'.$image_path );
	$src_width = $image_meta[0];
	$src_height = $image_meta[1];

	$target_dimensions = get_image_dimensions( $target_width, $src_width, $src_height );

	$width = $target_dimensions[0];
	$height = $target_dimensions[1];

	$html = '<img src="'.$eigenheim->baseurl.'content/'.$image_path.'" width="'.$width.'" height="'.$height.'" loading="lazy">';

	return $html;
}


function get_image_dimensions( $target_width, $src_width, $src_height ) {

	if( $src_width <= $target_width ) {
		return array( $src_width, $src_height );
	}
	
	$width = $target_width;
	$height = (int) round($src_height/$src_width*$width);

	return array( $width, $height );
}


function handle_image_display( $file_path ) {

	global $eigenheim;

	$cache_active = $eigenheim->config->get( 'image_cache_active' );
	$target_width = $eigenheim->config->get( 'image_target_width' );
	$jpg_quality = $eigenheim->config->get( 'image_jpg_quality' );

	$image_meta = getimagesize( $file_path );
	$filesize = filesize( $file_path );

	$src_width = $image_meta[0];
	$src_height = $image_meta[1];
	$image_type = $image_meta[2];

	$file_extension = '';
	if( $image_type == IMAGETYPE_JPEG ) {
		$file_extension = 'jpg';
		$mime_type = 'image/jpeg';
	} elseif( $image_type == IMAGETYPE_PNG ) {
		$file_extension = 'png';
		$mime_type = 'image/png';
	} else {
		$eigenheim->debug( 'unknown image type '.$image_type);
		exit;
	}

	$cache_string = $file_path.$filesize;

	$cache_folder = $eigenheim->abspath.'cache/';
	$cache_name = md5($cache_string).'_'.$target_width.'_'.$jpg_quality.'.'.$file_extension;
	$cache_file = $cache_folder.$cache_name;

	if( file_exists($cache_file) ) {
		// return cached file, then end
		$fp = fopen($cache_file, 'rb');
		header("Content-Type: ".$mime_type);
		header("Content-Length: ".filesize($cache_file));
		fpassthru($fp);
		exit;
	}

	if( $image_type == IMAGETYPE_JPEG ) {
		$src_image = imagecreatefromjpeg( $file_path );
		if( ! $src_image ) {
			$eigenheim->debug( 'could not load jpg image' );
			exit;
		}

	} elseif( $image_type == IMAGETYPE_PNG ) {
		$src_image = imagecreatefrompng( $file_path );
		if( ! $src_image ) {
			$eigenheim->debug( 'could not load png image' );
			exit;
		}

	}

	if( $src_width > $target_width ) {
		
		$target_dimensions = get_image_dimensions( $target_width, $src_width, $src_height);

		$width = $target_dimensions[0];
		$height = $target_dimensions[1];

		if( $width <= 0 || $height <= 0 ) {
			$eigenheim->debug( 'width or height <= 0', $width, $height );
			exit;
		}

		$target_image = imagecreatetruecolor($width, $height);
		imagecopyresized($target_image, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

		imagedestroy($src_image);

	} else {
		$target_image = $src_image;
	}



	if( $image_type == IMAGETYPE_JPEG ) {
		header( 'Content-Type: '.$mime_type );
		imagejpeg( $target_image, NULL, $jpg_quality );

		if( $cache_active ) {
			imagejpeg( $target_image, $cache_file, $jpg_quality );
		}

	} elseif( $image_type == IMAGETYPE_PNG ) {
		header( 'Content-Type: '.$mime_type );
		imagepng($target_image);

		if( $cache_active ) {
			imagepng( $target_image, $cache_file );
		}

	}

	imagedestroy( $target_image );
	exit;

}
