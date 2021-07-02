
var timeoutInMiliseconds = 60000;
var timeoutId; 
  
function startTimer() { 
    // window.setTimeout returns an Id that can be used to start and stop a timer
    timeoutId = window.setTimeout(doInactive, timeoutInMiliseconds)
}
  
function doInactive() {
    // does whatever you need it to actually do - probably signs them out or stops polling the server for info
	console.log("doInactive called");
	/*
	var projectId = jQuery('#page-content-wrapper').attr("data-project-id");
	var url = BioDiv.root + "&view=startkiosk&project_id=" + projectId;
	var userKey = jQuery('#page-content-wrapper').attr("data-user-key");
	url += "&user_key=" + userKey;
	window.location.href = "" + url;
	*/
	var projectId = jQuery('#page-content-wrapper').attr("data-project-id");
	var url = BioDiv.root + "&task=kiosk_timeout_v1&project_id=" + projectId;
	var userKey = jQuery('#page-content-wrapper').attr("data-user-key");
	url += "&user_key=" + userKey;
	url += "&" + userKey;
	//jQuery.get(url);
	window.location.href = "" + url;
	
}
 
function setupTimers () {
    document.addEventListener("mousemove", resetTimer, false);
    document.addEventListener("mousedown", resetTimer, false);
    document.addEventListener("keypress", resetTimer, false);
	document.addEventListener("touchstart", resetTimer, false);
    document.addEventListener("touchmove", resetTimer, false);
     
    startTimer();
}

function resetTimer() { 
    window.clearTimeout(timeoutId)
    startTimer();
}


jQuery(document).ready(function(){


	removeClicks = function (){
	    jQuery('.remove_animal').click(function (){
			resetTimer();
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
	
	jQuery('#control_nextseq').click(function (){
	resetTimer()
	id = jQuery(this).attr("id");
	var sideBarToggled = jQuery('#wrapper').is(".toggled");
	var extra = "";
	if ( sideBarToggled ) extra = "&toggled=" + "1";
	var currCount = parseInt(jQuery("#page-content-wrapper").attr("data-classify-count"));
	if ( document.getElementsByClassName("remove_animal").length > 0 ) {
		currCount += 1;
	}
	extra += "&classify_count=" +  currCount;
	var projectId = parseInt(jQuery("#page-content-wrapper").attr("data-project-id"));
	extra += "&project_id=" +  projectId;
	url = BioDiv.root + "&task=get_photo&format=raw&action=" + id + extra;
	jQuery.ajax(url, {'success': function() {
			window.location.reload(true);
			if (document.getElementById('sub-photo-1')) {
				jQuery('#control_nextseq').prop('disabled', true);
			}
			else {
				jQuery('#control_nextseq').prop('disabled', false);
			}
		}});
	
	});
	
	jQuery('.species_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#species_value').attr('value', species_id);
		jQuery('#classify_number').attr('value', 1);
		jQuery('#classify_gender').val(84);
		jQuery('#classify_age').val(85);
		
		
		jQuery('#species_helplet').empty();
		jQuery('.species_classify').show();
		var url = BioDiv.root + "&view=ajax&format=raw&option_id=" + species_id;
		jQuery('#species_helplet').load(url);
		
		// Ensure that no hyperlinks can be clicked in kiosk mode
		jQuery('#species_helplet').on('click', 'a', function(e) {
			e.preventDefault();
			console.log(jQuery(this).attr('href'));
		});
	
	});

	jQuery('#classify-save').click(function (){
		resetTimer();
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
		resetTimer();
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
	
	jQuery(".carousel").on("touchstart", function(event){
        var xClick = event.originalEvent.touches[0].pageX;
		jQuery(this).one("touchmove", function(event){
			var xMove = event.originalEvent.touches[0].pageX;
			if( Math.floor(xClick - xMove) > 5 ){
				jQuery(this).carousel('next');
			}
			else if( Math.floor(xClick - xMove) < -5 ){
				jQuery(this).carousel('prev');
			}
    });
    jQuery(".carousel").on("touchend", function(){
            jQuery(this).off("touchmove");
		});
	});
	
	// Hide sidebar if the wrapper is toggled
	/*
	if ( document.getElementById("wrapper").getElementsByClassName("toggled").length > 0 ) {
		jQuery('slide-out-tab').click();
	}
	*/
	
	var projectId = jQuery('#page-content-wrapper').attr("data-project-id");
	if ( projectId == 20 ) {
		jQuery('.view-kiosk > body').css({"zoom": "0.9"});
	}

	// Add any remove click functions on refresh.
	removeClicks();
	
	setupTimers();
	
	// Disable pinch zoom
	/*
	document.addEventListener('touchmove', function(event){ 
		if ( event.touches.length === 2 ) {
			event.stopPropagation(); 
			event.preventDefault(); 
		}
	},
	{passive: false}
	);
	*/
	
	// Remove attribution links - or any links in the species-helplet
	
	
	// Stop kiosk users right clicking using long press
	document.addEventListener('contextmenu', event => event.preventDefault());
	
});

	
    
