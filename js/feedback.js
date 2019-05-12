
var timeoutInMiliseconds = 60000;
var timeoutId; 
  
function startTimer() { 
    // window.setTimeout returns an Id that can be used to start and stop a timer
    timeoutId = window.setTimeout(doInactive, timeoutInMiliseconds)
}
  
function doInactive() {
    // does whatever you need it to actually do - probably signs them out or stops polling the server for info
	console.log("doInactive called");
	var projectId = jQuery('#feedback').attr("data-project-id");
	var url = BioDiv.root + "&view=startkiosk&project_id=" + projectId;
	var userKey = jQuery('#feedback').attr("data-user-key");
	url += "&user_key=" + userKey;
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
	
	var backgroundUrl = jQuery('#feedback').attr("data-project-img");
	var bgString = "url('" + backgroundUrl + "')";
	console.log("Got project image url: " + backgroundUrl);
	console.log("Got bg string: " + bgString);
	jQuery('#feedback').css({"background-image": bgString, "background-color": "#477171", "color": "white"}); 


	setupTimers();
	
});

	
    
