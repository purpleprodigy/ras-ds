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

/**
 * Class PDFFilter.
 *
 * This class is used to filter the HTML that will get sent to the mPDF PDF
 * renderer.
 *
 * @since   1.0.0
 *
 * @package BrightNucleus\RASDS_Charts
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class PDFFilter {

	use ConfigTrait;

	/**
	 * Instantiate a Plugin object.
	 *
	 * @since 1.0.0
	 *
	 * @param ConfigInterface|null $config The configuration settings to use.
	 *
	 * @throws FailedToProcessConfigException If the configuration could not be
	 *                                        processed.
	 */
	public function __construct( ConfigInterface $config = null ) {
		$this->processConfig( $config );
	}

	/**
	 * Register the PDF filtering.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		add_filter( 'gfpdf_pdf_html_output', [ $this, 'transform' ], 20, 5 );
	}

	/**
	 * Filter the HTML that gets sent to the PDF Renderer.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html HTML to filter.
	 * @param mixed  $form
	 * @param mixed  $entry
	 * @param mixed  $settings
	 * @param mixed  $helper
	 * @return string Filtered HTML.
	 */
	public function transform( $html, $form, $entry, $settings, $helper ) {

		foreach ( $this->getConfigKeys() as $transformation ) {
			$search    = $this->getConfigKey( $transformation, 'search' );
			$replace   = $this->getConfigKey( $transformation, 'replace' );
			$arguments = $this->hasConfigKey( $transformation, 'arguments' )
				? $this->getConfigKey( $transformation, 'arguments' )
				: [ ];

			$html = preg_replace_callback(
				$search,
				function ( $matches ) use ( $replace, $arguments ) {

					if ( is_callable( $arguments ) ) {
						$arguments = $arguments( $matches );
					}

					return vsprintf(
						$replace,
						$arguments
					);
				},
				$html );
		}

		return $html;
	}
}
