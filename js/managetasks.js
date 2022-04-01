

function uploadDone () {
		
	let setId = jQuery("#resourceSet").data("set_id");
	
	let url = BioDiv.root + "&view=updatetask&format=raw&uploaded=" + setId;
	jQuery("#displayArea").load(url, taskDoneOrUploaded);
	
}	



function activateTeacherTabs () {
	
	jQuery('.browseBadges').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
	});
	
	jQuery('.allStudentBadges').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		
		let url = BioDiv.root + "&view=viewbadges&format=raw";
		jQuery('#displayArea').load(url, activatebadgeButtons);
	});
	
	jQuery('.allTeacherTasks').click( function () {
		
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		
		let url = BioDiv.root + "&view=viewbadges&format=raw&teacher=1";
		jQuery('#displayArea').load(url, activatebadgeButtons);
	});
	
	
	
}


jQuery(document).ready(function(){
	
	activateTeacherTabs();
	
	jQuery(".browseBadges").trigger("click");
	
	
	
});
