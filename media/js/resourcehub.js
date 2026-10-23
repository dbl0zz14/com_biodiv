

function uploadDone () {
	
	let setId = jQuery("#resourceSet").data("set_id");
	//let url = BioDiv.root + "&view=resourceset&set_id=" + setId;
	//jQuery("#displayArea").load(url, resourceListLoaded);
	
	let url = "bes-resource-set?set_id=" + setId;
	window.location.href = url;
	
}


// Filter the resources list on search
function searchCurrentResources () {
	var value = jQuery(this).val().toLowerCase();
		
	jQuery(".resource_file").filter(function() {
		
		let thisText = this.innerHTML;
		
		let isMatch = thisText.toLowerCase().indexOf(value) > -1;
	  
		// Add class to parent of non matching elements
		if ( isMatch ) {
			jQuery(this).parents(".panel").show();
		}
		else {
			//let thisPanel = this.parents("panel");
			let thisPanel = jQuery(this).parents(".panel");
			thisPanel.hide();
		}
	});
	
	//displaySpeciesPage( 0, numSpeciesPerPage );
}



jQuery(document).ready(function(){

	
	jQuery(".resourceUpload").click(function (){
		
		emptySearchBox();
		
		let url = BioDiv.root + "&view=resourceupload&format=raw";
		jQuery('#uploadArea').load(url, setUploadButton);
		
	});
	
	jQuery(".pinned").click(function (){
		
		emptySearchBox();
		
		jQuery(".filterButton").removeClass("active");
		jQuery(this).addClass("active");
		
		let listUrl = BioDiv.root + "&view=resourcelist&format=raw";
		jQuery('#displayArea').load(listUrl, resourceListLoaded);
		
	});
	
	jQuery(".favourites").click(function (){
		
		emptySearchBox();
		
		jQuery(".filterButton").removeClass("active");
		jQuery(this).addClass("active");
		
		let listUrl = BioDiv.root + "&view=resourcelist&format=raw&fav=1";
		jQuery('#displayArea').load(listUrl, resourceListLoaded);
		
	});
	
	jQuery(".latestUpload").click(function (){
		
		emptySearchBox();
		
		jQuery(".filterButton").removeClass("active");
		jQuery(this).addClass("active");
		
		let listUrl = BioDiv.root + "&view=resourcelist&format=raw&mine=1";
		jQuery('#displayArea').load(listUrl, resourceListLoaded);
		
	});
	
	jQuery(".resource-btn").click(getResourcesByType);
	
	jQuery("#searchResources").on ("keyup", searchCurrentResources);
	//jQuery("#searchResources").on ("search", searchAllResources);
	
	//let listUrl = BioDiv.root + "&view=resourcelist&format=raw";
	//jQuery("#displayArea").load(listUrl, resourceListLoaded);
});
