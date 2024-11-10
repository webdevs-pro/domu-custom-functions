jQuery(window).on('elementor/frontend/init', function () {

	var DCF_Dates_Range_Form = function ($scope, $) {
		// Find the input fields by name attribute
		var moveInDateField = $scope.find('input[name="udfm_einzugsdatum"]');
		var moveOutDateField = $scope.find('input[name="udfm_auszugsdatum"]');

		// Function to enable only the first day of each month
		var enableFirstDayOfMonth = function(date) {
			return date.getDate() === 1;
		};

		// Function to enable only the last day of each month
		var enableLastDayOfMonth = function(date) {
			var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
			return date.getDate() === lastDay.getDate();
		};

		// Initialize Flatpickr on the move-in date field
		if (moveInDateField.length) {
			moveInDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
					// defaultDate: new Date().setDate(1), // Set the calendar to open at the first day of the month
					allowInput: true,
					enable: [enableFirstDayOfMonth],
					onReady: function(selectedDates, dateStr, instance) {
						// Add a custom class to the Flatpickr wrapper
						$(instance.calendarContainer).addClass('domu-flatpikr-wrapper');
					},
					onChange: function(selectedDates, dateStr, instance) {
						// Calculate the date three months after the selected move-in date
						if (selectedDates.length > 0) {
							var minMoveOutDate = new Date(selectedDates[0]);
							minMoveOutDate.setMonth(minMoveOutDate.getMonth() + 3);
							minMoveOutDate.setDate(new Date(minMoveOutDate.getFullYear(), minMoveOutDate.getMonth() + 1, 0).getDate()); // Set to last day of the month

							// Update the move-out date field with the new minimum date
							if (moveOutDateField.length) {
									moveOutDateField.flatpickr({
										dateFormat: "Y-m-d",
										altInput: true,
										altFormat: "d.m.Y",
										minDate: minMoveOutDate,
										allowInput: true,
										enable: [enableLastDayOfMonth],
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

		// Initialize Flatpickr on the move-out date field with a default minDate of today and only last days selectable
		if (moveOutDateField.length) {
			moveOutDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
					allowInput: true,
					enable: [enableLastDayOfMonth],
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
