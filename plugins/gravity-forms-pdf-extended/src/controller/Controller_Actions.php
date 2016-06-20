<?php

namespace GFPDF\Controller;

use GFPDF\Helper\Helper_Abstract_Controller;
use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Abstract_Model;
use GFPDF\Helper\Helper_Abstract_View;
use GFPDF\Helper\Helper_Abstract_Form;
use GFPDF\Helper\Helper_Notices;

use Psr\Log\LoggerInterface;

/**
 * Actions Controller
 *
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2016, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF.

    Gravity PDF – Copyright (C) 2016, Blue Liquid Designs

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Controller_Actions
 * Controller to trigger anything that requires a one-time user interaction
 * Examples include a configuration importer, or a promo
 *
 * @since 4.0
 */
class Controller_Actions extends Helper_Abstract_Controller implements Helper_Interface_Actions {

	/**
	 * Holds the abstracted Gravity Forms API specific to Gravity PDF
	 *
	 * @var \GFPDF\Helper\Helper_Form
	 *
	 * @since 4.0
	 */
	protected $gform;

	/**
	 * Holds our log class
	 *
	 * @var \Monolog\Logger|LoggerInterface
	 *
	 * @since 4.0
	 */
	protected $log;

	/**
	 * Holds our Helper_Notices object
	 * which we can use to queue up admin messages for the user
	 *
	 * @var \GFPDF\Helper\Helper_Notices
	 *
	 * @since 4.0
	 */
	protected $notices;

	/**
	 * Setup our class by injecting all our dependancies
	 *
	 * @param Helper_Abstract_Model|\GFPDF\Model\Model_Actions $model   Our Actions Model the controller will manage
	 * @param Helper_Abstract_View|\GFPDF\View\View_Actions    $view    Our Actions View the controller will manage
	 * @param \GFPDF\Helper\Helper_Abstract_Form               $gform   Our abstracted Gravity Forms helper functions
	 * @param \Monolog\Logger|LoggerInterface                  $log     Our logger class
	 * @param \GFPDF\Helper\Helper_Notices                     $notices Our notice class used to queue admin messages and errors
	 *
	 * @since 4.0
	 */
	public function __construct( Helper_Abstract_Model $model, Helper_Abstract_View $view, Helper_Abstract_Form $gform, LoggerInterface $log, Helper_Notices $notices ) {

		/* Assign our internal variables */
		$this->gform   = $gform;
		$this->log     = $log;
		$this->notices = $notices;

		/* Load our model and view */
		$this->model = $model;
		$this->model->setController( $this );

		$this->view = $view;
		$this->view->setController( $this );
	}

	/**
	 * Initialise our class defaults
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function init() {
		$this->add_actions();
	}

	/**
	 * Apply any actions
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function add_actions() {
		add_action( 'admin_init', array( $this, 'route' ) );
		add_action( 'admin_init', array( $this, 'route_notices' ), 20 ); /* Run later than our route check */

		/* Add AJAX endpoints */
		add_action( 'wp_ajax_multisite_v3_migration', array( $this->model, 'ajax_multisite_v3_migration' ) );
	}

	/**
	 * Holds our one-time action routes
	 *
	 * Routes should contain the following keys: action, action_text, condition, process, view, capability
	 * action: The action ID to be processed
	 * action_text: The text used in our main button
	 * condition: The function or method to call to determine if a notice should be displayed (Boolean)
	 * process: The function to handle a successful action. On success the disable_route() method should be called
	 * view: The function used to display the notice content
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_routes() {

		$routes = array(
			array(
				'action'      => 'review_plugin',
				'action_text' => __( 'Review Submitted', 'gravity-forms-pdf-extended' ),
				'condition'   => array( $this->model, 'review_condition' ),
				'process'     => array( $this->model, 'dismiss_notice' ),
				'view'        => array( $this->view, 'review_plugin' ),
				'capability'  => 'gravityforms_view_settings',
				'view_class'  => 'gfpdf-alert-mascot',
			),

			array(
				'action'      => 'migrate_v3_to_v4',
				'action_text' => __( 'Begin Migration', 'gravity-forms-pdf-extended' ),
				'condition'   => array( $this->model, 'migration_condition' ),
				'process'     => array( $this->model, 'begin_migration' ),
				'view'        => array( $this->view, 'migration' ),
				'capability'  => 'update_plugins',
			),
		);

		/* See https://gravitypdf.com/documentation/v4/gfpdf_one_time_action_routes/ for more details about this filter */
		return apply_filters( 'gfpdf_one_time_action_routes', $routes );
	}

	/**
	 * Setup our route notices, if they should be enabled
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function route_notices() {

		/* Prevent actions being displayed on our welcome pages */
		if ( ! is_admin() ||
		     ( rgget( 'page' ) == 'gfpdf-getting-started' ) || ( rgget( 'page' ) == 'gfpdf-update' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		) {
			return null;
		}

		foreach ( $this->get_routes() as $route ) {

			/* Before displaying check the user has the correct capabilities, the notice isn't already been dismissed and the route condition has been met */
			if ( $this->gform->has_capability( $route['capability'] ) &&
			     ! $this->model->is_notice_already_dismissed( $route['action'] ) &&
			     call_user_func( $route['condition'] )
			) {

				$this->log->addNotice( 'Trigger Action Notification.', array(
					'route' => $route,
				) );

				$class = ( isset( $route['view_class'] ) ) ? $route['view_class'] : '';
				$this->notices->add_notice( call_user_func( $route['view'], $route['action'], $route['action_text'] ), $class );
			}
		}
	}

	/**
	 * Run approprate events
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function route() {

		foreach ( $this->get_routes() as $route ) {

			/* Check we have a valid action and the display condition is true */
			if ( rgpost( 'gfpdf_action' ) == 'gfpdf_' . $route['action'] && call_user_func( $route['condition'] ) ) {

				/* Check user capability */
				if ( ! $this->gform->has_capability( $route['capability'] ) ) {

					$this->log->addCritical( 'Lack of User Capabilities.', array(
						'user'      => wp_get_current_user(),
						'user_meta' => get_user_meta( get_current_user_id() ),
					) );

					wp_die( __( 'You do not have permission to access this page', 'gravity-forms-pdf-extended' ) );
				}

				/* Check nonce is valid */
				if ( ! wp_verify_nonce( rgpost( 'gfpdf_action_' . $route['action'] ), 'gfpdf_action_' . $route['action'] ) ) {

					$this->log->addWarning( 'Nonce Verification Failed.' );
					$this->notices->add_error( __( 'There was a problem processing the action. Please try again.', 'gravity-forms-pdf-extended' ) );

					continue;
				}

				/* Check if the user wants to dismiss the notice, otherwise process the route */
				if ( isset( $_POST['gfpdf-dismiss-notice'] ) ) {
					$this->log->addNotice( 'Dismiss Action.', array(
						'route' => $route,
					) );

					$this->model->dismiss_notice( $route['action'] );
				} else {
					$this->log->addNotice( 'Trigger Action Process.', array(
						'route' => $route,
					) );
					
					call_user_func( $route['process'], $route['action'], $route );
				}
			}
		}
	}
}
