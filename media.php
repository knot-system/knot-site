<?php

// TODO/CLEANUP: revisit this. currently, in the html the original file size gets included. also, the cache does not get refreshed, if the image changes but the filename stays the same. and we need some config options. and this code is janky.



// NOTE: these defines need to happen in the root media.php, because they depend on the location of this file
define( 'EH_ABSPATH', realpath(dirname(__FILE__)).'/' );

$basefolder = str_replace( 'media.php', '', $_SERVER['PHP_SELF']);
define( 'EH_BASEFOLDER', $basefolder );


$cache_active = true; // TODO: make this a config option
$target_width = 1200; // TODO: make this a config option
$jpg_quality = 80;    // TODO: make this a config option

$file_path = EH_ABSPATH.str_replace( EH_BASEFOLDER, '', $_SERVER['REQUEST_URI'] );

$image_meta = getimagesize( $file_path );


$file_extension = '';
if( $image_meta['mime'] == 'image/jpeg' ) $file_extension = 'jpg';
elseif( $image_meta['mime'] == 'image/png' ) $file_extension = 'png';


$cache_folder = EH_ABSPATH.'cache/';
$cache_name = md5($file_path).'.'.$file_extension;
$cache_file = $cache_folder.$cache_name;

if( file_exists($cache_file) ) {
	// return cached file, then end
	$fp = fopen($cache_file, 'rb');
	header("Content-Type: ".$image_meta['mime']);
	header("Content-Length: " . filesize($cache_file));
	fpassthru($fp);
	exit;
}

if( $image_meta['mime'] == 'image/jpeg' ) {
	$src_image = imagecreatefromjpeg( $file_path );
	if( ! $src_image ) {
		echo '<strong>Error:</strong> could not load jpg image';
		exit;
	}

} elseif( $image_meta['mime'] == 'image/png' ) {
	$src_image = imagecreatefrompng( $file_path );
	if( ! $src_image ) {
		echo '<strong>Error:</strong> could not load png image';
		exit;
	}

} else {
	echo '<strong>Error:</strong> unknown mime type';
	exit;
}

$src_width = $image_meta[0];
$src_height = $image_meta[1];

if( $src_width > $target_width ) {
	$width = $target_width;
	$height = (int) round($src_height/$src_width*$width);

	$target_image = imagecreatetruecolor($width, $height);
	imagecopyresized($target_image, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

	imagedestroy($src_image);

} else {
	$target_image = $src_image;
}



if( $image_meta['mime'] == 'image/jpeg' ) {
	header( 'Content-Type: '.$image_meta['mime'] );
	imagejpeg( $target_image, NULL, $jpg_quality );

	if( $cache_active ) {
		imagejpeg( $target_image, $cache_file, $jpg_quality );
	}

} elseif( $image_meta['mime'] == 'image/png' ) {
	header( 'Content-Type: '.$image_meta['mime'] );
	imagepng($target_image);

	if( $cache_active ) {
		imagepng( $target_image, $cache_file );
	}

}

imagedestroy( $target_image );
