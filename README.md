# Eigenheim

a small website system, that acts as a micropub server so you can use whatever micropub client you want to write new posts. this will be part of a larger system.

this is currently a very early alpha stage. **you should not use this for now. THINGS WILL BREAK!**

here be dragons:

## initial setup

(this needs at least PHP 8 for now, and maybe forever, because PHP7 is already EOL)

copy all the files into a directory on your webserver, then open the url to this path in a webbrowser. follow the installation instructions.

you can then edit the textfiles that get created in the *content/* folder to change the content, and log in to a micropub client with your url to add new posts.

## updating

when you want update the system, download the latest release. backup your content/ folder, your config.php and the .htaccss file (and your custom theme, if you have any). you can delete the system/ folder, the index.php and the theme/default/ folder, and then re-upload them from the release .zip file.

if you want to reset the whole system, delete the following files and folders and open the url in a webbrowser to re-trigger the setup process:
- `.htaccess`
- `config.php`
- maybe the `content/` folder, if you want to reset the content as well
- maybe a custom theme in the `theme/` folder

## additional options

you may want to edit the *config.php* a bit after the initial setup and add additional information:

```php
<?php

return [
	'site_title' => 'My Eigenheim Site',
	'auth_mail' => 'mail@example.com',
	//'theme' => 'default',
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

## custom theme

You can duplicate the *theme/default/* folder, rename it and change the css. You can also create a *theme/{themename}/snippets/* folder and copy files from *system/site/snippets/* into this folder, to overwrite them on a per theme basis. all the files in the *snippets* folder have a version number at the start of the file, so you can see if they were updated since you last copied them.

you can define the theme in the *config.php* file like this: 'theme' => '{themename}',
