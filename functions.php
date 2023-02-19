<?php 
function load_assets() {

    wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/main.css', null, filemtime(get_stylesheet_directory() . '/assets/css/main.css') );
    wp_enqueue_script( 'main-script', get_stylesheet_directory_uri() . '/assets/js/bundle.js', array(), filemtime(get_stylesheet_directory() . '/assets/js/bundle.js'), true );
}
add_action('wp_enqueue_scripts', 'load_assets');