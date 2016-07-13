<?php

namespace GFPDF;

use GFPDF\Controller;
use GFPDF\Model;
use GFPDF\View;
use GFPDF\Helper;

use GFPDF_Core;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\LogglyFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\LogglyHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\WebProcessor;

use GFFormsModel;
use GFLogging;

/**
 * Bootstrap / Router Class
 * The bootstrap is loaded on WordPress 'plugins_loaded' functionality
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

/*
 * Load dependancies
 */
require_once( PDF_PLUGIN_DIR . 'src/autoload.php' );

/**
 * @since 4.0
 */
class Router implements Helper\Helper_Interface_Actions, Helper\Helper_Interface_Filters {

	/**
	 * Holds our log class
	 *
	 * @var \Monolog\Logger
	 *
	 * @since 4.0
	 */
	public $log;

	/**
	 * Holds the abstracted Gravity Forms API specific to Gravity PDF
	 *
	 * @var \GFPDF\Helper\Helper_Form
	 *
	 * @since 4.0
	 */
	public $gform;

	/**
	 * Holds our Helper_Notices object
	 * which we can use to queue up admin messages for the user
	 *
	 * @var \GFPDF\Helper\Helper_Notices
	 *
	 * @since 4.0
	 */
	public $notices;

	/**
	 * Holds our Helper_Data object
	 * which we can autoload with any data needed
	 *
	 * @var \GFPDF\Helper\Helper_Data
	 *
	 * @since 4.0
	 */
	public $data;

	/**
	 * Holds our Helper_Abstract_Options / Helper_Options_Fields object
	 * Makes it easy to access global PDF settings and individual form PDF settings
	 *
	 * @var \GFPDF\Helper\Helper_Options_Fields
	 *
	 * @since 4.0
	 */
	public $options;

	/**
	 * Holds our Helper_Misc object
	 * Makes it easy to access common methods throughout the plugin
	 *
	 * @var \GFPDF\Helper\Helper_Misc
	 *
	 * @since 4.0
	 */
	public $misc;

	/**
	 * Makes our MVC classes sudo-singletons by allowing easy access to the original objects
	 * through `$singleton->get_class();`
	 *
	 * @var \GFPDF\Helper\Helper_Singleton
	 *
	 * @since 4.0
	 */
	public $singleton;

	/**
	 * Add user depreciation notice for any methods not included in current object
	 *
	 * @param string $name      The function name to be called
	 * @param array  $arguments An enumerated array containing the parameters passed to the $name'ed method
	 *
	 * @since 4.0
	 */
	public function __call( $name, $arguments ) {
		trigger_error( sprintf( __( '"%s" has been depreciated as of Gravity PDF 4.0', 'gravity-forms-pdf-extended' ), $name ), E_USER_DEPRECATED );
	}

	/**
	 * Add user depreciation notice for any methods not included in current object
	 *
	 * @param string $name      The function name to be called
	 * @param array  $arguments An enumerated array containing the parameters passed to the $name'ed method
	 *
	 * @since  4.0
	 */
	public static function __callStatic( $name, $arguments ) {
		trigger_error( sprintf( __( '"%s" has been depreciated as of Gravity PDF 4.0', 'gravity-forms-pdf-extended' ), $name ), E_USER_DEPRECATED );
	}

	/**
	 * Fired on the `after_setup_theme` action to initialise our plugin
	 *
	 * We do this on this hook instead of plugins_loaded so that users can tap into all our actions and filters
	 * directly from their theme (usually the functions.php file).
	 *
	 * @since 4.0
	 */
	public static function initialise_plugin() {

		global $gfpdf;

		/* Initialise our Router class */
		$gfpdf = new Router();
		$gfpdf->init();

		/* Add backwards compatibility support */
		$depreciated = new GFPDF_Core();
		$depreciated->setup_constants();
		$depreciated->setup_depreciated_paths();
	}

