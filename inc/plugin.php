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

	// // Register CSS file
	// wp_enqueue_style(
	// 	'domu-custom-css', // Handle for the style
	// 	DCF_PLUGIN_DIR_URL  . '/inc/shortcodes/assets/domu-custom.css',
	// 	array(), // Dependencies (empty array means no dependencies)
	// 	DCF_PLUGIN_VERSION,
	// 	'all' // Media type: 'all', 'screen', 'print', etc.
	// );
}
