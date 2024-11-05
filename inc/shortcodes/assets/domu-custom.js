jQuery(document).ready(function($) {
	// jQuery function for dynamically added prev/next buttons inside the slide content
	$(document).on('click', '.slide-inner-button-next', function() {
		var swiperContainer = $(this).closest('.swiper-slider-container');  // Find closest swiper container
		var swiperInstance = swiperContainer[0].swiper;  // Get the swiper instance from the container
		
		if (swiperInstance) {
			swiperInstance.slideNext();  // Go to the next slide
			updateSlideFractions(swiperInstance);
		}
	});

	$(document).on('click', '.slide-inner-button-prev', function() {
		var swiperContainer = $(this).closest('.swiper-slider-container');  // Find closest swiper container
		var swiperInstance = swiperContainer[0].swiper;  // Get the swiper instance from the container
		
		if (swiperInstance) {
			swiperInstance.slidePrev();  // Go to the previous slide
			updateSlideFractions(swiperInstance);
		}
	});

	setTimeout(function() {
		// Locate each .slide-inner-fraction element within active slides and update fractions in the closest swiper instance
		$('.swiper-slide-active .slide-inner-fraction').each(function() {
			var swiperContainer = $(this).closest('.swiper-slider-container');  // Find the closest swiper container
	
			var swiperInstance = swiperContainer[0]?.swiper;  // Access the swiper instance from the container
	
			if (swiperInstance) {
				// Run updateSlideFractions on the closest swiper instance
				updateSlideFractions(swiperInstance);
			}
		});
	}, 1000)


	// Function to update the fraction display inside slides using the 'aria-label' attribute
	function updateSlideFractions(swiperSlider) {
		// Loop through each slide and update the '.slide-inner-fraction' element with the content from 'aria-label'
		swiperSlider.slides.each(function (slide, index) {
			var fractionElement = $(slide).find('.slide-inner-fraction');
			var ariaLabel = $(slide).attr('aria-label');  // Get the aria-label value
			
			if (fractionElement.length && ariaLabel) {
				// Remove spaces from the ariaLabel
				var ariaLabelNoSpaces = ariaLabel.replace(/\s+/g, '');  // Using a regular expression to remove all spaces
				
				// Set the content to the aria-label value without spaces (e.g., '1/2')
				fractionElement.html(ariaLabelNoSpaces);
			}
		});
	}

});