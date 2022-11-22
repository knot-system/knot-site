<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;


function get_pages() {

	$pages = database_get_pages();

	return $pages;

}


function get_page( $page_id ) {

	$page = database_get_page( $page_id );

	return $page;
}
