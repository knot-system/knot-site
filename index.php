<?php

define( 'EH_ABSPATH', realpath(dirname(__FILE__)).'/' );

$basefolder = str_replace( 'index.php', '', $_SERVER['PHP_SELF']);
define( 'EH_BASEFOLDER', $basefolder );

if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
else $baseurl = 'http://';
$baseurl .= $_SERVER['HTTP_HOST'];
$baseurl .= $basefolder;
define( 'EH_BASEURL', $baseurl );

include_once( EH_ABSPATH.'system/load.php' );
