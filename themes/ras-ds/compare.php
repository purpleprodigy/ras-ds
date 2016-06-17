<?php
/*
Template Name: Compare
*/

Namespace RASDS;

//* Remove the standard entry content
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

add_action( 'genesis_before_while', __NAMESPACE__ . '\add_compare_content' );
/**
 * Add comparison content to the Compare page.
 * (/compare-other-completed-tests)
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_compare_content() {
	$page = get_post( get_option( 'page_for_posts' ) );

	if ( ! $page ) {
		return;
	}

	include( CHILD_THEME_DIR . '/lib/views/compare.php' );
}

genesis();