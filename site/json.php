<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

$posts = get_posts();

$json = array(
	'version' => 'https://jsonfeed.org/version/1.1',
	'title' => 'Eigenheim JSON Feed',
	'description' => '',
	'home_page_url' => EH_BASEURL,
	'feed_url' => EH_BASEURL.'feed/json',
	'items' => $posts
);

header('Content-Type: application/json; charset=utf-8');

echo json_encode( $json );
