

function schoolUploadDone () {
		
	let setId = jQuery("#resourceSet").data("set_id");
	
	// let url = BioDiv.root + "&view=resourcelist&format=raw&set_id=" + setId;
	// jQuery("#displayArea").load(url, resourceListLoaded);
	
	let url = BioDiv.root + "&view=updatetask&format=raw&schooluploaded=" + setId;
	jQuery("#displayArea").load(url, taskDoneOrUploaded);
	
}	


function studentsLoaded () {
	
	setApproveReject ();
	
	resourceListLoaded();
}

function reduceApproveCount() {
	
	let studentsBadge = jQuery("#studentsBadge");
	
	if ( studentsBadge.length > 0 ) {
		let prevNumToApprove = parseInt(jQuery("#studentsBadge").text());
		
		let newNum = prevNumToApprove - 1;
		
		if ( newNum < 1 ) {
			jQuery("#studentsBadge").remove();
		}
		else {
			jQuery("#studentsBadge").text("" + newNum);
		}
		
	}
	
}

function setApproveReject () {
	
	jQuery('.approveTask').click( function () {
		
		let elementId = this.id;
		let idbits = elementId.split("_");
		let studentTaskId = idbits.pop();
		
		jQuery(this).hide();
		jQuery("#rejectTask_"+studentTaskId).show();
		jQuery("#taskRejected_"+studentTaskId).hide();
		jQuery("#taskApproved_"+studentTaskId).show();
			
		let url = BioDiv.root + "&view=updatetask&format=raw&approve=1&id=" + studentTaskId;
		jQuery.get(url, reduceApproveCount);
		
	});
	
	jQuery('.rejectTask').click( function () {
		
		let elementId = this.id;
		let idbits = elementId.split("_");
		let studentTaskId = idbits.pop();
		
		jQuery(this).hide();
		jQuery("#approveTask_"+studentTaskId).show();
		jQuery("#taskRejected_"+studentTaskId).show();
		jQuery("#taskApproved_"+studentTaskId).hide();
		
		let url = BioDiv.root + "&view=updatetask&format=raw&reject=1&id=" + studentTaskId;
		jQuery.get(url, reduceApproveCount);
		
	});
	
	resourceListLoaded();
}

function activateStudentTabs () {
	
	jQuery('.studentProgressTab').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		let url = BioDiv.root + "&view=studentprogress&format=raw";
		jQuery("#displayArea").load(url, activateStudentProgressButtons);
		
	});
	
	jQuery('.manageStudentsTab').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		let url = BioDiv.root + "&view=managestudents&format=raw";
		jQuery("#displayArea").load(url, activateManageStudents);
		
	});
	
	jQuery('.schoolTaskTab').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		let url = BioDiv.root + "&view=schooltask&format=raw";
		jQuery("#displayArea").load(url, activateSchoolTaskButtons);
		
	});
	
}

function activateManageStudents () {
	
	studentsLoaded();
	
	
}


function activateSchoolTaskButtons () {
	
	jQuery(".schoolTask").click( function () {
		
		let myId = this.id;
		jQuery(".schoolTask:not(#thisid)").removeClass('active');
		jQuery(this).toggleClass('active');
		
	});
	
	jQuery("#selectAllStudents").change( function () {
		
		let checkValue = this.checked;
		jQuery(".studentCheckbox").attr("checked", checkValue);
		
	} );
	
	jQuery("#searchTasks").on ("keyup", searchSchoolTasks);
	
	jQuery('#schoolTaskForm').submit(uploadSchoolTask);
	
}



function activateStudentProgressButtons () {
	
}




// Filter the resources list on search
function searchSchoolTasks () {
	var value = jQuery(this).val().toLowerCase();
		
	jQuery(".schoolTask").filter(function() {
		
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
	
	//displaySpeciesPage( 0, numSpeciesPerPage );
}


function uploadSchoolTask(e) {
	
	jQuery("#schoolTaskMsg").empty();
	
	//let url = BioDiv.root + "&view=resourceset&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "schoolTaskForm" ) {
		
		success = validateSchoolTaskForm(fd);
	}
	
	if ( success ) {
		
		console.log ( "Form is valid" );
		
		let activeTask = jQuery(".schoolTask.active")[0];
		let chosenTaskId = activeTask.id;
		let idbits = chosenTaskId.split("_");
		let taskId = idbits.pop();
		fd.append("task", taskId)
		
		let header5 = jQuery(".schoolTask.active").find("h5");
		let uploadName = header5.text();
		fd.append("uploadName", uploadName);
		
		let descPara = jQuery(".schoolTask.active").find("small");
		let uploadDescription = descPara.text();
		fd.append("uploadDescription", uploadDescription);
		
		fd.append("schoolTask", "1");
		//fd.append("school", "1");
		
		fd.append("source", "role");
		
		
		//url = BioDiv.root + "&view=resourceset&format=raw";
		//url = BioDiv.root + "&view=taskupload&format=raw";
		let url = BioDiv.root + "&view=newresourceset&format=raw";
		
		//jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(schoolResourceSetCreated);
		
	}
	else {
		jQuery("#schoolTaskMsg").text("Please choose a task and at least one student");
	}
	
}


function schoolResourceSetCreated ( data ) {
	jQuery("#displayArea").html(data);
	//jQuery("#uploadFiles").html(data);
	doSchoolUpload();
}



function validateSchoolTaskForm ( fd ) {
	
	let success = true;
	
	// For schoolTaskForm, check values
	if ( jQuery(".schoolTask.active").length > 0 ) {
		jQuery ('[name=taskSelect]').removeClass('invalid');
	}
	else {
		console.log ( "No task selected" );
		success = false;
		jQuery ('[name=taskSelect]').addClass('invalid');
	}

	if ( jQuery("input[type='checkbox']:checked").length > 0 ) {
		jQuery ('[name=studentSelect]').removeClass('invalid');
	}
	else {
		console.log ( "No student is selected" );
		success = false;
		jQuery ('[name=studentSelect]').addClass('invalid');
		
	}

	return success;
}


function setSchoolUploadButtons () {
	

}


jQuery(document).ready(function(){
	
	activateStudentTabs();
	
	jQuery(".manageStudentsTab").trigger("click");
	
	
	
});