	/**
	 * Setup our plugin functionality
	 * Note: This method runs during the `after_setup_theme` action
	 *
	 * @since 4.0
	 */
	public function init() {

		/* Set up our logger is not running via CLI (unit testing) */
		$this->setup_logger();

		/* Set up our form object */
		$this->gform = new Helper\Helper_Form();

		/* Set up our data access layer */
		$this->data = new Helper\Helper_Data();
		$this->data->init();

		/* Set up our misc object */
		$this->misc = new Helper\Helper_Misc( $this->log, $this->gform, $this->data );

		/* Set up our notices */
		$this->notices = new Helper\Helper_Notices();
		$this->notices->init();

		/* Set up our options object - this is initialised on admin_init but other classes need to access its methods before this */
		$this->options = new Helper\Helper_Options_Fields( $this->log, $this->gform, $this->data, $this->misc, $this->notices );

		/* Setup our Singleton object */
		$this->singleton = new Helper\Helper_Singleton();

		/* Load modules */
		$this->installer();
		$this->welcome_screen();
		$this->gf_settings();
		$this->gf_form_settings();
		$this->pdf();
		$this->shortcodes();
		$this->actions();

		/* Add localisation support */
		$this->add_localization_support();

		/**
		 * Run generic actions and filters needed to get the plugin functional
		 * The controllers will set more specific actions / filters as needed
		 */
		$this->add_actions();
		$this->add_filters();

		/*
		 * Trigger action to signify Gravity PDF is now loaded
		 *
		 * See https://gravitypdf.com/documentation/v4/gfpdf_fully_loaded/ for more details about this action
		 */
		do_action( 'gfpdf_fully_loaded', $this );

	}

