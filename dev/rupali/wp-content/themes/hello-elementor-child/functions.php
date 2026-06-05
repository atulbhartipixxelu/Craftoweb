<?php
function mytheme_child_enqueue_styles() {

    $parent_style = 'parent-style';

    wp_enqueue_style(
        $parent_style,
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );

    // SweetAlert2
    wp_enqueue_script(
        'sweetalert2', 
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
        array(), 
        null, 
        true
    );

    // Auth JS (after SweetAlert2)
    wp_enqueue_script(
        'auth-js', 
        get_stylesheet_directory_uri() . '/auth.js', 
        ['jquery', 'sweetalert2'], 
        null, 
        true
    );

    wp_localize_script('auth-js', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}

add_action('wp_enqueue_scripts', 'mytheme_child_enqueue_styles');


// Disable WordPress Speculative Loading to resolve Elementor JS conflicts
add_filter( 'wp_speculative_loading_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_output_speculation_rules_script', 0 );





