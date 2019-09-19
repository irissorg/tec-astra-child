<?php
/**
 * TEC Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package TEC
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define('CHILD_THEME_TEC_VERSION', '1.0.0');

/**
 * Enqueue styles & javascript
 */
function child_enqueue_shiz()
{
    wp_enqueue_style('tec-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_TEC_VERSION, 'all');
    wp_enqueue_script('tec-theme-js', get_stylesheet_directory_uri() . '/tec-focus.js', array(), CHILD_THEME_TEC_VERSION, 'all');
}
add_action('wp_enqueue_scripts', 'child_enqueue_shiz', 15);


//Add Astra Hooks - Docs
add_action('astra_primary_content_bottom', 'add_tec_docs');
add_action('astra_primary_content_bottom', 'add_tec_share');
function add_tec_docs(){
    if (is_singular(array( 'events', 'page', 'post' )) and !is_front_page() and have_rows('files') ) {
        $context = Timber::get_context();
        $post = new TimberPost();
        $context['post'] = $post;
        Timber::render( 'documents.twig', $context );
    }
}

function add_tec_share(){
    if (is_singular(array( 'events', 'page', 'post' )) and !is_front_page()) {
        // Output Twitter Share button - via plugin
        echo do_shortcode('[scriptless]');
    }
}




//Astra Hook - Events
add_action('astra_sidebars_before', 'add_tec_events');
function add_tec_events(){
    if (is_singular(array( 'events' )) and !is_front_page()) {
        $context = Timber::get_context();
        $post = new TimberPost();
        $context['post'] = $post;

            // Do we span months?
            $context['spanmonths'] = false;
            if (get_field('when_end', false, false)){
                    $sdate = new DateTime(get_field('when', false, false));
                    $edate = new DateTime(get_field('when_end', false, false));
                if ($edate->format('F') !=  $sdate->format('F')){
                        $context['spanmonths'] = true;
                }
            }

        Timber::render( 'events.twig', $context );
    }
}


// Shortcode to render edit button where we want it
add_shortcode('tec_edit_button', function () {
    $out = edit_post_link('Edit This', '', '', '', 'tec-edit-button');
    return $out;
});


// Shortcode to calculate a date range
add_shortcode('tec_event_range', function () {
    if (get_field('when_end')) :
            $startdate = new DateTime(get_field('when', false, false));
            $endate = new DateTime(get_field('when_end', false, false));
                    
        if ($startdate->format('F') !=  $endate->format('F')) :
            $out = $startdate->format('j M Y') . ' - ' . $endate->format('j M Y');
        endif;
                    
        if ($startdate->format('F') ==  $endate->format('F')) :
            $out = $startdate->format('j') . ' - ' . $endate->format('j F Y');
        endif;
        elseif (!get_field('when_end')) :
                    $out = get_field('when');
    endif;
                    return $out;
});


//Set default image for event content types
add_filter('acf/prepare_field/name=event-image', 'acf_use_default_image');

function acf_use_default_image($field)
{
    if ($field['value'] === null) {
        $field['value'] = 354; // attachment id
    }
    return $field;
}


// Add "nocookie" To WordPress oEmbeded Youtube Videos
function ev_youtube_nocookie_oembed($html)
{
    return str_replace('youtube.com', 'youtube-nocookie.com', $html);
}
add_filter('embed_oembed_html', 'ev_youtube_nocookie_oembed'); // WordPress
add_filter('video_embed_html', 'ev_youtube_nocookie_oembed'); //Jetpack



function remove_api()
{
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
}
add_action('after_setup_theme', 'remove_api');





/**
 * Setup default Astra settings for a single post layout for specific post type.
 *
 * This Removes header, Footer, Post title, Featured image, Post navigation.
 * Sets content layout to "Fullwidth Stretched" and sidear layout to `left-siebar`
 */
function your_prefix_disale_header_footer()
{
    // bail early if the current post type if not the one we want to customize.
    //
    if ('events' !== get_post_type() && is_singular()) {
        return;
    }
    // Disable Header, Above/below header, Sticky header.
    //remove_action( 'astra_header', 'astra_header_markup' );
    // Disable Footer widgets, Footer bar.
    //remove_action( 'astra_footer', 'astra_footer_markup' );
    // Set the page page layout to 'no-sidebar'.
    add_filter('astra_page_layout', 'your_prefix_change_page_layout');
    // Set the content layout to 'page-builde' i.e. Fullwidth Stretched.
    //add_filter( 'astra_get_content_layout', 'your_prefix_astra_content_layout' );
    // Disable Post title.
    //add_filter( 'astra_the_title_enabled', '__return_false' );
    // Disale Featured image.
    //add_filter( 'astra_featured_image_enabled', '__return_false' );
    // Disale post navigation links.
    add_filter('astra_single_post_navigation_enabled', '__return_false');
    // Disable Comments.
    // This filter is from WordPress filter to disable comments.
    add_filter('comments_open', '__return_false');
}
add_action('wp', 'your_prefix_disale_header_footer');
/**
 * Returns string for  no sidear layout.
 *
 * @return 'String' String to set the page layout to 'no-sidebar'.
 */
function your_prefix_change_page_layout()
{
    return 'right-sidebar';
}
/**
 * String to set Content layout to Fullwidth Stretched.
 *
 * @return 'String' String 'page-builder' as the content layout.
 */
//function your_prefix_astra_content_layout() {
//  return 'page-builder';
//}

function tec_remove_sidebar()
{
    //get page type early if not search bail dude!
    if (!is_search()) {
        return;
    }
    add_filter('astra_page_layout', 'no-sidebar');
}
add_action('wp', 'tec_remove_sidebar');
