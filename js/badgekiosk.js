


jQuery(document).ready(function(){

	// Set up the kiosk page
	let kioskPage = BioDiv.kiosk;
	let projectId = BioDiv.projectId;
	let classId = BioDiv.classId;
	
	let params = JSON.parse ( BioDiv.kioskParams );
	
	if ( params.systemType == "QUIZ" ) {
		
		// On start up, load the requested quiz page
		url = BioDiv.root + "&view=kioskquizstandard&format=raw&project_id=" + projectId + "&topic=" + params.topic;
		
		jQuery('#kiosk').load(url, kioskStandardQuizSuccess);
	}
	else if ( params.systemType == "CLASSQUIZ" ) {
		
		// On start up, load the requested quiz page
		let classStr = "";
		if ( classId ) {
			classStr = "&class_id=" + classId;
		}
		url = BioDiv.root + "&view=kioskquizstandard&format=raw&project_id=" + projectId + "&topic=" + params.topic + classStr;
	
		jQuery('#kiosk').load(url, kioskStandardQuizSuccess);
	}
	else if ( params.systemType == "CLASSIFY" ) {
		
		url = BioDiv.root + "&view=kioskclassifyproject&format=raw&project_id=" + projectId;
		
		if ( !params.enoughToClassify ) {
			url += "&classify_second_project=1";
		}
	
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
	}
	else if ( params.systemType == "CLASSCLASSIFY" ) {
		
		// On start up, load the requested quiz page
		let classStr = "";
		if ( classId ) {
			classStr = "&class_id=" + classId;
		}
		url = BioDiv.root + "&view=kioskclassifyproject&format=raw&project_id=" + projectId + classStr;
		
		if ( !params.enoughToClassify ) {
			url += "&classify_second_project=1";
		}
	
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
	}
	else if ( params.systemType == "CLASSIFYOTHER" ) {
		
		url = BioDiv.root + "&view=kioskclassifyproject&format=raw&classify_second_project=1&project_id=" + projectId;
	
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
	}
	else if ( params.systemType == "CLASSCLASSIFYOTHER" ) {
		
		// On start up, load the requested quiz page
		let classStr = "";
		if ( classId ) {
			classStr = "&class_id=" + classId;
		}
		url = BioDiv.root + "&view=kioskclassifyproject&format=raw&classify_second_project=1&project_id=" + projectId + classStr;
	
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
	}
	else if ( params.systemType == "CLASSIFYSPECIES" ) {
		
		url = BioDiv.root + "&view=kioskclassifyproject&format=raw&classify_second_project=1&project_id=" + projectId;
	
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
	}
	else if ( params.systemType == "CLASSCLASSIFYSPECIES" ) {
		
		// On start up, load the requested quiz page
		let classStr = "";
		if ( classId ) {
			classStr = "&class_id=" + classId;
		}
		url = BioDiv.root + "&view=kioskclassifyproject&format=raw&classify_second_project=1&project_id=" + projectId + classStr;
	
		jQuery('#kiosk').load(url, kioskClassifyProjectSuccess);
	}
	else {
	
		// On start up, load the start-kiosk page
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	}
		
	
	// Stop kiosk users right clicking using long press
	document.addEventListener('contextmenu', event => event.preventDefault());
	
	jQuery('#home_button').click( function () {
		
		classifyCount = 0;
		
		let url = BioDiv.root + "&view=kioskstart&format=raw";
	
		jQuery('#kiosk').load(url, kioskStartSuccess);
	});
	
	
	
});

	
    
