<?php
/**
 * Plugin Name: Domu Custom Functions
 * Plugin URI: https://frymo.de
 * Version: 1.0.0
 * Description: Custom functions for Domu.
 * Author: Stark Systems UG
 * Author URI: https://stark-systems.de
 */


use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

final class DCF_Plugin {

	public function __construct() {
		$this->define_constants();
		$this->include_files();
		$this->init_plugin_update_checker();
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
