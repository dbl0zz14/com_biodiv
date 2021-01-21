jQuery(document).ready(function(){
	
	checkRadioDefault = function ( name ) {
		jQuery("input[name='"+name+"']:first").prop('checked', true);
	}


	BioDiv.likeActions = function (){
	    jQuery('#not-favourite').click(function(){
		jQuery('#not-favourite').hide();
		jQuery('#favourite').show();
		var activeId = jQuery('#photoCarouselInner').find(".active").attr("data-photo-id");
		if ( !activeId ) {
			activeId = jQuery('#videoContainer').attr("data-photo-id");
		}
		if ( !activeId ) {
			activeId = jQuery('#audioContainer').attr("data-photo-id");
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
		if ( !activeId ) {
			activeId = jQuery('#audioContainer').attr("data-photo-id");
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


	/* Move to classify/classifybirds while the species list is reworked - in fact, in this used..?
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
	*/
	

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
			document.exitFullscreen();
		} 
		else if (document.webkitExitFullscreen) 
		{
			document.webkitExitFullscreen();
		} 
		else if (document.mozCancelFullScreen) 
		{
			document.mozCancelFullScreen();
		} 
		else if (document.msExitFullscreen) 
		{
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
		if ( !activeId ) {
			activeId = jQuery('#audioContainer').attr("data-photo-id");
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
		if ( !activeId ) {
			activeId = jQuery('#audioContainer').attr("data-photo-id");
		}
		var url = BioDiv.root + "&task=unlike_photo&photo_id=" + activeId;
		jQuery.ajax(url);
	    });

	
	
	jQuery('#classify_increase').click(function (){
		this.parentNode.querySelector('input[type=number]').stepUp()
		/*jQuery('#classify_number').stepUp();*/
		
	    });
	
	jQuery('#classify_decrease').click(function (){
		this.parentNode.querySelector('input[type=number]').stepDown()
		/*jQuery('#classify_number').stepDown();*/
		
	    });

	jQuery('#photoCarousel').click(function (){
		jQuery('#photo-carousel-control-right').focus();
		});
	
	jQuery("#menu-toggle").click(function(e) {
		e.preventDefault();
		jQuery("#wrapper").toggleClass("toggled");
		});
	
	var haveFullscreen = document.fullscreenEnabled || /* Standard syntax */
						document.webkitFullscreenEnabled || /* Chrome, Safari and Opera syntax */
						document.mozFullScreenEnabled ||/* Firefox syntax */
						document.msFullscreenEnabled; /* IE/Edge syntax */
						
	if ( !haveFullscreen ) {
		jQuery('#fullscreen-button').hide();
		jQuery('#fullscreen-exit-button').hide();
	}

	// to test IE: jQuery('#fullscreen-exit-button').hide();
	// For sequences of more than 1 photo or for videos disable NextSequence until the user has viewed all.
	if ( document.getElementById('sub-photo-1') ) {
		jQuery('#control_nextseq').prop('disabled',true);
	}
	
	if ( document.getElementById('classify-video') ) {
		jQuery('#control_nextseq').prop('disabled',true);
	}
		
	
	jQuery('#photo-carousel-control-right').focus();
	
	if (document.getElementById('nothingDisabled')) {
		jQuery('#control_content_86').prop('disabled', true);
	}
	else {
		jQuery('#control_content_86').prop('disabled', false);
	}
	
	
});

	
    
