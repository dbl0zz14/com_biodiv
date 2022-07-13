

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
	
	/*
	jQuery('.allStudentBadges').click( function () {
		
		let divId = this.id;
		let idbits = divId.split("_");
		let moduleId = idbits.pop();
	
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		
		let url = BioDiv.root + "&view=viewbadges&format=raw&module=" + moduleId;
		jQuery('#displayArea').load(url, activatebadgeButtons);
	});
	
	jQuery('.allTeacherTasks').click( function () {
		
		let divId = this.id;
		let idbits = divId.split("_");
		let moduleId = idbits.pop();
	
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		
		let url = BioDiv.root + "&view=viewbadges&format=raw&teacher=1&module=" + moduleId;
		jQuery('#displayArea').load(url, activatebadgeButtons);
	});
	
	*/
	
}


jQuery(document).ready(function(){
	
	activateTeacherTabs();
	
	jQuery(".chooseModule").trigger("click");
	
	
	
});
