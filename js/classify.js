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
		console.log("remove click actions added" );
		console.log("Setting Nothing" );
		if (document.getElementById('nothingDisabled')) {
			console.log("...to disabled" );
			jQuery('#control_content_86').prop('disabled', true);
		}
		else {
			console.log("...to enabled" );
			jQuery('#control_content_86').prop('disabled', false);
		}
	}
	
	BioDiv.likeActions = function (){
	    console.log("Adding like actions");
		jQuery('#not-favourite').click(function(){
		jQuery('#not-favourite').hide();
		jQuery('#favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		var url = BioDiv.root + "&task=like_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });
		
		jQuery('#favourite').click(function(){
		jQuery('#favourite').hide();
		jQuery('#not-favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		var url = BioDiv.root + "&task=unlike_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });
	}
	
	jQuery('#photoCarousel').bind('slid.bs.carousel', function (e) {
		// Need to change the facebook href to use the new photo_id
		//console.log("Got slid event");
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		var fbHref = BioDiv.root + '&view=show&photo-id=' + activeId;
		//console.log( "Updating fb link to " + fbHref );
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
	
	jQuery('#classify_tags').load(BioDiv.root + '&view=tags&format=raw', BioDiv.removeClick);

	jQuery('#classify-save').click(function (){
		jQuery('#classify_modal').modal('hide');
		formData = jQuery('#classify-form').serialize();
		url = BioDiv.root + "&task=add_animal&format=raw";
		jQuery('#classify_tags').load(url, formData, BioDiv.removeClick);
		
	    });


	jQuery('.species_select').click(function (){
		console.log("species select clicked");
		id = jQuery(this).attr("id");
		idbits = id.split("_");
		species_id = idbits.pop();
		jQuery('.species_header').hide();
		jQuery('#species_header_'+species_id).show();
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
		}
		else{
		    jQuery('.species_classify').hide();
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
		var url = BioDiv.root + "&task=like_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });



	jQuery('#favourite').click(function(){
		jQuery('#favourite').hide();
		jQuery('#not-favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
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
		console.log("About to call next_sequence");
		url = BioDiv.root + "&task=get_photo&format=raw&action=" + id;
		jQuery.ajax(url, {'success': function() {
			    window.location.reload(true);
				console.log("Next sequence success");
				if (document.getElementById('sub-photo-1')) {
					console.log("more than one photo" );
					jQuery('#control_nextseq').prop('disabled', true);
				}
				else {
					console.log("only one photo in sequence" );
					jQuery('#control_nextseq').prop('disabled', false);
				}
			}});
		
	    });

	jQuery('#photoCarousel').click(function (){
		jQuery('#photo-carousel-control-right').focus();
		console.log("focus set");
	    });



	jQuery('.species-carousel-control').tooltip({'delay': {'show': 1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#species-indicators li').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Control list of species', 'placement': 'top'});
	jQuery('#favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to remove favourite status', 'placement': 'bottom'});
	jQuery('#not-favourite').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Click to make this one of your favourites', 'placement': 'bottom'});

	jQuery('.sub-photo');
		
	jQuery('#photo-carousel-control-right').focus();
	
	if (document.getElementById('sub-photo-1')) {
					console.log("more than one photo" );
					jQuery('#control_nextseq').prop('disabled', true);
				}
    });
    
