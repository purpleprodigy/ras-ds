<?php
/**
 * Display URL Param Shortcode
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Purple Prodigy
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS\Shortcode;

add_shortcode( 'display_url_param', __NAMESPACE__ . '\show_second_email' );
/**
 * Show the second email in the shortcode
 *
 * @since 1.0.0
 *
 * @param array $attributes
 *
 * @return $param
 */
function show_second_email( $attributes, $content ) {
	$defaults = array(
		'param' => '',
	);

	$attributes = shortcode_atts( $defaults, $attributes, 'display_url_param' );

	// array_key_exists($_GET['second-email'] );
	$param = $_GET['second-email'];
	return esc_html( $param );

}
