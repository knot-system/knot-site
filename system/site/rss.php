<?php

if( ! $eigenheim ) exit;

header('Content-Type: application/rss+xml; charset=utf-8');

$posts = get_posts();
if( $posts ) $posts = $posts->posts;

?><?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">

	<channel>
		<title><?= $eigenheim->config->get( 'site_title' ) ?></title>
		<link><?= url() ?></link>
		<description></description>
<?php /*
		<lastBuildDate></lastBuildDate>
		<language></language>
		<copyright></copyright>
		<pubDate></pubDate>
*/ ?>

<?php foreach( $posts as $post ) :
	$post = $post->fields;

	if( ! empty($post['image']) ) $post['content_html'] = '<p><img src="'.$post['image'].'"></p>'.$post['content_html'];
	?>
		<item>
			<title><?= $post['title'] ?></title>
			<description><![CDATA[<?= $post['content_html'] ?>]]></description>
			<link><?= $post['permalink'] ?></link>
			<guid><?= $post['id'] ?></guid>
			<pubDate><?= date( 'r', $post['timestamp'] ) ?></pubDate>
<?php
			$author = get_author_information();
			if( ! empty( $author['display_name'] ) ) :
			?>
			<author><?= $author['display_name'] ?></author>
<?php endif; ?>
		</item>
<?php endforeach; ?>

	</channel>

</rss>