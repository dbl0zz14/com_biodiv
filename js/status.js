jQuery(document).ready(function(){
	
	
	const loadingMsg = BioDiv.loadingMsg;
	
	// Show a message while the image/video/audio loads
	jQuery(".classify_btn").click(function (){
		jQuery(this).text(String(loadingMsg));
		jQuery(".loader").removeClass('invisible');
			
	});
	
	// Survey modal will be only be present if setting is yes and user is scheduled for a survey
	jQuery('#survey_modal').modal('show');
	jQuery('#hide_partic_info').hide();
	jQuery('#partic_info').hide();
	jQuery('#consent_reminder').hide();
	
	jQuery('#show_partic_info').click(function (){
		jQuery('#partic_info').show();
		jQuery('#hide_partic_info').show();
		jQuery(this).hide();
			
	});
	
	jQuery('#hide_partic_info').click(function (){
		jQuery('#partic_info').hide();
		jQuery('#show_partic_info').show();
		jQuery(this).hide();
			
	});
	
	
	jQuery("#take_survey").submit( function() {
		
		var consentCheckbox = jQuery("#consent_checkbox").length;
		if ( consentCheckbox ) {
			if ( jQuery("#consent_checkbox").prop("checked") == true) {
				return true;
			}
			else {
				jQuery("#require_consent").addClass("well well-sm");
				jQuery('#consent_reminder').show();
				return false;
			}
		}
		// No consent check box (follow up survey)
		return true;
	});
	
	
	jQuery("#no_survey").click( function() {
		var surveyId = jQuery(this).attr("data-survey-id");
		var url = BioDiv.root + "&task=no_survey&format=raw&survey=" + surveyId;
		jQuery.ajax(url);
	});
	
});

	
    
