# Eigenheim

this is very, very early alpha stage. **you should not use this for now. THINGS WILL BREAK!** here be dragons:

copy all the files into a directory on your webserver

then add a file 'site/config.php' with this content:

```php
<?php

return [
	'auth_mail' => 'mail@example.com',
	//'microsub' => 'https://www.example.com/microsub'
];

```

replace `mail@example.com` with your e-mail address, for the login code
if you want to add a microsub endpoint, replace `https://www.example.com/microsub` with the endpoint of your choice and uncomment the line

open the page. see if it works.

log in to a micropub client with your url and add content.
