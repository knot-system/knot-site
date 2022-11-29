<?php

if( ! defined( 'EH_ABSPATH') ) exit;


function get_image_html( $image_path ) {

	$target_width = get_config( 'image_target_width', 1200 );

	$image_meta = getimagesize( EH_ABSPATH.'content/'.$image_path );
	$src_width = $image_meta[0];
	$src_height = $image_meta[1];

	$target_dimensions = get_image_dimensions( $target_width, $src_width, $src_height );

	$width = $target_dimensions[0];
	$height = $target_dimensions[1];

	$html = '<img src="'.EH_BASEURL.'content/'.$image_path.'" width="'.$width.'" height="'.$height.'" loading="lazy">';

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
