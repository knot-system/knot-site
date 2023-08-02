<?php

// these options are displayed in the 'homestead-control' module

return [
	'site_title' => [
		'type' => 'string',
		'description' => '',
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
		'description' => '',
	],
	'indieauth-metadata' => [
		'type' => 'url',
		'description' => '',
	],
	'posts_per_page' => [
		'type' => 'int',
		'description' => '',
	],
	'link_detection' => [
		'type' => 'bool',
		'description' => '',
	],
	'link_preview' => [
		'type' => 'bool',
		'description' => '',
	],
];
