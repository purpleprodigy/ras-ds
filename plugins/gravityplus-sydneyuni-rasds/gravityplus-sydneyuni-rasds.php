<?php
/**
 * @wordpress-plugin
 * Plugin Name: RAS-DS Gravity Forms Customizations
 * Plugin URI: https://gravityplus.pro
 * Description: Gravity Forms and GF Add-On customizations
 * Version: 1.0.0
 * Author: gravity+
 * Author URI: https://gravityplus.pro/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package   GFP_SydneyUni_RASDS
 * @version   1.0.0
 * @author    Naomi C. Bush for gravity+ <support@gravityplus.pro>
 * @license   GPL-2.0+
 * @link      https://gravityplus.pro
 * @copyright 2016 gravity+
 *
 * last updated: June 15, 2016
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GFP_RASDS_CURRENT_VERSION', '1.0.0' );
define( 'GFP_RASDS_FILE', __FILE__ );
define( 'GFP_RASDS_PATH', plugin_dir_path( __FILE__ ) );
define( 'GFP_RASDS_URL', plugin_dir_url( __FILE__ ) );
define( 'GFP_RASDS_SLUG', plugin_basename( dirname( __FILE__ ) ) );

/**
 * Class GFP_SydneyUni_RASDS
 *
 * Gravity Forms customizations
 *
 * @since 1.0.0
 *
 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
 */
class GFP_SydneyUni_RASDS {

	/**
	 * Add hooks
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 */
	public function run() {

		add_filter( 'gravityview_field_entry_link', array( $this, 'gravityview_field_entry_link' ), 10, 4 );

		add_filter( 'gform_entry_meta', array( $this, 'gform_entry_meta' ), 100, 2 );

		add_filter( 'gravityview_search_criteria', array( $this, 'gravityview_search_criteria' ), 200, 3 );

		add_filter( 'gravityview/fields/custom/content_after', array(
			$this,
			'gravityview_fields_custom_content_after'
		) );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_action( 'wp_ajax_rasds_send_pdf', array( $this, 'rasds_send_pdf' ) );

		add_action( 'gform_after_submission_4', array( $this, 'gform_after_submission_4' ) );

		add_filter( 'gravityview/single/title/out_loop', array( $this, 'gravityview_single_title_out_loop' ) );

		add_filter( 'gravityview_field_entry_value', array( $this, 'gravityview_field_entry_value' ), 10, 4 );

		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

		add_filter( 'widget_display_callback', array( $this, 'widget_display_callback' ), 10, 3 );

	}

	/**
	 * Redirect logged-in users to dashboard
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 */
	public function template_redirect() {

		global $post;

		if ( is_user_logged_in() && 'rasds' == $post->post_name ) {

			wp_redirect( get_bloginfo( 'url' ) . ' "/start-new-ras-ds/dashboard/' );

			exit;
		}

	}

	/**
	 * Hide GF User Registration Login widget for logged-out visitors
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $instance_settings
	 * @param $instance
	 * @param $args
	 *
	 * @return bool
	 */
	public function widget_display_callback( $instance_settings, $instance, $args ) {

		if ( 'gform_login_widget' == $instance->id_base && ! is_user_logged_in() ) {

			$instance_settings = false;

		}

		return $instance_settings;
	}

	/**
	 * Add JS
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 */
	public function wp_enqueue_scripts() {

		if ( ! $this->is_valid_webpage_for_assets() ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'gfp_rasds_google_jsapi', 'https://www.google.com/jsapi', array( 'jquery' ), GFP_RASDS_CURRENT_VERSION, true );

		wp_enqueue_script( 'gfp-rasds', GFP_RASDS_URL . "gfp-rasds{$suffix}.js", array(
			'jquery',
			'gfp_rasds_google_jsapi'
		), GFP_RASDS_CURRENT_VERSION, true );
	}

