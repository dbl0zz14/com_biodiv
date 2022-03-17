

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
	
	
jQuery(document).ready(function(){
	
	jQuery('#createSchoolUsers').submit(createUsers);
	
});


