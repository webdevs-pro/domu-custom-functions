jQuery(document).ready(function($) {
	// Select the container with the specific ID
	var $container = $('#appartments-listing');

	// Listen for clicks on links within this container
	$container.on('click', 'a', function(event) {
	event.preventDefault(); // Prevent the default link click behavior

	// Retrieve the current URL parameter `frymo_query`
	var urlParams = new URLSearchParams(window.location.search);
	var frymoQuery = urlParams.get('frymo_query');

	// Only modify the URL if `frymo_query` is found
	if (frymoQuery) {
		// Create a new URL object from the clicked link's href
		var linkUrl = new URL($(this).attr('href'), window.location.origin);

		// Append the `frymo_query` parameter to the link's URL
		linkUrl.searchParams.set('frymo_query', frymoQuery);

		// Redirect to the modified link URL
		window.location.href = linkUrl.toString();
	} else {
		// If no `frymo_query` is found, follow the original link
		window.location.href = $(this).attr('href');
	}
	});
});
