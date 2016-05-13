<?php
/**
 * Setup the theme.
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://purpleprodigy.com
 * @license     GNU General Public License 2.0+
 */

namespace RASDS;

/**
 * Theme setup.
 *
 * Attach all of the site-wide functions to the correct hooks and filters. All
 * the functions themselves are defined below this setup function.
 *
 * @since 1.0.0
 */
add_action( 'genesis_setup', __NAMESPACE__ . '\setup', 15 );

function setup() {
	//* Add HTML5 markup structure.
	add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

	//* Add viewport meta tag for mobile browsers.
	add_theme_support( 'genesis-responsive-viewport' );

	//* Remove genesis structural wraps.
	add_theme_support(
		'genesis-structural-wraps',
		array(
			'',
		)
	);

	//* Remove the edit link
	add_filter( 'edit_post_link', '__return_empty_string' );

	//* Enable shortcodes in widgets.
	add_filter( 'widget_text', 'do_shortcode' );

	//* Remove the header right widget area
	unregister_sidebar( 'header-right' );

	//* Unregister layouts that use secondary sidebar.
		genesis_unregister_layout( 'sidebar-content' );
		genesis_unregister_layout( 'content-sidebar' );
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );

	// Register the default widget areas.
	register_widget_areas();

	//* Remove the site description
		remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

	//* Unregister sidebars.
		unregister_sidebar( 'sidebar' );
		unregister_sidebar( 'sidebar-alt' );

	//* Reposition the secondary navigation menu
		remove_action( 'genesis_after_header', 'genesis_do_subnav' );
		add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

	//* Remove genesis footer creds text
	remove_action('genesis_footer', 'genesis_do_footer');
}

add_filter( 'theme_page_templates', __NAMESPACE__ . '\remove_genesis_page_templates' );
/**
 * Remove Genesis Blog page template.
 *
 * @param array $page_templates Existing recognised page templates.
 *
 * @return array Amended recognised page templates.
 */
function remove_genesis_page_templates( $page_templates ) {
	unset( $page_templates['page_blog.php'] );

	return $page_templates;
}