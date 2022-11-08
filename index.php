<?php

define( 'EH_ABSPATH', realpath(dirname(__FILE__)).'/' );

if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) $baseurl = 'https://';
else $baseurl = 'http://';
$baseurl .= $_SERVER['HTTP_HOST'];
$baseurl .= $_SERVER['REQUEST_URI'];

define( 'EH_BASEURL', $baseurl );

include_once( EH_ABSPATH.'system/load.php' );
