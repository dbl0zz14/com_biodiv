
function reloadCurrentPage () {
	window.location.reload(true);
}
	

function setReloadPage () {	
	jQuery ('.reloadPage').click( function () {
		reloadCurrentPage();
	});
}

	
	
jQuery(document).ready(function(){
	
	setReloadPage();
});
