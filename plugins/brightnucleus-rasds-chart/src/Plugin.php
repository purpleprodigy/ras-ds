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
 */

namespace BrightNucleus\RASDS_Charts;

use BrightNucleus\Config\ConfigInterface;
use BrightNucleus\Config\ConfigTrait;
use BrightNucleus\Config\Exception\FailedToProcessConfigException;
use BrightNucleus\Dependency\DependencyManager;
use BrightNucleus\Shortcode\ShortcodeManager;
use Exception;

/**
 * Class Plugin.
 *
 * @since  1.0.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class Plugin {

	use ConfigTrait;

	/**
	 * Instantiate a Plugin object.
	 *
	 * @since 1.0.0
	 *
	 * @param ConfigInterface|null $config Optional. The configuration settings
	 *                                     to use.
	 *
	 * @throws FailedToProcessConfigException If the configuration could not be
	 *                                        processed.
	 */
	public function __construct( ConfigInterface $config = null ) {
		$this->processConfig( $config ?: $this->fetchDefaultConfig() );
	}

	/**
	 * Hook up the plugin to the WordPress environment.
	 *
	 * @since 1.0.0
	 */
	public function run() {

		// This plugin depends on the class GFP_SydneyUni_RASDS from the
		// gravityplus-sydneyuni-rasds plugin.
		if ( ! class_exists( 'GFP_SydneyUni_RASDS' ) ) {
			trigger_error(
				__(
					'Class GFP_SydneyUni_RASDS from gravityplus-sydneyuni-rasds plugin not found. '
					. 'Bright Nucleus RAS-DS Charts plugin activation aborted.',
					'bn-rasds-charts'
				),
				E_USER_WARNING
			);
			return;
		}

		add_action( 'init', [ $this, 'init_shortcodes' ], 20 );
		add_action( 'init', [ $this, 'init_pdf_filter' ], 20 );
	}

	/**
	 * Initialize the shortcodes and hook them up to WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init_shortcodes() {

		try {
			// Initialize dependencies.
			$dependencies = new DependencyManager(
				$this->config->getSubConfig( 'DependencyManager' ),
				false
			);
			// Register dependencies.
			add_action( 'wp_loaded', [ $dependencies, 'register' ], 99, 1 );

			// Initialize shortcodes.
			$shortcodes = new ShortcodeManager(
				$this->config->getSubConfig( 'ShortcodeManager' ),
				$dependencies
			);
			// Register shortcodes.
			add_action( 'wp_loaded', [ $shortcodes, 'register' ], 99 );

		} catch ( Exception $exception ) {
			trigger_error(
				sprintf(
					__( 'Could not initialize Bright Nucleus RAS-DS Charts shortcodes. Reason: %1$s.',
						'bn-rasds-charts' ),
					$exception->getMessage()
				)
			);
		}
	}

	/**
	 * Initialize the PDF filter and hook them up to WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init_pdf_filter() {

		try {
			// Initialize PDF Filter.
			$pdf_filter = new PDFFilter(
				$this->config->getSubConfig( 'PDFFilter' )
			);
			// Register PDF Filter.
			add_action( 'wp_loaded', [ $pdf_filter, 'register' ], 99 );

		} catch ( Exception $exception ) {
			trigger_error(
				sprintf(
					__( 'Could not initialize Bright Nucleus RAS-DS Charts PDF filter. Reason: %1$s.',
						'bn-rasds-charts' ),
					$exception->getMessage()
				)
			);
		}
	}
}
