<?php

return [
	'theme' => 'default',
	'posts_per_page' => 5,
	'microsub' => false,
	'logging' => false,
	'debug' => false,
	'author' => [],
	'allowed_html_elements' => [ 'p', 'br', 'i', 'b', 'em', 'strong', 'a', 'ul', 'ol', 'li', 'span', 'img' ],
	'image_target_width' => 1000,
	'preview_target_width' => 1000,
	'image_jpg_quality' => 70,
	'image_png_to_jpg' => true,
	'feed_limit_posts' => 20,
	'add_footnote_to_links' => true,
	'cache_lifetime' => 60*60*24*30, // 30 days, in seconds
];
