jQuery(window).on('elementor/frontend/init', function () {

	var DCF_Gallery_Slider_Control = function ($scope, $) {

		var targetWidgetID = $scope.find('.controls-wrapper').data('target-widget-id');

		if (!targetWidgetID) {
			return;
		}

		var targetWidget = $('#' + targetWidgetID);
		var swiperContainer = $(targetWidget).find('.swiper-slider-container');  // Find the closest swiper container
		var prevEl = $scope.find('.prev-button');
		var nextEl = $scope.find('.next-button');
		var fractionEl = $scope.find('.fraction');

		var checkInterval = 100;  // Interval time in milliseconds
		var maxWaitTime = 2000;   // Maximum wait time in milliseconds
		var elapsedTime = 0;

		var interval = setInterval(function () {
			var swiperInstance = swiperContainer[0]?.swiper;  // Access the swiper instance from the container

			if (swiperInstance) {
					clearInterval(interval);  // Stop checking once swiperInstance is available
					
					// Function to update navigation button states
					function updateNavButtons() {
						if (swiperInstance.isBeginning) {
							prevEl.addClass('disabled');
						} else {
							prevEl.removeClass('disabled');
						}

						if (swiperInstance.isEnd) {
							nextEl.addClass('disabled');
						} else {
							nextEl.removeClass('disabled');
						}
					}

					// Initialize navigation buttons state on load
					updateNavButtons();

					// Update navigation buttons state on slide change
					swiperInstance.on('slideChange', updateNavButtons);

					// Navigation button click events
					$(prevEl).on('click', function() {
						swiperInstance.slidePrev();
					});

					$(nextEl).on('click', function() {
						swiperInstance.slideNext();
					});

					// Function to update fraction display
					function updateFraction() {
						var currentSlide = swiperInstance.activeIndex + 1;  // Swiper index is zero-based
						var totalSlides = swiperInstance.slides.length;
						fractionEl.text(currentSlide + ' / ' + totalSlides);
					}

					// Update fraction on load
					updateFraction();

					// Update fraction on slide change
					swiperInstance.on('slideChange', updateFraction);
			}

			elapsedTime += checkInterval;
			if (elapsedTime >= maxWaitTime) {
					clearInterval(interval);  // Stop checking after the max wait time
					console.log('swiperInstance not available after 2 seconds');
			}
		}, checkInterval);
	};

	elementorFrontend.hooks.addAction('frontend/element_ready/dcf-gallery-slider-control.default', DCF_Gallery_Slider_Control);
});
