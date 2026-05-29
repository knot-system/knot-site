<?php


function feed_add_utm( $item, $utm_source ) {

	// add utm_source to the post url:
	$separator = strpos($item['url'], '?') !== false ? '&' : '?';
	$item['url'] = $item['url'] . $separator . 'utm_source=' . $utm_source;

	// add utm_source to internal links in content_html:
	$item['content_html'] = preg_replace_callback(
		'/href="(https?:\/\/[^"]*tilman\.me[^"]*)"/i',
		function( $matches ) use ( $utm_source ) {
			$url = $matches[1];
			$separator = strpos($url, '?') !== false ? '&' : '?';
			return 'href="' . $url . $separator . 'utm_source=' . $utm_source . '"';
		},
		$item['content_html']
	);

	return $item;

}
