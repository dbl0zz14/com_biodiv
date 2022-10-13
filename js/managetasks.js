

function uploadDone () {
		
	let setId = jQuery("#resourceSet").data("set_id");
	
	let url = BioDiv.root + "&view=updatetask&format=raw&uploaded=" + setId;
	jQuery("#displayArea").load(url, taskDoneOrUploaded);
	
}	



function activateTeacherTabs () {
	
	jQuery('.manageTasksBtn').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
	});
	
		
}


jQuery(document).ready(function(){
	
	activateTeacherTabs();
	
	jQuery(".chooseModule").trigger("click");
	
	
	
});
