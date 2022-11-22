# Eigenheim

a small website system, that acts as a micropub server so you can use whatever micropub client you want to write new posts. this will be part of a larger system.

this is currently a very early alpha stage. **you should not use this for now. THINGS WILL BREAK!**

here be dragons:

## initial setup

(this needs at least PHP 8 for now, and maybe forever, because PHP7 is already EOL)

copy all the files into a directory on your webserver, then open the url to this path in a webbrowser. follow the installation instructions.

you can then edit the textfiles that get created in the *content/* folder to change the content, and log in to a micropub client with your url to add new posts.

if you want to reset the whole system, delete the following files and folders and open the url in a webbrowser:
- `content/`
- `.htaccess`
- `site/config.php`

## additional options

you may want to edit the *site/config.php* a bit after the initial setup and add additional information:

```php
<?php

return [
	'site_title' => 'My Eigenheim Site',
	'auth_mail' => 'mail@example.com',
	//'microsub' => 'https://www.example.com/microsub',
	//'logging' => false,
	'author' => [
		'p-name' => 'My Author Name',
		'u-email' => 'mail@example.com',
		'p-note' => 'This is my Website. Hi.',
		// .. and other h-card properties; for an overview see
		// https://microformats.org/wiki/h-card#Properties
	],
];

```

if you want to add a microsub endpoint, replace `https://www.example.com/microsub` with the endpoint of your choice and uncomment the line. you can also add additional author information - use h-card properties here (but not all may be used in the frontend; this will be expanded later).

