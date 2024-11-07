<?php
/**
 * Setup tripgo Child Theme's textdomain.
 *
 * Declare textdomain for this child theme.
 * Translations can be filed in the /languages/ directory.
 */
function tripgo_child_theme_setup() {
	load_child_theme_textdomain( 'tripgo-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'tripgo_child_theme_setup' );


add_action( 'wp_enqueue_scripts', 'tripgo_enqueue_styles' );
function tripgo_enqueue_styles() {
    $parenthandle = 'tripgo-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}

add_filter( 'wp_mail_smtp_core_wp_mail_function_incorrect_location_notice', '__return_false' );