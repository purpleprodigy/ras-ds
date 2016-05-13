<?php
/**
 * Footer
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS;

add_filter( 'genesis_attr_site-footer', __NAMESPACE__ . '\change_subnav_attributes' );
/**
 * Add new class to secondary navigation.
 *
 * @since 1.0.0
 *
 * @param $attributes
 *
 * @return string
 */
function change_subnav_attributes( $attributes ) {
	$attributes['class'] .= ' footer';

	return $attributes;
}

add_filter( 'genesis_do_subnav', __NAMESPACE__ . '\add_div_around_secondary_navigation', 10, 3 );
/**
 * Add `div` around the secondary navigation.
 *
 * @since 1.0.0
 *
 * @param string $html
 * @param string $nav_html
 * @param array $args
 *
 * @return string
 */
function add_div_around_secondary_navigation( $html, $nav_html, array $args ) {
	if ( ! in_array( $args['theme_location'], array( 'secondary' ) ) ) {
		return $html;
	}

	return sprintf( '<div class="container-fluid"><div class="row"><div class="col-sm-6 text-1">%s</div><div class="col-sm-6 text-1 footer-copyright">
					<p class="mar-0 text-2">Copyright &copy; 2016 University of Sydney</p>
				</div></div></div>', $html );
}

add_filter( 'wp_nav_menu_args', __NAMESPACE__ . '\add_class_to_ul_secondary_navigation' );
/**
 * Change menu class in the secondary navigation.
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return string
 */
function add_class_to_ul_secondary_navigation( array $args ) {
	if ( ! array_key_exists( 'theme_location', $args ) || $args['theme_location'] != 'secondary' ) {
		return $args;
	}

	$args['menu_class'] .= ' list-inline mar-b-1';

	return $args;
}


