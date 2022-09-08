
function uploadDone () {
	reloadCurrentPage();
}

jQuery(document).ready(function(){
	
	resourceListLoaded();
	
	jQuery("#errorsModal").modal('show');

});
