<?php // EIGENHEIM start


// NOTE: these defines need to happen in the root index.php, because they depend on the location of this file
define( 'EH_ABSPATH', realpath(dirname(__FILE__)).'/' );

$basefolder = str_replace( 'index.php', '', $_SERVER['PHP_SELF']);
define( 'EH_BASEFOLDER', $basefolder );

if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
else $baseurl = 'http://';
$baseurl .= $_SERVER['HTTP_HOST'];
$baseurl .= $basefolder;
define( 'EH_BASEURL', $baseurl );

include_once( EH_ABSPATH.'system/functions.php' );


if( ! file_exists(EH_ABSPATH.'site/config.php') || isset($_GET['setup']) ) {
	include_once( EH_ABSPATH.'system/setup.php');
	exit;
}


$route = get_route();

$template = $route['template'];
$args = false;
if( ! empty($route['args']) ) $args = $route['args'];

// TODO: check if template file exists before we include it and fall back to an error page if it doesn't
include_once( EH_ABSPATH.'site/'.$template.'.php' );


die(); // EIGENHEIM end