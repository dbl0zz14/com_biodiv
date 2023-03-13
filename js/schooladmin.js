function createGuid()
{
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	    var r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
	    return v.toString(16);
	});
}

function doLogoUpload () {
	
	setReloadPage();
	
	let guid = createGuid();
	let checkUploadUrl = BioDiv.root + "&task=verify_school_logo&guid=" + guid;
	let uploadUrl = BioDiv.root + "&task=upload_school_logo";
	let schoolId = jQuery("#schoolId").val();
	checkUploadUrl += "&school_id=" + schoolId;
	uploadUrl += "&school_id=" + schoolId;
	
	let uploadObj = jQuery('#resourceuploader').uploadFile({
		
		sequential: true,
		
		allowedTypes: "jpg,JPG,JPEG,png,PNG",

		url: uploadUrl,
		
		multiple: true,
		
		fileName: "myfile",
		
		onSubmit:function(files)
		{
			jQuery('#fileuploadspinner').show();
			jQuery.ajax(checkUploadUrl);
		},

		onSuccess: function(files, data, xhr, pd){
			jQuery.ajax(checkUploadUrl + "&done=1");
		},

		onError: function(files,status,errMsg,pd){
			console.log("upload resource file error: " + errMsg);
			jQuery("#logoErrorMessage").text(errMsg);
		},

		afterUploadAll: function(){
			jQuery('#fileuploadspinner').hide();
			logoUploaded();
			
		}
	});
	
	
}

function activateAllAccountButtons () {
	
	jQuery(".addTeacher").click(setAddTeacherFields);
	jQuery(".addStudent").click(setAddStudentFields);
	jQuery('#addSchoolUserForm').submit(addSchoolUser);
	jQuery('#addClassForm').submit(addClass);
	
	jQuery(".schoolOnly").click(showSchoolSection);
	jQuery(".teachersOnly").click(showTeachersSection);
	jQuery(".classesOnly").click(showClassesSection);
	jQuery(".studentsOnly").click(showStudentsSection);
	
	jQuery(".schoolComplete").click(schoolComplete);
	jQuery(".teacherComplete").click(teacherComplete);
	jQuery(".classComplete").click(classComplete);
	jQuery(".studentComplete").click(studentComplete);
	
	jQuery(".doLater").click(showChecklistNoUpdate);
	
	activateSchoolButtons ();
	activateTeacherListButtons ();
	activateClassListButtons ();
	activateStudentListButtons ();	
	
}

function activateSchoolButtons () {
	
	jQuery("#editSchoolForm").submit(editSchool);
	jQuery("#uploadSchoolLogo").click(doLogoUpload);
	
}


function activateTeacherListButtons () {
	
	jQuery(".editTeacher").click(setTeacherFields);
	jQuery('#editTeacherForm').submit(editTeacher);
	
}


function activateClassListButtons () {
	
	jQuery(".editClass").click(setClassFields);
	jQuery('#editClassForm').submit(editClass);
	
}


function activateStudentListButtons () {
	
	jQuery(".editStudent").click(setStudentFields);
	jQuery('#editStudentForm').submit(editStudent);
	
}


function showSchoolSection () {
	
	jQuery(".schoolAdminSection").addClass("hidden");
	jQuery("#schoolDetailsPanel").removeClass("hidden");
}


function showTeachersSection () {
	
	jQuery(".schoolAdminSection").addClass("hidden");
	jQuery("#teacherAccountsPanel").removeClass("hidden");
}


function showClassesSection () {
	
	jQuery(".schoolAdminSection").addClass("hidden");
	jQuery("#classAccountsPanel").removeClass("hidden");
}


function showStudentsSection () {
	
	jQuery(".schoolAdminSection").addClass("hidden");
	jQuery("#studentAccountsPanel").removeClass("hidden");
}




function showChecklistNoUpdate () {
	
	jQuery(".schoolAdminSection").addClass("hidden");
	jQuery("#checklist").removeClass("hidden");
	
	
}


function showChecklist () {
	
	// jQuery(".schoolAdminSection").addClass("hidden");
	// jQuery("#checklist").removeClass("hidden");
	
	let url = "bes-school-admin";
	if ( jQuery(".fa-check-square-o").length == 4 ) {
		url += "?checklist=1";
	}
	window.location.href = url;
}


