<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager as Controls_Manager;
use Elementor\Group_Control_Typography as Group_Control_Typography;

class DCF_Dates_Range_Form extends Elementor\Widget_Base {

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

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<form>';

			echo '<label>';
				echo 'Einzugsdatum';
				echo '<input type="text" class="dcf-date-input" name="einzugsdatum" readonly="readonly" placeholder="Select Date"/>';
			echo '</label>';
			
			echo '<label>';
				echo 'Auszugsdatum';
				echo '<input type="text" class="dcf-date-input" name="auszugsdatum" readonly="readonly" placeholder="Select Date"/>';
			echo '</label>';

			echo '<input type="submit" value="Check" />';

		echo '</form>';
	}

}