# Eigenheim

a small website system, that acts as a micropub server so you can use whatever micropub client you want to write new posts. this will be part of a larger system.

this is currently a very early alpha stage. **you should not use this for now. THINGS WILL BREAK!**

here be dragons:

(this needs at least PHP 8 for now, and maybe forever, because PHP7 is already EOL)

copy all the files into a directory on your webserver. make sure to also copy the *.htacces* file. if this system exists in a subfolder, you need to edit the `RewriteBase` line in the *.htaccess* file.

then add a file 'site/config.php' with this content:

```php
<?php

return [
	'auth_mail' => 'mail@example.com',
	//'microsub' => 'https://www.example.com/microsub',
	'author' => [
		'p-name' => 'My Author Name',
		'u-email' => 'mail@example.com',
		'p-note' => 'This is my Website. Hi.',
		// .. and other h-card properties, see https://microformats.org/wiki/h-card#Properties for an overview
	],
];

```

replace `mail@example.com` with your e-mail address, for the login code
if you want to add a microsub endpoint, replace `https://www.example.com/microsub` with the endpoint of your choice and uncomment the line
add author information. you can use h-card properties here, but not all may be used in the frontend. this will be expanded later.

open the page. see if it works.

log in to a micropub client with your url and add content.
