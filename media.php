<?php

// TODO/CLEANUP: the old cached image does not get cleared if the target_width or jpg_quality or filename or filesize change. we may want to periodically clear out the /cache folder, or old files in the /cache folder




// NOTE: these defines need to happen in the root media.php, because they depend on the location of this file
define( 'EH_ABSPATH', realpath(dirname(__FILE__)).'/' );

$basefolder = str_replace( 'media.php', '', $_SERVER['PHP_SELF']);
define( 'EH_BASEFOLDER', $basefolder );


include_once( EH_ABSPATH.'system/functions/config.php' );
include_once( EH_ABSPATH.'system/functions/media.php' );


$cache_active = get_config( 'image_cache_active', true );
$target_width = get_config( 'image_target_width', 1200 );
$jpg_quality = get_config( 'image_jpg_quality', 80 );

$file_path = EH_ABSPATH.str_replace( EH_BASEFOLDER, '', $_SERVER['REQUEST_URI'] );

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
	echo '<strong>Error:</strong> unknown image type ('.$image_type.')';
	if( isset($_GET['debug']) ) {
		echo '<pre>';
		var_dump($image_meta);
		echo '</pre>';
	}
	exit;
}


$cache_string = $file_path.$filesize;

$cache_folder = EH_ABSPATH.'cache/';
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
		echo '<strong>Error:</strong> could not load jpg image';
		exit;
	}

} elseif( $image_type == IMAGETYPE_PNG ) {
	$src_image = imagecreatefrompng( $file_path );
	if( ! $src_image ) {
		echo '<strong>Error:</strong> could not load png image';
		exit;
	}

}

if( $src_width > $target_width ) {
	
	$target_dimensions = get_image_dimensions( $target_width, $src_width, $src_height);

	$width = $target_dimensions[0];
	$height = $target_dimensions[1];

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
