<?php
/**
 * Plugin Name: Domu Custom Functions
 * Plugin URI: https://frymo.de
 * Version: 1.9.1
 * Description: Custom functions for Domu.
 * Author: Stark Systems UG
 * Author URI: https://stark-systems.de
 */


use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

final class DCF_Plugin {

	public function __construct() {
		// Check if the main plugin exists and is active
		if ( ! $this->is_main_plugin_active() ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_main_plugin_missing' ] );
			add_action( 'admin_init', [ $this, 'deactivate_plugin' ] );
			return;
		}

		$this->define_constants();
		$this->include_files();
		$this->init_plugin_update_checker();
	}

	private function is_main_plugin_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'frymo/frymo.php' );
	}


	public function admin_notice_main_plugin_missing() {
		echo '<div class="notice notice-error"><p>';
		echo 'Domu Custom Functions requires the Frymo plugin to be active. Please activate Frymo to use this plugin.';
		echo '</p></div>';
	}


	public function deactivate_plugin() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	function define_constants() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( ! function_exists( 'get_home_path' ) ) {
			require_once ( ABSPATH . 'wp-admin/includes/file.php' );
		}
		define( 'DCF_PLUGIN_VERSION', get_plugin_data( __FILE__ )['Version'] );
		// define( 'DD_HOME_PATH', get_home_path() );
		define( 'DCF_HOME_PATH', ABSPATH );
		define( 'DCF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'DCF_PLUGIN_DIR', dirname( __FILE__ ) );
		define( 'DCF_PLUGIN_FILE', __FILE__ );
		define( 'DCF_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	function include_files() {
		require_once ( DCF_PLUGIN_DIR . '/inc/vendor/autoload.php' );
		require_once ( DCF_PLUGIN_DIR . '/inc/plugin.php' );
	}

	function init_plugin_update_checker() {
		$UpdateChecker = PucFactory::buildUpdateChecker(
			'https://github.com/webdevs-pro/domu-custom-functions',
			__FILE__,
			'domu-custom-functions'
		);
		
		//Set the branch that contains the stable release.
		$UpdateChecker->setBranch( 'main' );
	}

}
new DCF_Plugin();
