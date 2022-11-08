<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>Eigenheim</title>
</head>
<body>
	<h1>Eigenheim</h1>
<?php

$files = \Eigenheim\Files::read_dir( '' );

foreach( $files as $filename ){

	$file_contents = \Eigenheim\Files::read_file( $filename );

	$title = $file_contents['title'];
	$text = $file_contents['text'];

	?>
	<hr>
	<h2><?= $title ?></h2>
	<p><?= $text ?></p>
	<?php
}
?>
<hr>

</body>
</html>