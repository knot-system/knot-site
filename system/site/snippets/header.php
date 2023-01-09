<?php

// Version: alpha.15

if( ! $eigenheim ) exit;

head_html();

?>

<header>
	<h1><a href="<?= url() ?>"><?= $eigenheim->config->get('site_title') ?></a></h1>
	<?php
	$navigation = get_navigation();
	if( $navigation ) :
	?><nav>
		<ul>
		<?php
		foreach( $navigation as $page ) :
			$classes = array();
			if( $page['is_current_page'] ) $classes[] = 'current-page';
			?>	<li<?= get_class_attribute($classes) ?>><a href="<?= $page['permalink'] ?>"><?= $page['title'] ?></a></li>
		<?php
		endforeach;
		?></ul>
	</nav><?php
	endif;
	?>

</header>

<main>
