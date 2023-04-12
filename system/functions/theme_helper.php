<?php


function get_post_id_from_slug( $slug ) {

	global $core;
	$posts = $core->posts->posts;
	foreach( $posts as $post_id => $post ) {

		if( ! isset($post->slug) ) continue;

		if( $post->slug == $slug ) {
			return $post_id;
		}
	}

	return false;
}



function get_navigation(){

	global $core;

	$pages = $core->pages->get();

	if( ! $pages ) return false;

	$route = $core->route;
	$current_page_id = false;
	if( $route->get('template') == 'page' && ! empty($route->get('args')['page_id']) ) {
		$current_page_id = $route->get('args')['page_id'];
	}

	$navigation = [];

	foreach( $pages as $page ) {

		if( empty($page->fields) ) continue;

		$is_current_page = false;
		if( $current_page_id && $page->id == $current_page_id ) {
			$is_current_page = true;
		}

		$navigation[] = array(
			'title' => $page->fields['title'],
			'permalink' => $page->fields['permalink'],
			'is_current_page' => $is_current_page
		);
		
	}

	if( ! count($navigation) ) return false;

	return $navigation;
}



function doing_feed(){
	// currently displaying rss or json feed
	
	global $core;

	if( empty($core->doing_feed) ) return false;

	return !! $core->doing_feed;
}


function head_html(){

	global $core;

	$body_classes = array();

	$color_scheme = $core->config->get('theme-color-scheme');
	if( $color_scheme ) $body_classes[] = 'theme-color-scheme-'.$color_scheme;

?><!DOCTYPE html>
<!--
___________.__                     .__           .__         
\_   _____/|__| ____   ____   ____ |  |__   ____ |__| _____  
 |    __)_ |  |/ ___\_/ __ \ /    \|  |  \_/ __ \|  |/     \ 
 |        \|  / /_/  >  ___/|   |  \   Y  \  ___/|  |  Y Y  \
/_______  /|__\___  / \___  >___|  /___|  /\___  >__|__|_|  /
		\/   /_____/      \/     \/     \/     \/         \/ 
-->
<html lang="en">
<head>
<?php
	$core->theme->print_metatags( 'header' );
?>


<?php
	$core->theme->print_stylesheets();
?>

<?php
	$core->theme->print_scripts();

	?>
	
</head>
<body<?= get_class_attribute($body_classes) ?>><?php

}

function foot_html(){

	global $core;

	$core->theme->print_metatags( 'footer' );
?>

<?php
	$core->theme->print_scripts( 'footer' );

?>


</body>
</html>
<?php
}
