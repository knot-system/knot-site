<?php

// NOTE: you can overwrite these options:
// - in your custom theme, via /theme/{themename}/config.php
// - and/or via the config.php in the root folder

return [
	'site_title' => 'Knot Site',
	'debug' => false,
	'logging' => true,
	'theme' => 'default',
	'theme-color-scheme' => 'default', // depends on the theme; the default theme supports 'blue', 'green', 'red', 'lilac'
	'microsub' => false, // set to a URL to include a microsub endpoint
	'indieauth-metadata' => false, // set to a URL for the IndieAuth server metadata discovery
	'posts_per_page' => 5,
	'author' => [
		'p-name' => false,
		'u-url' => false
	],
	'allowed_html_elements' => [ 'del', 'pre', 'blockquote', 'code', 'b', 'strong', 'u', 'i', 'em', 'ul', 'ol', 'li', 'p', 'br', 'span', 'a', 'img', 'video', 'audio' ],
	'image_target_width' => 1000,
	'preview_target_width' => 1000,
	'image_jpg_quality' => 70, // quality of jpg images; you neeed to empty the cache when changing this option
	'image_png_to_jpg' => true, // convert png images to jpg (faster, but looses transparency); you need to empty the cache when changing this option
	'image_background_color' => [ 255, 255, 255 ], // backgroundcolor for transparent images, when 'image_png_to_jpg' option is set to true; you need to empty the cache when changing this option
	'feed_limit_posts' => 20,
	'slug_max_length' => 80,
	'add_footnote_to_links' => true,
	'cache_lifetime' => 60*60*24*30, // 30 days, in seconds
	'user_agent' => 'knot/site/', // version will be automatically appended
	'baseurl_overwrite' => false, // overwrite the baseurl; see #transparent-subfolder in the README.md
	'basefolder_overwrite' => false, // overwrite the basefolder; see #transparent-subfolder in the README.md
	'link_detection' => true, // automatically detect links in post content; if set to false, link_preview is also set to false, regardless of its config option
	'link_preview' => true, // show link previews
	'link_preview_max_age' => 60*60*6, // refresh link previews after x seconds
	'link_preview_nojs_refresh' => false, // refresh link previews via PHP; this makes pageloading with preview links slower, but does not rely on JavaScript to fetch link previews in the background
];
