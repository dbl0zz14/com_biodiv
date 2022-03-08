function createGuid()
{
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	    var r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
	    return v.toString(16);
	});
}

function setUploadButton () {
	
	//jQuery('#resourceuploader').click(createResourceSet);
	jQuery('#resourceUploadForm').submit(createResourceSet);
}

function createResourceSet(e) {
	
	let url = BioDiv.root + "&view=resourceset&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "resourceUploadForm" ) {
		
		success = validateResourceForm(fd);
	}
	
	if ( success ) {
		
		url = BioDiv.root + "&view=resourceset&format=raw";
		
		//jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(resourceSetCreated);
		
	}
	
}

function resourceSetCreated ( data ) {
	jQuery("#displayArea").html(data);
	doUpload();
}


function validateResourceForm ( fd ) {
	
	let success = true;
	
	// For resourceUploadForm, check values
	if ( fd.has("uploadName") ) {
		jQuery ('[name=uploadName]').removeClass('invalid');
	}
	else {
		console.log ( "Form has no uploadName: " + fd.get("uploadName") );
		
		success = false;
		jQuery ('[name=uploadName]').addClass('invalid');
		
	}
	
	return success;
}


function doUpload ( isSchool = false ) {
	
	console.log("doUpload");
	
	let guid = createGuid();
	let checkUploadUrl = BioDiv.root + "&task=verify_resource_set&guid=" + guid;
	let uploadUrl = BioDiv.root + "&task=upload_resource_set";
	
	if ( isSchool ) uploadUrl += "&school=1";
	
	let uploadObj = jQuery('#resourceuploader').uploadFile({
		
		sequential: true,
		
		allowedTypes: "jpg,JPG,JPEG,mp4,MP4,mp3,MP3,m4a,M4A,pdf,PDF,docx,AVI",

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
			//		    alert(data);
		},

		onError: function(files,status,errMsg,pd){
			console.log("upload resource file error: " + errMsg);
		},

		afterUploadAll: function(){
			jQuery('#fileuploadspinner').hide();
			uploadDone();
			
		}
	});
	
	
}


function doSchoolUpload () {
	
	let guid = createGuid();
	let checkUploadUrl = BioDiv.root + "&task=verify_resource_set&guid=" + guid;
	let uploadUrl = BioDiv.root + "&task=upload_resource_set&school=1";
	
	let uploadObj = jQuery('#resourceuploader').uploadFile({
		
		sequential: true,
		
		allowedTypes: "jpg,JPG,JPEG,mp4,MP4,mp3,MP3,m4a,M4A,pdf,PDF,docx,AVI",

		url: uploadUrl,
		
		multiple: true,
		
		fileName: "myfile",
		
		onSubmit:function(files)
		{
			console.log ( "upload resource files on submit function" );
			jQuery('#fileuploadspinner').show();
			jQuery.ajax(checkUploadUrl);
		},

		onSuccess: function(files, data, xhr, pd){
			console.log ( "upload resource file success, verifying" );
			jQuery.ajax(checkUploadUrl + "&done=1");
			//		    alert(data);
		},

		onError: function(files,status,errMsg,pd){
			console.log("upload resource file error: " + errMsg);
		},

		afterUploadAll: function(){
			console.log ( "upload resource files all uploaded" );
			jQuery('#fileuploadspinner').hide();
			schoolUploadDone();
			
		}
	});
	
	
}


    