function logoUploaded () {
	
	let url = BioDiv.root + "&view=schoolgrid&format=raw&school=1";
	
	jQuery("#schoolList").load(url, schoolGridLoaded);
	
	
}


function schoolGridLoaded () {
	
	activateSchoolButtons ();
	jQuery("#editSchoolModal").modal('hide');
}


function schoolComplete () {
	
	let url = BioDiv.root + "&view=updateschool&format=raw&school=1";
	
	jQuery.ajax({
		type: 'POST',
		url: url,
		processData: false,
		contentType: false
	}).done(schoolCompleted);
}

function schoolCompleted (data) {
	
	let updateResult = JSON.parse( data );
	if ( updateResult.updated == 1 ) {
		jQuery("#schoolSetupDone").html(updateResult.icon);
		jQuery("#schoolSetupButton").html(updateResult.buttonText);
		jQuery("#schoolSetupButton").removeClass("toSetUp");
		jQuery("#schoolSetupButton").removeClass("btn-primary");
		jQuery("#schoolSetupButton").addClass("btn-info");
		if ( updateResult.allSetUp ) {
			jQuery("#setupDone").removeClass("hidden");
			jQuery("#setupNotDone").addClass("hidden");
		}
		setupBtnColor ();
		showChecklistNoUpdate();
	}
	
}


function teacherComplete () {
	
	let url = BioDiv.root + "&view=updateschool&format=raw&teacher=1";
	
	jQuery.ajax({
		type: 'POST',
		url: url,
		processData: false,
		contentType: false
	}).done(teacherCompleted);
}

function teacherCompleted (data) {
	
	let updateResult = JSON.parse( data );
	if ( updateResult.updated == 1 ) {
		jQuery("#teacherSetupDone").html(updateResult.icon);
		jQuery("#teacherSetupButton").html(updateResult.buttonText);
		jQuery("#teacherSetupButton").removeClass("toSetUp");
		jQuery("#teacherSetupButton").removeClass("btn-primary");
		jQuery("#teacherSetupButton").addClass("btn-info");
		if ( updateResult.allSetUp ) {
			jQuery("#setupDone").removeClass("hidden");
			jQuery("#setupNotDone").addClass("hidden");
		}
		setupBtnColor ();
		showChecklistNoUpdate();
	}
	
}


function classComplete () {
	
	let url = BioDiv.root + "&view=updateschool&format=raw&class=1";
	
	jQuery.ajax({
		type: 'POST',
		url: url,
		processData: false,
		contentType: false
	}).done(classCompleted);
}

function classCompleted (data) {
	
	let updateResult = JSON.parse( data );
	if ( updateResult.updated == 1 ) {
		jQuery("#classSetupDone").html(updateResult.icon);
		jQuery("#classSetupButton").html(updateResult.buttonText);
		jQuery("#classSetupButton").removeClass("toSetUp");
		jQuery("#classSetupButton").removeClass("btn-primary");
		jQuery("#classSetupButton").addClass("btn-info");
		if ( updateResult.allSetUp ) {
			jQuery("#setupDone").removeClass("hidden");
			jQuery("#setupNotDone").addClass("hidden");
		}
		setupBtnColor ();
		showChecklistNoUpdate();
	}
	
}


function studentComplete () {
	
	let url = BioDiv.root + "&view=updateschool&format=raw&student=1";
	
	jQuery.ajax({
		type: 'POST',
		url: url,
		processData: false,
		contentType: false
	}).done(studentCompleted);
}


function studentCompleted (data) {
	
	let updateResult = JSON.parse( data );
	if ( updateResult.updated == 1 ) {
		jQuery("#studentSetupDone").html(updateResult.icon);
		jQuery("#studentSetupButton").html(updateResult.buttonText);
		jQuery("#studentSetupButton").removeClass("toSetUp");
		jQuery("#studentSetupButton").removeClass("btn-primary");
		jQuery("#studentSetupButton").addClass("btn-info");
		if ( updateResult.allSetUp ) {
			jQuery("#setupDone").removeClass("hidden");
			jQuery("#setupNotDone").addClass("hidden");
		}
		setupBtnColor () 
		showChecklistNoUpdate();
	}
	
}


