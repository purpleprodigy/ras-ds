<?php

namespace GFPDF\Model;

use GFPDF\Helper\Helper_Abstract_Model;
use GFPDF\Helper\Helper_Abstract_Options;
use GFPDF\Helper\Helper_Data;
use GFPDF\Helper\Helper_Notices;
use GFPDF\Helper\Helper_Migration;

use GPDFAPI;

/**
 * Action Model
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
 * Model_Actions
 *
 * Handles the grunt work of our one-time actions
 *
 * @since 4.0
 */
class Model_Actions extends Helper_Abstract_Model {

	/**
	 * Holds our Helper_Data object
	 * which we can autoload with any data needed
	 *
	 * @var \GFPDF\Helper\Helper_Data
	 *
	 * @since 4.0
	 */
	protected $data;

	/**
	 * Holds our Helper_Abstract_Options / Helper_Options_Fields object
	 * Makes it easy to access global PDF settings and individual form PDF settings
	 *
	 * @var \GFPDF\Helper\Helper_Options_Fields
	 *
	 * @since 4.0
	 */
	protected $options;

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
	 * @param \GFPDF\Helper\Helper_Data             $data    Our plugin data store
	 * @param \GFPDF\Helper\Helper_Abstract_Options $options Our options class which allows us to access any settings
	 * @param \GFPDF\Helper\Helper_Notices          $notices Our notice class used to queue admin messages and errors
	 *
	 * @since 4.0
	 */
	public function __construct( Helper_Data $data, Helper_Abstract_Options $options, Helper_Notices $notices ) {

		/* Assign our internal variables */
		$this->data    = $data;
		$this->options = $options;
		$this->notices = $notices;
	}

