function createGuid()
{
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	    var r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
	    return v.toString(16);
	});
}



function setUploadButton () {
	
	setReloadPage();
	
	jQuery(".resourceNextBtn").click(resourceNext);
	jQuery(".resourceBackBtn").click(resourceBack);
	
	setInputCounters();
	setHideMetaError();
	
	jQuery(".hideMetaError").click(()=>{
		jQuery("#resourceMetaErrorMsg").hide();
	});
	
	jQuery("input[name=source]").change(function(e) {
		
		let checkedRadio = jQuery('input[name=source]:checked', '#resourceUploadForm').val();
		
		if ( checkedRadio == "external" ) {
			jQuery(".externalExtras").show();
		}
		else {
			jQuery(".externalExtras").hide();
		}
	});
	
	jQuery('#resourceUploadForm').submit(createResourceSet);
	
}

function setTaskUploadButton () {
	
	jQuery('#taskUploadForm').submit(createResourceSet);
}

function createResourceSet(e) {
	
	let url = BioDiv.root + "&view=newresourceset&format=raw";
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "resourceUploadForm" ) {
		
		success = validateResourceForm(fd);
	}
	
	if ( success ) {
		
		url = BioDiv.root + "&view=newresourceset&format=raw";
		
		//jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(resourceSetCreated);
		
	}
	else {
		jQuery("#resourceMetaErrorMsg").show();
	}
	
}

function resourceSetCreated ( data ) {
	jQuery("#uploadFiles").html(data);
	jQuery(".metaPage").hide();
	jQuery("#uploadFilesPage").show();
	doUpload();
}



function doUpload ( isSchool = false ) {
	
	setReloadPage();
	
	let guid = createGuid();
	let checkUploadUrl = BioDiv.root + "&task=verify_resource_set&guid=" + guid;
	let uploadUrl = BioDiv.root + "&task=upload_resource_set";
	
	if ( isSchool ) uploadUrl += "&school=1";
	
	let uploadObj = jQuery('#resourceuploader').uploadFile({
		
		sequential: true,
		
		allowedTypes: "jpg,JPG,JPEG,mp4,MP4,mp3,MP3,m4a,M4A,pdf,PDF,docx,AVI,pptx,doc,DOC,odp,ODP",

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
			jQuery("#errorMessage").text(errMsg);
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
		
		allowedTypes: "jpg,JPG,JPEG,mp4,MP4,mp3,MP3,m4a,M4A,pdf,PDF,docx,AVI,pptx,doc,DOC,odp,ODP",

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


    
