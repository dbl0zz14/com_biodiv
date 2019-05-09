jQuery(document).ready(function(){

	var backgroundUrl = jQuery('#start-kiosk-jumbotron').attr("data-project-img");
	var bgString = "url('" + backgroundUrl + "')";
	console.log("Got project image url: " + backgroundUrl);
	console.log("Got bg string: " + bgString);
	jQuery('#start-kiosk-jumbotron').css({"background-image": bgString, "background-color": "#477171", "color": "white"}); 

});

	
    
