jQuery(document).ready(function(){
	
	jQuery("#no_survey").click( function() {
		var surveyId = jQuery(this).attr("data-survey-id");
		var url = BioDiv.root + "&task=no_survey&format=raw&survey=" + surveyId;
		jQuery.ajax(url);
	});
	
});

	
    
