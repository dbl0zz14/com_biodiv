



function signUp(e) {
	
	let url = BioDiv.root + "&view=addsignup&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = false;
	
	if ( formId == "signupForm" ) {
		
		success = validateSignupForm ( fd );
	}
	
	if ( success ) {
		
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(signupComplete);
		
	}
}


function validateSignupForm ( fd ) {
	
	let success = true;
	
	// if ( fd.has("suName") ) {
		
		// let actualChars = fd.get("suName").length;
		
		// if ( actualChars > 2 ) {
			// jQuery ('[name=suName]').removeClass('invalid');
		// }
		// else {
			// console.log ( "User name too short : " + fd.get("suName") );
			// success = false;
			// jQuery ('[name=suName]').addClass('invalid');
			// jQuery('#addUserFailMessage').text('Name must be at least 3 characters long');
		// }
	// }
	// else {
		// console.log ( "Form has no name" );
		// success = false;
		// jQuery ('[name=suName]').addClass('invalid');
		
	// }
	// if ( fd.has("username") ) {
		
		// let actualChars = fd.get("username").length;
		// //(/\s/.test(str))
		// if ( (/\s/).test(fd.get("username")) ) {
			// console.log ( "User username has whitespace : " + fd.get("username") );
			// success = false;
			// jQuery ('[name=username]').addClass('invalid');
			// jQuery('#addUserFailMessage').text('Username must not contain white space');
		// }
		// else if ( actualChars < 3 ) {
			// console.log ( "User username too short : " + fd.get("username") );
			// success = false;
			// jQuery ('[name=username]').addClass('invalid');
			// jQuery('#addUserFailMessage').text('Username must be 3 or more characters long');
		// }
		// else {
			// jQuery ('[name=username]').removeClass('invalid');
		// }
		
	// }
	// else {
		// console.log ( "Form has no username" );
		// success = false;
		// jQuery ('[name=username]').addClass('invalid');
		
	// }
	// if ( fd.has("password") ) {
		
		// let pw = fd.get("password");
		// let actualChars = pw.length;
		
		// if ( actualChars < 3 ) {
			// console.log ( "User password too short : " );
			// success = false;
			// jQuery ('[name=password]').addClass('invalid');
			// jQuery('#addUserFailMessage').text('Password must be 3 or more characters long');
		// }
		// else if ( pw != fd.get("password2") ) {
			// console.log ( "Passwords must match" );
			// success = false;
			// jQuery ('[name=password2]').addClass('invalid');
			// jQuery('#addUserFailMessage').text('Passwords must match');
			
		// }
		// else {
			// jQuery ('[name=password]').removeClass('invalid');
			// jQuery ('[name=password2]').removeClass('invalid');
		// }
		
	// }
	// else {
		// console.log ( "Form has no password" );
		// success = false;
		// jQuery ('[name=password]').addClass('invalid');
		
	// }
	return success;
	
}

function signupComplete ( data ) {
	
	jQuery("#displayArea").html(data);
	
}


jQuery(document).ready(function(){
	
	jQuery('#signupForm').submit(signUp);
		
});
