jQuery(window).on('elementor/frontend/init', function () {

	var DCF_Dates_Range_Form = function ($scope, $) {
		 // Find the input fields by name attribute
		 var moveInDateField = $scope.find('input[name="einzugsdatum"]');
		 var moveOutDateField = $scope.find('input[name="auszugsdatum"]');

		 // Initialize Flatpickr on the move-in date field
		 if (moveInDateField.length) {
			  moveInDateField.flatpickr({
					dateFormat: "Y-m-d",
					altInput: true,
					altFormat: "d.m.Y",
					minDate: "today",
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
										 minDate: minMoveOutDate
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
					minDate: "today"
			  });
		 }
	};

	elementorFrontend.hooks.addAction('frontend/element_ready/dcf-dates-range-form.default', DCF_Dates_Range_Form);
});
