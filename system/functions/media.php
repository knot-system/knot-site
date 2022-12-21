<?php


function get_image_html( $image_path, $type = false ) {
	global $eigenheim;

	$target_width = $eigenheim->config->get( 'image_target_width', 1200 );

	if( $type == 'remote' ) {
		$local_image_path = $eigenheim->abspath.'cache/remote-image/'.$image_path;
		$local_image_url = $eigenheim->baseurl.'remote-image/'.$image_path; // TODO: check, how we want to handle this
	} else {
		$local_image_path = $eigenheim->abspath.'content/'.$image_path;
		$local_image_url = $eigenheim->baseurl.'content/'.$image_path;
	}

	if( ! file_exists( $local_image_path) ) {
		$eigenheim->debug("local image file does not exist", $local_image_path);
		return false;
	}

	$image_meta = getimagesize( $local_image_path );
	if( ! $image_meta ) {
		$eigenheim->debug("no image meta", $local_image_path);
		return false;
	}

	$src_width = $image_meta[0];
	$src_height = $image_meta[1];

	$target_dimensions = get_image_dimensions( $target_width, $src_width, $src_height );

	$width = $target_dimensions[0];
	$height = $target_dimensions[1];

	if( $width > $height ) {
		$format = 'landscape';
	} elseif( $width < $height ) {
		$format = 'portrait';
	} else {
		$format = 'square';
	}

	$classes = array( 'content-image', 'content-image-format-'.$format );

	$preview_base64 = get_image_preview_base64( $local_image_path );

	$html = '<figure class="'.implode(' ', $classes).'" style="aspect-ratio: '.$width/$height.'">
			<span class="content-image-inner" style="background-image: url('.$preview_base64.');">
				<img src="'.$local_image_url.'" width="'.$width.'" height="'.$height.'" loading="lazy" style="background: transparent; display: block;">
			</span>
		</figure>';

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


function get_image_preview_base64( $file_path ) {

	global $eigenheim;
	
	$target_width = 50;
	$jpg_quality = 40;

	$image_meta = getimagesize( $file_path );
	if( ! $image_meta ) {
		$eigenheim->debug("no image meta", $file_path);
		return false;
	}

	$src_width = $image_meta[0];
	$src_height = $image_meta[1];
	$image_type = $image_meta[2];
	
	$filesize = filesize( $file_path );
	if( ! $filesize ) {
		$eigenheim->debug("no image filesize", $file_path);
		return false;
	}

	$cache_string = $file_path.$filesize;

	$cache = new Cache( 'image-preview', $cache_string );

	$cache_content = $cache->getData();
	if( $cache_content ) {
		// return cached file, then end
		return $cache_content;
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

	} else {
		$eigenheim->debug( 'unknown image type '.$image_type);
		exit;	
	}


	$target_dimensions = get_image_dimensions( $target_width, $src_width, $src_height);

	$width = $target_dimensions[0];
	$height = $target_dimensions[1];

	if( $width <= 0 || $height <= 0 ) {
		imagedestroy( $src_image );
		$eigenheim->debug( 'width or height <= 0', $width, $height );
		exit;
	}

	$target_image = imagecreatetruecolor($width, $height);
	imagecopyresized($target_image, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

	for( $i = 0; $i < 5; $i++ ) {
		imagefilter( $target_image, IMG_FILTER_GAUSSIAN_BLUR );
	}

	imagedestroy($src_image);

	ob_start();
	imagejpeg( $target_image, NULL, $jpg_quality );
	$image_data = ob_get_contents();
	ob_end_clean();

	$base64_data = 'data:image/jpeg;base64,'.base64_encode($image_data);

	$cache->addData( $base64_data );

	imagedestroy( $target_image );

	return $base64_data;
}


function handle_image_display( $file_path ) {

	global $eigenheim;

	$target_width = $eigenheim->config->get( 'image_target_width' );
	$jpg_quality = $eigenheim->config->get( 'image_jpg_quality' );
	$png_to_jpg = $eigenheim->config->get( 'image_png_to_jpg' );

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

		if( $png_to_jpg ) {
			$file_extension = 'jpg';
			$mime_type = 'image/jpeg';
		}
	} else {
		$eigenheim->debug( 'unknown image type '.$image_type);
		exit;
	}

	$cache_string = $file_path.$filesize.$target_width.$jpg_quality;
	$cache = new Cache( 'image', $cache_string );

	$cache_content = $cache->getData();
	if( $cache_content ) {
		// return cached file, then end
		header("Content-Type: ".$mime_type);
		header("Content-Length: ".$cache->filesize);
		echo $cache_content;
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
			imagedestroy( $src_image );
			$eigenheim->debug( 'width or height <= 0', $width, $height );
			exit;
		}

		$target_image = imagecreatetruecolor($width, $height);
		imagecopyresized($target_image, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

		imagedestroy($src_image);

	} else {
		$target_image = $src_image;
	}

	if( $image_type == IMAGETYPE_JPEG
	 || ($png_to_jpg && $image_type == IMAGETYPE_PNG) ) {

		ob_start();
		imagejpeg( $target_image, NULL, $jpg_quality );
		$data = ob_get_contents();
		ob_end_clean();
		$cache->addData( $data );

		header( 'Content-Type: image/jpeg' );
		echo $data;

	} elseif( $image_type == IMAGETYPE_PNG ) {

		ob_start();
		$data = imagepng( $target_image );
		ob_end_clean();
		$cache->addData( $data );

		header( 'Content-Type: image/png' );
		echo $data;

	}

	imagedestroy( $target_image );
	exit;

}
