<?php


function url( $path = '' ) {
	global $eigenheim;
	return $eigenheim->url( $path );
}


function trailing_slash_it( $string ){
	global $eigenheim;
	return $eigenheim->trailing_slash_it( $string );
}


function add_stylesheet( $path ) {
	global $eigenheim;
	$eigenheim->theme->add_stylesheet( $path );
}


function snippet( $path, $args = array(), $return = false ) {
	global $eigenheim;
	return $eigenheim->theme->snippet( $path, $args, $return );
}


function get_class_attribute( $classes ) {

	if( ! is_array( $classes ) ) $classes = explode( ' ', $classes );

	$classes = array_unique( $classes ); // remove double class names
	$classes = array_filter( $classes ); // remove empty class names

	if( ! count($classes) ) return '';

	return ' class="'.implode( ' ', $classes ).'"';
}


function get_author_information( $raw = false ){

	global $eigenheim;
	$author = $eigenheim->get_author_information( $raw );

	return $author;
}


function get_navigation(){

	global $eigenheim;

	$pages = $eigenheim->pages->get();

	if( ! $pages ) return false;

	$route = get_route();
	$current_page_id = false;
	if( $route['template'] == 'page' && ! empty($route['args']['page_id']) ) {
		$current_page_id = $route['args']['page_id'];
	}

	$navigation = false;

	foreach( $pages as $page ) {

		$is_current_page = false;
		if( $current_page_id && $page->id == $current_page_id ) {
			$is_current_page = true;
		}

		$navigation[] = array(
			'title' => $page->fields['title'],
			'permalink' => $page->fields['permalink'],
			'is_current_page' => $is_current_page
		);
		
	}

	if( ! count($navigation) ) return false;

	return $navigation;
}
