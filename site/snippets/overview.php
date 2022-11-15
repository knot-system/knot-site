<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

?>
<header>
	<h1>Eigenheim</h1>
</header>
<main>
<?php

$posts = get_posts();

foreach( $posts as $post ) snippet( 'post', array( 'post' => $post ) );

snippet( 'author_info' );

?>
</main>
