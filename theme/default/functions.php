<?php

add_stylesheet( 'assets/fonts/fonts.css' );
add_stylesheet( 'assets/css/global.css' );

remove_metatag( 'generator' );
add_metatag( 'generator', '<meta tag="generator" content="Eigenheim v.'.$eigenheim->version().' with '.$eigenheim->theme->get('name').' v.'.$eigenheim->theme->get('version').'">' );
