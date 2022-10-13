

function createUsers ( e ) {
	
	e.preventDefault();
	
	jQuery("#newUsers").empty();
	
	jQuery("#newUsersMsg").text('Creating users, please wait...');
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "createSchoolUsers" ) {
		
		success = validateCreateUsersForm(fd);
	}
	
	if ( success ) {
		
		let url = BioDiv.root + "&view=batchusers&format=raw";
		
		jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(usersCreated);
		
	}	
}


function usersCreated ( data ) {

	var usersObj = JSON.parse(data);
		
	let link = document.createElement('a');
	link.innerHTML = 'Download';
	link.download = usersObj.filename;
					
	var str = '';
	
	var errorArray = usersObj.errors;

	for (var i = 0; i < errorArray.length; i++) {
		var error = errorArray[i];

		str += error.error + '\r\n';
	}
	
	var usersArray = usersObj.users;

	for (var i = 0; i < usersArray.length; i++) {
		var user = usersArray[i];

		str += user.username + ',' + user.password + '\r\n';
	}


	let blob = new Blob([str], {type: 'text/csv;charset=utf-8;'});

	link.href = URL.createObjectURL(blob);
	
	jQuery("#newUsers").append(link);
	
	jQuery("#newUsersMsg").text('Users created, click below to download');
	
	

}
	
function validateCreateUsersForm ( fd ) {
	
	let success = true;
	
	if ( fd.has("tandCsChecked") ) {
		jQuery ('[name=tandCsChecked]').removeClass('invalid');
	}
	else {
		console.log ( "T&Cs not agreed" );
		
		success = false;
		jQuery ('[name=tandCsChecked]').addClass('invalid');
		
	}
	if ( fd.has("fileStem") ) {
		jQuery ('[name=fileStem]').removeClass('invalid');
	}
	else {
		console.log ( "Filestem needed" );
		
		success = false;
		jQuery ('[name=fileStem]').addClass('invalid');
		
	}
	if ( fd.has("userStem") ) {
		jQuery ('[name=userStem]').removeClass('invalid');
	}
	else {
		console.log ( "Username stem required" );
		
		success = false;
		jQuery ('[name=userStem]').addClass('invalid');
		
	}
	if ( fd.has("passwordStem") ) {
		jQuery ('[name=passwordStem]').removeClass('invalid');
	}
	else {
		console.log ( "Password stem required" );
		
		success = false;
		jQuery ('[name=passwordStem]').addClass('invalid');
		
	}
	if ( fd.has("emailDomain") ) {
		jQuery ('[name=emailDomain]').removeClass('invalid');
	}
	else {
		console.log ( "email domain required" );
		
		success = false;
		jQuery ('[name=emailDomain]').addClass('invalid');
		
	}
	
	return success;
}
	
/*	
function createProject ( e ) {
	
	e.preventDefault();
	
	jQuery("#newProjectMsg").text('Creating project, please wait...');
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "createProject" ) {
		
		success = validateCreateProjectForm(fd);
	}
	
	if ( success ) {
		
		let url = BioDiv.root + "&task=createproject";
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		});
		
	}	
}


*/
	
function validateProject ( e ) {
	
	let success = true;
	
	
	jQuery("#projectMsg").empty();
	
	let projectName = jQuery("#projectName");
	let prettyName = jQuery("#prettyName");
	projectName.removeClass("invalid");
	prettyName.removeClass("invalid");
	
	if ( projectName.val() == "" ) {
		success = false;
		jQuery("#projectMsg").append('<div class="alert alert-danger">Name not set</div>');
		projectName.addClass("invalid");
	}
	
	if ( prettyName.val() == "" ) {
		success = false;
		jQuery("#projectMsg").append('<div class="alert alert-danger">Display name not set</div>');
		prettyName.addClass("invalid");
	}
	
	if ( !success ) {
		e.preventDefault();
	}
	
}




function setProjectFormButtons () {
	
	jQuery(".projectNextBack").click( function () {
		
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let divId = idbits.pop();
		
		let thisDiv = jQuery(this).parents( ".projectForm" );
		thisDiv.hide();
		jQuery("#" + divId).show();
	
	});
	
	jQuery(".showProjectSave").click( function () {
		
		jQuery(".projectSave").show();
	
	});
	
	jQuery(".hideProjectSave").click( function () {
		
		jQuery(".projectSave").hide();
	
	});
	
	jQuery('input[type=radio][name=isSchoolProject]').change(function() {

		if (this.value == 0) {

			jQuery("#schoolCreate").hide();
			jQuery("#schoolExists").hide();

		}
		else if (this.value == 1) {

			jQuery("#schoolCreate").hide();
			jQuery("#schoolExists").show();

		}
		else if (this.value == 2) {

			jQuery("#schoolCreate").show();
			jQuery("#schoolExists").hide();

		}

	});
	
	
}



function addSchoolUser ( e ) {

	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let url = BioDiv.root + "&view=addschooluser&format=raw";
	
	//jQuery('#userMessage').load(url, fd, BioDiv.removeClick);
	
	
	jQuery.ajax({
		type: 'POST',
		url: url,
		data: fd,
		processData: false,
		contentType: false
	}).done(printUserMessage);
		
}


function printUserMessage ( data ) {
	
	jQuery('#addSchoolUserMsg').append(data);
}



	
jQuery(document).ready(function(){
	
	jQuery('#createSchoolUsers').submit(createUsers);
	jQuery('#createProject').submit(validateProject);
	jQuery('#editProject').submit(validateProject);
	jQuery('.addSchoolUser').submit(addSchoolUser);
	
	setProjectFormButtons ();
	
});


