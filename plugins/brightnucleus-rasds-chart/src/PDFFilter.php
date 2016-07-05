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
use RGFormsModel;

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

	const GENERATE_PDF_NOTIFICATION = '570d8ba6222a7';
	const EMAIL_PDF_NOTIFICATION    = '57676111b7e6c';

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
		add_filter( 'gfpdf_pdf_filename', [ $this, 'change_filename' ], 10, 4 );
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

	/**
	 * Change the file name of the generated PDF.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $name Name to modify.
	 * @param object|array $form GF form
	 * @param array        $entry
	 * @param array        $settings
	 * @return string Modified name.
	 */
	public function change_filename( $name, $form, $entry, $settings ) {
		if ( $settings['id'] !== self::GENERATE_PDF_NOTIFICATION
		     && $settings['id'] !== self::EMAIL_PDF_NOTIFICATION
		) {
			return $name;
		}

		$entry_id      = isset( $_REQUEST['entry_id'] ) ? absint( $_REQUEST['entry_id'] ) : null;
		$comparison_id = isset( $_REQUEST['comparison_id'] ) ? absint( $_REQUEST['comparison_id'] ) : null;
		$chart_data    = new ChartData( $entry_id, $comparison_id );
		if ( $chart_data->is_comparison() ) {
			$comparison_entry = RGFormsModel::get_lead( $chart_data->get_comparison_id() );
			$name             = $this->strip_invalid_characters(
				$name . '_vs_' . $comparison_entry['id'] . '-' . $comparison_entry[1]
			);
		}
		return $name;
	}

	/**
	 * Remove any characters that are invalid in filenames (mostly on Windows
	 * systems).
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name The string / name to process
	 * @return string Filtered name.
	 */
	public function strip_invalid_characters( $name ) {
		$characters = array( '/', '\\', '"', '*', '?', '|', ':', '<', '>' );

		return str_replace( $characters, '_', $name );
	}
}
