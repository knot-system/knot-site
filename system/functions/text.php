<?php

if( ! $eigenheim ) exit;

// TODO: get rid of these helper functions?


function text_cleanup( $text ) {
	$text = new Text( $text );

	return $text->text_cleanup()->get();
}


function text_auto_p( $text ){
	$text = new Text($text);

	return $text->text_auto_p()->get();
}


function text_auto_a( $text ) {
	$text = new Text($text);

	return $text->text_auto_a()->get();
}
