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
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_dynamic_tags' ) );
		add_filter( 'frymo/isting_widget/sorting_options', array( $this, 'modify_listing_sorting_options' ) );
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

		require ( DCF_PLUGIN_DIR . '/inc/elementor/widgets/calculated-price.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new DCF_Calculated_Price() );

		require ( DCF_PLUGIN_DIR . '/inc/elementor/widgets/gallery-slider-control.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new DCF_Gallery_Slider_Control() );

		require ( DCF_PLUGIN_DIR . '/inc/elementor/widgets/services-grid.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new DCF_Services_Grid() );
	}



	/**
	 * Register frontend scripts
	*
	* @return void
	*/
	public function register_frontend_scripts() {
		wp_register_script( 'dcf-dates-range-form', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/dates-range-form.js', array( 'jquery' ), DCF_PLUGIN_VERSION, true );
		wp_register_script( 'dcf-gallery-slider-control', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/gallery-slider-control.js', array( 'jquery' ), DCF_PLUGIN_VERSION, true );
	}



	/**
	 * Register frontend styles
	*
	* @return void
	*/
	public function register_frontend_styles() {
		wp_register_style( 'dcf-dates-range-form', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/dates-range-form.css', array(), DCF_PLUGIN_VERSION );  
		wp_register_style( 'dcf-calculated-price', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/calculated-price.css', array(), DCF_PLUGIN_VERSION );  
		wp_register_style( 'dcf-gallery-slider-control', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/gallery-slider-control.css', array(), DCF_PLUGIN_VERSION );  
		wp_register_style( 'dcf-services-grid', DCF_PLUGIN_DIR_URL . 'inc/elementor/assets/services-grid.css', array(), DCF_PLUGIN_VERSION );  
	}





	// public function add_date_range_to_query_args( $args, $frymo_query, $settings ) {
	// 	if ( is_array( $frymo_query ) && ! empty( $frymo_query ) && isset( $settings['query_id'] ) && isset( $frymo_query[ $settings['query_id'] ] ) ) {
	// 		// Page load
	// 		$check_in_date  = $frymo_query[ $settings['query_id'] ]->udfm_einzugsdatum ?? '';
	// 		$check_out_date = $frymo_query[ $settings['query_id'] ]->udfm_auszugsdatum ?? '';

	// 	} else if ( isset( $_POST['action'] ) && $_POST['action'] == 'frymo_render_listing_widget' ) {
	// 		// AJAX call
	// 		$check_in_date  = $_POST['settings']['udfm_einzugsdatum'] ?? '';
	// 		$check_out_date = $_POST['settings']['udfm_auszugsdatum'] ?? '';
	// 	}

	// 	// Ensure the check-in date is provided
	// 	if ( ! isset( $check_in_date ) || ! $check_in_date  ) {
	// 		return $args; // No check-in date, return original args
	// 	}

	// 	// Plus 3 months by default
	// 	if ( ! isset( $check_out_date ) || ! $check_out_date  ) {
	// 		try {
	// 			$check_in_datetime = new DateTime( $check_in_date );
	// 			$check_in_datetime->modify('+2 months'); // Add 3 months
	// 			$check_in_datetime->modify('last day of this month'); // Set to last day of the month
	// 			$check_out_date = $check_in_datetime->format('Y-m-d'); // Format as needed
	// 		} catch (Exception $e) {
	// 			// Handle invalid date format if needed
	// 			return $args;
	// 		}
	// 	}



	// 	error_log( "check_in_date\n" . print_r( $check_in_date, true ) . "\n" );
	// 	error_log( "check_out_date\n" . print_r( $check_out_date, true ) . "\n" );

	// 	// Initialize meta query if not already set
	// 	$args['meta_query'] = $args['meta_query'] ?? [];

	// 	// Remove any existing date conditions
	// 	unset( $args['meta_query']['udfm_einzugsdatum'], $args['meta_query']['check_out_date'] );


	// 	if ( $check_in_date && $check_out_date ) {
	// 		$args['meta_query'][] = [
	// 			'relation' => 'AND',
	// 			[
	// 					'key' => 'einzugsdatum', // Date property will be occupied
	// 					'value' => $check_in_date,
	// 					'compare' => '>',
	// 					'type' => 'DATE'
	// 			],
	// 			[
	// 					'key' => 'auszugsdatum', // Date property will be free
	// 					'value' => $check_out_date,
	// 					'compare' => '<=',
	// 					'type' => 'DATE'
	// 			]
	// 		];
	// 	}

	// 	// Uncomment for debugging
	// 	// error_log( "args\n" . print_r( $args, true ) . "\n" );

	// 	return $args;
	// }




	public function add_date_range_to_query_args( $args, $frymo_query, $settings ) {
		
		if ( ( is_array( $frymo_query ) || is_object( $frymo_query ) )
			&& ! empty( $frymo_query )
			&& array_key_exists( $settings['query_id'], $frymo_query )
			&& isset( $frymo_query[ $settings['query_id'] ] )
		) {
			// Page load.
			$check_in_date  = $frymo_query[ $settings['query_id'] ]->udfm_einzugsdatum ?? '';
			$check_out_date = $frymo_query[ $settings['query_id'] ]->udfm_auszugsdatum ?? '';

		} elseif ( isset( $_POST['action'] ) && 'frymo_render_listing_widget' === $_POST['action'] ) {
			// AJAX call.
			$check_in_date  = $_POST['settings']['udfm_einzugsdatum'] ?? '';
			$check_out_date = $_POST['settings']['udfm_auszugsdatum'] ?? '';
		}

		// error_log( "check_in_date\n" . print_r( $check_in_date, true ) . "\n" );
		// error_log( "check_out_date\n" . print_r( $check_out_date, true ) . "\n" );


		// Ensure the check-in date is provided
		if ( empty( $check_in_date ) || empty( $check_out_date ) ) {

			if ( $settings['order_by'] === 'default' ) {
				$args['meta_key']  = 'auszugsdatum';
				$args['orderby']   = 'meta_value';
				$args['order']     = 'ASC';
			}

			// switch ( $settings['order_by'] ) {
			// 	case 'default':
			// 		$args['meta_key']  = 'auszugsdatum';
			// 		$args['orderby']   = 'meta_value';
			// 		$args['order']     = 'ASC';
			// 		break;
	
			// 	case 'price_asc':
			// 		$args['meta_key']  = 'frymo_price_num';
			// 		$args['orderby']   = 'meta_value';
			// 		$args['order']     = 'ASC';
			// 		break;
	
			// 	case 'price_desc':
			// 		$args['meta_key']  = 'frymo_price_num';
			// 		$args['orderby']   = 'meta_value';
			// 		$args['order']     = 'DESC';
			// 		break;
	
			// 	case 'rooms_asc':
			// 		$args['meta_key']  = 'anzahl_zimmer';
			// 		$args['orderby']   = 'meta_value';
			// 		$args['order']     = 'ASC';
			// 		break;
	
			// 	case 'rooms_desc':
			// 		$args['meta_key']  = 'anzahl_zimmer';
			// 		$args['orderby']   = 'meta_value';
			// 		$args['order']     = 'DESC';
			// 		break;
			// }

			return $args;
		}


		// Initialize meta query if not already set
		$args['meta_query'] = $args['meta_query'] ?? [];

		// Remove any existing date conditions
		unset( $args['meta_query']['udfm_einzugsdatum'], $args['meta_query']['udfm_auszugsdatum'] );


		$args_1 = array(
			'post_type'      => FRYMO_POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		);
		if ( $settings['order_by'] === 'default' ) {
			$args_1['meta_key']  = 'auszugsdatum';
			$args_1['orderby']   = 'meta_value';
			$args_1['order']     = 'ASC';
		} 

		switch ( $settings['order_by'] ) {
			case 'default':
				$args_1['meta_key']  = 'auszugsdatum';
				$args_1['orderby']   = 'meta_value';
				$args_1['order']     = 'ASC';
				break;

			case 'price_asc':
				$args_1['meta_key']  = 'frymo_price_num';
				$args_1['orderby']   = 'meta_value';
				$args_1['order']     = 'ASC';
				break;

			case 'price_desc':
				$args_1['meta_key']  = 'frymo_price_num';
				$args_1['orderby']   = 'meta_value';
				$args_1['order']     = 'DESC';
				break;

			case 'rooms_asc':
				$args_1['meta_key']  = 'anzahl_zimmer';
				$args_1['orderby']   = 'meta_value';
				$args_1['order']     = 'ASC';
				break;

			case 'rooms_desc':
				$args_1['meta_key']  = 'anzahl_zimmer';
				$args_1['orderby']   = 'meta_value';
				$args_1['order']     = 'DESC';
				break;
		}

		$query_1 = new WP_Query( $args_1 );
		// error_log( "query_1\n" . print_r( $query_1, true ) . "\n" );

		$all_posts = array();
		foreach ( $query_1->posts as $post_id ) {
			$all_posts[ $post_id ] = array(
				'check_in_date' => get_post_meta( $post_id, 'einzugsdatum', true ) ?? '',
				'check_out_date' => get_post_meta( $post_id, 'auszugsdatum', true ) ?? '',
			);
		}


		// // Sort $all_posts by check_out_date (einzugsdatum)
		// uasort(
		// 	$all_posts,
		// 	function ( $a, $b ) {
		// 		// Convert check_in_date to timestamps for comparison
		// 		$date_a = ! empty( $a['check_out_date'] ) ? strtotime( $a['check_out_date'] ) : PHP_INT_MAX;
		// 		$date_b = ! empty( $b['check_out_date'] ) ? strtotime( $b['check_out_date'] ) : PHP_INT_MAX;

		// 		// Return comparison result for sorting in ascending order
		// 		return $date_a <=> $date_b;
		// 	}
		// );




		$filter = array_filter( $all_posts, function( $dates ) use ( $check_in_date, $check_out_date ) {
			$property_check_in = ! empty( $dates['check_in_date'] ) ? strtotime( $dates['check_in_date'] ) : null;
			$property_check_out = ! empty( $dates['check_out_date'] ) ? strtotime( $dates['check_out_date'] ) : null;
	
			$requested_check_in = strtotime( $check_in_date );
			$requested_check_out = strtotime( $check_out_date );
	
			// Case 1: Property has no check-in or check-out dates (completely free)
			if ( is_null( $property_check_in ) && is_null( $property_check_out ) ) {
				return true;
			}
	
			// Case 2: Property's check-out date is strictly before the requested check-in date
			if ( ! is_null( $property_check_out ) && $property_check_out < $requested_check_in ) {
				return true;
			}
	
			// Case 3: Property's check-in date is strictly after the requested check-out date
			if ( ! is_null( $property_check_in ) && $property_check_in > $requested_check_out ) {
				return true;
			}
	
			// Case 4: Handle back-to-back bookings
			// Exclude properties where check-in is the day after requested check-out
			if ( ! is_null( $property_check_in ) && ! is_null( $property_check_out ) ) {
				// Check if check-out happens on the requested check-in date (overlap)
				if ( $property_check_out >= $requested_check_in && $property_check_in <= $requested_check_out ) {
					return false;
				}
	
				// Back-to-back: Check-out is immediately followed by check-in
				if ( $property_check_out + DAY_IN_SECONDS == $property_check_in ) {
					return false;
				}
			}
	
			return false; // Exclude everything else
		} );




		// Extract the IDs of the filtered posts
		$filtered_post_ids = array_keys( $filter );

		// Add 'post__in' to $args to only include filtered post IDs
		$args['post__in'] = $filtered_post_ids;
		$args['orderby']  = 'post__in';

		// Uncomment for debugging
		error_log( "args\n" . print_r( $args, true ) . "\n" );

		return $args;
	}



	



	public function register_dynamic_tags( $dynamic_tags ) {

		// Register the custom 'Domu' group
		$dynamic_tags->register_group( 'domu_group', [
			'title' => 'Domu'
		] );

		/**
		 * Outputs guest count with customizable singular or plural for 'Gast/Gäste'.
		 */
		$dynamic_tags->register(
			new class extends \Elementor\Core\DynamicTags\Tag {

				/**
				 * Get tag name.
				*/
				public function get_name() {
					return 'domu_guest_count';
				}

				/**
				 * Get tag title.
				*/
				public function get_title() {
					return 'Guest Count (Gast/Gäste)';
				}

				/**
				 * Get tag group.
				*/
				public function get_group() {
					return 'domu_group';
				}

				/**
				 * Get tag categories.
				*/
				public function get_categories() {
					return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
				}

				/**
				 * Define controls for singular and plural strings.
				*/
				protected function register_controls() {

					$this->add_control( 'singular_label', [
						'label'       => 'Singular Label',
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => 'Gast', // Default singular form.
						'placeholder' => 'e.g. Gast',
						'ai'          => [
							'active' => false,
						],
					] );

					$this->add_control( 'plural_label', [
						'label'       => 'Plural Label',
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => 'Gäste', // Default singular form.
						'placeholder' => 'e.g. Gäste',
						'ai'          => [
							'active' => false,
						],
					] );

				}

				/**
				 * Render the dynamic tag output.
				*/
				protected function render() {
					// Get the current post ID.
					$post_id = get_the_ID();

					// Retrieve the 'anzahl_der_gaste' meta field value.
					$openimmo_verwaltung_objekt = get_post_meta( $post_id, 'openimmo_verwaltung_objekt', true );

					// Default to 1 guest if the value is missing or invalid.
					$guests = isset( $openimmo_verwaltung_objekt['max_personen'] ) ? (int) $openimmo_verwaltung_objekt['max_personen'] : 1;

					// Get user-defined labels from the controls.
					$settings       = $this->get_settings();
					$singular_label = ! empty( $settings['singular_label'] ) ? sanitize_text_field( $settings['singular_label'] ) : 'Gast';
					$plural_label   = ! empty( $settings['plural_label'] ) ? sanitize_text_field( $settings['plural_label'] ) : 'Gäste';

					// Determine whether to use singular or plural.
					$guests_text = ( $guests == 1 ) ? $singular_label : $plural_label;

					// Output the formatted guest count with the correct label.
					echo esc_html( $guests . ' ' . $guests_text );
				}
			}
		);



		/**
		 * Output 'auszugsdatum' field as "verfügbar jetzt" if today or in the past, otherwise as a German-formatted date.
		 */
		$dynamic_tags->register(
			new class extends \Elementor\Core\DynamicTags\Tag {

				public function get_name() {
					return 'domu_available_from';
				}

				public function get_title() {
					return 'Available From Date';
				}

				public function get_group() {
					return 'domu_group';
				}

				public function get_categories() {
					return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
				}

				protected function render() {
					$post_id = get_the_ID();
					$date = get_post_meta( $post_id, 'auszugsdatum', true );
					
					if ( ! empty( $date ) ) {
						$current_date = new DateTime();
						$datetime = new DateTime( $date );

						if ( $datetime <= $current_date ) {
							echo 'sofort verfügbar';
						} else {
							echo 'verfügbar ab ' . esc_html( $datetime->format( 'd.m.Y' ) );
						}
					} else {
						echo 'sofort verfügbar';
					}
				}

			}
		);

	}




	public function modify_listing_sorting_options( $options ) {
		$options['default'] = 'zuerst verfügbar';

		return $options;
	}
  
}
new DCF_Elementor();