<?php


// TODO: cleanup

class Image {

	private $type;
	private $local_file_path;
	private $image_url;
	private $orientation = 9; // NOTE: EXIF orientation; 1 = 0°, 8 = 90°, 3 = 180°, 6 = 270°; 2, 7, 4, 5 is the same, but mirrored horizontally before rotating; 9 means undefined

	function __construct( $image_path, $type = false ) {

		global $core;

		$this->type = $type;

		if( $this->type == 'remote' ) {
			$this->local_file_path = $core->abspath.'cache/remote-image/'.$image_path;
			$this->image_url = $core->baseurl.'remote-image/'.$image_path;
		} else {
			$this->local_file_path = $core->abspath.$image_path;
			$this->image_url = $core->baseurl.$image_path;
		}

		if( ! file_exists( $this->local_file_path) ) {
			var_dump('not exists');
			$core->debug("local image file does not exist", $this->local_file_path);
			return false;
		}

		$exif = @exif_read_data( $this->local_file_path );
		if( $exif && ! empty($exif['Orientation']) ) {
			$this->orientation = $exif['Orientation'];
		}

	}


	function get_html_embed( $target_width = false ) {

		global $core;

		if( ! $target_width ) {
			$target_width = $core->config->get( 'image_target_width' );
		}

		$image_url = $this->image_url;
		$image_url .= '?width='.$target_width;

		$image_meta = getimagesize( $this->local_file_path );
		if( ! $image_meta ) {
			$core->debug("no image meta", $this->local_file_path);
			return false;
		}

		$src_width = $image_meta[0];
		$src_height = $image_meta[1];

		if( $this->orientation == 6 || $this->orientation == 8 || $this->orientation == 5 || $this->orientation == 7 ) {
			$src_width = $image_meta[1];
			$src_height = $image_meta[0];
		}


		$target_dimensions = $this->get_image_dimensions( $target_width, $src_width, $src_height );

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

		$preview_base64 = $this->get_image_preview_base64();

		$image_embed_html = '<figure class="'.implode(' ', $classes).'" style="aspect-ratio: '.$width/$height.'">
				<span class="content-image-inner" style="background-image: url('.$preview_base64.');">
					<img src="'.$image_url.'" width="'.$width.'" height="'.$height.'" loading="lazy" style="background: transparent; display: block;">
				</span>
			</figure>';

		return $image_embed_html;
	}


	function display() {

		global $core;

		$target_width = $core->config->get( 'image_target_width' );

		if( isset($_GET['width']) ) $target_width = (int) $_GET['width'];
		if( $target_width < 1 ) $target_width = 10;

		$jpg_quality = $core->config->get( 'image_jpg_quality' );
		$png_to_jpg = $core->config->get( 'image_png_to_jpg' );

		$image_meta = getimagesize( $this->local_file_path );
		$filesize = filesize( $this->local_file_path );

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
			$core->debug( 'unknown image type '.$image_type);
			exit;
		}

		$cache_string = $this->local_file_path.$filesize.$target_width.$jpg_quality;
		$cache = new Cache( 'image', $cache_string );

		$cache_content = $cache->get_data();
		if( $cache_content ) {
			// return cached file, then end
			header("Content-Type: ".$mime_type);
			header("Content-Length: ".$cache->filesize);
			echo $cache_content;
			exit;
		}


		if( $image_type == IMAGETYPE_JPEG ) {
			$src_image = imagecreatefromjpeg( $this->local_file_path );
			if( ! $src_image ) {
				$core->debug( 'could not load jpg image' );
				exit;
			}

		} elseif( $image_type == IMAGETYPE_PNG ) {
			$src_image = imagecreatefrompng( $this->local_file_path );

			if( ! $src_image ) {
				$core->debug( 'could not load png image' );
				exit;
			}

			// handle transparency loading:
			imageAlphaBlending( $src_image, false );
			imageSaveAlpha( $src_image, true );

			if( $png_to_jpg ) {
				// set transparent background to specific color, when converting to jpg:
				$transparent_color = $core->config->get( 'image_background_color' );
				$background_image = imagecreatetruecolor( $src_width, $src_height );
				$background_color = imagecolorallocate( $background_image, $transparent_color[0], $transparent_color[1], $transparent_color[2] );
				imagefill( $background_image, 0, 0, $background_color );
				imagecopy( $background_image, $src_image, 0, 0, 0, 0, $src_width, $src_height );
				$src_image = $background_image;
				imagedestroy( $background_image );
			}

		}


