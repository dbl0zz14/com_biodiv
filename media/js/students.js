

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
	
	jQuery('.studentAccountsTab').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		let url = BioDiv.root + "&view=studentaccounts&format=raw";
		jQuery("#displayArea").load(url, activateStudentAccountButtons);
		
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


function activateStudentAccountButtons () {
	
	
	jQuery(".editStudent").click(setStudentFields);
	jQuery('#editStudentForm').submit(editStudent);
	
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


function setStudentFields () {
	
	let id = jQuery(this).attr("id");
	const idbits = id.split("_");
	let studentId = idbits.pop();
	
	let studentUsername = jQuery("#studentUsername_" + studentId).text();
	let studentName = jQuery("#studentName_" + studentId).text();
	let studentActive = jQuery("#studentActive_" + studentId).attr('data-isActive');
	
	jQuery("#studentId").attr('value', studentId);
	jQuery("#studentUsername").text(studentUsername);
	jQuery("#studentName").val(studentName);
	if ( studentActive != "0" ) {
		jQuery("#studentActive").prop("checked", true);
	}
	
}




function editStudent(e) {
	
	let url = BioDiv.root + "&task=edit_student";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "editStudentForm" ) {
		
		success = validateEditStudentForm ( fd );
	}
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(editStudentComplete);
		
	}
	
	
}

function validateEditStudentForm ( fd ) {
	
	let success = true;
	
	// For resourceUploadForm, check values
	if ( fd.has("studentName") ) {
		
		let actualChars = fd.get("studentName").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=uploadName]').removeClass('invalid');
		}
		else {
			console.log ( "Student name too short : " + fd.get("studentName") );
			success = false;
			jQuery ('[name=studentName]').addClass('invalid');
		}
	}
	else {
		console.log ( "Form has no studentName: " + fd.get("studentName") );
		success = false;
		jQuery ('[name=studentName]').addClass('invalid');
		
	}
	return success;
	
}

function editStudentComplete () {
	
	jQuery("#editStudentModal").modal('hide');
	jQuery(".studentAccountsTab").trigger("click");
}


jQuery(document).ready(function(){
	
	activateStudentTabs();
	
	jQuery(".manageStudentsTab").trigger("click");
	
	
	
});
