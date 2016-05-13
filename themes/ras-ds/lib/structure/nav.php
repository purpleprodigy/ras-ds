<?php
/**
 * Navigation
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS;

add_filter( 'genesis_attr_nav-primary', __NAMESPACE__ . '\change_nav_attributes' );
/**
 * Change the class for the primary navigation.
 *
 * @since 1.0.0
 *
 * @return $attributes
 */
function change_nav_attributes( $attributes ) {
    $attributes['class'] = 'nav';

	return $attributes;
}

add_filter( 'genesis_do_nav' , __NAMESPACE__ . '\add_div_around_navigation', 10, 3 );
/**
 * Add `div` around the navigation.
 *
 * @since 1.0.0
 *
 * @param string $html
 * @param string $nav_html
 * @param array $args
 *
 * @return string
 */
function add_div_around_navigation( $html, $nav_html, array $args ) {
	if ( ! in_array( $args['theme_location'], array( 'primary' ) ) ) {
		return $html;
	}

	return sprintf( '<div class="row-nav"><div class="container-fluid">%s</div></div><div id="main">', $html );
}
