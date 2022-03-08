
function resourceListLoaded () {
	
	jQuery(".show_resource").click(function (){
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let resourceId = idbits.pop();
		
		let prevBit = idbits.pop();
		let tagId = "";
		if ( prevBit.search("resource") < 0 ) {
			tagId = prevBit + '_';
		}
		
		let hideDivId = "#hide_resource_" + tagId + resourceId;
		let resourceDivId = "#resource_" + tagId + resourceId;
		
		jQuery(this).hide();
		jQuery( hideDivId ).show();
		
		if ( jQuery(resourceDivId).is(':empty') ) {
			let url = BioDiv.root + "&view=resourcefile&format=raw&resource_id=" + resourceId;
			jQuery(resourceDivId).load(url);
		}
		
		jQuery(resourceDivId).parent().show();
		
	});
	
	jQuery(".hide_resource").click(function (){
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let resourceId = idbits.pop();
		let prevBit = idbits.pop();
		let tagId = "";
		if ( prevBit.search("resource") < 0 ) {
			tagId = prevBit + '_';
		}
		let showDivId = "#show_resource_" + tagId + resourceId;
		let resourceDivId = "#resource_" + tagId + resourceId;
		
		jQuery(this).hide();
		jQuery( showDivId ).show();
		
		jQuery(resourceDivId).parent().hide();
		
	});
	
	
	jQuery(".share_resource").click(shareResource);
	jQuery(".favourite_resource").click(favouriteResource);
	jQuery(".unfavourite_resource").click(unfavouriteResource);
	jQuery(".like_resource").click(likeResource);
	jQuery(".unlike_resource").click(unlikeResource);
	jQuery(".pin_resource").click(pinResource);
	jQuery(".unpin_resource").click(unpinResource);
	
}

function favouriteResource () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
	
	jQuery(".favourite_resource_" + resourceId).hide();
	jQuery(".unfavourite_resource_" + resourceId).show();
			
	let listUrl = BioDiv.root + "&view=favouriteresource&format=raw&fav=1&id=" + resourceId;
	
	jQuery.ajax(listUrl);
}

function unfavouriteResource () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
	
	jQuery(".unfavourite_resource_" + resourceId).hide();
	jQuery(".favourite_resource_" + resourceId).show();
		
	let listUrl = BioDiv.root + "&view=favouriteresource&format=raw&fav=0&id=" + resourceId;
	
	jQuery.ajax(listUrl);
}


function likeResource () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
	
	jQuery(".like_resource_" + resourceId).hide();
	jQuery(".unlike_resource_" + resourceId).show();
			
	let listUrl = BioDiv.root + "&view=likeresource&format=raw&like=1&id=" + resourceId;
	
	jQuery(".num_likes_" + resourceId).load(listUrl);
}

function unlikeResource () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
	let prevBit = idbits.pop();
	let tagId = "";
	if ( prevBit.search("resource") < 0 ) {
		tagId = prevBit + '_';
	}
	let likeDiv = "#like_resource_" + tagId + resourceId;
	
	jQuery(".unlike_resource_" + resourceId).hide();
	jQuery(".like_resource_" + resourceId).show();
		
	let listUrl = BioDiv.root + "&view=likeresource&format=raw&like=0&id=" + resourceId;
	
	jQuery(".num_likes_" + resourceId).load(listUrl);
}


function pinResource () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
			
	let url = BioDiv.root + "&view=pinresource&format=raw&pin=1&id=" + resourceId;
	
	jQuery.ajax(url);
}

function unpinResource () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
			
	let url = BioDiv.root + "&view=pinresource&format=raw&pin=0&id=" + resourceId;
	
	jQuery.ajax(url);
}

function shareResource () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let resourceId = idbits.pop();
	let accessLevel = idbits.pop();
	let prevBit = idbits.pop();
	let tagId = "";
	if ( prevBit.search("resource") < 0 ) {
		tagId = prevBit + '_';
	}
	
	// let currIcon = jQuery("#dropdown-toggle_" + tagId + resourceId).find("i");
	// let shareIcon = jQuery(this).find("i");
	// let currIconClass = currIcon.attr("class");
	// let newIconClass = shareIcon.attr("class");
	// currIcon.removeClass(currIconClass);
	// currIcon.addClass(newIconClass);
	
	let currIcon = jQuery("#dropdown-toggle_" + tagId + resourceId).find("i");
	let currIconClass = currIcon.attr("class");
	let shareIcon = jQuery(this).find("i");
	let newIconClass = shareIcon.attr("class");
	
	let currIcons = jQuery(".dropdown-toggle_" + resourceId).find("i");
	currIcons.removeClass ( currIconClass );
	currIcons.addClass ( newIconClass );
	
	let url = BioDiv.root + "&view=shareresource&format=raw&share=" + accessLevel + "&id=" + resourceId;
	
	jQuery.ajax(url);
}


function getResourcesByType () {
	
	emptySearchBox();
	jQuery(".filterButton").removeClass("active");
		
	let resourceType = jQuery(this).attr("data-resource-type");
	let listUrl = BioDiv.root + "&view=resourcelist&format=raw&type=" + resourceType;
	jQuery('#displayArea').load(listUrl, resourceListLoaded);
		
}


function searchAllResources () {
	let searchStr = jQuery(this).val();
	
	let searchStrPlus = searchStr.replace(/ /g, "%20");

	let listUrl = BioDiv.root + "&view=resourcelist&format=raw&search=" + searchStrPlus;
	jQuery('#displayArea').load(listUrl, resourceListLoaded);
		
}


function emptySearchBox () {
	jQuery("#searchResources").val("");
}

