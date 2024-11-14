<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager as Controls_Manager;
use Elementor\Group_Control_Typography as Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;


class DCF_Gallery_Slider_Control extends Elementor\Widget_Base {

	private $settings;
	private $frontend_settings;

	public function get_name() {
		return 'dcf-gallery-slider-control';
	}

	public function get_title() {
		return 'Gallery Slider Control';
	}

	public function get_icon() {
		return 'eicon-editor-code';
	}

	public function get_categories() {
		return ['domu'];
	}

	public function get_keywords() {
		return ['gallery'];
	}

	public function get_style_depends() {
		return ['dcf-gallery-slider-control'];
	}

	public function get_script_depends() {
		return ['dcf-gallery-slider-control'];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Settings', 'frymo' ),
		] );

			$this->add_control( 'widget_id', [
				'label' => 'Widget ID',
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'Widget ID without #',
				'ai' => [
					'active' => false,
				],
			] );

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings();

		?>
		<div class="controls-wrapper" data-target-widget-id="<?php echo esc_attr( $settings['widget_id'] ); ?>">
			
			<div class="prev-button">
				<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="24" cy="24" r="23.5" stroke="#161514"/>
					<path d="M27.5 16.5L20 24L27.5 31.5" stroke="#161514" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>

			<div class="next-button">
				<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="24" cy="24" r="23.5" stroke="#161514"/>
					<path d="M27.5 16.5L20 24L27.5 31.5" stroke="#161514" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</div>

			<div class="fraction">1 / 1</div>

		</div>
		<?php
	}

}