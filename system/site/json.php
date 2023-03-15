<?php

if( ! $core ) exit;

$core->doing_feed = true;

// see https://www.jsonfeed.org/version/1.1/ for details
$json = array(
	'version' => 'https://jsonfeed.org/version/1.1',
	'title' => $core->config->get( 'site_title' ),
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

$limit_count = $core->config->get('feed_limit_posts');
$posts = $core->posts->limit($limit_count)->get();

if( count($posts) ) {
	$json['items'] = array();
	foreach( $posts as $post ) {
		$json['items'][] = $post->fields;
	}
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode( $json );
