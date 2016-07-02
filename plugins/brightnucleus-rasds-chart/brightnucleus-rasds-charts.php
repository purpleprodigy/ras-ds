<?php
/**
 * Bright Nucleus RAS-DS Charts.
 *
 * Render SVG charts from Gravity Forms data.
 *
 * @package   BrightNucleus\RASDS_Charts
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      https://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 *
 * @wordpress-plugin
 * Plugin Name: Bright Nucleus RAS-DS Charts
 * Plugin URI:  https://www.brightnucleus.com/
 * Description: Render SVG charts from Gravity Forms data.
 * Version:     1.0.0
 * Author:      Alain Schlesser
 * Author URI:  https://www.brightnucleus.com/
 * Text Domain: bn-rasds-charts
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

namespace BrightNucleus\RASDS_Charts;

use BrightNucleus\Config\ConfigFactory;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Remember plugin root folder.
if ( ! defined( 'BN_RASDS_CHARTS_DIR' ) ) {
	define( 'BN_RASDS_CHARTS_DIR', plugin_dir_path( __FILE__ ) );
}

// Load Composer autoloader.
if ( file_exists( BN_RASDS_CHARTS_DIR . '/vendor/autoload.php' ) ) {
	require_once BN_RASDS_CHARTS_DIR . '/vendor/autoload.php';
}

// Fetch the configuration.
const CONFIG_PREFIX = __NAMESPACE__;
$config = ConfigFactory::create( BN_RASDS_CHARTS_DIR . '/config/defaults.php' );

// Launch the plugin.
add_action( 'init', [
	new Plugin( $config->getSubConfig( CONFIG_PREFIX ) ),
	'run',
] );
