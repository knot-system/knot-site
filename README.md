# Eigenheim

A small website system, that acts as a micropub server so you can use whatever micropub client you want to write new posts. this will be part of a larger system.

This is currently a very early alpha stage. **You should not use this for now. THINGS WILL BREAK!**

Here be dragons:

## Initial Setup

Your server needs to run at least PHP 8.0 or later.

Copy all the files into a directory on your webserver, then open the url to this path in a webbrowser. Follow the installation instructions.

You can then edit the textfiles that get created in the *content/* folder to change the content, and log in to a micropub client with your url to add new posts.

## Additional Options

You may want to edit the *config.php* a bit after the initial setup and add additional information:

```php
<?php

return [
	'site_title' => 'My Eigenheim Site',
	'auth_mail' => 'mail@example.com',
	//'theme' => 'default',
	//'posts_per_page' => 5,
	//'microsub' => 'https://www.example.com/microsub',
	//'logging' => false,
	//'debug' => false,
	'author' => [
		'p-name' => 'My Author Name',
		'u-email' => 'mail@example.com',
		'p-note' => 'This is my Website. Hi.',
		// .. and other h-card properties; for an overview see
		// https://microformats.org/wiki/h-card#Properties
	],
	//'image_cache_active' => true, // cache resized images
	//'image_target_width' => 1200, // resize bigger images to this width
	//'image_jpg_quality' => 80,    // when resizing/caching, use this quality for jpg files
	//'allowed_html_elements' => [ 'p', 'br', 'i', 'b', 'em', 'strong', 'a', 'ul', 'ol', 'li', 'span' ] // all other HTML-elements get removed from the content
];

```

If you want to add a microsub endpoint, replace `https://www.example.com/microsub` with the endpoint of your choice and uncomment the line. You can also add additional author information - use h-card properties here (but not all may be used in the frontend; this will be expanded later).

## Custom Theme

You can duplicate the *theme/default/* folder, rename it and update the theme name and author information in the *theme/{themename}/config.php*. You can also create a *theme/{themename}/snippets/* folder and copy files from *system/site/snippets/* into this folder, to overwrite them on a per-theme basis. All the files in the *snippets/* folder have a version number at the start of the file, so you can see if they were updated since you last copied them. The *theme/{themename}/functions.php* contains some functions that get called when the theme gets loaded.

You can define the theme your site uses in the *config.php* file like this:
```php
return [
	// site_title and other fields ...
	'theme' => '{themename}',
];
```

## Updating

When you want update the system, download the latest release. Backup your *content/* folder and your *config.php* (and your custom theme, if you have any). You can delete the *system/* folder, the *index.php* and the *theme/default/* folder, and then re-upload them from the release .zip file. We'll add an option to automatically update the system in the future.

If you want to reset the whole system, delete the following files and folders and open the url in a webbrowser to re-trigger the setup process:
- `.htaccess`
- `config.php`
- maybe the `content/` folder, if you want to reset the content as well
- maybe the custom theme folders in the `theme/` directory (leave the `theme/default/` directory there, though)
