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

	if( $width > $height ) {
		$format = 'landscape';
	} elseif( $width < $height ) {
		$format = 'portrait';
	} else {
		$format = 'square';
	}

	$classes = array( 'content-image', 'content-image-format-'.$format );
	$src = $eigenheim->baseurl.'content/'.$image_path;

	$preview_base64 = get_image_preview_base64($eigenheim->abspath.'content/'.$image_path);

	$html = '<figure class="'.implode(' ', $classes).'" style="aspect-ratio: '.$width/$height.'">
			<span class="content-image-inner" style="background: url('.$preview_base64.') no-repeat center center / cover; display: block;">
				<img src="'.$src.'" width="'.$width.'" height="'.$height.'" loading="lazy" style="background: transparent; display: block;">
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

	$cache_active = $eigenheim->config->get( 'image_cache_active' );

	if( $cache_active && ! is_dir($eigenheim->abspath.'cache') ) {
		if( mkdir( $eigenheim->abspath.'cache/', 0777, true ) === false ) {
			$eigenheim->debug( 'could not create cache dir' );
			$cache_active = false;
		}
	}
	
	$target_width = 50;
	$jpg_quality = 40;

	$image_meta = getimagesize( $file_path );
	$filesize = filesize( $file_path );

	$src_width = $image_meta[0];
	$src_height = $image_meta[1];
	$image_type = $image_meta[2];

	$cache_string = $file_path.$filesize;

	$cache_folder = $eigenheim->abspath.'cache/';
	$cache_name = md5($cache_string).'_preview_base64.txt';
	$cache_file = $cache_folder.$cache_name;

	if( $cache_active && file_exists($cache_file) ) {
		// return cached file, then end
		$cache_content = file_get_contents($cache_file);

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

	if( $cache_active ) {
		file_put_contents( $cache_file, $base64_data );
	}

	imagedestroy( $target_image );

	return $base64_data;
}


function handle_image_display( $file_path ) {

	global $eigenheim;

	$cache_active = $eigenheim->config->get( 'image_cache_active' );
	$target_width = $eigenheim->config->get( 'image_target_width' );
	$jpg_quality = $eigenheim->config->get( 'image_jpg_quality' );
	$png_to_jpg = $eigenheim->config->get( 'image_png_to_jpg' );

	if( $cache_active && ! is_dir($eigenheim->abspath.'cache') ) {
		if( mkdir( $eigenheim->abspath.'cache/', 0777, true ) === false ) {
			$eigenheim->debug( 'could not create cache dir' );
			$cache_active = false;
		}
	}

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

	$cache_string = $file_path.$filesize;

	$cache_folder = $eigenheim->abspath.'cache/';
	$cache_name = md5($cache_string).'_'.$target_width.'_'.$jpg_quality.'.'.$file_extension;
	$cache_file = $cache_folder.$cache_name;

	if( $cache_active && file_exists($cache_file) ) {
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
		header( 'Content-Type: image/jpeg' );
		imagejpeg( $target_image, NULL, $jpg_quality );

		if( $cache_active ) {
			imagejpeg( $target_image, $cache_file, $jpg_quality );
		}

	} elseif( $image_type == IMAGETYPE_PNG ) {
		header( 'Content-Type: image/png' );
		imagepng($target_image);

		if( $cache_active ) {
			imagepng( $target_image, $cache_file );
		}

	}

	imagedestroy( $target_image );
	exit;

}
