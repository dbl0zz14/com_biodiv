

function refreshGallery () {
	
	const year = jQuery('#year_select').val();
	const species = jQuery('#species_select').val();
	const sort = jQuery('#sort_select').val();
	const numperpage = jQuery('#num_select').val();
	
	try {
		// Get current URL without query parameters
		let baseUrl = window.location.origin + window.location.pathname;

		// Create new parameters
		let params = new URLSearchParams(window.location.search);
		
		params.set("year", year); 
		params.set("species", species); 
		params.set("sort", sort); 
		params.set("number", numperpage); 
		//params.set("timestamp", Date.now()); // Example: prevent caching

		// Reload page with new parameters
		window.location.href = baseUrl + "?" + params.toString();
        } catch (err) {
            console.error("Error reloading page with parameters:", err);
        }
	
}


jQuery(document).ready(function(){
	
	jQuery('#year_select').change(refreshGallery);
	jQuery('#species_select').change(refreshGallery);
	jQuery('#sort_select').change(refreshGallery);
	jQuery('#num_select').change(refreshGallery);

});
