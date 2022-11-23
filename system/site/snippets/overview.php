<?php

// Version: alpha.7

if( ! defined( 'EH_ABSPATH' ) ) exit;

$tag = false;
if( ! empty($args['tag']) ) $tag = $args['tag'];

if( $tag ) echo '<p><em>only showing posts with tag <strong>'.$tag.'</strong></em></p>';

$posts = $args['posts'];
$page = -1;
$show_pagination = false;
if( isset($args['page']) ) {
	$page = $args['page'];
	if( $page > -1 ) $show_pagination = true;
}

foreach( $posts as $post ) snippet( 'post', array( 'post' => $post ) );

$prev_page = false;
if( $page > 1 ) $prev_page = true;

$next_page = true;
if( count($posts) < get_config('posts_per_page') ) {
	// hide pagination if we are on the last page. this does not work, if the last page has exactle the right number of posts, then the next page will be blank, but that's 'good enough' for now
	// TODO: make this more robust
	$next_page = false;
}

if( ! $prev_page && ! $next_page ) $show_pagination = false;

if( $show_pagination ) {
	?>
	<nav class="pagination">
		<ul>
			<?php
			if( $prev_page ) {
				echo '<li><a href="'.url('page/'.($page-1)).'" rel="prev">&laquo; previous page</a></li>';
			} else {
				echo '<li>&laquo; previous page</li>';
			}
			
			if( $next_page ) { // TODO: find out if there are more posts on the next page
				echo '<li><a href="'.url('page/'.($page+1)).'" rel="next">next page &raquo;</a></li>';
			} else {
				echo '<li>next page &raquo;</li>';
			}
			?>
		</ul>
	</nav>
	<?php
}

snippet( 'author_info' );
