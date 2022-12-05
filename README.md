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
	//'image_png_to_jpg' => true,   // automatically convert .png images to .jpg, for faster loading
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

**Important:** Before updating, backup your `content/` folder and your `config.php` (and your custom theme inside the `theme/` folder, if you have any). Better be safe than sorry.

Read the release notes of all the releases back to your version. Sometimes, you may need to manually update specific files in your custom theme.

Create a new empty file called `update` (or `update.txt`) in the root folder of your installation. Then open the website, and append `?update` to the URL to trigger the update process. Follow the steps.

If you don't finish the update, manually delete the `update` (or `update.txt`) file (if the update process finishes, this file gets deleted automatically).

After updating, open your website and check if everything works as expected.

If you want to perform a manual update, delete the `system/` and `theme/default/` folders, as well as the `index.php` and `.htaccess` files from the root folder. Then download the latest (or an older) release from the releases page. Upload the `system/` and `theme/default/` folders and the `index.php` file from the downloaded release zip-file into your web directory. Then open the url in a webbrowser.

If you want to reset the whole system, delete the following files and folders and open the url in a webbrowser to re-trigger the setup process:
- `.htaccess`
- `config.php`
- maybe the `content/` folder, if you want to reset the content as well
- maybe the custom theme folders in the `theme/` directory (leave the `theme/default/` directory there, though)
