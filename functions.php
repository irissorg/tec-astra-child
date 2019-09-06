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
define( 'CHILD_THEME_TEC_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'tec-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_TEC_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


// Add "nocookie" To WordPress oEmbeded Youtube Videos
function ev_youtube_nocookie_oembed( $html ) {
	return str_replace( 'youtube.com', 'youtube-nocookie.com', $html );
}
add_filter( 'embed_oembed_html', 'ev_youtube_nocookie_oembed' ); // WordPress
add_filter( 'video_embed_html', 'ev_youtube_nocookie_oembed' ); //Jetpack



function remove_api () {
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
}
add_action( 'after_setup_theme', 'remove_api' );





/**
 * Setup default Astra settings for a single post layout for specific post type.
 * 
 * This Removes header, Footer, Post title, Featured image, Post navigation. 
 * Sets content layout to "Fullwidth Stretched" and sidear layout to `left-siebar`
 */
function your_prefix_disale_header_footer() {
	// bail early if the current post type if not the one we want to customize.
	// 
	if ( 'events' !== get_post_type() && is_singular() ) {
		return;
	}
	// Disable Header, Above/below header, Sticky header.
	//remove_action( 'astra_header', 'astra_header_markup' );
	// Disable Footer widgets, Footer bar.
	//remove_action( 'astra_footer', 'astra_footer_markup' );
	// Set the page page layout to 'no-sidebar'.
	add_filter( 'astra_page_layout', 'your_prefix_change_page_layout' );
	// Set the content layout to 'page-builde' i.e. Fullwidth Stretched.
	//add_filter( 'astra_get_content_layout', 'your_prefix_astra_content_layout' );
	// Disable Post title.
	//add_filter( 'astra_the_title_enabled', '__return_false' );
	// Disale Featured image.
	//add_filter( 'astra_featured_image_enabled', '__return_false' );
	// Disale post navigation links.
	add_filter( 'astra_single_post_navigation_enabled', '__return_false' );
	// Disable Comments.
	// This filter is from WordPress filter to disable comments.
	add_filter( 'comments_open', '__return_false' );
}
add_action( 'wp', 'your_prefix_disale_header_footer' );
/**
 * Returns string for  no sidear layout.
 * 
 * @return 'String' String to set the page layout to 'no-sidebar'.
 */
function your_prefix_change_page_layout () {
	return 'right-sidebar';
}
/**
 * String to set Content layout to Fullwidth Stretched.
 * 
 * @return 'String' String 'page-builder' as the content layout.
 */
//function your_prefix_astra_content_layout() {
//	return 'page-builder';
//}

function tec_remove_sidebar(){
	//get page type early if not search bail dude!
	if ( !is_search() ) {
		return;
	}
	add_filter( 'astra_page_layout', 'no-sidebar' );
	
}
add_action( 'wp', 'tec_remove_sidebar' );




// add_filter( 'pt_cv_field_thumbnail_nolink', '__return_true' );
// Content Views Pro - Show thumbnail without hyperlink




