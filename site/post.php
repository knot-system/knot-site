<?php

if( ! defined( 'EH_ABSPATH' ) ) exit;

snippet( 'header' );

snippet( 'single', array( 'post' => $post ) );

snippet( 'footer' );