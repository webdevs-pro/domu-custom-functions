<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


require_once ( DCF_PLUGIN_DIR . '/inc/elementor/elementor.php' );

// Register the script and style globally
add_action( 'elementor/frontend/after_register_scripts', 'dcf_register_assets' ); // Register the assets during the enqueue stage
function dcf_register_assets() {
	// Register JS file
	wp_enqueue_script(
		'domu-custom-js', // Handle for the script
		DCF_PLUGIN_DIR_URL  . '/inc/assets/domu-custom.js',
		array( 'jquery' ),
		DCF_PLUGIN_VERSION,
		true // Load in the footer
	);

	// Register CSS file
	wp_enqueue_style(
		'domu-custom-css', // Handle for the style
		DCF_PLUGIN_DIR_URL  . '/inc/assets/domu-custom.css',
		array(), // Dependencies (empty array means no dependencies)
		DCF_PLUGIN_VERSION,
		'all' // Media type: 'all', 'screen', 'print', etc.
	);
}


// Shortcode: Render Slider Nav Buttons
add_shortcode('domu_slider_nav_buttons_with_fractions', 'dcf_render_slider_nav_buttons_with_fractions');
function dcf_render_slider_nav_buttons_with_fractions() {
	ob_start();
	?>

	<div class="domu-slide-inner-control">
		<div class="slide-inner-button-prev">
			<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="24" cy="24" r="23.5" stroke="#161514"/>
					<path d="M27.5 16.5L20 24L27.5 31.5" stroke="#161514" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</div>
		<div class="slide-inner-button-next">
			<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="24" cy="24" r="23.5" stroke="#161514"/>
					<path d="M27.5 16.5L20 24L27.5 31.5" stroke="#161514" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</div>
		<div class="slide-inner-fraction">1/1</div>
	</div>

	<?php

	return ob_get_clean();
}

// Shortcode: Render Object Meta
add_shortcode('domu_object_meta', 'dcf_render_object_meta');
function dcf_render_object_meta() {

	$post_id = get_the_ID();
	$rooms   = get_post_meta( $post_id, 'anzahl_zimmer', true );
	$area    = get_post_meta( $post_id, 'frymo_area_num', true );
	$guests  = get_post_meta( $post_id, 'anzahl_der_gaste', true ) || 1;

	// Handle singular and plural for 'guest' (Gast/Gäste) in German
	if ( $guests == 1 ) {
		$guests_text = 'Gast'; // Singular form
	} else {
		$guests_text = 'Gäste'; // Plural form
	}

	$price = intval( get_post_meta( $post_id, 'frymo_price_num', true ) ) * 3;

	ob_start();

		// Initialize an empty array to store the meta information
		$meta_info = array();
		
		// Check if rooms meta exists and add it to the array
		if ( $rooms ) {
			$meta_info[] = '<span>' . esc_html( $rooms ) . ' Zimmer</span>';
		}
		
		// Check if area meta exists and add it to the array
		if ( $area ) {
			$meta_info[] = '<span>' . esc_html( $area ) . 'm²</span>';
		}
		
		// Check if guests count exists and add it to the array
		if ( $guests ) {
			$meta_info[] = '<span>' . esc_html( $guests ) . ' ' . esc_html( $guests_text ) . '</span>';
		}
		
		// Check if price meta exists and add it to the array
		if ( $price && function_exists( 'frymo_format_number_de' ) ) {
			$meta_info[] = '<span class="price">Ab ' . esc_html( frymo_format_number_de( $price ) ) . '€</span>';
		}
		
		// Join the array elements with the separator and wrap in flexbox
		if ( !empty( $meta_info ) ) {
			echo '<div class="domu-apartment-meta">';
				echo implode( '<span class="bullet">&bull;</span>', $meta_info );
				// echo implode( '<span class="bullet">&#9679;</span>', $meta_info );
			echo '</div>';
		}
	
	return ob_get_clean();
}












add_shortcode( 'custom_datepicker', 'custom_datepicker_shortcode' );
function custom_datepicker_shortcode() {
	// Enqueue jQuery UI Datepicker script and style only when shortcode is used
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

	ob_start();
	?>

	<div class="date-picker-container">
		<label for="einzugsdatum">Einzugsdatum (Move-in Date):</label>
		<input type="text" id="einzugsdatum" name="einzugsdatum" readonly="readonly">
		
		<label for="auszugsdatum">Auszugsdatum (Move-out Date):</label>
		<input type="text" id="auszugsdatum" name="auszugsdatum" readonly="readonly">


	</div>

	<script>
		jQuery(document).ready(function($) {
			// Set minimum range to 3 months from the start date
			let minRangeMonths = 3;

			// Initialize einzugsdatum date picker
			$("#einzugsdatum").datepicker({
					dateFormat: "dd.mm.yy",
					changeMonth: true,
					changeYear: true,
					onClose: function(selectedDate) {
						let startDate = $(this).datepicker("getDate");

						if (startDate) {
							startDate.setMonth(startDate.getMonth() + minRangeMonths);

							// Set the minDate for auszug date picker
							$("#auszugsdatum").datepicker("option", "minDate", startDate);
						}
					},
					beforeShowDay: function(date) {
						// Allow only the first day of any month to be selected
						return [date.getDate() === 1, ""];
					}
			});

			// Initialize auszugsdatum date picker
			$("#auszugsdatum").datepicker({
					dateFormat: "dd.mm.yy",
					changeMonth: true,
					changeYear: true
			});
		});
	</script>

	<style>
		/* Basic styling */
		.date-picker-container {
			display: flex;
			flex-direction: column;
			gap: 10px;
			max-width: 300px;
		}
		.date-picker-container label {
			font-weight: bold;
		}
		.date-picker-container input {
			padding: 8px;
			font-size: 1em;
			width: 100%;
		}
	</style>

	<?php
	return ob_get_clean();
}



