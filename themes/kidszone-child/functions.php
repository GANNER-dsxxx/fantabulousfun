<?php
add_action( 'wp_enqueue_scripts', 'kidszone_child_enqueue_styles', 100);
function kidszone_child_enqueue_styles() {
	wp_enqueue_style( 'kidszone-parent', get_theme_file_uri('/style.css') );
}

add_filter( 'wpcf7_validate_configuration', '__return_false' );