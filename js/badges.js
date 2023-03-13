
function uploadDone () {
		
	let badgeId = jQuery("#resourceSet").data("badge_id");
	let classStr = "";
	if ( BioDiv.classId ) {
		classStr = "&class_id=" + BioDiv.classId;
	}
	
	let url = BioDiv.root + "&view=updatebadge&format=raw&done=1&id=" + badgeId + classStr;
	jQuery.ajax(url).done(reloadCurrentPage);	
	
}	


function badgesLoaded () {
	
	setReloadPage();
	
	jQuery(".badgeBtn").click ( displayBadgeArticle );
	
	jQuery(".speciesBtn").click ( displaySpeciesArticle );
	
	//setHelpButtons ();
	
	// let speciesBtns = jQuery(".speciesBtn");
	// let speciesBtnCount = speciesBtns.length;
	// if ( speciesBtnCount > 0 ) {
		
		// speciesBtns[speciesBtnCount-1].scrollIntoView();
	// }
	
}


function displayBadgeArticle () {
	
	let badgeBtnId = this.id;
	let idbits = badgeBtnId.split("_");
	let articleId = idbits.pop();
	let badgeId = idbits.pop();
	let classStr = "";
	if ( BioDiv.classId ) {
		classStr = "&class_id=" + BioDiv.classId;
	}
	
	jQuery('#badgeArticle').empty();
	
	let url = BioDiv.root + "&view=badgearticle&format=raw&id=" + badgeId + classStr;
	jQuery('#badgeArticle').load(url, badgeArticleLoaded);
	
};


function displayActivityArticle ( badgeId ) {
	
	let classStr = "";
	if ( BioDiv.classId ) {
		classStr = "&class_id=" + BioDiv.classId;
	}
	
	jQuery('#activityArticle').empty();
	
	let url = BioDiv.root + "&view=badgearticle&format=raw&complete=1&id=" + badgeId + classStr;
	jQuery('#activityArticle').load(url);
	
};


function badgeArticleLoaded () {
	
	setReloadPage();
	
	jQuery(".badgeComplete").click(badgeComplete)
	
}



function badgeComplete () {
	
	jQuery("#badgeModal").modal('hide');
	jQuery("#badgeCompleteModal").modal('show');
	let badgeBtnId = this.id;
	let idbits = badgeBtnId.split("_");
	let badgeId = idbits.pop();
	let classStr = "";
	if ( BioDiv.classId ) {
		classStr = "&class_id=" + BioDiv.classId;
	}
	
	let url = BioDiv.root + "&view=badgecomplete&format=raw&id=" + badgeId + classStr;
	jQuery('#badgeComplete').load(url, badgeCompleteLoaded);
	
}



function badgeCompleteLoaded () {
	
	setReloadPage();
	jQuery(".resourceNextBtn").click(resourceNext);
	jQuery(".resourceBackBtn").click(resourceBack);
	
	jQuery(".doneNoFiles").click(doneNoFiles);
	jQuery('#badgeUploadForm').submit(createResourceSet);
	
	//jQuery("#displayArea")[0].scrollIntoView();
	

}


function doneNoFiles () {
	
	let noFilesId = this.id;
	let idbits = noFilesId.split("_");
	let badgeId = idbits.pop();
	let completeText = jQuery("#uploadDescription").val();
	let maxChars = jQuery("#uploadDescriptionCount").data("maxchars");
	let actualChars = completeText.length;
	
	if ( actualChars > maxChars ) {
		console.log ( "Description has too many chars: " + completeText );
		jQuery ('[name=uploadDescription]').addClass('invalid');
	}
	else if ( actualChars == 0 ) {
		console.log ( "Description needed: " );
		jQuery ('[name=uploadDescription]').addClass('invalid');
	}
	else {
		jQuery ('[name=uploadDescription]').removeClass('invalid');
	
		let postData = {
			done: 1,
			id: badgeId,
			done_text: completeText
		};
		
		if ( BioDiv.classId ) {
			postData.class_id = BioDiv.classId;
		}
			
		let url = BioDiv.root + "&view=updatebadge&format=raw";
			
		// jQuery.post(url, postData, function( data ) {
			// jQuery( "#displayArea" ).html( data );
			// setReloadPage();
		// });
		jQuery.post(url, postData, reloadCurrentPage);
	}
		
}
	


function displaySpeciesArticle () {
	
	let speciesBtnId = this.id;
	let idbits = speciesBtnId.split("_");
	let articleId = idbits.pop();
	let badgeId = idbits.pop();
	
	jQuery('#speciesArticle').empty();
	jQuery('#activityArticle').empty();
	jQuery('.modalNav.nav-tabs a:first').tab('show');
	
	let url = BioDiv.root + "&view=speciesarticle&format=raw&id=" + articleId;
	jQuery('#speciesArticle').load(url, setReloadPage);
	
	displayActivityArticle ( badgeId );
	
}



function filterBadges () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let filterStr = idbits.pop();
	
	jQuery('.badge_All').hide();
	jQuery('.badge_' + filterStr).show();
	
	jQuery('.filterBadgesBtn').removeClass('activeFilter');
	jQuery(this).addClass('activeFilter');
		
}


function setBadgeFilters () {
	
	jQuery(".filterBadgesBtn").click(filterBadges);
}


function awardLoaded () {
	
	jQuery("#printCert").click(loadCertificate);
	
	expandImage();
	wobbleImage();
	
	setReloadPage();
}


function badgeCollectLoaded () {
	
	spinImage();
	setReloadPage();
}



jQuery(document).ready(function(){
	
	setBadgeFilters();
	
	let classStr = "";
	if ( BioDiv.classId ) {
		classStr = "&class_id=" + BioDiv.classId;
	}
	if ( BioDiv.newBadgeId ) {
		
		jQuery("#badgeCollectModal").modal('show');
		
		jQuery('#besBadge').empty();
		
		let url = BioDiv.root + "&view=updatebadge&format=raw&collect=1&id=" + BioDiv.newBadgeId + classStr;
		jQuery('#besBadge').load(url, badgeCollectLoaded);
		
	}
	else if ( BioDiv.newAwardId ) {
		
		jQuery("#awardModal").modal('show');
		
		jQuery('#besAward').empty();
		
		let url = BioDiv.root + "&view=award&collect=1&format=raw&collect=1&id=" + BioDiv.newAwardId + classStr;
		jQuery('#besAward').load(url, awardLoaded);
		
	}
	
	jQuery('.modal').on("hidden.bs.modal", function (e) { 
        if (jQuery('.modal:visible').length) { 
            jQuery('body').addClass('modal-open'); 
        }
    });
	
	badgesLoaded();
	
	jQuery(".printCert").click(loadCertificate);
	
});