	/**
	 * Check if the current webpage is valid for the assets to be enqueued.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_valid_webpage_for_assets() {
		global $post;

//		if ( array_key_exists( 'template', $_GET ) && 'ras-ds-online-custom-template.php' == $_GET['template'] ) {
//			return true;
//		}

		if ( array_key_exists( 'gvid', $_GET ) && 352 == (int) $_GET['gvid'] ) {
			return true;
		}

		if ( 'compare-other-completed-tests' == $post->post_name ) {
			return true;
		}

		if ( array_key_exists( 'rasdsgventry', $_GET ) && $_GET['rasdsgventry'] ) {
			return true;
		}

//		return ( ( ! empty( $_GET['gvid'] ) && '352' == $_GET['gvid'] ) ||
//		         ! empty( $_GET['rasdsgventry'] ) ||
//		         'compare-other-completed-tests' == $post->post_name
//				);

		return false;
	}


	/**
	 * Send users to incomplete form when they click on an incomplete entry link
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $link
	 * @param $href
	 * @param $entry
	 * @param $field_settings
	 *
	 * @return string
	 */
	public function gravityview_field_entry_link( $link, $href, $entry, $field_settings ) {

		$gravityview_view = GravityView_View::getInstance();

		if ( '227' == $gravityview_view->view_id ) {

			$link_text = empty( $field_settings['entry_link_text'] ) ? __( 'View Details', 'gravityview' ) : $field_settings['entry_link_text'];

			$link_text = apply_filters( 'gravityview_entry_link', GravityView_API::replace_variables( $link_text, GFAPI::get_form( $entry['form_id'] ), $entry ) );

			$link = gravityview_get_link( $entry['resume_url'], $link_text );

		}

		return $link;

	}

	/**
	 * Add title to single entry view
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $single_title_out_loop
	 *
	 * @return bool
	 */
	public function gravityview_single_title_out_loop( $single_title_out_loop ) {

		if ( ! empty( $_GET['gvid'] ) && '352' == $_GET['gvid'] ) {

			$single_title_out_loop = true;

		}

		return $single_title_out_loop;
	}

	/**
	 *  Add filter options: progress is *not* complete filter, and Saved
	 *
	 * Note that this will not work if anyone else tries to modify the filters for these
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $entry_meta
	 * @param $form_id
	 *
	 * @return mixed
	 */
	public function gform_entry_meta( $entry_meta, $form_id ) {

		if ( array_key_exists( 'partial_entry_percent', $entry_meta ) ) {

			$entry_meta['partial_entry_percent']['filter'] = array(
				'operators' => array( 'is', 'isnot', '>' ),
				'choices'   => array(
					array( 'text' => 'Complete', 'value' => '', 'operators' => array( 'is', 'isnot' ) ),
					array( 'text' => '30%', 'value' => '30', 'operators' => array( '>' ) ),
					array( 'text' => '60%', 'value' => '60', 'operators' => array( '>' ) ),
				),
			);

		}

		if ( array_key_exists( 'date_saved', $entry_meta ) ) {

			$entry_meta['date_saved']['filter'] = array(
				'operators' => array( 'is', 'isnot', '>', '<' ),
			);

		}

		return $entry_meta;

	}

	/**
	 * Add back empty string search filter, for Progress entry meta filter column
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <support@gravityplus.pro>
	 *
	 * @param $criteria
	 * @param null $form_ids
	 * @param null $passed_view_id
	 *
	 * @return mixed
	 */
	public function gravityview_search_criteria( $criteria, $form_ids = null, $passed_view_id = null ) {

		if ( class_exists( 'GravityView_Advanced_Filtering' ) ) {

			global $gravityview_view;

			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ( defined( 'DOING_AJAX' ) && ! DOING_AJAX ) ) ) {
				return $criteria;
			}

			$view_id = ! empty( $passed_view_id ) ? $passed_view_id : GravityView_View::getInstance()->getViewId();

