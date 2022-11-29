<?php

// TODO/CLEANUP: revisit this. currently, in the html the original file size gets included. also, the cache does not get refreshed, if the image changes but the filename stays the same. and we need some config options. and this code is janky.




// NOTE: these defines need to happen in the root media.php, because they depend on the location of this file
define( 'EH_ABSPATH', realpath(dirname(__FILE__)).'/' );

$basefolder = str_replace( 'media.php', '', $_SERVER['PHP_SELF']);
define( 'EH_BASEFOLDER', $basefolder );


include_once( EH_ABSPATH.'system/functions/config.php' );


$cache_active = get_config( 'image_cache_active', true );
$target_width = get_config( 'image_target_width', 1200 );
$jpg_quality = get_config( 'image_jpg_quality', 80 );

$file_path = EH_ABSPATH.str_replace( EH_BASEFOLDER, '', $_SERVER['REQUEST_URI'] );

$image_meta = getimagesize( $file_path );

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
	echo '<strong>Error:</strong> unknown image type';
	exit;
}


$cache_folder = EH_ABSPATH.'cache/';
$cache_name = md5($file_path).'_'.$target_width.'_'.$jpg_quality.'.'.$file_extension;
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
	$width = $target_width;
	$height = (int) round($src_height/$src_width*$width);

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
