<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager as Controls_Manager;
use Elementor\Group_Control_Typography as Group_Control_Typography;

class DCF_Services_Grid extends Elementor\Widget_Base {

	private $settings;
	private $frontend_settings;

	public function get_name() {
		return 'dcf-services-grid';
	}

	public function get_title() {
		return 'Services Grid';
	}

	public function get_icon() {
		return 'eicon-editor-list-ul';
	}

	public function get_categories() {
		return ['domu'];
	}

	public function get_keywords() {
		return ['services', 'grid'];
	}

	public function get_style_depends() {
		return ['dcf-services-grid'];
	}

	// public function get_script_depends() {
	// 	return ['dcf-services-grid'];
	// }

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Content', 'frymo' ),
		] );

         $this->add_control( 'fallback_icon', [
            'label' => 'Fallback Icon', 
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
               'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
         ] );

		$this->end_controls_section();

      Frymo_Elementor::get_heading_controls_section( $this, 'h3', esc_html__( 'Ausstattung', 'frymo' ), true, true );

		$this->start_controls_section( 'section_style', [
			'label' => esc_html__( 'Style', 'frymo' ),
         'tab'   => Elementor\Controls_Manager::TAB_STYLE,
		] );

         $this->add_control( 'content_color', [
            'label'     => esc_html__( 'Color', 'elementor' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
               '{{WRAPPER}} .dcf-service-label' => 'color: {{VALUE}};',
            ],
         ] );

         $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'content_typography',
            'label'    => esc_html__( 'Typography', 'elementor' ),
            'selector' => '{{WRAPPER}} .dcf-service-label',
         ] );

		$this->end_controls_section();

		Frymo_Elementor::get_heading_style_controls_section( $this );

	}


	protected function render() {
		$settings = $this->get_settings();

      // Fetch all terms attached to the post for the 'udft_ausstattungen' taxonomy
      $terms = wp_get_post_terms( get_the_ID(), 'udft_ausstattungen' );

      if ( ( ! is_wp_error( $terms ) && ! empty( $terms ) ) || Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			Frymo_Elementor::render_heading( $this );
		}


      if ( is_wp_error( $terms ) || empty( $terms ) ) {
         return;
      }

      echo '<ul class="dcf-services-grid">';

      foreach ( $terms as $term ) {

         $icon_id = get_term_meta( $term->term_id, 'icon', true ) ?: $settings['fallback_icon']['id'];

         echo '<li class="dcf-service">';

            echo '<span class="dcf-service-icon">';

            if ( ! empty( $icon_id ) ) {
               echo wp_get_attachment_image( $icon_id, 'full', false );
            }

            echo '</span>';

            echo '<span class="dcf-service-label">' . $term->name . '</span>';

         echo '</li>';
      }

      echo '</ul>';
	}

}