	/**
	 * Check if the current notice has already been dismissed
	 *
	 * @param  string $type The current notice ID
	 *
	 * @return boolean       True if dismissed, false otherwise
	 *
	 * @since 4.0
	 */
	public function is_notice_already_dismissed( $type ) {

		$dismissed_notices = $this->options->get_option( 'action_dismissal', array() );

		if ( isset( $dismissed_notices[ $type ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Mark the current notice as being dismissed
	 *
	 * @param  string $type The current notice ID
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function dismiss_notice( $type ) {

		$dismissed_notices          = $this->options->get_option( 'action_dismissal', array() );
		$dismissed_notices[ $type ] = $type;
		$this->options->update_option( 'action_dismissal', $dismissed_notices );
	}

	/**
	 * Check if our review notice condition has been met
	 * A review will only display if more than 100 PDFs have been generated
	 *
	 * @return boolean
	 *
	 * @since 4.0
	 */
	public function review_condition() {

		$total_pdf_count = (int) $this->options->get_option( 'pdf_count', 0 );

		if ( 100 < $total_pdf_count ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if our v3 configuration file exists
	 *
	 * @return boolean
	 *
	 * @since 4.0
	 */
	public function migration_condition() {

		/* Check standard installation */
		if ( ! is_multisite() && is_file( $this->data->template_location . 'configuration.php' ) ) {
			return true;
		}

		/* Check multisite installation */
		if ( is_multisite() && is_super_admin() ) {
			if ( is_file( $this->data->multisite_template_location . 'configuration.php' ) ) {
				return true;
			} else {
				/* Check other multisites for a config file */
				$sites = wp_get_sites();
				foreach ( $sites as $site ) {
					if ( is_file( $this->data->template_location . '/' . $site['blog_id'] . '/configuration.php' ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Process our v3 to v4 migration
	 *
	 * @return boolean
	 *
	 * @since 4.0
	 */
	public function begin_migration() {

		if ( is_multisite() ) {

			/* Verify we have a site to migrate */
			$sites = wp_get_sites();
			$found = false;

			foreach ( $sites as $site ) {
				$site_config = $this->data->template_location . '/' . $site['blog_id'] . '/';

				if ( is_file( $site_config . 'configuration.php' ) ) {
					$found = true;
					break;
				}
			}

			if ( $found ) {
				/* Remove all notices to prevent any messages showing up on the migration screen */
				remove_all_actions( 'network_admin_notices' );
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );

				/* We need a user interface so queue this right before the admin page runs */
				add_action( 'all_admin_notices', array( $this, 'handle_multisite_migration' ) );

				/* Add our migration script */
				wp_enqueue_script( 'gfpdf_js_v3_migration' );
			}

		} else if ( is_file( $this->data->template_location . 'configuration.php' ) ) {
			$this->migrate_v3( $this->data->template_location );
		}
	}

	/**
	 * Does the migration and notice clearing (if unsuccessful)
	 *
	 * @param  string $path Path to the current site's template directory
	 *
	 * @return boolean
	 *
	 * @since    4.0
	 */
	private function migrate_v3( $path ) {

		$migration = new Helper_Migration( GPDFAPI::get_form_class(), GPDFAPI::get_log_class(), GPDFAPI::get_data_class(), GPDFAPI::get_options_class(), GPDFAPI::get_misc_class(), GPDFAPI::get_notice_class() );

		if ( $migration->begin_migration() ) {

			/**
			 * Migration Successful.
			 *
			 * If there was a problem removing the configuration file we'll automatically prevent the migration message displaying again
			 */
			if ( is_file( $path . 'configuration.php' ) ) {
				$this->dismiss_notice( 'migrate_v3_to_v4' );
			}

			return true;
		}

		return false;
	}

	/**
	 * Handles the multsite migration
	 *
	 * Use AJAX query to process each multisite individually
	 *
	 * @since 4.0
	 */
	public function handle_multisite_migration() {
		$controller = $this->getController();

		$args = array(
			'multisite_ids'    => $this->get_multisite_ids_with_v3_config(),
			'current_page_url' => add_query_arg( null, null ),
			'gf_forms_url'     => admin_url( 'admin.php?page=gf_edit_forms' ),
		);
		$controller->view->begin_multisite_migration( $args );
		$controller->view->end_multisite_migration();
	}

	/**
	 * Return an array of mulitsite blog IDs which have a v3 config
	 *
	 * @return array
	 *
	 * @since  4.0
	 */
	private function get_multisite_ids_with_v3_config() {
		$sites    = wp_get_sites();
		$blog_ids = array();

		foreach ( $sites as $site ) {
			$site_config = $this->data->template_location . $site['blog_id'] . '/';

			if ( is_file( $site_config . 'configuration.php' ) ) {
				$blog_ids[] = $site['blog_id'];
			}
		}

		return $blog_ids;
	}

	/**
	 * AJAX Endpoint for migrating each multisite to our v4 config
	 *
	 * @internal param $_POST ['nonce'] a valid nonce
	 * @internal param $_POST ['blog_id'] a valid site ID
	 *
	 * @since    4.0
	 */
	public function ajax_multisite_v3_migration() {
		$log = GPDFAPI::get_log_class();

		$log->addNotice( 'Running AJAX Endpoint', array(
			'type' => 'Multisite v3 to v4 config',
			'post' => $_POST,
		) );

		/* prevent unauthorized access */
		if ( ! is_multisite() || ! is_super_admin() ) {

			$log->addCritical( 'Lack of User Capabilities.', array(
				'user'        => wp_get_current_user(),
				'user_meta'   => get_user_meta( get_current_user_id() ),
				'multisite'   => is_multisite(),
				'super_admin' => is_super_admin(),
			) );

			header( 'HTTP/1.1 401 Unauthorized' );
			wp_die( '401' );
		}

		/*
         * Validate Endpoint
         */
		$nonce   = ( isset( $_POST['nonce'] ) ) ? $_POST['nonce'] : '';
		$blog_id = ( isset( $_POST['blog_id'] ) ) ? (int) $_POST['blog_id'] : 0;

		if ( ! wp_verify_nonce( $nonce, 'gfpdf_multisite_migration' ) ) {

			$log->addWarning( 'Nonce Verification Failed.' );

			header( 'HTTP/1.1 401 Unauthorized' );
			wp_die( '401' );
		}

		/* Check if we have a config file that should be migrated */
		$path = $this->data->template_location . $blog_id . '/';

		if ( ! is_file( $path . 'configuration.php' ) ) {

			$return = array(
				'error' => sprintf( __( 'No configuration.php file found for site #%s', 'gravity-forms-pdf-extended' ), $blog_id ),
			);

			$log->addError( 'AJAX Endpoint Failed', $return );

			echo json_encode( array( 'results' => $return ) );
			wp_die();
		}


		/* Setup correct migration settings */
		switch_to_blog( $blog_id );
		$this->data->multisite_template_location = $path;

		/* Do migration */
		if ( $this->migrate_v3( $path ) ) {
			echo json_encode( array( 'results' => 'complete' ) );
			wp_die();
		} else {

			$return = array(
				'error' => sprintf( __( 'Database import problem for site #%s', 'gravity-forms-pdf-extended' ), $blog_id ),
			);

			$log->addError( 'AJAX Endpoint Failed', $return );

			echo json_encode( array( 'results' => $return ) );
			wp_die();
		}

		$log->addError( 'AJAX Endpoint Failed' );
		header( 'HTTP/1.1 500 Internal Server Error' );
		wp_die( '500' );

	}

}
