<?php

// Version: alpha.12

if( ! $eigenheim ) exit;

$page = $args['page'];
if( ! $page ) return;

$title = $page->fields['title'];
$text = $page->fields['content_html'];

?>

	<article class="h-entry">

<?php
if( $title ) {
?>
		<h2 class="p-name"><?= $title ?></h2>

<?php
}

if( $text ) {
?>
		<div class="e-content">
			<?= $text ?>
		</div>
<?php
}
?>

	</article>
