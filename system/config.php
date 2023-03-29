<?php

// NOTE: you can overwrite these options:
// - in your custom theme, via /theme/{themename}/config.php
// - and/or via the config.php in the root folder

return [
	'site_title' => 'Eigenheim',
	'auth_mail' => false,
	'debug' => false,
	'logging' => true,
	'theme' => 'default',
	'theme-color-scheme' => 'default', // depends on the theme; the default theme supports 'blue', 'green', 'red', 'lilac'
	'posts_per_page' => 5,
	'microsub' => false,
	'author' => [
		'p-name' => false,
		'u-url' => false
	],
	'allowed_html_elements' => [ 'del', 'pre', 'blockquote', 'code', 'b', 'strong', 'u', 'i', 'em', 'ul', 'ol', 'li', 'p', 'br', 'span', 'a', 'img', 'video', 'audio' ],
	'image_target_width' => 1000,
	'preview_target_width' => 1000,
	'image_jpg_quality' => 70,
	'image_png_to_jpg' => true,
	'feed_limit_posts' => 20,
	'slug_max_length' => 80,
	'add_footnote_to_links' => true,
	'cache_lifetime' => 60*60*24*30, // 30 days, in seconds
	'user_agent' => 'maxhaesslein/eigenheim/', // version will be automatically appended
	'baseurl_overwrite' => false, // overwrite the baseurl; see #transparent-subfolder in the README.md
	'basefolder_overwrite' => false, // overwrite the basefolder; see #transparent-subfolder in the README.md
];