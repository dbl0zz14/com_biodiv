

jQuery(document).ready(function(){

	var backgroundUrl = jQuery('#start-kiosk-jumbotron').attr("data-project-img");
	var bgString = "url('" + backgroundUrl + "')";
	jQuery('#start-kiosk-jumbotron').css({"background-image": bgString, "background-color": "#477171", "color": "white"}); 

});

	
    
