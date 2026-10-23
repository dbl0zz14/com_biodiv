


jQuery(document).ready(function(){
	
	const maxClassifications = BioDiv.maxclass;
	const loadingMsg = BioDiv.loadingMsg;
	
	const eggsId = BioDiv.eggsId;
	const nestId = BioDiv.nestId;
	
	const numSpeciesPerPage = 36;
	
	

	removeClicks = function (){
		jQuery('.remove_animal').click(function (){
		    const id = jQuery(this).attr("id");
		    const idbits = id.split("_");
		    const animal_id = idbits.pop();
		    //const removeurl = BioDiv.root + "&task=remove_animal_single_tag&format=raw";
			const removeurl = BioDiv.root + "&task=remove_animal_single_tag&format=raw&animal_id=" + animal_id;
			//parentEl = document.getElementById('remove_animal_' + animal_id).parentElement.id;
			//let parenttag = jQuery('#remove_animal_' + animal_id).parent();
			//parenttag.load(removeurl, "", BioDiv.removeClick);
			//parenttag.load(removeurl);
			//parenttag.remove();
			let fd = new FormData();
			fd.append( 'animal_id', animal_id );
			jQuery.post(removeurl, null, function( data ) {
				
				const result = JSON.parse(data);
				if ( result.success ) {
					const removedAnimal = result.data.removedAnimal;
					const parenttag = jQuery('#remove_animal_' + removedAnimal).parent();
					parenttag.remove();
				}
				else {
					console.log ( result.message );
				}
				BioDiv.removeClick();
			});
		});
					
		if (document.getElementById('nothingDisabled')) {
			jQuery('.nothing').prop('disabled', true);
		}
		else {
			jQuery('.nothing').prop('disabled', false);
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
	
	
	function setReportModal () {
		
		const modal = new bootstrap.Modal('#report_media_modal');

		document.querySelector('#report_media').addEventListener('click', () => {
			modal.toggle();
		});
	}
	
	
	addClassificationByForm = function () {
		// Check still logged in
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			const timedOutModal = new bootstrap.Modal(document.getElementById('timed_out_modal'));
			timedOutModal.show();
			jQuery('#control_nextseq').prop('disabled', false);
			return;
		}
		
		// Check max classifications not yet reached
		const numClass = jQuery(".remove_animal").length;
		
		if ( numClass == maxClassifications ) {
			console.log("Error: reached max classifications - " + maxClassifications);
			jQuery('#too_many_modal').modal('show');
			return;
		}
		
		// OK to add a new classn
		formData = jQuery('#classify-form').serialize();
		url = BioDiv.root + "&task=add_animal_single_tag&format=raw";
		
		const nextNum = numClass + 1;
		const newDivId = "classification_" + nextNum;
		
		// Create a new div
		const tags = jQuery("#classify_tags").append("<div class='tagcontainer singletag-classification'></div>" );
		
		// And load the button
		//jQuery('.tagcontainer').last().post(url, formData, BioDiv.removeClick);
		jQuery.post(url, formData, function( data ) {
			jQuery('.tagcontainer').last().html( data );
			BioDiv.removeClick();
		});
		
		jQuery('.nothing-classification').remove();
		
	}
	
	addNothingClassificationByForm = function () {
		// Check still logged in
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			const timedOutModal = new bootstrap.Modal(document.getElementById('timed_out_modal'));
			timedOutModal.show();
			jQuery('#control_nextseq').prop('disabled', false);
			return;
		}
		
		// Check max classifications not yet reached
		const numClass = jQuery(".remove_animal").length;
		
		if ( numClass == maxClassifications ) {
			console.log("Error: reached max classifications - " + maxClassifications);
			jQuery('#too_many_modal').modal('show');
			return;
		}
		
		// OK to add a new classn
		formData = jQuery('#classify-nothing-form').serialize();
		url = BioDiv.root + "&task=add_animal_single_tag&format=raw";
		
		const nextNum = numClass + 1;
		const newDivId = "classification_" + nextNum;
		
		// Create a new div
		const tags = jQuery("#classify_tags").append("<div class='tagcontainer singletag-classification'></div>" );
		
		// And load the button
		//jQuery('.tagcontainer').last().post(url, formData, BioDiv.removeClick);
		jQuery.post(url, formData, function( data ) {
			jQuery('.tagcontainer').last().html( data );
			BioDiv.removeClick();
		});
		
	}
	
	addClassificationById = function ( id ) {
		// Check still logged in
		if ( document.getElementById('no_user_id') ) {
			console.log("Timed out need to log in again");
			jQuery('#timed_out_modal').modal('show');
			jQuery('#control_nextseq').prop('disabled', false);
			return;
		}
		
		// Check max classifications not yet reached
		const numClass = jQuery(".remove_animal").length;
		
		if ( numClass == maxClassifications ) {
			console.log("Error: reached max classifications - " + maxClassifications);
			jQuery('#too_many_modal').modal('show');
			return;
		}
		
		const photoId = jQuery("#currPhotoId").val();
		
		// OK to add a new classn
		const url = BioDiv.root + "&task=add_animal_single_tag&format=raw&species=" + id + "&photo_id=" + photoId;
		
		const nextNum = numClass + 1;
		const newDivId = "classification_" + nextNum;
		
		// Create a new div
		const tags = jQuery("#classify_tags").append("<div class='tagcontainer singletag-classification'></div>" );
		
		// And load the button
		//jQuery('.tagcontainer').last().load(url, BioDiv.removeClick);
		
		// Make sure form has the species id
		jQuery('#species_value').attr('value', id);

		const formData = jQuery('#classify-form').serialize();
		jQuery.post(url, formData, function( data ) {
			jQuery('.tagcontainer').last().html( data );
		});
		
		if ( id != "86" ) jQuery('.nothing-classification').remove();
		
	}
	
	jQuery('.species_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#species_value').attr('value', species_id);
		
		// Reset the defaults
		jQuery('#classify_number').attr('value', 1);
		checkRadioDefault ('gender');
		checkRadioDefault ('age');
		checkRadioDefault ('sure');
		jQuery('#classify_notes').val('');
		
		jQuery('#species_helplet').empty();
		jQuery('.species_classify').show();
		
		if ( species_id == eggsId ) {
			
			let firstInput = jQuery("input[name='gender']").first();
			let firstParent = firstInput.parents('.species_classify').first();
			firstParent.hide();
			
			firstInput = jQuery("input[name='age']").first();
			firstParent = firstInput.parents('.species_classify').first();
			firstParent.hide();
		}
		if ( species_id == nestId ) {
			
			let firstInput = jQuery("input[name='gender']").first();
			let firstParent = firstInput.parents('.species_classify').first();
			firstParent.hide();
			
			firstInput = jQuery("input[name='age']").first();
			firstParent = firstInput.parents('.species_classify').first();
			firstParent.hide();
			
			firstInput = jQuery("input[name='number']").first();
			firstParent = firstInput.parents('.species_classify').first();
			firstParent.hide();
		}
		
		var url = BioDiv.root + "&view=article&format=raw&option_id=" + species_id + "&title=1";
		jQuery('#species_helplet').load(url);
		
	});
	
	
	jQuery('.nothing').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#nothing_species_value').attr('value', species_id);
		
		jQuery("#classify_notes_nothing").prop("selectedIndex", 0);

        console.log ( "species_id = " + species_id );
        var url = BioDiv.root + "&view=article&format=raw&option_id=" + species_id ;
        jQuery('#nothing_helplet').load(url);
		
	});
	
	


	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		
		addClassificationByForm();
		
	});

		
	
	jQuery('#classify-save-nothing').click(function (){
		jQuery('#classify_nothing_modal').modal('hide');
		
		addNothingClassificationByForm();
		
	});

		
	
	jQuery('.classify_control').click(function (){
		
		//let id = jQuery(this).attr("id");
		
		//addClassificationById (id);
		
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		addClassificationById(species_id);
		
	});
	
	jQuery('#control_nextseq').click(function (){
	
		// Add loading indications
		jQuery(".loader").removeClass("invisible");
		jQuery(this).text(loadingMsg);
		jQuery(".loader").removeClass('invisible');
		
		id = jQuery(this).attr("id");
		var sideBarToggled = jQuery('#wrapper').is(".toggled");
		var extra = "";
		if ( sideBarToggled ) extra = "&toggled=" + "1";
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
	
	displaySpeciesPage = function ( start, len ) {
		
		jQuery(".species_group").hide();
		
		let displaySlice = jQuery(".tab-pane.active .match").slice(start, start + len);
		displaySlice.show();
		
		jQuery(".tab-pane.active .alwaysmatch").show();
		
		jQuery('.pagination li').removeClass('active');
		// NB have prev and next buttons too
		var activeChild = Math.round(start/len) + 2;
		jQuery('.pagination li:nth-child(' + activeChild + ')').addClass('active');
		
		// How many pages are there in the match list?
		let numMatches = jQuery(".tab-pane.active .match").length;
		let numPages = Math.ceil(numMatches/len);
		jQuery('.last-page').removeClass('last-page');
		
		if ( numPages == 1 ) {
			jQuery('.species_pagination').hide();
		}
		else {
			let lastDisplayedChild = parseInt(numPages+1);
			jQuery('.pagination li:nth-child(' + lastDisplayedChild + ')').addClass('last-page');
			
			jQuery('.species_pagination').show();
		
			// Show all then hide any pagination controls we don't need.
			jQuery('.pagination li').show();
			let formula = 'n + ' + parseInt(numPages+2);
			jQuery('.pagination li:nth-child(' + formula + ')').hide();
			jQuery('.next-page').show();
		}
		
		// Hide the blank padding cell if even number of matches
		let numDisplayed = displaySlice.length;
		if ( numDisplayed % 2 == 0 ) jQuery('.tab-pane.active .species_group_blank').hide();
		else jQuery('.tab-pane.active .species_group_blank').show();
	}
	
	// Filter the species list on search
	jQuery("#search_species").on("keyup", function() {
		var value = jQuery(this).val().toLowerCase();
		
		jQuery(".species_select_name").filter(function() {
		  toToggle = jQuery(this).text().toLowerCase().indexOf(value) > -1;
		  //jQuery(this).parent().toggle(toToggle)
		  // Add class to parent of non matching elements
		  if ( toToggle ) {
			  jQuery(this).parent().addClass ('match');
		  }
		  else {
			  jQuery(this).parent().removeClass ('match');
		  }
		});
		
		displaySpeciesPage( 0, numSpeciesPerPage );
		
	});
	
	jQuery('.species-tab').on('shown.bs.tab', function (e) {
		displaySpeciesPage( 0, numSpeciesPerPage );
  
	});
	
	jQuery('.pagination li').click(function (){
		let pageText = null;
		let page = null;
		let currPage = null;
		if ( jQuery(this).hasClass('prev-page') ) {
			pageText = jQuery('.pagination li.active').text();
			page = parseInt(pageText) -2;
			if ( page < 0 ) page = 0;
		}
		else if ( jQuery(this).hasClass('next-page') ) {
			pageText = jQuery('.pagination li.active').text();
			page = parseInt(pageText);
			
			let lastPageText = jQuery('.last-page').text();
			let lastPage = parseInt(lastPageText);
			if ( page == lastPage ) page = lastPage-1;
			
		}
		else {
			pageText = jQuery(this).text();
			page = parseInt(pageText) -1;
		}
		displaySpeciesPage( page*numSpeciesPerPage, numSpeciesPerPage );
	
	});
	
	
	// jQuery('#report_media').click(function (){
		
		// const modal = new bootstrap.Modal('#report_media_modal');

		// document.querySelector('#modalBtn').addEventListener('click', () => {
			// modal.toggle();
		// });
		
	// });
	
	jQuery('#report_media_save').click(function (){
		
		formData = jQuery('#reportMediaForm').serialize();
		
		url = BioDiv.root + "&task=add_report_media&format=raw";
		
		// And send the report
		jQuery.post(url, formData);
		//jQuery('#reportMediaDone').load(url, formData);
		
		// And disable the challenge button
		jQuery('#report_media').attr("disabled", true);
		
	});

	// Add any remove click functions on refresh.
	removeClicks();
	
	setReportModal ();
	
	displaySpeciesPage( 0, numSpeciesPerPage );
	


});

	
    
