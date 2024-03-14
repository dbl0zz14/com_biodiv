jQuery(document).ready(function(){
	
	//let clickedButtonId;
	
	// Survey modal will be only be present if setting is yes and user is scheduled for a survey
	jQuery('#survey_modal').modal('show');
	jQuery('#hide_partic_info').hide();
	jQuery('#partic_info').hide();
	jQuery('#consent_reminder').hide();
	jQuery('#refuse_reminder').hide();
	
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
	
	
	// jQuery(".btn").click(function() {
		// clickedButtonId = this.id;
	// } );
	
	
	// jQuery("#survey_consent_only").submit( function(e) {
		
		// let consentCheckbox = jQuery("#consent_checkbox").length;
		// if ( consentCheckbox ) {
			// //const clickedButton = jQuery('button[type="submit"]:focus');
			// //const clickedId = jQuery(this).attr("id");
			// //const clickedId = jQuery('button[type="submit"]:focus').attr("id");
			// //const clickedId = e.submitter.id;
			// //const submitter = e.submitter;
			// //const clickedId = e.originalEvent.target.id;
			// //const clickedButton = e.originalEvent.explicitOriginalTarget;
			// if ( clickedButtonId == "agree_consent" ) {
				// if ( jQuery("#consent_checkbox").prop("checked") == true ) {
					// return true;
				// }
				// else {
					// jQuery("#require_consent").addClass("well well-sm");
					// jQuery('#consent_reminder').show();
					// return false;
				// }
			// }
			// else if ( clickedButtonId == "refuse_consent" ) {
				// if ( jQuery("#consent_checkbox").prop("checked") == false ) {
					// return true;
				// }
				// else {
					// jQuery("#require_consent").addClass("well well-sm");
					// jQuery('#refuse_reminder').show();
					// return false;
				// }
			// }
		// }
		// // No consent check box 
		// return true;
	// });
	
	
	jQuery("#agree_consent").click( function() {
		
		if ( jQuery("#consent_checkbox").prop("checked") == false ) {
			jQuery("#require_consent").addClass("well well-sm");
			jQuery('#consent_reminder').show();
		}
		else {
			const surveyId = jQuery(this).attr("data-survey-id");
			const url = BioDiv.root + "&task=survey_consent_only&format=raw&survey=" + surveyId + "&consent=1";
			jQuery.ajax(url, {'success': function() {
				jQuery("#survey_modal").modal('hide');
			} } );
		}
	});
	
	
	jQuery("#refuse_consent").click( function() {
		
		if ( jQuery("#consent_checkbox").prop("checked") == true ) {
			jQuery("#require_consent").addClass("well well-sm");
			jQuery('#refuse_reminder').show();
		}
		else {
			const surveyId = jQuery(this).attr("data-survey-id");
			const url = BioDiv.root + "&task=survey_consent_only&format=raw&survey=" + surveyId + "&consent=0";
			jQuery.ajax(url, {'success': function() {
				jQuery("#survey_modal").modal('hide');
			} } );
		}
	});
	
	
	jQuery("#no_survey").click( function() {
		var surveyId = jQuery(this).attr("data-survey-id");
		var url = BioDiv.root + "&task=no_survey&format=raw&survey=" + surveyId;
		jQuery.ajax(url);
	});
	
});

	
    
