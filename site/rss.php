<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

header('Content-Type: application/rss+xml; charset=utf-8');

$posts = get_posts();

?><?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">

	<channel>
		<title>Eigenheim Feed</title>
		<link><?= EH_BASEURL ?></link>
<?php /*
		<lastBuildDate></lastBuildDate>
		<description></description>
		<language></language>
		<copyright></copyright>
		<pubDate></pubDate>
*/ ?>

<?php foreach( $posts as $post ) : ?>
		<item>
			<title><?= $post['title'] ?></title>
			<description><![CDATA[<?= $post['text'] ?>]]</description>
			<link><?= EH_BASEURL ?></link>
<?php /*
			<author></author>
			<guid></guid>
			<pubDate></pubDate>
*/ ?>
		</item>
<?php endforeach; ?>

	</channel>

</rss>