<?php

if( ! $eigenheim ) exit;

// see https://www.jsonfeed.org/version/1.1/ for details

$json = array(
	'version' => 'https://jsonfeed.org/version/1.1',
	'title' => $eigenheim->config->get( 'site_title' ),
	'description' => '',
	'home_page_url' => url(),
	'feed_url' => url('feed/json'),
);

$author = get_author_information();
if( $author ) {
	$json['authors'] = array(
		'name' => $author['display_name'],
	);

	if( ! empty($author['url']) ) $json['authors']['url'] = $author['url'];
	if( ! empty($author['avatar']) ) $json['authors']['avatar'] = $author['avatar'];
}

$limit_count = $eigenheim->config->get('feed_limit_posts');
$posts = $eigenheim->posts->limit($limit_count)->get();

if( count($posts) ) $json['items'] = $posts;

header('Content-Type: application/json; charset=utf-8');
echo json_encode( $json );
