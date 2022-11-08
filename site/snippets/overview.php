<header>
	<h1>Eigenheim</h1>
</header>
<main>
<?php

$posts = get_posts();

foreach( $posts as $post ) : ?>
	<section>
		<h2><?= $post['title'] ?></h2>
		<?= $post['text'] ?>
		<small><?= date( 'd.m.Y', $post['timestamp'] ) ?></small>
	</section>
<?php endforeach; ?>
</main>
