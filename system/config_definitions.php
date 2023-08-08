<?php

// these options are displayed in the 'knot-control' module

return [
	'site_title' => [
		'type' => 'string',
		'description' => 'the title of your website',
	],
	'theme' => [
		'type' => 'theme',
		'description' => 'you can add more themes in the <code>theme/</code> subfolder',
	],
	'theme-color-scheme' => [
		'type' => 'array',
		'description' => 'not all themes support (all) color schemes',
		'options' => ['default' => 'Default (blue)', 'green' => 'Green', 'red' => 'Red', 'lilac' => 'Lilac'],
	],
	'microsub' => [
		'type' => 'url',
		'description' => 'URL to a microsub endpoint',
	],
	'indieauth-metadata' => [
		'type' => 'url',
		'description' => 'URL to an IndieAuth Metadata endpoint',
	],
	'posts_per_page' => [
		'type' => 'int',
		'description' => 'the number of posts to display on a page',
	],
	'link_detection' => [
		'type' => 'bool',
		'description' => 'automatically detect links in post content; if set to <code>false</code>, <code>link_preview</code> is also set to <code>false</code>',
	],
	'link_preview' => [
		'type' => 'bool',
		'description' => 'show link previews below the post content; if <code>link_detection</code> is set to <code>false</code>, this option is also set to <code>false</code>',
	],
];
