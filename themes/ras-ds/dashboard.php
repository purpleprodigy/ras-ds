<?php
/*
Template Name: Dashboard
*/

Namespace RASDS;

//* Remove the standard entry title
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
//* Remove the standard entry content
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

add_action( 'genesis_before_while', __NAMESPACE__ . '\add_dashboard_content' );
/**
 * Add in the GravityForm, GravityView ID and comparison buttons for the Dashboard.
 * (/dashboard)
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_dashboard_content() {
	$page = get_post( get_option( 'page_for_posts' ) );

	if ( ! $page ) {
		return;
	}

	$content = wp_kses_post( $page->post_content );
	$content = do_shortcode( $content );
	include( CHILD_THEME_DIR . '/lib/views/dashboard.php' );
}

genesis();