function createGuid()
{
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
	    var r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
	    return v.toString(16);
	});
}

jQuery(document).ready(function(){
	var guid = createGuid();
	var checkUploadUrl = BioDiv.root + "&task=verify_upload&upload_id=" + BioDiv.upload_id + "&guid=" + guid
	var uploadObj = jQuery('#fileuploader').uploadFile({
		sequential: true,
		allowedTypes: "jpg,JPG,JPEG,mp4,MP4",

		url:BioDiv.root + "&task=uploadm&upload_id=" + BioDiv.upload_id,
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
		    console.log("uploadFile error: " + errMsg);
		},

    afterUploadAll: function(){
		    jQuery('#fileuploadspinner').hide();
		    //		    var url = BioDiv.root + "&task=sequence_photos&upload_id=" + BioDiv.upload_id;
		    //		    jQuery.ajax(url);
		    window.location.replace(BioDiv.root + "&view=upload");
		}
	    });
	
	
    });
    
