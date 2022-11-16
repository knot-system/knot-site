<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

?>
<header>
	<h1><a href="<?= EH_BASEURL ?>">Eigenheim</a></h1>
</header>
<main>
<?php

$tag = false;
if( ! empty($args['tag']) ) $tag = $args['tag'];

if( $tag ) echo '<p><em>only showing posts with tag <strong>'.$tag.'</strong></em></p>';

$posts = $args['posts'];

foreach( $posts as $post ) snippet( 'post', array( 'post' => $post ) );

snippet( 'author_info' );

?>
</main>
