jQuery(window).on('elementor/frontend/init', function () {

	var DCF_Dates_Range_Form = function ($scope, $) {
		// Find the input fields by name attribute
		var moveInDateField = $scope.find('input[name="udfm_einzugsdatum"]');
		var moveOutDateField = $scope.find('input[name="udfm_auszugsdatum"]');

		// Initialize Flatpickr on the move-in date field
		if (moveInDateField.length) {
			moveInDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
					allowInput: true,
					onReady: function(selectedDates, dateStr, instance) {
						 // Add a custom class to the Flatpickr wrapper
						 $(instance.calendarContainer).addClass('domu-flatpikr-wrapper');
					},
					onChange: function(selectedDates, dateStr, instance) {
						// Calculate the date three months after the selected move-in date
						if (selectedDates.length > 0) {
							var minMoveOutDate = new Date(selectedDates[0]);
							minMoveOutDate.setMonth(minMoveOutDate.getMonth() + 3);

							// Update the move-out date field with the new minimum date
							if (moveOutDateField.length) {
									moveOutDateField.flatpickr({
										dateFormat: "Y-m-d",
										altInput: true,
										altFormat: "d.m.Y",
										minDate: minMoveOutDate,
										allowInput: true,
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

		// Initialize Flatpickr on the move-out date field with a default minDate of today
		if (moveOutDateField.length) {
			moveOutDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
					allowInput: true,
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
