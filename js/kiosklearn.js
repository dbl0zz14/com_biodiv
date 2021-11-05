
var currFilterClass = null;
var currSpeciesPage = 0;



function kioskLearnSuccess () {
	
	currFilterClass = ".species";
	
	setFilterButtons();
	
	setScrollButtons();
	
	setSpeciesButtons();
	
	disableSpeciesArticleLinks();
	
	enableEmptyHelpletOnClose();
}


function setFilterButtons () {
	
	jQuery('#common_mammals').click(function (){
		
		currFilterClass = ".common_mammal";
		currSpeciesPage = 0;
		displaySpecies(currSpeciesPage);
		
	});
	jQuery('#all_mammals').click(function (){
		
		currFilterClass = ".mammal";
		currSpeciesPage = 0;
		displaySpecies(currSpeciesPage);
		
	});
	jQuery('#common_birds').click(function (){
		
		currFilterClass = ".common_bird";
		currSpeciesPage = 0;
		displaySpecies(currSpeciesPage);
		
	});
	jQuery('#all_birds').click(function (){
		
		currFilterClass = ".bird";
		currSpeciesPage = 0;
		displaySpecies(currSpeciesPage);
		
	});
	jQuery('#all_species').click(function (){
		
		currFilterClass = ".species";
		currSpeciesPage = 0;
		displaySpecies(currSpeciesPage);
		
	});
}


function setScrollButtons () {
	
	
	
	jQuery('#scroll_up_species').click(function (){
		displaySpecies( -1 );
	});
	
	jQuery('#scroll_down_species').click(function (){
		displaySpecies( 1 );
	});
	
}

function setSpeciesButtons () {
	
	jQuery('.learn-species-btn').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		//jQuery('.species_header').hide();
		//jQuery('#species_value').attr('value', species_id);
		
		jQuery('#learn_species_helplet').empty();
		jQuery('#learn_species_modal').show();
		var url = BioDiv.root + "&view=ajax&format=raw&option_id=" + species_id;
		jQuery('#learn_species_helplet').load(url);
		
	});
	
	jQuery('.learn-species-audio-btn').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		//jQuery('.species_header').hide();
		//jQuery('#species_value').attr('value', species_id);
		
		jQuery('#learn_species_helplet').empty();
		jQuery('#learn_species_modal').show();
		var url = BioDiv.root + "&view=kioskajaxaudio&format=raw&option_id=" + species_id;
		jQuery('#learn_species_helplet').load(url, audioSpeciesLoaded);
		
	});
}

function audioSpeciesLoaded () {
	if ( jQuery('.audio-species-sono').length > 0 ) {
		jQuery('iframe').remove();
	}
}

function disableSpeciesArticleLinks () {
	// Ensure that no hyperlinks can be clicked in kiosk mode
	jQuery('#learn_species_helplet').on('click', 'a', function(e) {
		e.preventDefault();
		console.log(jQuery(this).attr('href'));
	});
}

function enableEmptyHelpletOnClose () {
	
	jQuery('#learn_species_modal').on('hidden.bs.modal', function () {
		jQuery('#learn_species_helplet').empty();
	})
}


function displaySpecies ( page ) {
	
	let pageLength = 18;
	
	jQuery('.species').hide();
	
	let filterSpecies = jQuery(currFilterClass);
	
	let totalSpecies = filterSpecies.length;
	let numPages = Math.ceil(totalSpecies/pageLength);
		
	if ( page == 0 ) currSpeciesPage = 0;
	else if ( page == -1 ){
		currSpeciesPage = Math.max(0, currSpeciesPage-1);
	}
	else if ( page == 1 ) {
		currSpeciesPage = Math.min(currSpeciesPage+1, numPages );
	}
	
	let start = currSpeciesPage*pageLength;
	
	filterSpecies.slice(start, start + pageLength).show();
	
	if ( start == 0 ) {
		jQuery("#scroll_up_species").prop('disabled', true);
	}
	else {
		jQuery("#scroll_up_species").prop('disabled', false);
	}
	
	if ( start + pageLength >= totalSpecies ) {
		jQuery("#scroll_down_species").prop('disabled', true);
	}
	else {
		jQuery("#scroll_down_species").prop('disabled', false);
	}
	
}
	
    
