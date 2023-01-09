# Eigenheim

A small website system, that acts as a micropub server so you can use whatever micropub client you want to write new posts. This will be part of a larger system. More details will be added later.

This is currently in a early alpha stage. **You should not use this for now. THINGS WILL BREAK!**

Here be dragons:

## Initial Setup

Your server needs to run at least PHP 8.0 or later.

Copy all the files into a directory on your webserver, then open the url to this path in a webbrowser. Follow the installation instructions.

This will create a `config.php` in the root folder that will hold the configuration of your system, als well as a `content/` folder where all the content of your websites (posts and pages) live. These two items, the `config.php` and `content/` folder, are unique to your website and very important - keep a backup around, if you want to make sure to not lose anything.

The setup also creates some other files that are needed, like a (hidden) `.htaccess` file and a `cache/` folder. When delete those item, they will be re-created as needed. They will also be automatically deleted and recreated when you make a system update.

You can now edit the textfiles that get created in the `content/` folder to change the content, and log in to a micropub client with your url to add new posts.

## Additional Options

You may want to edit the `config.php` a bit after the initial setup and add additional information:

```php
<?php

return [
	'site_title' => 'My Eigenheim Site',
	'auth_mail' => 'mail@example.com',
	'theme' => 'default',
	'posts_per_page' => 5,
	'microsub' => 'https://www.example.com/microsub',
	'author' => [
		'p-name' => 'My Author Name',
		'u-email' => 'mail@example.com',
		'p-note' => 'This is my Website. Hi.',
		// .. and other h-card properties; for an overview see
		// https://microformats.org/wiki/h-card#Properties
	],
	'allowed_html_elements' => [ 'p', 'br', 'i', 'b', 'em', 'strong', 'a', 'ul', 'ol', 'li', 'span' ] // all other HTML-elements get removed from the content
	
	// for more config options, see the file system/site/config.php
	
];

```

If you want to add a microsub endpoint, replace `https://www.example.com/microsub` with the endpoint of your choice and uncomment the line. You can also add additional author information - use h-card properties here (but not all may be used in the frontend; this will be expanded later). All other options can be uncommented as well, the values displayed here are their default values, you can change them accordingly. The option names *may change* in a later version of this system. This section of the README will be expanded later, when we reach a stable state.

The loading order of the config is as follows:
1) `system/site/config.php`
   gets overwritten by:
2) `theme/{themename}/config.php` (if it exists)
   gets overwritten by:
3) `config.php` (in the root folder)

## Custom Theme

You can duplicate the `theme/default/` folder, rename it and update the theme name and author information in the `theme/{themename}/theme.php`.

You can define the theme your site uses in the `config.php` file like this:
```php
return [
	// site_title and other fields ...
	'theme' => '{themename}',
];
```

If the theme folder does not exist, the system will automatically use the *default* theme.

You can also create a `theme/{themename}/snippets/` folder and copy files from `system/site/snippets/` into this folder, to overwrite them on a per-theme basis. All the files in the `snippets/` folder have a version number at the start of the file, so you can see if they were updated since you last copied them. The auto-updater will also show you, which of the snippets in your custom theme are out of date and need updating.

The `theme/{themename}/functions.php` contains some functions that get called when the theme gets loaded.

The `theme/{themename}/config.php` can overwrite config options from `system/site/config.php` (but gets itself overwritten by the local `config.php` in the root directory), so the custom theme can for example set its own image sizes.

## Updating

**Important:** Before updating, backup your `content/` folder and your `config.php` (and your custom theme inside the `theme/` folder, if you have any). Better be safe than sorry.

Create a new empty file called `update` (or `update.txt`) in the root folder of your installation. Then open the website, and append `?update` to the URL to trigger the update process. **Important:** if you don't finish the update, manually delete the `update` (or `update.txt`) file (if the update process finishes, this file gets deleted automatically).

Follow the steps of the updater. It will show you all of the new release notes that happened since your currently installed version - read them carefully, especially at the moment, because some things will change and may need manual intervention. After the update is complete, and if you have a custom theme installed, it will list all the files you manually need to update in your custom theme - you should do so, or you may miss out on new functionality, or the site may even break completely.

After updating, open your website; this will trigger the setup process again, that creates some missing files. Then check if everything works as expected.

### manual update

If you want to perform a manual update, delete the `system/`, `theme/default/` and `cache/` folders, as well as the `index.php`, `.htaccess`, `README.md` and `changelog.txt` files from the root folder. Then download the latest (or an older) release from the releases page. Upload the `system/` and `theme/default/` folders and the `index.php`, `README.md` and `changelog.txt` file from the downloaded release zip-file into your web directory. Then open the url in a webbrowser; this will trigger the setup process again and create some missing files.

If you have a custom theme active, make sure all your snippets are up to date and at least the same version as the corresponding files inside `system/site/snippets/`.

### system reset

If you want to reset the whole system, delete the following files and folders and open the url in a webbrowser to re-trigger the setup process:
- `.htaccess`
- `config.php`
- the `cache/` folder
- maybe the `content/` folder, if you want to reset the content as well
- maybe the custom theme folders in the `theme/` directory (leave the `theme/default/` directory there, though)

## Backup

You should routinely backup your content. To do so, copy these files & folders to a secure location:

- the `content/` folder. This contains all your posts and pages, and all the images you posted
- the `config.php`. This contains things like the sitename and which theme you use and other settings
- if you have a custom theme inside the `theme/` directory, make a backup of it as well. The `theme/default/` theme comes with the system, so no need to back it up

When you want to restore a backup, delete the current folders & files from your webserver, and upload your backup. You should also delete the `cache/` folder, so everything gets re-cached and is up to date. If you also want to update or reset your system, see the *Update* section above.