		list( $src_image, $src_width, $src_height ) = $this->image_rotate( $src_image, $src_width, $src_height );

		
		if( $src_width > $target_width ) {
			
			$target_dimensions = $this->get_image_dimensions( $target_width, $src_width, $src_height);

			$width = $target_dimensions[0];
			$height = $target_dimensions[1];

			if( $width <= 0 || $height <= 0 ) {
				imagedestroy( $src_image );
				$core->debug( 'width or height <= 0', $width, $height );
				exit;
			}

			$target_image = imagecreatetruecolor($width, $height);

			if( ! $png_to_jpg && $image_type == IMAGETYPE_PNG ) {
				// handle alpha channel
				imageAlphaBlending( $target_image, false );
				imageSaveAlpha( $target_image, true );
			}

			imagecopyresized($target_image, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

		} else {

			$width = $src_width;
			$height = $src_height;

			$target_image = $src_image;
		}

		imagedestroy($src_image);


		if( $image_type == IMAGETYPE_JPEG
		 || ($png_to_jpg && $image_type == IMAGETYPE_PNG) ) {

			ob_start();
			imagejpeg( $target_image, NULL, $jpg_quality );
			$data = ob_get_contents();
			ob_end_clean();
			$cache->add_data( $data );

			header( 'Content-Type: image/jpeg' );
			echo $data;

		} elseif( $image_type == IMAGETYPE_PNG ) {

			ob_start();
			imagepng( $target_image );
			$data = ob_get_contents();
			ob_end_clean();
			$cache->add_data( $data );

			header( 'Content-Type: image/png' );
			echo $data;

		}

		imagedestroy( $target_image );
		exit;

	}


	function get_image_preview_base64() {

		global $core;
		
		$png_to_jpg = $core->config->get( 'image_png_to_jpg' );

		if( ! $png_to_jpg ) {
			// NOTE: when we use png files directly (and don't convert them to jpg), they could contain transparency. if they do, we cannot add a blurry preview base64 encoded image beneath it, because it would still be visible when the actual image (with transparency) is loaded, so we just return an empty preview image here (this is a 1x1 transparent pixel, base64 encoded):
			$transparent_pixel_base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
			return $transparent_pixel_base64;
		}

		$target_width = 50;
		$jpg_quality = 40;

		$image_meta = getimagesize( $this->local_file_path );
		if( ! $image_meta ) {
			$core->debug("no image meta", $this->local_file_path);
			return false;
		}

		$src_width = $image_meta[0];
		$src_height = $image_meta[1];
		$image_type = $image_meta[2];
		
		$filesize = filesize( $this->local_file_path );
		if( ! $filesize ) {
			$core->debug("no image filesize", $this->local_file_path);
			return false;
		}

		$cache_string = $this->local_file_path.$filesize;

		$cache = new Cache( 'image-preview', $cache_string );

		$cache_content = $cache->get_data();
		if( $cache_content ) {
			// return cached file, then end
			return $cache_content;
		}


		if( $image_type == IMAGETYPE_JPEG ) {
			$src_image = imagecreatefromjpeg( $this->local_file_path );
			if( ! $src_image ) {
				$core->debug( 'could not load jpg image' );
				exit;
			}

		} elseif( $image_type == IMAGETYPE_PNG ) {
			$src_image = imagecreatefrompng( $this->local_file_path );

			if( ! $src_image ) {
				$core->debug( 'could not load png image' );
				exit;
			}

			// handle transparency loading:
			imageAlphaBlending( $src_image, false );
			imageSaveAlpha( $src_image, true );

			// set transparent background to specific color:
			$transparent_color = $core->config->get( 'image_background_color' );
			$background_image = imagecreatetruecolor( $src_width, $src_height );
			$background_color = imagecolorallocate( $background_image, $transparent_color[0], $transparent_color[1], $transparent_color[2] );
			imagefill( $background_image, 0, 0, $background_color );
			imagecopy( $background_image, $src_image, 0, 0, 0, 0, $src_width, $src_height );
			$src_image = $background_image;
			imagedestroy( $background_image );

		} else {
			$core->debug( 'unknown image type '.$image_type);
			exit;	
		}



		list( $src_image, $src_width, $src_height ) = $this->image_rotate( $src_image, $src_width, $src_height );


		$target_dimensions = $this->get_image_dimensions( $target_width, $src_width, $src_height);

		$width = $target_dimensions[0];
		$height = $target_dimensions[1];

		if( $width <= 0 || $height <= 0 ) {
			imagedestroy( $src_image );
			$core->debug( 'width or height <= 0', $width, $height );
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

		$cache->add_data( $base64_data );

		imagedestroy( $target_image );

		return $base64_data;
	}




	function get_image_dimensions( $target_width, $src_width, $src_height ) {

		if( $src_width <= $target_width ) {
			return array( $src_width, $src_height );
		}
		
		$width = $target_width;
		$height = (int) round($src_height/$src_width*$width);

		return array( $width, $height );
	}

	
	function image_rotate( $image, $src_width, $src_height ) {

		$width = $src_width;
		$height = $src_height;

		$degrees = false;
		// NOTE: we ignore mirrored images (4, 5, 7) for now, and just rotate them like they would be non-mirrored (3, 6, 8)
		if( $this->orientation == 3 || $this->orientation == 4 ) {
			$degrees = 180;
		} elseif( $this->orientation == 6 || $this->orientation == 5 ) {
			$degrees = 270;
			$width = $src_height;
			$height = $src_width;
		} elseif( $this->orientation == 8 || $this->orientation == 7 ) {
			$degrees = 90;
			$width = $src_height;
			$height = $src_width;
		}

		if( $degrees ) $image = imagerotate( $image, $degrees, 0 );

		return [ $image, $width, $height ];
	}


}
