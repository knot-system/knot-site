<?php

// Version: alpha.11

if( ! $eigenheim ) exit;

$posts = $eigenheim->posts;

$tag = $args['tag'];

$show_pagination = true;

$prev_page = false;
if( $eigenheim->posts->page > 1 ) $prev_page = true;

$next_page = false;
if( $eigenheim->posts->page < $eigenheim->posts->maxPage ) {
	$next_page = true;
}

if( ! $prev_page && ! $next_page ) $show_pagination = false;

if( $show_pagination ) {

	$pre_url = '';
	if( ! empty($tag) ) $pre_url = 'tag/'.$tag.'/';

	?>
	<nav class="pagination">
		<ul>
			<?php
			if( $prev_page ) {
				echo '<li><a href="'.url($pre_url.'page/'.($eigenheim->posts->page-1)).'" rel="prev">&laquo; previous page</a></li>';
			} else {
				echo '<li>&laquo; previous page</li>';
			}
			
			if( $next_page ) {
				echo '<li><a href="'.url($pre_url.'page/'.($eigenheim->posts->page+1)).'" rel="next">next page &raquo;</a></li>';
			} else {
				echo '<li>next page &raquo;</li>';
			}
			?>
		</ul>
	</nav>
	<?php
}