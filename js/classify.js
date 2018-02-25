jQuery(document).ready(function(){

	BioDiv.removeClick = function (){
	    console.log("Adding remove click actions");
	    jQuery('.remove_animal').click(function (){
		    id = jQuery(this).attr("id");
		    idbits = id.split("_");
		    animal_id = idbits.pop();
		    console.log("Removing animal classification " + animal_id);
		    removeurl = BioDiv.root + "&task=remove_animal&format=raw&animal_id=" + animal_id;
		    jQuery('#classify_tags').load(removeurl, "", BioDiv.removeClick);
		    
		});

	}
	
	jQuery('#photoCarousel').bind('slid.bs.carousel', function (e) {
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");;
		BioDiv.curr_photo = activeId;
	    console.log("Current photo = " + activeId);
		
		jQuery('#currPhotoId').val(activeId);
		
		// check direction on the event and call url accordingly
		// only call next (which copies the classification) if going forwards
		var urlAction = "control_next";
		if ( e.direction != "left" ) {
			urlAction = "control_goto&photo=" + activeId;
		}
		url = BioDiv.root + "&task=get_photo&format=raw&action=" + urlAction;
		
		jQuery.ajax(url, {'success': function() {
				jQuery('#classify_tags').load(BioDiv.root + '&view=tags&format=raw', BioDiv.removeClick);
			}});
		
	});
	
	jQuery('#classify_tags').load(BioDiv.root + '&view=tags&format=raw', BioDiv.removeClick);

	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		formData = jQuery('#classify-form').serialize();
		url = BioDiv.root + "&task=add_animal&format=raw";
		jQuery('#classify_tags').load(url, formData, BioDiv.removeClick);
	    });


	jQuery('.species_select').click(function (){
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#species_header_'+species_id).show();
		jQuery('#species_value').attr('value', species_id);
		jQuery('#classify_number').attr('value', 1);
		inlist = jQuery.inArray(species_id, ["95", "96"]);
		jQuery('#species_helplet').empty();
		if(inlist<0){
		    jQuery('.species_classify').show();
		    var url = BioDiv.root + "&view=ajax&format=raw&option_id=" + species_id;
		    jQuery('#species_helplet').load(url);
		}
		else{
		    jQuery('.species_classify').hide();
		}

	    });

	jQuery('.species_header').hide();

	jQuery('#not-favourite').click(function(){
		jQuery('#not-favourite').hide();
		jQuery('#favourite').show();
		var url = BioDiv.root + "&task=like_photo";
		jQuery.ajax(url);
	    });



	jQuery('#favourite').click(function(){
		jQuery('#favourite').hide();
		jQuery('#not-favourite').show();
		var url = BioDiv.root + "&task=unlike_photo";
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
		
	    });
	
	jQuery('#control_nextseq').click(function (){
		id = jQuery(this).attr("id");
		console.log("About to call next_sequence");
		url = BioDiv.root + "&task=get_photo&format=raw&action=" + id;
		jQuery.ajax(url, {'success': function() {
			    window.location.reload(true);
				console.log("Next sequence success");
			}});
		
	    });

	

	jQuery('.species-carousel-control').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#species-indicators li').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to remove favourite status', 'placement': 'bottom'});
	jQuery('#not-favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to make this one of your favourites', 'placement': 'bottom'});

    });
    