function setupBtnColor () {
	
	jQuery('.toSetUp:first').removeClass("btn-info");
	jQuery('.toSetUp:first').addClass("btn-primary");
}

function setAddTeacherFields () {
	jQuery("#suRoleId").attr('value', 4);
	jQuery(".hiddenTeacher").hide();
}


function setAddStudentFields () {
	jQuery("#suRoleId").attr('value', 5);
	jQuery(".hiddenTeacher").show();
}


function setTeacherFields () {
	
	let id = jQuery(this).attr("id");
	const idbits = id.split("_");
	let teacherId = idbits.pop();
	
	let teacherUsername = jQuery("#teacherUsername_" + teacherId).text();
	let teacherName = jQuery("#teacherName_" + teacherId).text();
	let teacherActive = jQuery("#teacherActive_" + teacherId).attr('data-isActive');
	
	jQuery("#teacherId").attr('value', teacherId);
	jQuery("#teacherUsername").text(teacherUsername);
	jQuery("#teacherName").val(teacherName);
	if ( teacherActive != "0" ) {
		jQuery("#teacherActive").prop("checked", true);
	}
	else {
		jQuery("#teacherActive").prop("checked", false);
	}
	
}


function setClassFields () {
	
	let id = jQuery(this).attr("id");
	const idbits = id.split("_");
	let classId = idbits.pop();
	
	let className = jQuery("#className_" + classId).text();
	let classActive = jQuery("#classActive_" + classId).attr('data-isActive');
	
	let classAvatar = jQuery("#classAvatar_" + classId).attr('data-avatar');
	
	jQuery("#classId").attr('value', classId);
	jQuery("#editClassName").val(className);
	//jQuery("#className").val(className);
	if ( classActive != "0" ) {
		jQuery("#classActive").prop("checked", true);
	}
	else {
		jQuery("#classActive").prop("checked", false);
	}
	jQuery("#editClassAvatar").val(classAvatar);
	
}


function setStudentFields () {
	
	let id = jQuery(this).attr("id");
	const idbits = id.split("_");
	let studentId = idbits.pop();
	
	let studentUsername = jQuery("#studentUsername_" + studentId).text();
	let studentName = jQuery("#studentName_" + studentId).text();
	let studentClass = jQuery("#studentName_" + studentId).attr('data-classid');
	let studentActive = jQuery("#studentActive_" + studentId).attr('data-isActive');
	
	jQuery("#studentId").attr('value', studentId);
	jQuery("#studentUsername").text(studentUsername);
	jQuery("#studentName").val(studentName);
	jQuery('option[value="'+studentClass+'"]').prop("selected", true);
	if ( studentActive != "0" ) {
		jQuery("#studentActive").prop("checked", true);
	}
	else {
		jQuery("#studentActive").prop("checked", false);
	}
	
}


function addSchoolUser(e) {
	
	let url = BioDiv.root + "&view=addschooluser&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = false;
	
	let isTeacher = jQuery("#suRoleId").attr('value') == 4;
	let isStudent = jQuery("#suRoleId").attr('value') == 5;
	
	if ( formId == "addSchoolUserForm" ) {
		
		if ( isTeacher ) {
			success = validateAddTeacherForm ( fd );
		}
		else if ( isStudent ) {
			success = validateAddStudentForm ( fd );
		}
	}
	
	if ( success ) {
		
		if ( isTeacher ) {
			
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: fd,
				processData: false,
				contentType: false
			 }).done(addTeacherComplete)
			.fail(addUserFail);
		}
		else if ( isStudent ) {
		
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: fd,
				processData: false,
				contentType: false
			}).done(addStudentComplete)
			.fail(addUserFail);
		}
		
	}
}



function addClass(e) {
	
	let url = BioDiv.root + "&view=addclass&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = false;
	
	if ( formId == "addClassForm" ) {
		
		success = validateAddClassForm ( fd );
	}
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(addClassComplete)
			.fail(addUserFail);
		
	}
}




