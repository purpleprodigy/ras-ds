<?php
/**
 * RAS-DS is a customized child theme built on the Genesis Framework
 *
 * @package     RASDS
 * @since       1.0.0
 * @author      Rose Cox
 * @link        http://www.purpleprodigy.com
 * @licence     GNU General Public License 2.0+
 */
namespace RASDS;

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'RAS-DS' );
define( 'CHILD_THEME_URL', 'http://sydneyuni.dev' );
define( 'CHILD_THEME_VERSION', '1.0.1' );

if ( ! defined( 'CHILD_THEME_DIR' ) ) {
	define( 'CHILD_THEME_DIR', get_stylesheet_directory() );
}

// Do not remove! This is our autoloader for classes and files (composer).
require_once( __DIR__ . '/assets/vendor/autoload.php' );

//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );