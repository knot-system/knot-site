<?php

if( ! $eigenheim ) exit;


function log_message( $message ) {

	global $eigenheim;

	if( ! $eigenheim->config->get('logging') ) return;

	$timestamp = time();

	$date = date( 'Y-m-d H:i:s', $timestamp );

	if( is_array($message) ) {
		$message = implode("\n\r", $message);
	}

	$file_contents_append = "[".$date."]\n\r".$message."\n\r\n\r";

	$log_filepath = $eigenheim->abspath.'log/';

	if( ! is_dir( $log_filepath) ) {
		mkdir( $log_filepath, 0777, true );

		if( ! is_dir( $log_filepath) ) return;

	}

	$log_filename = $log_filepath.date('Y-m-d', $timestamp).'.txt';

	file_put_contents( $log_filename, $file_contents_append, FILE_APPEND );

}
