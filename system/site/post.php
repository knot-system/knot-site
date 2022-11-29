<?php

if( ! $eigenheim ) exit;

snippet( 'header' );

snippet( 'single', array( 'post' => $args['post'] ) );

snippet( 'footer' );