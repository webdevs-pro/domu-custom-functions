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


/**
 * Hook to add a custom menu item below "Comments."
 * Adds a "Services" menu item below "Comments" in the admin menu.
 *
 * @return void
 */
add_action( 'admin_menu', 'add_services_menu_below_comments' );
function add_services_menu_below_comments() {
	// Add the custom menu item.
	add_menu_page(
		__( 'Services', 'text-domain' ),
		__( 'Services', 'text-domain' ),
		'manage_categories',
		'edit-tags.php?taxonomy=udft_ausstattungen',
		'',
		'dashicons-saved',
		40
	);
}

/**
 * Highlight "Services" menu when on the taxonomy page.
 *
 * @param string $parent_file The parent file of the current submenu.
 * @return string Modified parent file.
 */
add_filter( 'parent_file', 'highlight_services_menu' );
function highlight_services_menu( $parent_file ) {
	// Check if we're on the 'udft_ausstattungen' taxonomy page.
	if ( isset( $_GET['taxonomy'] ) && 'udft_ausstattungen' === $_GET['taxonomy'] ) {
		$parent_file = 'edit-tags.php?taxonomy=udft_ausstattungen'; // Set "Services" as the highlighted menu.
	}

	return $parent_file;
}



/**
 * ACF Icon field for Services taxonomy term
 */
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key' => 'domu_services_group',
		'title' => 'Ausstattungen Term Fields',
		'fields' => array(
			array(
				'key' => 'service_icon',
				'label' => 'Icon',
				'name' => 'icon',
				'aria-label' => '',
				'type' => 'image',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'id',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
				'preview_size' => 'medium',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'taxonomy',
					'operator' => '==',
					'value' => 'udft_ausstattungen',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	) );
} );

