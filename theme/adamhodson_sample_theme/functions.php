<?php
/**
 * Theme Functions
 *
 * This file contains functions that set up the theme and add any extra
 * or custom functionality.
 *
 * @package WordPress
 * @version 1.0
 */


/**
 * Maximum width allowed for any content
 *
 * @since 1.0
 */
if ( ! isset( $content_width ) ) {
    $content_width = 660;
}

if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page();
    
}

if ( ! function_exists( 'theme_setup' ) ):
/**
 * bm Theme Setup
 *
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 *
 * @since 1.0
 */

require_once('lib/functions.php');

function theme_setup() {

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on twentyfifteen, use a find and replace
     * to change 'twentyfifteen' to the name of your theme in all the template files
     */
    load_theme_textdomain( 'bm', get_template_directory() . '/languages' );

    // Add RSS feed links to <head> for posts and comments.
    add_theme_support( 'automatic-feed-links' );

    /*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
    add_theme_support( 'title-tag' );

    /*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 825, 510, true );
    add_image_size( 'featured-post', 550, 365 );
    add_image_size( 'post-main-image', 1080, 700 );
    add_image_size( 'dashboard-thumb', 250, 250, true ); // Hard Crop Mode
    add_image_size( 'hero', 1880, 999999999 ); // Hard Crop Mode
    add_image_size( 'mid', 960, 999999999 ); 
    add_image_size( 'big_square', 1024, 1024 ); 
    add_image_size( 'small_square', 720, 720 ); 

    // This theme uses wp_nav_menu() in two locations.
    register_nav_menus( array(
        'primary-nav' => __( 'Primary Navigation', 'bm' ),
        'top-links'  => __( 'Top Bar Links', 'bm' ),
        'footer-links' => __( 'Footer Links', 'bm'),
    ) );

    /*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );

    /*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
    ) );

}
endif; // pn_setup
add_action( 'after_setup_theme', 'theme_setup' );

/**
 * Load required stylesheets & scripts
 * @since 1.0
 */
function pn_enqueue_style() {      
        wp_enqueue_style('theme-styles', get_template_directory_uri() . '/assets/css/enviro-styles.css', array(), null, false);            
        wp_enqueue_style('main-style', get_template_directory_uri() . '/style.css');
    
}

function pn_enqueue_script(){
    
        // wp_enqueue_script('jquery-min', get_template_directory_uri() . '/assets/vendors/jquery/jquery.1.11.3.min.js', array(), null, true);
        
        wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/dist/js/enviro-js.dist.js', '', '1.0.0', true);

        $php_array = array(
            'upload_url' => admin_url('async-upload.php'),
            'admin_ajax' => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('media-form'),
            'base_url' => get_home_url()
        );

        wp_localize_script('main-js', 'php_array', $php_array);

}

add_action( 'wp_enqueue_scripts', 'pn_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'pn_enqueue_script' );


/*
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * This code allows the theme to work without errors if the Options Framework plugin has been disabled.
 */

if ( !function_exists( 'of_get_option' ) ) {
    function of_get_option($name, $default = false) {

        $optionsframework_settings = get_option('optionsframework');

        // Gets the unique option id
        $option_name = $optionsframework_settings['id'];

        if ( get_option($option_name) ) {
            $options = get_option($option_name);
        }

        if ( isset($options[$name]) ) {
            return $options[$name];
        } else {
            return $default;
        }
    }
}

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

show_admin_bar( false );
