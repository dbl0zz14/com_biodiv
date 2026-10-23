
function uploadDone () {
	const classId = BioDiv.classId;
	const badgeId = BioDiv.badge;
	if ( badgeId && classId ) {
		checkForClassBadge();	
	}
	else {
		loadPosts();
	}
}	


function checkForClassBadge () {
	
	const classId = BioDiv.classId;
	const badgeId = BioDiv.badge;
	
	if ( badgeId && classId ) {
		
		const setId = jQuery("#resourceSet").data("set_id");
		url = BioDiv.root + "&task=write_badge_progress&format=raw&badge=" + badgeId + "&class_id=" + classId + "&related=" + setId;
		jQuery.ajax(url, {'success': loadPosts});
	}
	else {
		loadPosts();
	}
	
}



function loadPosts () {
	
	jQuery("#deletePostModal").modal('hide');
	const classId = BioDiv.classId;
	const badgeId = BioDiv.badge;
	let urlExtra = "";
	if ( badgeId && classId ) {
		
		urlExtra += "&badge=" + badgeId + "&class_id=" + classId;
		
	}
	const url = BioDiv.root + "&view=communityposts&format=raw" + urlExtra;
	jQuery("#postsArea").load(url, postsLoaded);
	
}


function postsLoaded () {
	
	jQuery("#displayArea").scrollTop(0);
	jQuery("#newPostModal").modal('hide');
	jQuery(".postCol").dblclick(toggleLike);
	jQuery(".likeSet").click(likeSet);
	jQuery(".unlikeSet").click(unlikeSet);
	jQuery(".newPage").click(newPage);
	jQuery(".deletePost").click(deletePost);
	jQuery("#deleteNow").click(deletePostNow);
	setReloadPage();
	loadPdfThumbnails();
	if ( jQuery("#newBadge").data("newbadgeid") ) {
		jQuery("#badgeCompleteModal").modal('show');
	}
	
}


function deletePost () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	jQuery("#deleteNow").attr( "data-set", setId );
	
}


function deletePostNow () {
	
	let setId = jQuery("#deleteNow").attr( "data-set" );
	
	const url = BioDiv.root + "&task=delete_post&format=raw&set_id=" + setId;
	jQuery.ajax(url, {'success': loadPosts});
}


// function unlikePost () {
	
	// let id = jQuery(this).attr("id");
	// let idbits = id.split("_");
	// let setId = idbits.pop();
	// unlikeSetId ( setId );
	
// }



function newPage () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let pageNum = idbits.pop();
	const school = jQuery(this).data("school");
	
	let url = BioDiv.root + "&view=communityposts&format=raw&page=" + pageNum;
	if ( school > 0 ) {
		url += "&school=" + school;
	}
	
	jQuery('#postsArea').load(url, postsLoaded);
	
}
	
function searchSchools () {
	var value = jQuery(this).val().toLowerCase();
		
	jQuery(".school_btn").filter(function() {
		
		let thisText = this.innerHTML;
		
		let isMatch = thisText.toLowerCase().indexOf(value) > -1;
	  
		// Add class to parent of non matching elements
		if ( isMatch ) {
			jQuery(this).show();
		}
		else {
			jQuery(this).hide();
		}
	});
	
	//displaySpeciesPage( 0, numSpeciesPerPage );
}


function filterPosts ( schoolId ) {
	
	
	let url = BioDiv.root + "&view=communityposts&format=raw";
	if ( schoolId != "All" ) {
		url += "&school=" + schoolId;
	}
	
	jQuery('#postsArea').load(url, postsLoaded);
	
	// if ( schoolId == "All" ) {
		
		// jQuery(".postCol").show();
	
	// }
	// else {
		
		// jQuery(".postCol").hide();
		// jQuery(".postCol_" + schoolId).show();
		
	// }
}


function newPostLoaded () {
	
	setReloadPage();
	
	jQuery(".resourceNextBtn").click(resourceNext);
	jQuery(".resourceBackBtn").click(resourceBack);
	
	setInputCounters();
	
	jQuery('#uploadPostForm').submit(createResourceSet);
	
}


// function highlightActiveFilter () {
	
	// jQuery(".communityBtn").find(".panel").removeClass("activeFilterPanel");
	
	// const currElement = jQuery(this);
	// currElement.find(".panel").addClass("activeFilterPanel");
	
// }
		

jQuery(document).ready(function(){
	
	let classId = BioDiv.classId;
	let badgeId = BioDiv.badge;
	
	jQuery("#searchSchools").on ("focus", function() {
		jQuery(".schoolList").show();
	});
	
	jQuery("#searchSchools").on ("keyup", searchSchools);
	
	jQuery(".school_btn").click( function () {
		
		jQuery(".schoolList").hide();
		jQuery("#searchSchools").val(jQuery(this).text());
		
		const currElement = jQuery(this);
		let id = currElement.attr("id");
		let idbits = id.split("_");
		let schoolId = idbits.pop();
		
		filterPosts ( schoolId );
	});
	
	jQuery(".school_filter").click( function () {
		
		jQuery(".schoolList").hide();
		jQuery("#searchSchools").val("");
		jQuery("#searchRow").removeClass("in");
		
		const currElement = jQuery(this);
		let id = currElement.attr("id");
		let idbits = id.split("_");
		let schoolId = idbits.pop();
		
		filterPosts ( schoolId );
	});
	
	//jQuery(".communityBtn").click(highlightActiveFilter);
	
	jQuery(".uploadPost").click(function (){
		
		let url = BioDiv.root + "&view=uploadpost&format=raw";
		
		if ( badgeId && classId ) { 
			url += "&badge=" + badgeId + "&class_id=" + classId;
		}
		jQuery('#postArea').load(url, newPostLoaded);
		
	});
	
	loadPosts();
	
	
});



