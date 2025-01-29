jQuery(document).ready(function(){
	
	addResultsHandlers();
	
	addFullScreenFnly();
	
	jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
	jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});
	
});

	
    
