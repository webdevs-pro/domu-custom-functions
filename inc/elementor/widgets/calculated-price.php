<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class DCF_Calculated_Price extends Elementor\Widget_Base {

	private $settings;
	private $frontend_settings;

	public function get_name() {
		return 'dcf-calculated-price';
	}

	public function get_title() {
		return 'Calculated Price';
	}

	public function get_icon() {
		return 'eicon-product-price';
	}

	public function get_categories() {
		return ['domu'];
	}

	public function get_keywords() {
		return ['price'];
	}

	public function get_style_depends() {
		return ['dcf-calculated-price'];
	}

	// public function get_script_depends() {
	// 	return ['dcf-calculated-price'];
	// }

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Settings', 'frymo' ),
		] );


		$this->end_controls_section();
	}


	protected function render() {
		$this->settings = $this->get_settings();

		// error_log( "this->settings\n" . print_r( $this->settings, true ) . "\n" );

		$frymo_query   = $_GET['frymo_query'] ?? '';
		$frymo_query   = (array) json_decode( stripslashes( $frymo_query ) );

		if ( ( is_array( $frymo_query ) || is_object( $frymo_query ) )
			&& ! empty( $frymo_query )
			&& array_key_exists( ( $this->settings['query_id'] ?? '' ), $frymo_query )
			&& isset( $frymo_query[ ( $this->settings['query_id'] ?? '' ) ] )
		) {
			// Page load.
			$check_in_date  = $frymo_query[ ( $this->settings['query_id'] ?? '' ) ]->udfm_einzugsdatum ?? '';
			$check_out_date = $frymo_query[ ( $this->settings['query_id'] ?? '' ) ]->udfm_auszugsdatum ?? '';

		} elseif ( isset( $_POST['action'] ) && 'frymo_render_listing_widget' === $_POST['action'] ) {
			// AJAX call.
			$check_in_date  = $_POST['settings']['udfm_einzugsdatum'] ?? '';
			$check_out_date = $_POST['settings']['udfm_auszugsdatum'] ?? '';
		}

		$monthly_price = get_post_meta( get_the_ID(), 'frymo_price_num', true );

		if ( ! empty( $check_in_date ) && ! empty( $check_out_date ) ) {
			$number_of_months = $this->calculate_month_difference( $check_in_date, $check_out_date );
			$total_price      = intval( $monthly_price ) * $number_of_months; 
		} else {
			$total_price      = intval( $monthly_price ) * 3; 
		}

		echo '<div class="calculated-price">';

			echo '<span class="monthly-price">' . frymo_format_number_de( $monthly_price ) . '€/mo</span>';
			
			if ( ! empty( $check_in_date ) && ! empty( $check_out_date ) ) {
				echo '<span class="total-price">' . frymo_format_number_de( $total_price ) . '€ Total</span>';
			} else {
				echo '<span class="total-price">Ab ' . frymo_format_number_de( $total_price ) . '€</span>';
			}

		echo '</div>';
	}


	/**
	 * Calculate the difference in months between two dates, rounded to the nearest half-month.
	 *
	 * @param string $start_date The start date in 'Y-m-d' format.
	 * @param string $end_date   The end date in 'Y-m-d' format.
	 *
	 * @return float The number of months, rounded to the nearest half-month.
	 */
	function calculate_month_difference( $start_date, $end_date ) {
		// Convert the date strings to DateTime objects.
		$start = new DateTime( $start_date );
		$end   = new DateTime( $end_date );

		// Calculate the difference between the dates.
		$interval = $start->diff( $end );

		// Calculate the full month difference.
		$months = ( $interval->y * 12 ) + $interval->m;

		// Add the days as a fraction of the month (assuming 30.44 days per month on average).
		$days_fraction = $interval->d / 30.44;
		$total_months  = $months + $days_fraction;

		// Round to the nearest half-month.
		$rounded_months = round( $total_months * 2 ) / 2;

		return $rounded_months;
	}

}