	/**
	 * Add required plugin actions
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function add_actions() {

		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'init', array( $this, 'load_assets' ), 15 );

		/* Cache our Gravity PDF Settings and register our settings fields with the Options API */
		add_action( 'init', array( $this, 'init_settings_api' ), 1 );
		add_action( 'admin_init', array( $this, 'setup_settings_fields' ), 1 );
	}

	/**
	 * Add required plugin filters
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function add_filters() {

		/* Automatically handle GF noconflict mode */
		add_filter( 'gform_noconflict_scripts', array( $this, 'auto_noconflict_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( $this, 'auto_noconflict_styles' ) );

		/* Enable Gravity Forms Logging */
		add_filter( 'gform_logging_supported', array( $this, 'add_gf_logger' ) );

		/* Add quick links on the plugins page */
		add_filter( 'plugin_action_links_' . PDF_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		/* Add class when on Gravity PDF pages */
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}


	/**
	 * Setup WordPress localization support
	 *
	 * @since 4.0
	 */
	private function add_localization_support() {
		load_plugin_textdomain( 'gravity-forms-pdf-extended', false, dirname( plugin_basename( __FILE__ ) ) . '/assets/languages/' );
	}

	/**
	 * Initialise our logging class (we're using Monolog instead of Gravity Form's KLogger)
	 * and set up appropriate handlers based on the logger settings
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function setup_logger() {

		/* Initialise our logger */
		$this->log = new Logger( 'gravitypdf' );

		/* Setup our Gravity Forms local file logger, if enabled */
		$this->setup_gravityforms_logging();

		/* Check if we have a handler pushed and add our Introspection and Memory Peak usage processors */
		if ( sizeof( $this->log->getHandlers() ) > 0 && substr( php_sapi_name(), 0, 3 ) !== 'cli' ) {
			$this->log->pushProcessor( new IntrospectionProcessor );
			$this->log->pushProcessor( new MemoryPeakUsageProcessor );
		} else {
			/* Disable logging if using CLI, or if Gravity Forms logging isn't enabled */
			$this->log->pushHandler( new NullHandler( Logger::INFO ) ); /* throw logs away */
		}
	}

	/**
	 * Setup Gravity Forms logging, if currently enabled by the user
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	private function setup_gravityforms_logging() {

		/* Check if Gravity Forms logging is enabled and push stream logging */
		if ( class_exists( 'GFLogging' ) ) {

			/*
			 * Get the current plugin logger settings and check if it's enabled
			 * The new version of the logger uses the add-on storage method, while the old one stores it in gf_logging_settings
			 * so we'll test which settings we should use and get the appropriate log level
			 */
			if ( ! defined( 'GF_LOGGING_VERSION' ) || version_compare( GF_LOGGING_VERSION, '1.1', '<' ) ) {
				$settings     = get_option( 'gf_logging_settings' );
				$log_level    = (int) rgar( $settings, 'gravity-pdf' );
				$log_filename = GFFormsModel::get_upload_root() . 'logs/gravity-pdf.txt';
			} else {
				$gf_logger          = GFLogging::get_instance();
				$gf_logger_settings = $gf_logger->get_plugin_settings();
				$log_level          = ( isset( $gf_logger_settings['gravity-pdf']['log_level'] ) ) ? (int) $gf_logger_settings['gravity-pdf']['log_level'] : 0;
				$log_filename       = $gf_logger::get_log_file_name( 'gravity-pdf' );
			}

			/* Enable logging if not equivalent to empty (0) and not level 6 (which is apprently off in GF world) */
			if ( ! empty( $log_level ) && $log_level !== 6 ) {

				/* Convert Gravity Forms log levels to the appropriate Monolog level */
				$monolog_level = ( $log_level === 4 ) ? Logger::ERROR : Logger::INFO;

				/* Setup our stream and change the format to more-suit Gravity Forms */
				$formatter = new LineFormatter( "%datetime% - %level_name% --> %message% %context% %extra%<br>\n" );
				$stream    = new StreamHandler( $log_filename, $monolog_level );
				$stream->setFormatter( $formatter );

				/* Add our log file stream */
				$this->log->pushHandler( $stream );
			}
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param    mixed $links Plugin Action links
	 *
	 * @return    array
	 *
	 * @since 4.0
	 */
	public function plugin_action_links( $links ) {

		$action_links = array(
			'settings' => '<a href="' . esc_url( $this->data->settings_url ) . '" title="' . esc_attr( __( 'View Gravity PDF Settings', 'gravity-forms-pdf-extended' ) ) . '">' . __( 'Settings', 'gravity-forms-pdf-extended' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param    mixed $links Plugin Row Meta
	 * @param    mixed $file  Plugin Base file
	 *
	 * @return    array
	 *
	 * @since  4.0
	 */
	public function plugin_row_meta( $links, $file ) {

		if ( $file == PDF_PLUGIN_BASENAME ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( 'https://gravitypdf.com/documentation/v4/five-minute-install/' ) . '" title="' . esc_attr( __( 'View Gravity PDF Documentation', 'gravity-forms-pdf-extended' ) ) . '">' . __( 'Docs', 'gravity-forms-pdf-extended' ) . '</a>',
				'support' => '<a href="' . esc_url( $this->data->settings_url . '&tab=help' ) . '" title="' . esc_attr( __( 'Get Help and Support', 'gravity-forms-pdf-extended' ) ) . '">' . __( 'Support', 'gravity-forms-pdf-extended' ) . '</a>',
				'shop'    => '<a href="' . esc_url( 'https://gravitypdf.com/shop/' ) . '" title="' . esc_attr( __( 'View Gravity PDF Theme Shop', 'gravity-forms-pdf-extended' ) ) . '">' . __( 'Theme Shop', 'gravity-forms-pdf-extended' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	/**
	 * If on a Gravity Form page add a new class
	 *
	 * @param array $classes
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	public function add_body_class( $classes ) {

		if ( $this->misc->is_gfpdf_page() ) {
			$classes .= ' gfpdf-page';
		}

		return $classes;
	}

	/**
	 * Register all css and js which can be enqueued when needed
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function register_assets() {
		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register requrired CSS
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	private function register_styles() {
		$version = PDF_EXTENDED_VERSION;
		$suffix  = '.min';
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			$suffix = '';
		}

		wp_register_style( 'gfpdf_css_styles', PDF_PLUGIN_URL . 'src/assets/css/gfpdf-styles' . $suffix . '.css', array( 'wp-color-picker' ), $version );
		wp_register_style( 'gfpdf_css_admin_styles', PDF_PLUGIN_URL . 'src/assets/css/gfpdf-admin-styles' . $suffix . '.css', array(), $version );
		wp_register_style( 'gfpdf_css_chosen_style', PDF_PLUGIN_URL . 'bower_components/chosen/chosen.min.css', array( 'wp-jquery-ui-dialog' ), $version );
	}

	/**
	 * Register requrired JS
	 *
	 * @since 4.0
	 *
	 * @return void
	 *
	 */
	private function register_scripts() {

		$version = PDF_EXTENDED_VERSION;
		$suffix  = '.min';
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			$suffix = '';
		}

		wp_register_script( 'gfpdf_js_settings', PDF_PLUGIN_URL . 'src/assets/js/gfpdf-settings' . $suffix . '.js', array(
			'wpdialogs',
			'jquery-ui-tooltip',
			'gform_forms',
			'gform_form_admin',
			'jquery-color',
			'wp-color-picker',
		), $version );
		wp_register_script( 'gfpdf_js_backbone', PDF_PLUGIN_URL . 'src/assets/js/gfpdf-backbone' . $suffix . '.js', array(
			'gfpdf_js_settings',
			'backbone',
			'underscore',
			'gfpdf_js_backbone_model_binder',
			'wpdialogs',
		), $version );
		wp_register_script( 'gfpdf_js_chosen', PDF_PLUGIN_URL . 'bower_components/chosen/chosen.jquery.min.js', array( 'jquery' ), $version );
		wp_register_script( 'gfpdf_js_backbone_model_binder', PDF_PLUGIN_URL . 'bower_components/backbone.modelbinder/Backbone.ModelBinder.min.js', array(
			'backbone',
			'underscore',
		), $version );
		wp_register_script( 'gfpdf_js_entries', PDF_PLUGIN_URL . 'src/assets/js/gfpdf-entries' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'gfpdf_js_v3_migration', PDF_PLUGIN_URL . 'src/assets/js/gfpdf-migration' . $suffix . '.js', array( 'gfpdf_js_settings' ), $version );

		/*
        * Localise admin script
        */
		wp_localize_script( 'gfpdf_js_settings', 'GFPDF', $this->data->get_localised_script_data( $this->options, $this->gform ) );
	}


	/**
	 * Load any assets that are needed
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function load_assets() {

		if ( $this->misc->is_gfpdf_page() ) {
			/* load styles */
			wp_enqueue_style( 'gfpdf_css_styles' );
			wp_enqueue_style( 'gfpdf_css_chosen_style' );

			/* load scripts */
			wp_enqueue_script( 'gfpdf_js_settings' );
			wp_enqueue_script( 'gfpdf_js_chosen' );

			/* add media uploader */
			wp_enqueue_media();
		}

		if ( $this->misc->is_gfpdf_settings_tab( 'help' ) || $this->misc->is_gfpdf_settings_tab( 'tools' ) ) {
			wp_enqueue_script( 'gfpdf_js_backbone' );
		}

		if ( is_admin() && rgget( 'page' ) == 'gf_entries' ) {
			wp_enqueue_script( 'gfpdf_js_entries' );
			wp_enqueue_style( 'gfpdf_css_styles' );
		}

		if ( is_admin() ) {
			wp_enqueue_style( 'gfpdf_css_admin_styles' );
		}
	}

	/**
	 * Auto no-conflict any preloaded scripts that begin with 'gfpdf_'
	 *
	 * @since 4.0
	 *
	 * @param array $items The current list of no-conflict scripts
	 *
	 * @return array
	 */
	public function auto_noconflict_scripts( $items ) {

		$wp_scripts = wp_scripts();

		/**
		 * Set defaults we will allow to load on GF pages which are needed for Gravity PDF
		 * If any Gravity PDF modules requires WordPress-specific JS files you should add them to this list
		 */
		$default_scripts = array(
			'editor',
			'word-count',
			'quicktags',
			'wpdialogs-popup',
			'media-upload',
			'wplink',
			'backbone',
			'underscore',
			'media-editor',
			'media-models',
			'media-views',
			'plupload',
			'plupload-flash',
			'plupload-html4',
			'plupload-html5',
			'plupload-silverlight',
			'wp-plupload',
			'gform_placeholder',
			'jquery-ui-autocomplete',
			'thickbox',
		);

		foreach ( $wp_scripts->queue as $object ) {
			if ( substr( $object, 0, 8 ) === 'gfpdf_js' ) {
				$items[] = $object;
			}
		}

		if ( $this->misc->is_gfpdf_page() ) {
			$items = array_merge( $default_scripts, $items );
		}

		/* See https://gravitypdf.com/documentation/v4/gfpdf_gf_noconflict_scripts/ for more details about this filter */
		return apply_filters( 'gfpdf_gf_noconflict_scripts', $items );
	}

	/**
	 * Auto no-conflict any preloaded styles that begin with 'gfpdf_'
	 *
	 * @since 4.0
	 *
	 * @param array $items The current list of no-conflict styles
	 *
	 * @return array
	 */
	public function auto_noconflict_styles( $items ) {

		$wp_styles = wp_styles();

		/**
		 * Set defaults we will allow to load on GF pages which are needed for Gravity PDF
		 * If any Gravity PDF modules requires WordPress-specific CSS files you should add them to this list
		 */
		$default_styles = array(
			'editor-buttons',
			'wp-jquery-ui-dialog',
			'media-views',
			'buttons',
			'thickbox',
		);

		foreach ( $wp_styles->queue as $object ) {
			if ( substr( $object, 0, 9 ) === 'gfpdf_css' ) {
				$items[] = $object;
			}
		}

		if ( $this->misc->is_gfpdf_page() ) {
			$items = array_merge( $default_styles, $items );
		}

		/* See https://gravitypdf.com/documentation/v4/gfpdf_gf_noconflict_styles/ for more details about this filter */
		return apply_filters( 'gfpdf_gf_noconflict_styles', $items );
	}

	/**
	 * Register our plugin with Gravity Form's Logger
	 *
	 * @param array $loggers
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function add_gf_logger( $loggers ) {
		$loggers['gravity-pdf'] = __( 'Gravity PDF', 'gravity-forms-pdf-extended' );

		return $loggers;
	}

	/**
	 * Bootstrap our settings API for use
	 *
	 * @return void
	 *
	 * @return 4.0
	 */
	public function init_settings_api() {
		/* load our options API */
		$this->options->init();
	}

	/**
	 * Register our admin settings
	 *
	 * @return void
	 *
	 * @return 4.0
	 */
	public function setup_settings_fields() {
		/* register our options settings */
		$this->options->register_settings( $this->options->get_registered_fields() );
	}

	/**
	 * Loads our Gravity PDF installer classes
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function installer() {
		$model = new Model\Model_Install( $this->gform, $this->log, $this->data, $this->misc, $this->notices );
		$class = new Controller\Controller_Install( $model, $this->gform, $this->log, $this->notices, $this->data, $this->misc );
		$class->init();

		/* set up required data */
		$class->setup_defaults();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
	}

	/**
	 * Include Welcome Screen functionality for installation / upgrades
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function welcome_screen() {

		$model = new Model\Model_Welcome_Screen( $this->log );
		$view  = new View\View_Welcome_Screen( array(
			'display_version' => PDF_EXTENDED_VERSION,
		), $this->gform );

		$class = new Controller\Controller_Welcome_Screen( $model, $view, $this->log, $this->data, $this->options );
		$class->init();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
		$this->singleton->add_class( $view );
	}

	/**
	 * Include Settings Page functionality
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function gf_settings() {

		$model = new Model\Model_Settings( $this->gform, $this->log, $this->notices, $this->options, $this->data, $this->misc );
		$view  = new View\View_Settings( array(), $this->gform, $this->log, $this->options, $this->data, $this->misc );

		$class = new Controller\Controller_Settings( $model, $view, $this->gform, $this->log, $this->notices, $this->data, $this->misc );
		$class->init();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
		$this->singleton->add_class( $view );
	}

	/**
	 * Include Form Settings (PDF) functionality
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function gf_form_settings() {

		$model = new Model\Model_Form_Settings( $this->gform, $this->log, $this->data, $this->options, $this->misc, $this->notices );
		$view  = new View\View_Form_Settings( array() );

		$class = new Controller\Controller_Form_Settings( $model, $view, $this->data, $this->options, $this->misc );
		$class->init();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
		$this->singleton->add_class( $view );
	}

	/**
	 * Include PDF Display functionality
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function pdf() {

		$model = new Model\Model_PDF( $this->gform, $this->log, $this->options, $this->data, $this->misc, $this->notices );
		$view  = new View\View_PDF( array(), $this->gform, $this->log, $this->options, $this->data, $this->misc );

		$class = new Controller\Controller_PDF( $model, $view, $this->gform, $this->log, $this->misc );
		$class->init();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
		$this->singleton->add_class( $view );
	}

	/**
	 * Include PDF Shortcodes functionality
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function shortcodes() {

		$model = new Model\Model_Shortcodes( $this->gform, $this->log, $this->options, $this->misc );
		$view  = new View\View_Shortcodes( array() );

		$class = new Controller\Controller_Shortcodes( $model, $view, $this->log );
		$class->init();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
		$this->singleton->add_class( $view );
	}

	/**
	 * Include one-time actions functionality
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function actions() {

		$model = new Model\Model_Actions( $this->data, $this->options, $this->notices );
		$view  = new View\View_Actions( array() );

		$class = new Controller\Controller_Actions( $model, $view, $this->gform, $this->log, $this->notices );
		$class->init();

		/* Add to our singleton controller */
		$this->singleton->add_class( $class );
		$this->singleton->add_class( $model );
		$this->singleton->add_class( $view );
	}

	/**
	 * Backwards compatibility with our early v3 templates
	 *
	 * @param $form_id
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_config_data( $form_id ) {
		return $this->get_default_config_data( $form_id );
	}

	/**
	 * Add backwards compatbility with v3.x.x default PDF template files
	 * This function will now pull the PDF configuration details from our query variables / or our backwards compatible URL params method
	 *
	 * @param integer $form_id The Gravity Form ID
	 *
	 * @return array The matched configuration being requested
	 *
	 * @since 4.0
	 */
	public function get_default_config_data( $form_id ) {

		$pid = $GLOBALS['wp']->query_vars['pid'];

		$settings = $this->options->get_pdf( $form_id, $pid );

		if ( is_wp_error( $settings ) ) {

			$this->log->addError( 'Invalid PDF Settings.', array(
				'form_id'          => $form_id,
				'pid'              => $pid,
				'WP_Error_Message' => $settings->get_error_message(),
				'WP_Error_Code'    => $settings->get_error_code(),
			) );

			/* Reset the settings so it forces everything to false */
			$settings = array();
		}

		return array(
			'empty_field'     => ( isset( $settings['show_empty'] ) && $settings['show_empty'] == 'Yes' ) ? true : false,
			'html_field'      => ( isset( $settings['show_html'] ) && $settings['show_html'] == 'Yes' ) ? true : false,
			'page_names'      => ( isset( $settings['show_page_names'] ) && $settings['show_page_names'] == 'Yes' ) ? true : false,
			'section_content' => ( isset( $settings['show_section_content'] ) && $settings['show_section_content'] == 'Yes' ) ? true : false,
		);
	}
}


/**
 * Execute our bootstrap class
 *
 * We were forced to forgo initialising the plugin using an anonymous function call due to
 * our AJAX calls in our unit testing suite failing (boo)
 */
add_action( 'after_setup_theme', '\GFPDF\Router::initialise_plugin' );

