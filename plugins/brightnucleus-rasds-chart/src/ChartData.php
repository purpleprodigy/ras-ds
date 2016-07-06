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

	/** @var array Comparison form entries data */
	protected $comparison_entry;

	/** @var GFP_SydneyUni_RASDS Gravity Forms View interpreter */
	protected $gfv_interpreter;

	/**
	 * Instantiate a ChartData object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|int|null           $entry            Optional. Form entries
	 *                                                   object or ID.
	 * @param array|int|null           $comparison_entry Optional. Comparison
	 *                                                   form entries object or
	 *                                                   ID.
	 * @param GFP_SydneyUni_RASDS|null $gfv_interpreter  Optional. GravityView
	 *                                                   interpretation object.
	 */
	public function __construct( $entry = null, $comparison_entry = null, $gfv_interpreter = null ) {
		global $gfp_sydneyuni_rasds;
		$this->gfv_interpreter  = $gfv_interpreter ?: $gfp_sydneyuni_rasds;
		$this->entry            = $this->get_entry( $entry );
		$this->comparison_entry = $this->get_comparison_entry( $comparison_entry );
	}

	/**
	 * Get the form entries object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|int|null $entry Optional. Form entries object or ID.
	 *
	 * @return array Form entry.
	 */
	protected function get_entry( $entry = null ) {
		if ( is_numeric( $entry ) ) {
			$entry = GFAPI::get_entry( absint( $entry ) );
		}
		if ( is_object( $entry ) || is_array( $entry ) ) {
			return $entry;
		}

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
	 * Get the comparison form entries object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|int|null $entry Optional. Comparison form entries object or
	 *                              ID.
	 *
	 * @return array Form entry.
	 */
	protected function get_comparison_entry( $entry = null ) {
		if ( is_numeric( $entry ) && absint( $entry ) > 0 ) {
			$entry = GFAPI::get_entry( absint( $entry ) );
		}
		if ( is_object( $entry ) || is_array( $entry ) ) {
			return $entry;
		}

		$entry = null;

		if ( $this->is_comparison() ) {
			$entry = GFAPI::get_entry( $this->get_comparison_id() );
			return $entry;
		}

		return null;
	}

	/**
	 * Check whether the current view is a comparison.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the current view is a comparison.
	 */
	public function is_comparison() {
		return 0 !== $this->get_comparison_id()
		       || null !== $this->comparison_entry;
	}

	/**
	 * Get the ID of the comparison entry.
	 *
	 * @since 1.0.0
	 *
	 * @return int ID of the comparison entry.
	 */
	public function get_comparison_id() {
		$id = 0;
		if ( ! empty( $_GET['rasdscompare'] ) ) {
			$id = absint( $_GET['rasdscompare'] );
		}
		if ( ! empty( $_REQUEST['comparison_id'] ) ) {
			$id = absint( $_REQUEST['comparison_id'] );
		}
		return $id;
	}

	/**
	 * Get the charting data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Charting data.
	 */
	public function get_data() {
		$data['raw']     = $this->get_raw_data();
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
	 * Get the raw data array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of raw data.
	 */
	protected function get_raw_data() {
		return $this->is_comparison()
			? $this->get_dual_raw_data( $this->entry, $this->comparison_entry )
			: $this->get_single_raw_data( $this->entry );
	}

	/**
	 * Get the raw data for a comparison of two entries.
	 *
	 * @since 1.0.0
	 *
	 * @param object|array $entry_a First form entries object to get the raw
	 *                              data from.
	 * @param object|array $entry_b Second form entries object to get the raw
	 *                              data from.
	 * @return array Array of raw data.
	 */
	protected function get_dual_raw_data( $entry_a, $entry_b ) {
		$segment1 = $entry_a['1'];
		$segment2 = $entry_b['1'];

		$data['segments'] = [ $segment1, $segment2 ];

		$data['results'] = [
			'Doing Things I Value'     => [
				$segment1 => $entry_a[90],
				$segment2 => $entry_b[90],
			],
			'Looking Forward'          => [
				$segment1 => $entry_a[91],
				$segment2 => $entry_b[91],
			],
			'Mastering My Illness'     => [
				$segment1 => $entry_a[92],
				$segment2 => $entry_b[92],
			],
			'Connecting And Belonging' => [
				$segment1 => $entry_a[93],
				$segment2 => $entry_b[93],
			],
		];

		return $data;
	}

	/**
	 * Get the raw data for a single entry.
	 *
	 * @since 1.0.0
	 *
	 * @param object|array $entry Form entries object to get the raw data from.
	 * @return array Array of raw data.
	 */
	protected function get_single_raw_data( $entry ) {
		return [
			'results'      => [
				'Doing Things I Value'     => $entry[90],
				'Looking Forward'          => $entry[91],
				'Mastering My Illness'     => $entry[92],
				'Connecting And Belonging' => $entry[93],
			],
			'second_entry' => false,
		];
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
				absint( $data[ $raw['segments'][0] ] ),
				absint( $data[ $raw['segments'][1] ] ),
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
				absint( $data ),
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
}
