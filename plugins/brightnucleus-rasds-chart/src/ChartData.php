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

use GFAPI;
use GFP_SydneyUni_RASDS;
use GravityView_View;

/**
 * Class ChartData.
 *
 * @since   1.0.0
 *
 * @package BrightNucleus\RASDS_Charts
 * @author  Alain Schlesser <alain.schlesser@gmail.com>
 */
class ChartData {

	/** @var array Form entries data */
	protected $entry;

	/** @var GFP_SydneyUni_RASDS Gravity Forms View interpreter */
	protected $gfv_interpreter;

	/**
	 * Instantiate a ChartData object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|null               $entry           Optional. Form entries
	 *                                                  object.
	 * @param GFP_SydneyUni_RASDS|null $gfv_interpreter Optional. GravityView
	 *                                                  interpretation object.
	 */
	public function __construct( $entry = null, $gfv_interpreter = null ) {
		global $gfp_sydneyuni_rasds;
		$this->gfv_interpreter = $gfv_interpreter ?: $gfp_sydneyuni_rasds;
		$this->entry           = $entry ?: $this->get_entry();
	}

	/**
	 * Get the form entries object.
	 *
	 * @since 1.0.0
	 *
	 * @return array Form entry.
	 */
	protected function get_entry() {
		$gravityview_view = GravityView_View::getInstance();
		$entry            = $gravityview_view->getCurrentEntry();
		if ( ! empty( $entry ) ) {
			return $entry;
		}

		if ( isset( $_GET['lid'] ) ) {
			$entry = GFAPI::get_entry( absint( $_GET['lid'] ) );
			return $entry;
		}

		return null;
	}

	/**
	 * Get the charting data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Charting data.
	 */
	public function get_data() {
		$data['raw']     = $this->is_comparison()
			? $this->gfv_interpreter->get_dual_data_results_from_entry( $this->entry )
			: $this->gfv_interpreter->get_single_data_results_from_entry( $this->entry );
		$data['values']  = $this->is_comparison()
			? $this->get_dual_values( $data['raw'] )
			: $this->get_single_values( $data['raw'] );
		$data['options'] = $this->is_comparison()
			? $this->get_dual_options( $data['raw'] )
			: $this->get_single_options( $data['raw'] );
		$data['type']    = $this->is_comparison()
			? 'GroupedBarGraph'
			: 'BarGraph';
		$data['colours'] = $this->is_comparison()
			? [ '#CE3D20', '#8EB304' ]
			: [ '#CE3D20' ];
		return $data;
	}

	/**
	 * Check whether the current view is a comparison.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the current view is a comparison.
	 */
	public function is_comparison() {
		return false === empty( $_GET['rasdscompare'] );
	}

	/**
	 * Get the values for a comparison view.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw Raw data.
	 * @return array Values to use.
	 */
	protected function get_dual_values( $raw ) {
		$values = [ ];
		foreach ( $raw['results'] as $key => $data ) {
			$values[] = [
				$this->wrap_key( $key ),
				absint( $data[ $raw['segments'][0] ] * 100 ),
				absint( $data[ $raw['segments'][1] ] * 100 ),
			];
		}
		return $values;
	}

	/**
	 * Wrap a graph key by injecting a line-feed into the string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key to wrap.
	 * @return string Wrapped key.
	 */
	protected function wrap_key( $key ) {
		$wrapped = false;
		$length  = strlen( $key );
		for ( $index = 0; $index <= $length; $index ++ ) {
			$char = substr( $key, $index, 1 );
			if ( ! $wrapped && $char === ' ' && $index >= ( $length / 2.5 ) ) {
				$key     = substr_replace( $key, "\n", $index, 1 );
				$wrapped = true;
			}
		}
		return $key;
	}

	/**
	 * Get the values for a single view.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw Raw data.
	 * @return array Values to use.
	 */
	protected function get_single_values( $raw ) {
		$values = [ ];
		foreach ( $raw['results'] as $key => $data ) {
			$values[] = [
				$this->wrap_key( $key ),
				absint( $data * 100 ),
			];
		}
		return $values;
	}

	/**
	 * Get the chart options for a comparison view.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw Raw data.
	 * @return array Options to use.
	 */
	protected function get_dual_options( $raw ) {
		return [
			'legend_columns' => 2,
			'legend_entries' => [
				$raw['segments'][0],
				$raw['segments'][1],
			],
		];
	}

	/**
	 * Get the chart options for a single view.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw Raw data.
	 * @return array Options to use.
	 */
	protected function get_single_options( $raw ) {
		return [ ];
	}

	/**
	 * Get the ID of the comparison entry.
	 *
	 * @since 1.0.0
	 *
	 * @return int ID of the comparison entry.
	 */
	public function get_comparison_id() {
		return empty( $_GET['rasdscompare'] )
			? 0
			: absint( $_GET['rasdscompare'] );
	}
}
