

function uploadDone () {
		
	console.log ( "Student task uploaded" );
	
	let setId = jQuery("#resourceSet").data("set_id");
	console.log ( "Resource set id = " + setId );
	
	//let typeId = jQuery("#studentDashRow").data("resource_type");
	
	let url = BioDiv.root + "&view=resourcelist&format=raw&set_id=" + setId;
	jQuery("#displayArea").load(url, resourceListLoaded);
	
}	


function activateStudentTaskButtons () {
	
	jQuery(".studentTask").click( function () {
		
		let myId = this.id;
		jQuery(".studentTask:not(#thisid)").removeClass('active');
		jQuery(this).toggleClass('active');
		
	});
	
	jQuery("#searchTasks").on ("keyup", searchStudentTasks);
	
	jQuery('#studentTaskForm').submit(uploadStudentTask);
	
}


// Filter the resources list on search
function searchStudentTasks () {
	var value = jQuery(this).val().toLowerCase();
		
	jQuery(".studentTask").filter(function() {
		
		let thisText = this.innerHTML;
		
		let isMatch = thisText.toLowerCase().indexOf(value) > -1;
	  
		// Show or hide the task
		if ( isMatch ) {
			jQuery(this).show();
		}
		else {
			jQuery(this).hide();
		}
	});
	
}


function uploadStudentTask(e) {
	
	jQuery("#schoolTaskMsg").empty();
	
	let url = BioDiv.root + "&view=resourceset&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "studentTaskForm" ) {
		
		success = validateStudentTaskForm(fd);
	}
	
	if ( success ) {
		
		console.log ( "Form is valid" );
		
		let activeTask = jQuery(".studentTask.active")[0];
		let chosenTaskId = activeTask.id;
		let idbits = chosenTaskId.split("_");
		let taskId = idbits.pop();
		fd.append("task", taskId)
		
		let header5 = jQuery(".studentTask.active").find("h5");
		let uploadName = header5.text();
		fd.append("uploadName", uploadName);
		
		let descPara = jQuery(".studentTask.active").find("small");
		let uploadDescription = descPara.text();
		fd.append("uploadDescription", uploadDescription);
		
		
		
		url = BioDiv.root + "&view=resourceset&format=raw";
		
		//jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(studentResourceSetCreated);
		
	}
	else {
		jQuery("#studentTaskMsg").text("Please choose an activity first");
	}
	
}


function studentResourceSetCreated ( data ) {
	jQuery("#displayArea").html(data);
	doUpload(true);
}



function validateStudentTaskForm ( fd ) {
	
	let success = true;
	
	// For studentTaskForm, check values
	if ( jQuery(".studentTask.active").length > 0 ) {
		jQuery ('[name=taskSelect]').removeClass('invalid');
	}
	else {
		console.log ( "No task selected" );
		success = false;
		jQuery ('[name=taskSelect]').addClass('invalid');
	}

	return success;
}




jQuery(document).ready(function(){
	
		
		
});
