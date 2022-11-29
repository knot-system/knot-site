<?php

if( ! $eigenheim ) exit;

snippet( 'header' );

snippet( 'page_content', array( 'page' => $args['page'] ) );

snippet( 'footer' );