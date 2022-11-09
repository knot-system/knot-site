<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

header('Content-Type: application/rss+xml; charset=utf-8');

$posts = get_posts();

?><?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">

	<channel>
		<title>Eigenheim Feed</title>
		<link><?= EH_BASEURL ?></link>
		<description></description>
<?php /*
		<lastBuildDate></lastBuildDate>
		<language></language>
		<copyright></copyright>
		<pubDate></pubDate>
*/ ?>

<?php foreach( $posts as $post ) : ?>
		<item>
			<title><?= $post['title'] ?></title>
			<description><![CDATA[<?= $post['text'] ?>]]></description>
			<link><?= EH_BASEURL ?></link>
			<pubDate><?= date( 'r', $post['timestamp'] ) ?></pubDate>
<?php /*
			<author></author>
			<guid></guid>
*/ ?>
		</item>
<?php endforeach; ?>

	</channel>

</rss>