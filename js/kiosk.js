

var kioskPage = null;

var timeoutInMiliseconds = 60000;
var timeoutId; 
  
function startTimer() { 
    // window.setTimeout returns an Id that can be used to start and stop a timer
    timeoutId = window.setTimeout(doInactive, timeoutInMiliseconds)
}
  
function doInactive() {
    // does whatever you need it to actually do - probably signs them out or stops polling the server for info
	/*
	var projectId = jQuery('#start-kiosk-jumbotron').attr("data-project-id");
	var url = BioDiv.root + "&task=kiosk_timeout&project_id=" + projectId;
	var userKey = jQuery('#start-kiosk-jumbotron').attr("data-user-key");
	url += "&user_key=" + userKey;
	url += "&" + userKey;
	*/
	
	console.log ( "Timed out" );
	
	url = kioskPage;
	
	//jQuery.get(url);
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


function setBackgroundImage () {
	var backgroundUrl = jQuery('#start-kiosk-jumbotron').attr("data-project-img");
	var bgString = "url('" + backgroundUrl + "')";
	jQuery('#start-kiosk-jumbotron').css({"background-image": bgString});
}


function kioskFullscreenExtras () {
	
	// Change control colour on fullscreen
	jQuery('#fullscreen-button').click(function (){
		
		jQuery('#photoCarousel > a.carousel-control.photo-carousel-control').css("color", "white");
	});
	jQuery('#fullscreen-exit-button').click(function (){
		
		jQuery('#photoCarousel > a.carousel-control.photo-carousel-control').css("color", "black");
	});
}


function kioskStartSuccess () {
		
	setBackgroundImage();
	
	setIntroButton();
	setLearnButton ();
	setClassifyButton ();
	setQuizButton();
	setMapButton();
	setAboutButton();

	
}


function kioskIntroSuccess () {
		
	setLearnButton();
	setQuizButton();
	setTutorialButton();
	setClassifyButton();
	setQuizButton();
	setMapButton();
	setAboutButton();
	
}



function setIntroButton () {
	
	jQuery('#kiosk_start').click(function (){
		
		var url = BioDiv.root + "&view=kioskintro&format=raw";
		jQuery('#kiosk').load(url, kioskIntroSuccess);
		
	});
	
}


function setLearnButton () {
	
	jQuery('#kiosk_animals').click(function (){
		
		var url = BioDiv.root + "&view=kiosklearn&format=raw";
		jQuery('#kiosk').load(url, kioskLearnSuccess);
		
	});
}

function setClassifyButton () {
	
	jQuery('#kiosk_classify').click(function (){
		
		var url = BioDiv.root + "&view=kioskclassify&format=raw";
		jQuery('#kiosk').load(url, kioskClassifySuccess);
		
	});
}

function setQuizButton () {
	
	jQuery('#kiosk_quiz').click(function (){
		
		var url = BioDiv.root + "&view=kioskquiz&format=raw";
		jQuery('#kiosk').load(url, kioskQuizSuccess);
		
	});
}


function setMapButton () {
	
	jQuery('#kiosk_map').click(function (){
		
		var url = BioDiv.root + "&view=kioskmap&format=raw";
		jQuery('#kiosk').load(url, kioskMapSuccess);
		
	});
	
}


function setAboutButton () {
	
	jQuery('#kiosk_project').click(function (){
		
		var url = BioDiv.root + "&view=kioskabout&format=raw";
		jQuery('#kiosk').load(url, kioskAboutSuccess);
		
	});
	
}




function setHomeButton () {
	
	jQuery('.back_to_home').click( function () {
		
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	});

}

function setClassifyProjectButton () {
	
	jQuery('#classify_project').click(function (){
		
		var url = BioDiv.root + "&view=kioskclassifyproject&format=raw";
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
		
	});
	
}

function setClassifySecondProjectButton () {
	
	jQuery('#classify_wider').click(function (){
		
		var url = BioDiv.root + "&view=kioskclassifyproject&format=raw&classify_second_project=1";
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
		
	});
	
}

function setTutorialButton () {
	
	jQuery('#classify_tutorial').click(function (){
		
		var url = BioDiv.root + "&view=kioskclassifytutorial&format=raw";
		jQuery('#kiosk').load(url, kioskClassifyTutorialSuccess);
		
	});
	
}

jQuery(document).ready(function(){

	// Set up the kiosk page
	kioskPage = BioDiv.kiosk;
	
	// On start up, load the start-kiosk page
	let url = BioDiv.root + "&view=kioskstart&format=raw";
	
	jQuery('#kiosk').load(url, kioskStartSuccess);
	

	setupTimers();
	
	// Stop kiosk users right clicking using long press
	document.addEventListener('contextmenu', event => event.preventDefault());
	
	jQuery('#home_button').click( function () {
		
		classifyCount = 0;
		
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	});
	
	
	
});

	
    