function editSchool(e) {
	
	let url = BioDiv.root + "&view=editschooluser&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "editSchoolForm" ) {
		
		success = validateEditSchoolForm ( fd );
	}
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(editSchoolComplete);
		
	}
}


function editTeacher(e) {
	
	let url = BioDiv.root + "&view=editschooluser&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "editTeacherForm" ) {
		
		success = validateEditTeacherForm ( fd );
	}
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(editTeacherComplete);
		
	}
}


function editClass(e) {
	
	let url = BioDiv.root + "&view=editschooluser&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "editClassForm" ) {
		
		success = validateEditClassForm ( fd );
	}
	
	if ( success ) {
		
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(editClassComplete);
		
	}
}


function editStudent(e) {
	
	let url = BioDiv.root + "&view=editschooluser&format=raw";
	
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



function validateAddTeacherForm ( fd ) {
	
	let success = validateAddSchoolUserCommonFields ( fd );
	
	if ( fd.has("suEmail") ) {
		
		let pw = fd.get("suEmail");
		let actualChars = pw.length;
		
		if ( pw != fd.get("suEmail2") ) {
			console.log ( "Emails must match" );
			success = false;
			jQuery ('[name=suEmail2]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Emails must match');
			
		}
		else {
			jQuery ('[name=suEmail]').removeClass('invalid');
			jQuery ('[name=suEmail2]').removeClass('invalid');
		}
		
	}
	else {
		console.log ( "Form has no email" );
		success = false;
		jQuery ('[name=suEmail]').addClass('invalid');
		
	}
	return success;
}


function validateAddStudentForm ( fd ) {
	
	let success = validateAddSchoolUserCommonFields ( fd );
	
	if ( fd.has("suEmail") ) {
		
		let pw = fd.get("suEmail");
		let actualChars = pw.length;
		
		if ( pw != fd.get("suEmail2") ) {
			console.log ( "Emails must match" );
			success = false;
			jQuery ('[name=suEmail2]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Emails must match');
			
		}
		else {
			jQuery ('[name=suEmail]').removeClass('invalid');
			jQuery ('[name=suEmail2]').removeClass('invalid');
		}
		
	}
	return success;
}


function validateAddSchoolUserCommonFields ( fd ) {
	
	let success = true;
	
	if ( fd.has("suName") ) {
		
		let actualChars = fd.get("suName").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=suName]').removeClass('invalid');
		}
		else {
			console.log ( "User name too short : " + fd.get("suName") );
			success = false;
			jQuery ('[name=suName]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Name must be at least 3 characters long');
		}
	}
	else {
		console.log ( "Form has no name" );
		success = false;
		jQuery ('[name=suName]').addClass('invalid');
		
	}
	if ( fd.has("suUsername") ) {
		
		let actualChars = fd.get("suUsername").length;
		//(/\s/.test(str))
		if ( (/\s/).test(fd.get("suUsername")) ) {
			console.log ( "User username has whitespace : " + fd.get("suUsername") );
			success = false;
			jQuery ('[name=suUsername]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Username must not contain white space');
		}
		else if ( actualChars < 3 ) {
			console.log ( "User username too short : " + fd.get("suUsername") );
			success = false;
			jQuery ('[name=suUsername]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Username must be 3 or more characters long');
		}
		else {
			jQuery ('[name=suUsername]').removeClass('invalid');
		}
		
	}
	else {
		console.log ( "Form has no username" );
		success = false;
		jQuery ('[name=suUsername]').addClass('invalid');
		
	}
	if ( fd.has("suPassword") ) {
		
		let pw = fd.get("suPassword");
		let actualChars = pw.length;
		
		if ( actualChars < 3 ) {
			console.log ( "User password too short : " );
			success = false;
			jQuery ('[name=suPassword]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Password must be 3 or more characters long');
		}
		else if ( pw != fd.get("suPassword2") ) {
			console.log ( "Passwords must match" );
			success = false;
			jQuery ('[name=suPassword2]').addClass('invalid');
			jQuery('#addUserFailMessage').text('Passwords must match');
			
		}
		else {
			jQuery ('[name=suPassword]').removeClass('invalid');
			jQuery ('[name=suPassword2]').removeClass('invalid');
		}
		
	}
	return success;
	
}


