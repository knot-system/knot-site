<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

?><!DOCTYPE html>
<!--
___________.__                     .__           .__         
\_   _____/|__| ____   ____   ____ |  |__   ____ |__| _____  
 |    __)_ |  |/ ___\_/ __ \ /    \|  |  \_/ __ \|  |/     \ 
 |        \|  / /_/  >  ___/|   |  \   Y  \  ___/|  |  Y Y  \
/_______  /|__\___  / \___  >___|  /___|  /\___  >__|__|_|  /
        \/   /_____/      \/     \/     \/     \/         \/ 
-->
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>Eigenheim</title>
	<link rel="authorization_endpoint" href="https://indieauth.com/auth" />
	<link rel="token_endpoint" href="https://tokens.indieauth.com/token" />
	<link rel="me authn" href="mailto:<?= \Eigenheim\Config::getConfig('auth_mail') ?>">
	<link rel="micropub" href="<?= \Eigenheim\Micropub::getEndpoint() ?>" />
	<link rel="stylesheet" href="<?= EH_BASEURL ?>assets/css/global.css?v=<?= \Eigenheim\Core::getVersion() ?>">
</head>
<body>
<header>
	<h1>Eigenheim</h1>
</header>
<main>
<?php

	$files = \Eigenheim\Files::read_dir( '' );

	foreach( $files as $filename ){

		$file_contents = \Eigenheim\Files::read_file( $filename );

		$title = $file_contents['name'];
		$text = \Eigenheim\Text::auto_p($file_contents['content']);

		if( ! $text ) continue;

	?>
	<hr>
	<h2><?= $title ?></h2>
	<?= $text ?>
<?php
	}
	?>
	<hr>
</main>
<footer>Eigenheim v.<?= \Eigenheim\Core::getVersion() ?></footer>
</body>
</html>