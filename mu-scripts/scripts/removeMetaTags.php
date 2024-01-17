<?php
/**
 * Removes software version from header
 * remove the unwanted <meta> links
 */
add_action( 'init', function () {
    global $sitepress;

    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'woo_version');
    remove_action('wp_head', 'meta_generator_tag');
    remove_action( 'wp_head', array( $sitepress, 'meta_generator_tag' ) );

    //Desactivar soporte y estilos de Emojis
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    // Slider revolution
    add_filter('revslider_meta_generator', '__return_empty_string');
});
