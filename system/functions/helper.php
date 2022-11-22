<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function snippet( $path, $args = array(), $return = false ) {
	
	$include_path = EH_ABSPATH.'site/snippets/'.$path.'.php';

	if( ! file_exists( $include_path) ) return;

	ob_start();

	include( $include_path );

	$snippet = ob_get_contents();
	ob_end_clean();

	if( $return === true ) {
		return $snippet;
	}

	echo $snippet;

}


function trailing_slash_it( $string ){
	// add a slash at the end, if there isn't already one ..

	$string = preg_replace( '/\/*$/', '', $string );
	$string .= '/';

	return $string;
}
