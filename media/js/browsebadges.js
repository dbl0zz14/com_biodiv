
function uploadDone () {
		
	let setId = jQuery("#resourceSet").data("set_id");
	// let url = BioDiv.root + "&view=resourcelist&format=raw&set_id=" + setId;
	// jQuery("#displayArea").load(url, resourceListLoaded);
	
	let url = BioDiv.root + "&view=updatetask&format=raw&uploaded=" + setId;
	jQuery("#displayArea").load(url, taskDoneOrUploaded);
}	


jQuery(document).ready(function(){
	
	activatebadgeButtons();
	
});
