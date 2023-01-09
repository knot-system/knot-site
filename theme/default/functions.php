<?php

// add theme stylesheets:
add_stylesheet( 'assets/fonts/fonts.css' );
add_stylesheet( 'assets/css/global.css' );


// preload important webfonts:
add_metatag( 'font_preload_nunito-400', '<link rel="preload" href="'.$eigenheim->theme->url.'assets/fonts/nunito-v25-latin/nunito-v25-latin-regular.woff2" as="font" type="font/woff2" crossorigin="anonymous">' );
add_metatag( 'font_preload_patua-one-400', '<link rel="preload" href="'.$eigenheim->theme->url.'assets/fonts/patua-one-v16-latin/patua-one-v16-latin-regular.woff2" as="font" type="font/woff2" crossorigin="anonymous">' );


// change the 'generator' meta-tag to include the current theme:
remove_metatag( 'generator' );
add_metatag( 'generator', '<meta tag="generator" content="Eigenheim v.'.$eigenheim->version().' with '.$eigenheim->theme->get('name').' v.'.$eigenheim->theme->get('version').'">' );
