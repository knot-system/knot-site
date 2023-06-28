<?php

// NOTE: you can overwrite these options:
// - in your custom theme, via /theme/{themename}/config.php
// - and/or via the config.php in the root folder

return [
	'site_title' => 'Eigenheim',
	'debug' => false,
	'logging' => true,
	'theme' => 'default',
	'theme-color-scheme' => 'default', // depends on the theme; the default theme supports 'blue', 'green', 'red', 'lilac'
	'posts_per_page' => 5,
	'indieauth-metadata' => true, // IndieAuth metadata tag, for IndieAuth discovery; if set to false, it gets omitted; if set to true, it defaults to the internal indieauth-metadata endpoint; if set to a url, the url will be included
	'endpoint-discovery-via-header' => true, // if set to true, the discovery endpoints will be included via the http 'Link' header; if set to false, the discovery endpoints will be included via <link rel=".." href=".."> meta tags
	'authorization_endpoint' => false,
	'token_endpoint' => false,
	'code_challenge_methods_supported' => ['S256'], // array; could be 'plain' or 'S256', depends on authorization and token endpoint
	'rel-me' => false, // the 'rel="me authn"' href value, if needed; can be "mailto:mail@example.com" if you want to use a mail address
	'microsub' => false, // set to a URL to include a microsub endpoint
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
	'user_agent' => 'maxhaesslein/eigenheim/', // version will be automatically appended
	'baseurl_overwrite' => false, // overwrite the baseurl; see #transparent-subfolder in the README.md
	'basefolder_overwrite' => false, // overwrite the basefolder; see #transparent-subfolder in the README.md
	'link_detection' => true, // automatically detect links in post content; if set to false, link_preview is also set to false, regardless of its config option
	'link_preview' => true, // show link previews
	'link_preview_max_age' => 60*60*6, // refresh link previews after x seconds
	'link_preview_nojs_refresh' => false, // refresh link previews via PHP; this makes pageloading with preview links slower, but does not rely on JavaScript to fetch link previews in the background
];