			if ( ! empty( $view_id ) ) {

				$view_filters = GravityView_Advanced_Filtering::get_view_filter_vars( $view_id );

				if ( ! empty( $view_filters ) && is_array( $view_filters ) ) {

					$filters_to_add_back = array();

					//add back empty search values
					foreach ( $view_filters as $k => $filter ) {

						// Don't use `empty()` because `0` is a valid value
						if ( $k !== 'mode' && ( $filter['value'] === '' ) ) {

							$filters_to_add_back[] = $filter;

						}

					}

					unset( $k, $filter );

					// add advanced filters if defined
					if ( 0 < count( $filters_to_add_back ) ) {

						if ( 0 == count( $criteria['search_criteria']['field_filters'] ) ) {

							foreach ( $view_filters as $k => $filter ) {

								if ( $k === 'mode' ) {

									$criteria['search_criteria']['field_filters']['mode'] = $k;

								}

							}

							unset( $filter );

						}

						foreach ( $filters_to_add_back as $filter ) {

							$filter = GravityView_Advanced_Filtering::parse_advanced_filters( $filter, $view_id );

							$criteria['search_criteria']['field_filters'][] = $filter;

						}

					}

				}

			}

		}

		return $criteria;
	}

	/**
	 * Turn HTML values to JSON for passing back to single entry view
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $value
	 * @param $entry
	 * @param $field_settings
	 * @param $field_data
	 *
	 * @return mixed|string|void
	 */
	public function gravityview_field_entry_value( $value, $entry, $field_settings, $field_data ) {

		$gravityview_view = GravityView_View::getInstance();

		if ( '352' == $gravityview_view->view_id && GravityView_frontend::is_single_entry() ) {

			if ( in_array( $field_settings['custom_class'], array(
				'rasds-bar-chart',
				'key',
				'rasds-results-understanding'
			) ) ) {

				$value = json_encode( $value, JSON_HEX_QUOT | JSON_HEX_TAG );

			}
		}


		return $value;
	}

	/**
	 * Get content for custom single entry view template
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $entry
	 * @param $fields
	 *
	 * @return array
	 */
	public static function get_results_page_content( $entry, $fields ) {

		$gravityview_view = GravityView_View::getInstance();

		$comparison = ! empty( $_GET['rasdscompare'] );


		if ( $comparison ) {

			$comparison_entry_id = $_GET['rasdscompare'];

			$comparison_entry = GFAPI::get_entry( $comparison_entry_id );

	//		$test1 = GVCommon::format_date( $entry['test-title:1'], array( 'format' => 'j F Y' ) );
			$test1 = GVCommon::$fields['test-title'];
			$test2 = GVCommon::format_date( $comparison_entry['date_created'], array( 'format' => 'j F Y' ) );

		}

		$default_atts = array(
			'slug'       => $gravityview_view->getTemplatePartSlug(),
			'context'    => $gravityview_view->getContext(),
			'entry'      => $gravityview_view->getCurrentEntry(),
			'form'       => $gravityview_view->getForm(),
			'hide_empty' => $gravityview_view->getAtts( 'hide_empty' ),
			'markup'     => json_encode( array(
				'id'    => "{{ field_id }}",
				'class' => "{{class}}",
				'label' => "{{label_value}}",
				'value' => "{{value}}"
			) ),
			'zone_id'    => 'single_table-columns'
		);


		$tables = $bar_chart = $key = $understanding = array();


		foreach ( $fields as $field ) {

			$final_atts = $default_atts;

			$final_atts['field'] = $field;

			if ( in_array( $field['custom_class'], array(
				'rasds-bar-chart',
				'key',
				'rasds-results-understanding'
			) ) ) {

				$final_atts['markup'] = "{{value}}";

				$field_output = json_decode( gravityview_field_output( $final_atts ), true );

			} else {

				$field_output = json_decode( gravityview_field_output( $final_atts ), true );

				if ( $comparison ) {

					$final_atts['entry'] = $comparison_entry;

					$comparison_field_output = json_decode( gravityview_field_output( $final_atts ), true );

				}

			}

			switch ( $field['custom_class'] ) {

				case 'rasds-bar-chart':

					$bar_chart = array( 'class' => $field['custom_class'], 'output' => $field_output );

					break;

				case 'key':

					$key = array( 'class' => $field['custom_class'], 'output' => $field_output );

					break;

				case 'rasds-results-section-header':

					if ( ! empty( $table ) ) {

						if ( ! empty( $row ) ) {
							$table['rows'][] = $row;
						}

						unset( $row );

						$tables[] = $table;

						unset( $table );
					}

					$table['section_header'] = array(
						'class' => $field_output['class'],
						'label' => $field_output['label']
					);

					break;

				case 'rasds-results-section-score':

					$table['section_score'] = array(
						'class' => $field_output['class'],
						'label' => $field_output['label'],
						'value' => $field_output['value']
					);

					if ( $comparison ) {

						$table['section_score']['comparison'] = $comparison_field_output['value'];

					}

					break;

				case 'rasds-results-section-percentage':

					$table['section_percentage'] = array(
						'class' => $field_output['class'],
						'label' => $field_output['label'],
						'value' => $field_output['value']
					);

					if ( $comparison ) {

						$table['section_percentage']['comparison'] = $comparison_field_output['value'];

					}

					break;

				case 'rasds-results-total-score':

					$total_score = array(
						'class' => $field_output['class'],
						'label' => $field_output['label'],
						'value' => $field_output['value']
					);

					if ( $comparison ) {

						$total_score['comparison'] = $comparison_field_output['value'];

					}

					break;

				case 'rasds-results-total-percentage':

					$total_percentage = array(
						'class' => $field_output['class'],
						'label' => $field_output['label'],
						'value' => $field_output['value']
					);

					if ( $comparison ) {

						$total_percentage['comparison'] = $comparison_field_output['value'];

					}

					break;

				case 'rasds-results-understanding':

					$understanding = array( 'class' => $field['custom_class'], 'output' => $field_output );

					break;

				default;

					if ( ! empty( $row ) && array_key_exists( 'comment', $row ) && ! empty( $row['question'] ) && array_key_exists( 'score', $row ) ) {

						$table['rows'][] = $row;

						unset( $row );

					}


					if ( 'Comment' == $field['label'] ) {

						$row['comment'] = $field_output['value'];

						if ( $comparison ) {

							$row['comparison_comment'] = $comparison_field_output['value'];

						}

					} else {

						$row['question'] = $field_output['label'];

						$row['score'] = $field_output['value'];

						if ( $comparison ) {

							$row['comparison_score'] = $comparison_field_output['value'];

						}

					}

			}

			unset( $field_output );

		}

		if ( ! empty( $row ) ) {
			$table['rows'][] = $row;
		}

		$tables[] = $table;

		unset( $table, $field );


		$content = array(
			'comparison'       => $comparison,
			'tables'           => $tables,
			'bar_chart'        => $bar_chart,
			'key'              => $key,
			'understanding'    => $understanding,
			'total_score'      => $total_score,
			'total_percentage' => $total_percentage
		);

		if ( $comparison ) {

			$content = array_merge( $content, array( 'test1' => $test1, 'test2' => $test2 ) );

		}

		return $content;
	}

	/**
	 * Render bar chart on results page
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function gravityview_fields_custom_content_after( $content ) {
		$gravityview_view = GravityView_View::getInstance();
		if ( ! $this->is_bar_chart_view( $gravityview_view ) ) {
			return $content;
		}

		$entry = $gravityview_view->getCurrentEntry();
		$data  = $this->get_formatted_data_for_bar_chart( $entry );

		return $this->get_chart_html_markup( $data, $entry );
	}

	/**
	 * Checks if this view is for a bar chart.
	 *
	 * @since 1.0.0
	 *
	 * @param GravityView_View $gravityview_view
	 *
	 * @return bool
	 */
	function is_bar_chart_view( $gravityview_view ) {
		return '352' == $gravityview_view->view_id && 'rasds-bar-chart' == $gravityview_view->getCurrentFieldSetting( 'custom_class' );
	}

	/**
	 * Fetches the bar chart data and then runs it through the formatter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry
	 * @param array $data
	 *
	 * @return array
	 */
	public function get_formatted_data_for_bar_chart( $entry, $data = array() ) {
		$data = empty( $data ) ? $this->get_data_from_view( $entry ) : $data;
		$data = $this->format_bar_chart_data( $data );

		return $data;
	}

	/**
	 * Builds the Bar Chart's HTML markup and then returns it. (c
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 * @param array $entry
	 * @param bool $return_html When true, the HTML is returned to the caller.
	 *
	 * @return string
	 */
	public function get_chart_html_markup( $data, $entry, $return_html = true ) {
		$chart_object_info = $this->get_chart_options_info( $data, $entry );

		$formatted_chart_object_info = $this->format_chart_object_info_for_js( $chart_object_info );
		
		$ajaxurl = admin_url( 'admin-ajax.php', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' );

		$view_file = 'views/bar-chart.php';

		if ( $return_html ) {

			ob_start();
			include( $view_file );

			return ob_get_clean();
		}

		include( $view_file );
	}

	/**
	 * Get chart options info.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 * @param array $entry
	 *
	 * @return array
	 */
	protected function get_chart_options_info( $data, array $entry ) {
		$second_entry_id = $this->get_second_entry_id();

		$chart_options = array(
			'vAxis'     => array(
				'format' => 'percent',
				'ticks'  => array( 25 / 100, 50 / 100, 75 / 100, 100 / 100 )
			),
			'colors'    => array( '#CE3D20', '#8EB304' ),
			'width'     => 700,
			'height'    => 500,
			'legend'    => array( 'position' => 'none' ),
			'hAxis'     => array( 'showTextEvery' => '1' ),
			'tooltip'   => array( 'trigger' => 'none' ),
			'chartArea' => array( 'backgroundColor' => array( 'stroke' => '#666', 'strokeWidth' => 1 ) ),
			'segmented' => ! empty( $second_entry_id )
		);

		if ( ! empty( $second_entry_id ) ) {

			$chart_options['legend']['position'] = 'top';

			$chart_options['bar']['groupWidth'] = '95%';
		}

		return array(
			'chart_type' => 'Bar',
			'id'         => $entry['id'],
			'data'       => $data,
			'options'    => $chart_options
		);
	}

	/**
	 * Get data from the view.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry
	 *
	 * @return array
	 */
	public function get_data_from_view( array $entry ) {

		if ( empty( $_GET['rasdscompare'] ) ) {
			return $this->get_single_data_results_from_entry( $entry );
		}

		return $this->get_dual_data_results_from_entry( $entry );
	}

	/**
	 * Get Single data results from the entry.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry
	 *
	 * @return array
	 */
	public function get_single_data_results_from_entry( array $entry ) {

		return array(
			'results'      => array(
				'Doing Things I Value'     => $entry[90] / 100,
				'Looking Forward'          => $entry[91] / 100,
				'Mastering My Illness'     => $entry[92] / 100,
				'Connecting And Belonging' => $entry[93] / 100
			),
			'second_entry' => false,
		);
	}

	/**
	 * Get dual data results from the entry.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry
	 *
	 * @return array
	 */
	public function get_dual_data_results_from_entry( array $entry ) {
		$second_entry_id = $this->get_second_entry_id();

		$second_entry = GFAPI::get_entry( $second_entry_id );

		$segment1 = GVCommon::format_date( $entry['date_created'], array( 'format' => 'j F Y' ) );

		$segment2 = GVCommon::format_date( $second_entry['date_created'], array( 'format' => 'j F Y' ) );

		$data['segments'] = array( $segment1, $segment2 );

		$data['results'] = array(
			'Doing Things I Value'     => array(
				$segment1 => $entry[90] / 100,
				$segment2 => $second_entry[90] / 100
			),
			'Looking Forward'          => array(
				$segment1 => $entry[91] / 100,
				$segment2 => $second_entry[91] / 100
			),
			'Mastering My Illness'     => array(
				$segment1 => $entry[92] / 100,
				$segment2 => $second_entry[92] / 100
			),
			'Connecting And Belonging' => array(
				$segment1 => $entry[93] / 100,
				$segment2 => $second_entry[93] / 100
			)
		);

		return $data;
	}
	
	/**
	 * Get the second entry ID, if available.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool Returns false if it does not exist.
	 */
	protected function get_second_entry_id() {
		if ( array_key_exists( 'rasdscompare', $_GET ) && $_GET['rasdscompare'] ) {
			return $_GET['rasdscompare'];
		}

		return false;
	}

	/**
	 * Format bar chart data for Google Charts rendering
	 *
	 * Taken from GFChart
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function format_bar_chart_data( $data ) {

		if ( empty( $data['segments'] ) ) {

			$bar_chart_data['cols'] = array(
				array( 'type' => 'string' ),
				array( 'type' => 'number' ),
				array( 'type' => 'string', 'role' => 'annotation' )
			);

			foreach ( $data['results'] as $label => $number ) {

				$bar_chart_data['rows'][]['c'] = array(
					array( 'v' => $label ),
					array( 'v' => $number ),
					array( 'v' => round( $number * 100 ) . '%' )
				);

			}

		} else {

			$bar_chart_data['cols'] = array(

				array( 'type' => 'string' )

			);

			for ( $i = 0; $i < count( $data['segments'] ); $i ++ ) {

				$bar_chart_data['cols'][] = array( 'type' => 'number', 'label' => $data['segments'][ $i ] );

			}

			$row = array();

			foreach ( $data['results'] as $label => $row_data ) {

				$row[] = array( 'v' => $label );

				foreach ( $data['segments'] as $segment ) {

					$row[] = array( 'v' => array_key_exists( $segment, $row_data ) ? $row_data[ $segment ] : '0' );

				}

				$bar_chart_data['rows'][]['c'] = $row;

				unset( $row );

			}

		}


		return $bar_chart_data;
	}

	/**
	 *Taken from WP_Scripts->localize and GFChart
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param array $chart_object_info
	 *
	 * @return mixed
	 */
	private function format_chart_object_info_for_js( $chart_object_info ) {

		if ( ! is_array( $chart_object_info ) ) {
			return $chart_object_info;
		}

		foreach ( $chart_object_info as $key => $value ) {

			if ( ! is_scalar( $value ) ) {
				continue;
			}

			$chart_object_info[ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
		}

		return $chart_object_info;
	}

	/**
	 * Send PDF notification when email link is clicked
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 */
	public function rasds_send_pdf() {

		$entry_id = rgpost( 'entry_id' );

		$entry = GFAPI::get_entry( $entry_id );

		$notification_id = '570d8ba6222a7';

		$email = rgpost( 'send_to' );

		$this->trigger_notification( $notification_id, $email, $entry );

		wp_send_json_success();

	}

	/**
	 * Send notification for 'Send results to a second email address' form
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param array $entry
	 */
	public function gform_after_submission_4( $entry ) {

		$email = rgar( $entry, '1' );

		$entry_id = rgar( $entry, '2' );

		$entry_to_send = GFAPI::get_entry( $entry_id );

		$notification_id = '570d8ba6222a7';

		$this->trigger_notification( $notification_id, $email, $entry_to_send );

	}

	/**
	 * Trigger notification
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @param string $notification_id
	 * @param string $email
	 * @param array $entry
	 */
	private function trigger_notification( $notification_id, $email = '', $entry ) {

		$form = GFAPI::get_form( $entry['form_id'] );

		$notification = $form['notifications'][ $notification_id ];

		if ( ! empty( $email ) ) {

			$notification['to']     = $email;
			$notification['toType'] = 'email';
		}

		GFCommon::send_notification( $notification, $form, $entry );

	}

	/**
	 * Get entries ID, label, and value for comparisons
	 *
	 * @since 1.0.0
	 *
	 * @author Naomi C. Bush for gravity+ <naomi@gravityplus.pro>
	 *
	 * @return array
	 */
	public static function get_entries_list_for_comparisons() {

		get_gravityview( '352' );

		$gview = GravityView_View::getInstance( '352' );

		$entries_list = array();

		foreach ( $gview->getEntries() as $entry ) {

			$fields = $gview->getField( 'directory_table-columns' );

			foreach ( $fields as $field ) {

				if ( 'entry_link' == $field['id'] ) {

					$entries_list[] = array(
						'id'    => $entry['id'],
						'label' => apply_filters( 'gravityview_entry_link', GravityView_API::replace_variables( $field['entry_link_text'], $gview->getForm(), $entry ) ),
						'value' => gv_entry_link( $entry )
					);


					break;

				}

			}

		}

		return $entries_list;
	}

}

$gfp_sydneyuni_rasds = new GFP_SydneyUni_RASDS();
$gfp_sydneyuni_rasds->run();