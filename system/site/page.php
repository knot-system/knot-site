<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

snippet( 'header' );

snippet( 'page_content', array( 'page' => $args['page'] ) );

snippet( 'footer' );