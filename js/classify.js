jQuery(document).ready(function(){


	BioDiv.removeClick = function (){
	    jQuery('.remove_animal').click(function (){
		    id = jQuery(this).attr("id");
		    idbits = id.split("_");
		    animal_id = idbits.pop();
		    removeurl = BioDiv.root + "&task=remove_animal&format=raw&animal_id=" + animal_id;
		    jQuery('#classify_tags').load(removeurl, "", BioDiv.removeClick);
		});
		if (document.getElementById('nothingDisabled')) {
			jQuery('#control_content_86').prop('disabled', true);
		}
		else {
			jQuery('#control_content_86').prop('disabled', false);
		}
	}
	
	BioDiv.likeActions = function (){
	    jQuery('#not-favourite').click(function(){
		jQuery('#not-favourite').hide();
		jQuery('#favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		if ( !activeId ) {
			activeId = jQuery('#videoContainer').attr("data-photo-id");
		}
		var url = BioDiv.root + "&task=like_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });
		
		jQuery('#favourite').click(function(){
		jQuery('#favourite').hide();
		jQuery('#not-favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		if ( !activeId ) {
			activeId = jQuery('#videoContainer').attr("data-photo-id");
		}
		var url = BioDiv.root + "&task=unlike_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });
	}
	 
	jQuery('#photoCarousel').bind('slid.bs.carousel', function (e) {
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		var fbHref = BioDiv.root + '&view=show&photo-id=' + activeId;
		jQuery('.fb-like').attr('data-href', fbHref);
		try {
			FB.XFBML.parse();
		} catch (ex){console.log("Exception when parsing fb like");}
		
		var lastId = jQuery('#photoCarouselInner').find(".last-photo").attr("data-photo-id");
		if ( activeId == lastId ) jQuery('#control_nextseq').prop('disabled', false);
		
		url = BioDiv.root + "&task=check_like&format=raw&photo_id=" + activeId;
		
		jQuery.ajax(url, {'success': function() {
			jQuery('#like_image_container').load(BioDiv.root + '&view=like&format=raw', BioDiv.likeActions);
		}});
			
	});
	
	jQuery('#classify-video').bind('ended', function (e) {
		//console.log("video ended, enable next sequence");
		jQuery('#control_nextseq').prop('disabled',false);
	});
	
	jQuery('#classify_tags').load(BioDiv.root + '&view=tags&format=raw', BioDiv.removeClick);

	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		formData = jQuery('#classify-form').serialize();
		url = BioDiv.root + "&task=add_animal&format=raw";
		jQuery('#classify_tags').load(url, formData, BioDiv.removeClick);
		
	});


	jQuery('.filter_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		filter_id = idbits.pop();
		url = BioDiv.root + "&task=get_species&format=raw&filterid=" + filter_id;
		jQuery('#carousel-species').load(url, {'success': function() {
			    console.log ( "filter loaded" );  
			}}
			);
	});
		
	jQuery('.species_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		// for kiosk jQuery('#species_header_'+species_id).show();
		jQuery('#species_value').attr('value', species_id);
		jQuery('#classify_number').attr('value', 1);
		jQuery('#classify_gender').val(84);
		jQuery('#classify_age').val(85);
		inlist = jQuery.inArray(species_id, ["95", "96"]);
		jQuery('#species_helplet').empty();
		if(inlist<0){
		    jQuery('.species_classify').show();
		    var url = BioDiv.root + "&view=ajax&format=raw&option_id=" + species_id;
		    jQuery('#species_helplet').load(url);
			if( !jQuery('#species_helplet').length ) {
				jQuery('#species_header_'+species_id).show();
			}
		}
		else{
		    jQuery('.species_classify').hide();
		}
		
	    });

	jQuery('#fullscreen-button').click(function (){
		var photos = document.getElementById('photoCarousel');
		if("requestFullscreen" in photos) 
		{
			photos.requestFullscreen();
		} 
		else if ("webkitRequestFullscreen" in photos) 
		{
			photos.webkitRequestFullscreen();
		} 
		else if ("mozRequestFullScreen" in photos) 
		{
			photos.mozRequestFullScreen();
		} 
		else if ("msRequestFullscreen" in photos) 
		{
			photos.msRequestFullscreen();
		}
				
	});
		
	jQuery('#fullscreen-exit-button').click(function (){
		
		if(document.exitFullscreen) 
		{
			console.log("Found exitFullscreen");
			document.exitFullscreen();
		} 
		else if (document.webkitExitFullscreen) 
		{
			console.log("Found webkitExitFullscreen");
			document.webkitExitFullscreen();
		} 
		else if (document.mozCancelFullScreen) 
		{
			console.log("Found mozCancelFullScreen");
			document.mozCancelFullScreen();
		} 
		else if (document.msExitFullscreen) 
		{
			console.log("Found msExitFullscreen");
			document.msExitFullscreen();
		}
		else {
			console.log("No exit found");
			
		}		
	});
		
	jQuery('.species_header').hide();
	
	jQuery('#classify_modal').bind('shown.bs.modal', function (e) {
       jQuery('#classify-save').focus();
    });

	jQuery('#classify_modal').bind('hidden.bs.modal', function (e) {
       setTimeout(function(){
		jQuery('#photo-carousel-control-right').focus();         
		},100);
    });


	jQuery('#not-favourite').click(function(){
		jQuery('#not-favourite').hide();
		jQuery('#favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		if ( !activeId ) {
			activeId = jQuery('#videoContainer').attr("data-photo-id");
		}
		var url = BioDiv.root + "&task=like_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });



	jQuery('#favourite').click(function(){
		jQuery('#favourite').hide();
		jQuery('#not-favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		if ( !activeId ) {
			activeId = jQuery('#videoContainer').attr("data-photo-id");
		}
		var url = BioDiv.root + "&task=unlike_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });

	jQuery('.classify_control').click(function (){
		id = jQuery(this).attr("id");
		jQuery('#photo_img').fadeTo(1.0,0.1);
		url = BioDiv.root + "&task=get_photo&format=raw&action=" + id;
		jQuery.ajax(url, {'success': function() {
			    //   jQuery('#debug').load(url, {'success': function() {
				jQuery('#classify_tags').load(BioDiv.root + '&view=tags&format=raw', BioDiv.removeClick);
			    //window.location.reload(true);
			}});
		jQuery('#photo-carousel-control-right').focus();
	    });
	
	jQuery('#control_nextseq').click(function (){
		id = jQuery(this).attr("id");
		url = BioDiv.root + "&task=get_photo&format=raw&action=" + id;
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

	jQuery('#photoCarousel').click(function (){
		jQuery('#photo-carousel-control-right').focus();
		console.log("focus set");
	    });
	
	if ( !document.fullscreenEnabled ) {
		jQuery('#fullscreen-button').hide();
		jQuery('#fullscreen-exit-button').hide();
	}

	jQuery('.species-carousel-control').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#species-indicators li').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to remove favourite status', 'placement': 'bottom'});
	jQuery('#not-favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to make this one of your favourites', 'placement': 'bottom'});
	jQuery('.species-tab').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Filter list of species', 'placement': 'top'});
	jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
	jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});

	//jQuery('#fullscreen-exit-button').hide();
	
	jQuery('.sub-photo');
	
	// to test IE: jQuery('#fullscreen-exit-button').hide();
	jQuery('#photo-carousel-control-right').focus();
	
	// For sequences of more than 1 photo or for videos disable NextSequence until the user has viewed all.
	if ( document.getElementById('sub-photo-1') ) {
		jQuery('#control_nextseq').prop('disabled',true);
	}
	
	if ( document.getElementById('classify-video') ) {
		jQuery('#control_nextseq').prop('disabled',true);
	}
		
});

	
    
