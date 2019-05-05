jQuery(document).ready(function(){

	var backgroundUrl = jQuery('#start-kiosk-jumbotron').attr("data-project-img");
	var bgString = "url('" + backgroundUrl + "')";
	console.log("Got project image url: " + backgroundUrl);
	console.log("Got bg string: " + bgString);
	jQuery('#start-kiosk-jumbotron').css({"background-image": bgString, "background-color": "#477171", "color": "white"}); 
	//jQuery('#start-kiosk-jumbotron').css({/*"background-color": "#477171", */"background-image":"http://localhost/rhombus/biodivimages/projects/youngfox1.jpg",/*"background-image": "http://localhost/rhombus/biodivimages/projects/hancock_urban.jpg", */   "color": "white"}); 
	
	// background-image: url("http://localhost/rhombus/biodivimages/projects/hancock_urban.jpg");
 
	/*
	jQuery('#start-kiosk-spotting').click(function (){
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
	*/

});

	
    
