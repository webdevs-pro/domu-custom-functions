<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class DCF_Elementor {

	const MINIMUM_ELEMENTOR_VERSION = '3.15.0';
	const MINIMUM_PHP_VERSION = '7.3';

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_widgets_categories' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ), 9999 );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );
		add_action( 'elementor/widgets/register', array( $this, 'on_widgets_registered' ) );
		add_filter( 'frymo/query/', array( $this, 'add_date_range_to_query_args' ), 10, 3 );
	}

	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}
	}



	/**
	 * Register custom widgets categories
	*
	* @param \Elementor\Elements_Manager $elements_manager
	*
	* @return void
	*/
	function register_widgets_categories( \Elementor\Elements_Manager $elements_manager ) {
		$elements_manager->add_category( 'domu', [
			'title' => 'Domu',
			'icon' => 'fa fa-plug',
		] );
	}



	/**
	 * Admin notice
	*
	* Warning when the site doesn't have Elementor installed or activated.
	*
	* @return void
	*/
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			'"%1$s" requires "%2$s" to be installed and activated.',
			'<strong>' . 'Domu Custom Functions' . '</strong>',
			'<strong>' . 'Elementor' . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}



	/**
	 * Admin notice
	*
	* Warning when the site doesn't have a minimum required Elementor version.
	*
	* @return void
	*/
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			'"%1$s" requires "%2$s" version %3$s or greater.',
			'<strong>' . 'Domu Custom Functions' . '</strong>',
			'<strong>' . 'Elementor' . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}



	/**
	 * Admin notice
	*
	* Warning when the site doesn't have a minimum required PHP version.
	*
	* @return void
	*/
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			'"%1$s" requires "%2$s" version %3$s or greater.',
			'<strong>' . 'Domu Custom Functions' . '</strong>',
			'<strong>' . 'PHP' . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}



	/**
	 * Register DT widgets
	*
	* @return void
	*/
	public function on_widgets_registered() {
		require ( DCF_PLUGIN_DIR . '/inc/elementor/widgets/dates-range-form.php' );

		\Elementor\Plugin::instance()->widgets_manager->register( new DCF_Dates_Range_Form() );
	}



	/**
	 * Register frontend scripts
	*
	* @return void
	*/
	public function register_frontend_scripts() {
		wp_register_script( 'dcf-dates-range-form', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/dates-range-form.js', array( 'jquery' ), DCF_PLUGIN_VERSION, true );
	}



	/**
	 * Register frontend styles
	*
	* @return void
	*/
	public function register_frontend_styles() {
		wp_register_style( 'dcf-dates-range-form', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/dates-range-form.css', array(), DCF_PLUGIN_VERSION );  
	}

	// public function add_date_range_to_query_args( $args, $frymo_query, $settings ) {
	// 	// error_log( "add_date_range_to_query_args args\n" . print_r( $args, true ) . "\n" );
	// 	// error_log( "add_date_range_to_query_args frymo_query\n" . print_r( $frymo_query, true ) . "\n" );
	// 	// error_log( "add_date_range_to_query_args settings\n" . print_r( $settings, true ) . "\n" );


	// 	if ( is_array( $frymo_query ) && ! empty( $frymo_query ) && isset( $settings['query_id'] ) && isset( $frymo_query[ $settings['query_id'] ] ) ) {
	// 		// Page load
	// 		$check_in_date  = $frymo_query[ $settings['query_id'] ]->udfm_einzugsdatum ?? '';
	// 		$check_out_date = $frymo_query[ $settings['query_id'] ]->udfm_auszugsdatum ?? '';


	// 	} else if ( isset( $_POST['action'] ) && $_POST['action'] == 'frymo_render_listing_widget' ) {
	// 		// AJAX call
	// 		$check_in_date  = $_POST['settings']['udfm_einzugsdatum'] ?? '';
	// 		$check_out_date  = $_POST['settings']['udfm_auszugsdatum'] ?? '';
	// 	}

	// 	// error_log( "check_in_date\n" . print_r( $check_in_date, true ) . "\n" );
	// 	// error_log( "check_out_date\n" . print_r( $check_out_date, true ) . "\n" );

	// 	// einzugsdatum - post meta field name for start of ocupation date
	// 	// auszugsdatum - post meta field name for end of ocupation date

	// 	if ( $check_in_date ) {
	// 		unset ( $args['meta_query']['udfm_einzugsdatum'] );
	// 	}

	// 	if ( $check_out_date ) {
	// 		unset ( $args['meta_query']['check_out_date'] );
	// 	}

	// 	// Date query goes here


	// 	error_log( "args\n" . print_r( $args, true ) . "\n" );

	// 	return $args;
	// }




	public function add_date_range_to_query_args( $args, $frymo_query, $settings ) {
		if ( is_array( $frymo_query ) && ! empty( $frymo_query ) && isset( $settings['query_id'] ) && isset( $frymo_query[ $settings['query_id'] ] ) ) {
			// Page load
			$check_in_date  = $frymo_query[ $settings['query_id'] ]->udfm_einzugsdatum ?? '';
			$check_out_date = $frymo_query[ $settings['query_id'] ]->udfm_auszugsdatum ?? '';

		} else if ( isset( $_POST['action'] ) && $_POST['action'] == 'frymo_render_listing_widget' ) {
			// AJAX call
			$check_in_date  = $_POST['settings']['udfm_einzugsdatum'] ?? '';
			$check_out_date = $_POST['settings']['udfm_auszugsdatum'] ?? '';
		}

		// Ensure the check-in date is provided
		if ( ! $check_in_date ) {
			return $args; // No check-in date, return original args
		}

		// Initialize meta query if not already set
		$args['meta_query'] = $args['meta_query'] ?? [];

		// Remove any existing date conditions
		unset( $args['meta_query']['udfm_einzugsdatum'], $args['meta_query']['check_out_date'] );

		// Case 1: Only check-in date is provided
		if ( $check_in_date && ! $check_out_date ) {
			$args['meta_query'][] = [
				'relation' => 'OR',
				[
						'key' => 'einzugsdatum', // Date property will be occupied
						'value' => $check_in_date,
						'compare' => '>',
						'type' => 'DATE'
				],
				[
						'key' => 'auszugsdatum', // Date property will be free
						'value' => $check_in_date,
						'compare' => '<=',
						'type' => 'DATE'
				]
			];
		}

		// Case 2: Both check-in and check-out dates are provided
		elseif ( $check_in_date && $check_out_date ) {
			$args['meta_query'][] = [
				'relation' => 'OR',
				[
						'key' => 'einzugsdatum',
						'value' => $check_out_date,
						'compare' => '>',
						'type' => 'DATE'
				],
				[
						'key' => 'auszugsdatum',
						'value' => $check_in_date,
						'compare' => '<=',
						'type' => 'DATE'
				]
			];
		}

		// Uncomment for debugging
		// error_log( "args\n" . print_r( $args, true ) . "\n" );

		return $args;
}
  
}
new DCF_Elementor();