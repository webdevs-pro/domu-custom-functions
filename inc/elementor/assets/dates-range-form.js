jQuery(window).on('elementor/frontend/init', function () {

	var DCF_Dates_Range_Form = function ($scope, $) {
		// Find the input fields by name attribute
		var moveInDateField = $scope.find('input[name="udfm_einzugsdatum"]');
		var moveOutDateField = $scope.find('input[name="udfm_auszugsdatum"]');

		// Function to allow only the 1st and 14th days of each month for check-in
		var AllowedCheckInDays = function(date) {
			return date.getDate() === 1 || date.getDate() === 15;
		};

		// Function to allow only the 14th and the last day of each month for check-out
		var AllowedCheckOutDays = function(date) {
			var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
			return date.getDate() === 14 || date.getDate() === lastDay;
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
					onReady: function(selectedDates, dateStr, instance) {
						// Add a custom class to the Flatpickr wrapper
						$(instance.calendarContainer).addClass('domu-flatpikr-wrapper');
					},
			});
		}

		frymoProcessSubmitingSearchFilterForm($scope);
	};

	elementorFrontend.hooks.addAction('frontend/element_ready/dcf-dates-range-form.default', DCF_Dates_Range_Form);
});
