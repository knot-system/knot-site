<?php

if( ! $eigenheim ) exit;


function get_pages() {

	// TODO: pages should be something like $eigenheim->pages()->get()

	global $eigenheim;
	$database = new Database( '', true, 'page.txt' );
	$objects = $database->get();

	$pages = array();

	foreach( $objects as $object ) {
		$page = new Page( $object );
		$pages[] = $page;
	}

	return $pages;
}


function get_page( $page_id ) {

	// TODO: this should be something like $eigenheim->pages()->get($page_id)

	global $eigenheim;
	$database = new Database( '', true, 'page.txt' );
	$objects = $database->get();

	if( ! array_key_exists($page_id, $objects) ) return false;

	$page = new Page( $objects[$page_id] );
	
	return $page;
}
