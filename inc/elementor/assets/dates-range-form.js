jQuery(window).on('elementor/frontend/init', function () {

	var DCF_Dates_Range_Form = function ($scope, $) {
		// Find the input fields by name attribute
		var moveInDateField = $scope.find('input[name="udfm_einzugsdatum"]');
		var moveOutDateField = $scope.find('input[name="udfm_auszugsdatum"]');

		// Function to allow only the 1st and 16th days of each month for check-in
		var AllowedCheckInDays = function(date) {
			return date.getDate() === 1 || date.getDate() === 16;
		};

		// Function to allow only the 15th and the last day of each month for check-out
		var AllowedCheckOutDays = function(date) {
			var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
			return date.getDate() === 15 || date.getDate() === lastDay;
		};

		// Manually define German locale strings
		flatpickr.l10ns.de = {
			weekdays: {
				shorthand: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
				longhand: [
					"Sonntag",
					"Montag",
					"Dienstag",
					"Mittwoch",
					"Donnerstag",
					"Freitag",
					"Samstag"
				]
			},
			months: {
				shorthand: [
					"Jan",
					"Feb",
					"Mär",
					"Apr",
					"Mai",
					"Jun",
					"Jul",
					"Aug",
					"Sep",
					"Okt",
					"Nov",
					"Dez"
				],
				longhand: [
					"Januar",
					"Februar",
					"März",
					"April",
					"Mai",
					"Juni",
					"Juli",
					"August",
					"September",
					"Oktober",
					"November",
					"Dezember"
				]
			},
			firstDayOfWeek: 1, // Monday is the first day of the week in Germany
			rangeSeparator: " bis ", // Separator for date ranges
			weekAbbreviation: "KW", // Abbreviation for "calendar week"
			scrollTitle: "Zum Ändern scrollen", // Tooltip for scrolling
			toggleTitle: "Zum Umschalten klicken", // Tooltip for toggling
			time_24hr: true // Use 24-hour time format
		};

		// Initialize Flatpickr on the move-in date field
		if (moveInDateField.length) {
			moveInDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
					allowInput: true,
					enable: [AllowedCheckInDays],
					locale: flatpickr.l10ns.de,
					onReady: function(selectedDates, dateStr, instance) {
						// Add a custom class to the Flatpickr wrapper
						$(instance.calendarContainer).addClass('domu-flatpikr-wrapper');
					},
					onChange: function(selectedDates, dateStr, instance) {
						// Calculate the date three months after the selected move-in date
						if (selectedDates.length > 0) {
							var minMoveOutDate = new Date(selectedDates[0]);
							minMoveOutDate.setMonth(minMoveOutDate.getMonth() + 3);
							minMoveOutDate.setDate(1); // Ensure it starts from the first day of the minimum month

							// Update the move-out date field with the new minimum date and enable the allowed days
							if (moveOutDateField.length) {
									moveOutDateField.flatpickr({
										dateFormat: "Y-m-d",
										altInput: true,
										altFormat: "d.m.Y",
										minDate: minMoveOutDate,
										allowInput: true,
										enable: [AllowedCheckOutDays],
										onReady: function(selectedDates, dateStr, instance) {
											// Add a custom class to the Flatpickr wrapper
											$(instance.calendarContainer).addClass('domu-flatpikr-wrapper');
										},
									});
							}
						}
					}
			});
		}

		// Initialize Flatpickr on the move-out date field with a default minDate of today and only 14th and last days selectable
		if (moveOutDateField.length) {
			moveOutDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
					allowInput: true,
					enable: [AllowedCheckOutDays],
					locale: flatpickr.l10ns.de,
					onReady: function(selectedDates, dateStr, instance) {
						// Add a custom class to the Flatpickr wrapper
						$(instance.calendarContainer).addClass('domu-flatpikr-wrapper');
					},
			});
		}

		// Remove all parameters from current query
		var form = $scope.find('form');
		var filterInitialSettings = JSON.parse($scope.attr('data-initial-settings'));

		
		$(form).on('submit', function(e) {
			e.preventDefault(); // Prevent default submit behavior initially

			var current_url = window.location.href;
			var frymo_query_raw = location.search.split('frymo_query=')[1];

			if (typeof frymo_query_raw !== 'undefined') {
				 var frymo_query = JSON.parse(decodeURIComponent(frymo_query_raw));
			} else {
				 var frymo_query = {};
			}

			// console.log('config', config);

			if (typeof filterInitialSettings.query_id === 'undefined') {
				filterInitialSettings.query_id = '';
			}


			frymo_query[filterInitialSettings.query_id] = {};
			



			if (Object.keys(frymo_query).length !== 0) {
				 var new_url = frymoReplaceUrlParam(current_url, 'frymo_query', encodeURI(JSON.stringify(frymo_query)));
			} else {
				 var new_url = frymoRemoveParameterFromUrl(current_url, 'frymo_query');
			}

			window.history.pushState(JSON.stringify(frymo_query), '', new_url);

		});

		frymoProcessSubmitingSearchFilterForm($scope);


		$(document).on('click', '.dcf-date-input-clear-icon', function (e) {
			e.preventDefault();

			// Clear the move-in date field
			moveInDateField[0]._flatpickr.clear();

			// Maybe clear query
			var frymo_query_raw = location.search.split('frymo_query=')[1];
			if (typeof frymo_query_raw !== 'undefined') {
				form.submit();
			}

		});
		

		$(document).on('click', 'a.domu-no-results-button', function (e) {
			e.preventDefault(); // Prevent default link behavior, if any

			// Find the target element and its direct child
			var target = $('.elementor-widget-dcf-dates-range-form');
			var childElement = target.find('>.elementor-widget-container'); // Direct child selector

			if (target.length && childElement.length) {
					// Get the child's offset
					var childOffsetTop = childElement.offset().top;

					// Get the CSS transform property
					var transform = childElement.css('transform'); // Example: matrix(1, 0, 0, 1, 0, -50)

					var translateY = 0; // Default translateY is 0 if no transform is applied

					if (transform && transform !== 'none') {
						// Extract the transform values
						var matrixValues = transform.match(/matrix.*\((.+)\)/);
						if (matrixValues && matrixValues[1]) {
							// Split matrix values into an array and get the 6th value (translateY in pixels)
							translateY = parseFloat(matrixValues[1].split(',')[5]); 
						}
					}

					// If the transform includes percentages (e.g., translateY(-50%)), calculate percentage manually
					if (transform.includes('%')) {
						// Get the height of the child element
						var childHeight = childElement.outerHeight(); // Height of the child element in pixels

						// Match percentage values from the `transform` property
						var percentageMatch = transform.match(/translateY\(([-\d.]+)%\)/);
						if (percentageMatch && percentageMatch[1]) {
							var percentageValue = parseFloat(percentageMatch[1]); // Extract the percentage value
							translateY = (percentageValue / 100) * childHeight; // Convert percentage to pixels
						}
					}

					// Get the padding of the child element
					var paddingTop = parseInt(childElement.css('padding-top')) || 0; // Default padding-top is 0 if not set

					// Calculate the adjusted scroll position
					var adjustedPosition = childOffsetTop + translateY - paddingTop + 20; // Add 20px for extra spacing

					// Scroll smoothly to the calculated position
					$('html, body').animate(
						{
							scrollTop: adjustedPosition, // Scroll to the adjusted position
						},
						500 // Animation duration: 500ms
					);
			} else {
					if (!target.length) {
						console.error('Target element not found: .elementor-widget-dcf-dates-range-form');
					}
					if (!childElement.length) {
						console.error('Child element not found: .elementor-widget-container');
					}
			}
		});

	};

	elementorFrontend.hooks.addAction('frontend/element_ready/dcf-dates-range-form.default', DCF_Dates_Range_Form);
});