function validateEditSchoolForm ( fd ) {
	
	let success = true;
	
	if ( fd.has("schoolName") ) {
		
		let actualChars = fd.get("schoolName").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=schoolName]').removeClass('invalid');
		}
		else {
			console.log ( "School name too short : " + fd.get("schoolName") );
			success = false;
			jQuery ('[name=schoolName]').addClass('invalid');
			jQuery('#editSchoolFailMessage').text('School name must be at least 3 characters long');
		}
	}
	else {
		console.log ( "Form has no schoolname" );
		success = false;
		jQuery ('[name=schoolName]').addClass('invalid');
		
	}
	return success;
	
}


function validateAddClassForm ( fd ) {
	
	let success = true;
	
	if ( fd.has("className") ) {
		
		let actualChars = fd.get("className").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=className]').removeClass('invalid');
		}
		else {
			console.log ( "Class name too short : " + fd.get("name") );
			success = false;
			jQuery ('[name=className]').addClass('invalid');
			jQuery('#addClassFailMessage').text('Name must be at least 3 characters long');
		}
	}
	else {
		console.log ( "Form has no name" );
		success = false;
		jQuery ('[name=className]').addClass('invalid');
		
	}
	return success;
	
}


function validateEditTeacherForm ( fd ) {
	
	let success = true;
	
	if ( fd.has("teacherName") ) {
		
		let actualChars = fd.get("teacherName").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=teacherName]').removeClass('invalid');
		}
		else {
			console.log ( "Teacher name too short : " + fd.get("teacherName") );
			success = false;
			jQuery ('[name=teacherName]').addClass('invalid');
		}
	}
	else {
		console.log ( "Form has no teacherName" );
		success = false;
		jQuery ('[name=teacherName]').addClass('invalid');
		
	}
	return success;
	
}


function validateEditClassForm ( fd ) {
	
	let success = true;
	
	// For resourceUploadForm, check values
	if ( fd.has("editClassName") ) {
		
		let actualChars = fd.get("editClassName").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=editClassName]').removeClass('invalid');
		}
		else {
			console.log ( "Class name too short : " + fd.get("editClassName") );
			success = false;
			jQuery ('[name=editClassName]').addClass('invalid');
		}
	}
	else {
		console.log ( "Form has no editClassName"  );
		success = false;
		jQuery ('[name=editClassName]').addClass('invalid');
		
	}
	return success;
	
}


function validateEditStudentForm ( fd ) {
	
	let success = true;
	
	// For resourceUploadForm, check values
	if ( fd.has("studentName") ) {
		
		let actualChars = fd.get("studentName").length;
		
		if ( actualChars > 2 ) {
			jQuery ('[name=studentName]').removeClass('invalid');
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


function addUserFail ( data ) {
	console.log(data);
	jQuery("#addUserFailMessage").html("Sorry there was a problem creating the user");
}


function addTeacherComplete ( data ) {
	
	jQuery("#teacherList").html(data);
	activateTeacherListButtons ();
	jQuery("#addSchoolUserModal").modal('hide');
	
	
}


function addStudentComplete ( data ) {
	
	jQuery("#studentList").html(data);
	activateStudentListButtons ();
	jQuery("#addSchoolUserModal").modal('hide');
	
}


function editSchoolComplete ( data ) {
	
	jQuery("#schoolList").html(data);
	activateSchoolButtons ();
	jQuery("#editSchoolModal").modal('hide');
}


function addClassComplete ( data ) {
	
	jQuery("#classList").html(data);
	activateClassListButtons ();
	jQuery("#addClassModal").modal('hide');
}


function editTeacherComplete ( data ) {
	
	jQuery("#teacherList").html(data);
	activateTeacherListButtons ();
	jQuery("#editTeacherModal").modal('hide');
	
}


function editClassComplete ( data ) {
	
	jQuery("#classList").html(data);
	activateClassListButtons ();
	jQuery("#editClassModal").modal('hide');
	
}


function editStudentComplete ( data ) {
	
	jQuery("#studentList").html(data);
	activateStudentListButtons ();
	jQuery("#editStudentModal").modal('hide');
	
}


jQuery(document).ready(function(){
	
	activateAllAccountButtons();
		
});
