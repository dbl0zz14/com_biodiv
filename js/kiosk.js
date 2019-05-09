jQuery(document).ready(function(){


	removeClicks = function (){
	    jQuery('.remove_animal').click(function (){
		    id = jQuery(this).attr("id");
		    idbits = id.split("_");
		    animal_id = idbits.pop();
		    removeurl = BioDiv.root + "&task=remove_animal_single_tag&format=raw&animal_id=" + animal_id;
			parentEl = document.getElementById('remove_animal_' + animal_id).parentElement.id;
		    if ( parentEl == 'first_classification' ) {
				jQuery('#first_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'second_classification' ) {
				jQuery('#second_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else if ( parentEl == 'third_classification'  ) {
				jQuery('#third_classification').load(removeurl, "", BioDiv.removeClick);
			}
			else {
				console.log("Error unexpected parent: parent element id = " + parentEl);
			}
		});
		if (document.getElementById('nothingDisabled')) {
			jQuery('#control_content_86').prop('disabled', true);
		}
		else {
			jQuery('#control_content_86').prop('disabled', false);
		}
	}
	
	BioDiv.removeClick = function (){
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
		}
		else {
			removeClicks();
		}
	}

	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		formData = jQuery('#classify-form').serialize();
		url = BioDiv.root + "&task=add_animal_single_tag&format=raw";
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
		}
		// How many animals do we have so far?
		else if ( document.getElementById('first_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#first_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('second_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#second_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('third_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#third_classification').load(url, formData, BioDiv.removeClick);
			jQuery('.nothing-classification').remove();
		}
		else {
			console.log("Error: already have three classifications and a fourth requested");
			jQuery('#too_many_modal').modal('show');
		}
		//jQuery('#classify_tags').load(url, formData, BioDiv.removeClick);
		
	});


	jQuery('.classify_control').click(function (){
		id = jQuery(this).attr("id");
		url = BioDiv.root + "&task=add_animal_single_tag&format=raw&species=" + id;
		// How many animals do we have so far?
		if ( document.getElementById('first_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#first_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('second_classification').getElementsByClassName("remove_animal").length == 0 ) {
			jQuery('#second_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else if (document.getElementById('third_classification').getElementsByClassName("remove_animal").length == 0  ) {
			jQuery('#third_classification').load(url, BioDiv.removeClick);
			if ( id != "86" ) jQuery('.nothing-classification').remove();
		}
		else {
			console.log("Error: already have three classifications and a fourth requested");
			jQuery('#too_many_modal').modal('show');
		}
	});	
	
	// Add any remove click functions on refresh.
	removeClicks();
	
});

	
    
