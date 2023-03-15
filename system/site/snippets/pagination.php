<?php

// Version: 0.1.0

if( ! $core ) exit;

$posts = $core->posts;

$tag = $args['tag'];

$show_pagination = true;

$prev_page = false;
if( $core->posts->page > 1 ) $prev_page = true;

$next_page = false;
if( $core->posts->page < $core->posts->maxPage ) {
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
?>
			<li><a href="<?= url($pre_url.'page/'.($core->posts->page-1)) ?>" rel="prev">&laquo; previous page</a></li>
<?php
			} else {
?>
			<li>&laquo; previous page</li>
<?php
			}
			
			if( $next_page ) {
?>
			<li><a href="<?= url($pre_url.'page/'.($core->posts->page+1)) ?>" rel="next">next page &raquo;</a></li>
<?php
			} else {
?>
			<li>next page &raquo;</li>
<?php
			}
			?>
		</ul>
	</nav>

<?php
}