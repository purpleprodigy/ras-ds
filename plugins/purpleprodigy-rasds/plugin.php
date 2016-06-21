<?php
/**
 * RAS-DS Online
 *
 * @package     RASDS
 * @author      Purple Prodigy
 * @copyright   2016 Purple Prodigy
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: RAS-DS Online website setup
 * Plugin URI:  http://purpleprodigy.com/
 * Description: RAS-DS Online plugin with setup files for the website. This includes shortcodes and helper files.
 * Version:     1.0.0
 * Author:      Purple Prodigy
 * Author URI:  http://purpleprodigy.com/
 * Text Domain: ras-ds-online
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
namespace RASDS;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

require_once( __DIR__ . '/assets/vendor/autoload.php' );

add_action( 'init', __NAMESPACE__ . '\launch' );
function launch() {

}