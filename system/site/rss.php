<?php

if( ! $core ) exit;

$core->doing_feed = true;

header('Content-Type: application/rss+xml; charset=utf-8');

$limit_count = get_config('feed_limit_posts');
$posts = $core->posts->limit($limit_count)->get();

?><?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">

	<channel>
		<title><?= get_config( 'site_title' ) ?></title>
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