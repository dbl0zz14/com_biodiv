
function uploadDone () {
	reloadCurrentPage();
}


function setShareLevelForSet () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	let accessLevel = idbits.pop();
	
	let url = BioDiv.root + "&view=shareresourceset&format=raw&share=" + accessLevel + "&id=" + setId;
	
	jQuery.ajax(url).done(reloadCurrentPage);
}


function addBadgeToSet () {
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	
	let url = BioDiv.root + "&view=selectbadges&format=raw&set=" + setId;
	
	jQuery("#addBadgeArea").load(url, setBadgeSave);
}


function setBadgeSave () {
	
	jQuery('#addBadgeForm').submit(addBadges);
	
}


function addBadges ( e ) {
	
	e.preventDefault();
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( success ) {
		
		let url = BioDiv.root + "&view=addsetbadges&format=raw";
	
		//jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(badgesAdded);
		
	}
	
}


function badgesAdded ( data ) {
	jQuery("#setBadges").html(data);
	jQuery("#addBadgeModal").modal('hide');
}



jQuery(document).ready(function(){
	
	resourceListLoaded();
	
	jQuery(".likeSet").click(likeSet);
	jQuery(".unlikeSet").click(unlikeSet);
	jQuery(".faveSet").click(faveSet);
	jQuery(".unfaveSet").click(unfaveSet);
	jQuery(".shareSet").click(setShareLevelForSet);
	jQuery(".addBadgeToSet").click(addBadgeToSet);
	
	//jQuery(".setBadge").click(displayBadgeArticle);
	jQuery(".setBadge").click(displayReadOnlyBadgeArticle);
	
	//jQuery("#errorsModal").modal('show');

});
