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
