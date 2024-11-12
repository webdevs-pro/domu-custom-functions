<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager as Controls_Manager;
use Elementor\Group_Control_Typography as Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;


class DCF_Dates_Range_Form extends Elementor\Widget_Base {

	private $settings;
	private $frontend_settings;

	public function get_name() {
		return 'dcf-dates-range-form';
	}

	public function get_title() {
		return 'Dates Range Form';
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return ['domu'];
	}

	public function get_keywords() {
		return ['form', 'search'];
	}

	public function get_style_depends() {
		return ['dcf-dates-range-form', 'flatpickr'];
	}

	public function get_script_depends() {
		return ['dcf-dates-range-form', 'flatpickr'];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Settings', 'frymo' ),
		] );

			$this->add_control( 'form_action', [
				'label'   => esc_html__( 'Formular Aktion', 'frymo' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ajax'   => 'AJAX',
					'reload' => esc_html__( 'Reload', 'frymo' ),
				],
				'default' => 'ajax'
			] );

			$this->add_control( 'redirect_url', [
				'type'          => Controls_Manager::URL,
				'label' => esc_html__( 'Weiterleitungs-URL', 'frymo' ),
				'show_external' => true,
				'dynamic'       => [
					'active'     => true,
					'categories' => [ TagsModule::URL_CATEGORY ],
					'property'   => 'url',
				],
				'condition'     => [
					'form_action' => 'reload',
				]
			] );

			$this->add_control( 'query_id', [
				'type'        => Controls_Manager::TEXT,
				'label'       => esc_html__( 'Eindeutige ID', 'frymo' ),
				// 'placeholder' => esc_html__( 'z.B. meine_suchleiste', 'frymo' ),
				'label_block' => true,
				'description' => esc_html__( 'Trage hier eine ID ein, um mehrere Listen- oder Kartenwidgets mit dem Filter zu verbinden.', 'frymo' ),
				'ai'          => [
					'active' => false,
				],
			] );

		$this->end_controls_section();
	}

	public function get_settings_for_frontend() {
		$frymo_query = $_GET['frymo_query'] ?? '';
		$frymo_query = (array) json_decode( stripslashes( $frymo_query ) );

		$frontend_settings = array(
			'form_action'     => $this->settings['form_action'],
			'redirect_url'    => $this->settings['redirect_url'],
			'query_id'        => $this->settings['query_id'],
		);


		// $object_vars = get_object_vars( $frymo_query[ $this->settings['query_id'] ] ?? new stdClass() );

		// // Filter keys that start with 'udft_' or 'udfm_' and merge them into $frontend_settings
		// foreach ( $object_vars as $key => $value ) {
		// 	if ( strpos( $key, 'udft_' ) === 0 || strpos( $key, 'udfm_' ) === 0 ) {
		// 		$frontend_settings[ $key ] = explode( ',', $value );
		// 	}
		// }

		return $frontend_settings;
	}

	protected function render() {
		$this->settings          = $this->get_settings();
		$this->frontend_settings = $this->get_settings_for_frontend();

		$frymo_query = $_GET['frymo_query'] ?? '';
		$frymo_query = (array) json_decode( stripslashes( $frymo_query ) );

		// store initial widget settings
		$this->add_render_attribute( '_wrapper', [
			'data-frymo-query-id'   => $this->settings['query_id'],
			'data-initial-settings' => htmlspecialchars( json_encode( $this->frontend_settings ), ENT_QUOTES, 'UTF-8' ),
		] );

		$einzugsdatum = $frymo_query[ $this->settings['query_id'] ]->udfm_einzugsdatum ?? '';
		$auszugsdatum = $frymo_query[ $this->settings['query_id'] ]->udfm_auszugsdatum ?? '';

		echo '<form>';

			echo '<label>';
				echo 'Einzugsdatum';
				echo '<input type="text" class="dcf-date-input" name="udfm_einzugsdatum" readonly="readonly" value="' . $einzugsdatum . '" placeholder="Select Date" required/>';
			echo '</label>';
			
			echo '<label>';
				echo 'Auszugsdatum';
				echo '<input type="text" class="dcf-date-input" name="udfm_auszugsdatum" readonly="readonly" value="' . $auszugsdatum . '" placeholder="Select Date" required/>';
			echo '</label>';

			echo '<input type="submit" class="frymo-submit" value="Check" />';

		echo '</form>';
	}

}