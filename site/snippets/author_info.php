<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

$info = get_author_information( true );

if( ! count($info) ) return;

?>
<section class="h-card author">

	<h2>Information</h2>

	<?php
	foreach( $info as $property => $content ) {

		if( $property == 'p-additional-name' ) {
			?>
			<abbr class="<?= $property ?>"><?= $content ?></abbr>
			<?php
		} elseif( $property == 'u-photo' ) {
			?>
			<img class="<?= $property ?>" src="<?= $content ?>">
			<?php
		} elseif( str_starts_with( $property, 'u-') ) {
			?>
			<a class="<?= $property ?>" href="<?= $content ?>"><?= $content ?></a>
			<?php
		} elseif( str_starts_with( $property, 'dt-') ) {
			?>
			<time class="<?= $property ?>"><?= $content ?></time>
			<?php
		} else { // starts with p- or any other field
			?>
			<div class="<?= $property ?>"><?= $content ?></div>
			<?php
		}

		?>
		<?php
	}
	?>
</section>
