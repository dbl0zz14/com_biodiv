

function uploadDone () {
	
	let setId = jQuery("#resourceSet").data("set_id");
	//let url = BioDiv.root + "&view=resourceset&set_id=" + setId;
	//jQuery("#displayArea").load(url, resourceListLoaded);
	
	let url = "bes-resource-set?set_id=" + setId;
	window.location.href = url;
	
}


// Filter the resources list on search
// function filterResourcesByType () {
	// //var value = jQuery(this).find(".filterText").val().toLowerCase();
	
	// let value = jQuery(this).find(".filterText")[0].innerHTML;
		
	// jQuery(".resourceCardType").filter(function() {
		
		// let thisText = this.innerHTML;
		
		// let isMatch = thisText.indexOf(value) > -1;
	  
		// if ( isMatch ) {
			// jQuery(this).parents(".resourceCardColumn").show();
		// }
		// else {
			// let thisPanel = jQuery(this).parents(".resourceCardColumn");
			// thisPanel.hide();
		// }
	// });
	
// }


// function filterResourcesByGroup () {
	
	// let id = jQuery(this).attr("id");
	// let idbits = id.split("_");
	// let groupType = idbits.pop();
	
	// let groupTypeClass = "is" + groupType;
		
	// jQuery(".resource_panel_body").filter(function() {
		
		// let isMatch = jQuery(this).hasClass(groupTypeClass);
	  
		// if ( isMatch ) {
			// jQuery(this).parents(".resourceCardColumn").show();
		// }
		// else {
			// let thisPanel = jQuery(this).parents(".resourceCardColumn");
			// thisPanel.hide();
		// }
	// });
	
// }

// function filterResourcesAny () {
	
	// jQuery(".resourceCardColumn").hide();
	
	// jQuery(".resource_panel_body").each(function() {
		
		// let isMatch = jQuery(this).hasClass("filterMatch");
	  
		// if ( isMatch ) {
			// jQuery(this).parents(".resourceCardColumn").show();
		// }
		
	// });
	
// }

// function filterResourcesAll () {
	
	// jQuery(".resourceCardColumn").hide();
	
	// let checkedClasses = jQuery(".checked").map(function() { 
		
		// return jQuery(this).val(); 
	// });
			
	// jQuery(".resource_panel_body").each(function() {
		
		// let thisPanelBody = jQuery(this);
		// let matches = checkedClasses.filter ( function ( a, b ) {
			// return thisPanelBody.hasClass(b);
		// } );
		
		// if ( matches.length == checkedClasses.length ) {
			// thisPanelBody.parents(".resourceCardColumn").show();
		// }
		
	// });
	
// }

function searchWithFilters ( page = 1 ) {
	
	let checkedClasses = [];
	
	jQuery("input:checked").each( function () {
		
		let val = jQuery(this).val();
	
		checkedClasses.push(val);
	} );
	
	// let url = window.location.href;
	
	// let hasParam = url.indexOf('?') >= 0;
	
	// let filterString = "";
	// if ( hasParam ) {
		// filterString = '&filter=' + JSON.stringify(checkedClasses);
	// }
	// else {
		// filterString = '?filter=' + JSON.stringify(checkedClasses);
	// }
	
	// let filterPos = url.indexOf('&filter');
	
	// if ( filterPos > 0 ) {
		// url = url.substring(0, filterPos);
	// }
	
	// filterPos = url.indexOf('?filter');
	
	// if ( filterPos > 0 ) {
		// url = url.substring(0, filterPos);
	// }
	
	// filterPos = url.indexOf('&page');
	
	// if ( filterPos > 0 ) {
		// url = url.substring(0, filterPos);
	// }
	
	let searchStr = jQuery("#searchResources").val();
	let searchOrFilter = '';
	let pageStr = '';
	
	if ( searchStr.length > 0 ) {
		
		searchOrFilter = '?search=' + searchStr;
		
		if ( page > 1 ) {
			pageStr = '&page=' + page;
		}
	}
	else if ( checkedClasses.length > 0 ) {
		
		searchOrFilter = '?filter=' + JSON.stringify(checkedClasses);
		
		if ( page > 1 ) {
			pageStr = '&page=' + page;
		}
	}
	else if ( page > 1 ) {
		pageStr = '?page=' + page;
	}
	
	window.location.href = 'bes-search-resources' + searchOrFilter + pageStr;
		
}


function showAllFilters () {
	jQuery("#filterMenu").show();
}

function showAllResources () {
	
	window.location.href = 'bes-search-resources';
}

function showPinnedResources () {
	
	let url = 'bes-search-resources?filter=["pin"]';
	
	window.location.href = url;
}


// function filterCheckboxChange () {
	
	// let filterClass = "." + jQuery(this).val();
    // if (jQuery(this).is(':checked')) {
        // jQuery(this).addClass("checked");
        // jQuery(filterClass).addClass("filterMatch");
    // } 
	// else {
        // jQuery(this).removeClass("checked");
        // jQuery(filterClass).removeClass("filterMatch");
    // }
// }


function resourceTypeChecked () {
	
	if (jQuery(this).is(':checked')) {
		jQuery(".resourceTypeCheckbox").not(this).prop('checked', false);
    }
}


function setsLoaded () {
	
	jQuery("#displayArea").scrollTop(0);
	//jQuery(".resourceSet").dblclick(toggleLike);
	jQuery(".likeSet").click(likeSet);
	jQuery(".unlikeSet").click(unlikeSet);
	jQuery(".faveSet").click(faveSet);
	jQuery(".unfaveSet").click(unfaveSet);
	jQuery(".pinSet").click(pinSet);
	jQuery(".unpinSet").click(unpinSet);
	jQuery(".setCarouselControl").click(handleSetCarouselClick);
	jQuery(".openSet").click(loadResourceSet);
	
	setReloadPage();
	
	// if ( jQuery("#newBadge").data("newbadgeid") ) {
		// jQuery("#badgeCompleteModal").modal('show');
	// }
}

function handleSetCarouselClick ( e ) {
	
	e.preventDefault();
}


function newFilterSearch () {
	
	emptySearchBox ();
	searchWithFilters ();
}


function newPage () {
	
	let pageBtnId = this.id;
	let idbits = pageBtnId.split("_");
	let pageNum = idbits.pop();
	
	searchWithFilters ( pageNum )
}


jQuery(document).ready(function(){

	//jQuery(".filterByType").click(filterResourcesByType);
	//jQuery(".filterByGroup").click(filterResourcesByGroup);
	jQuery(".clearFilters").click(showAllResources);
	
	jQuery("#moreFilters").click(showAllFilters);
	jQuery(".hideMiniMenu").click(hideMiniMenu);
	
	jQuery(".resourceTypeCheckbox:checkbox").change( resourceTypeChecked );
	
	//jQuery("#applyFiltersAll").click(filterResourcesAll);
	//jQuery("#applyFiltersAny").click(filterResourcesAny);
	
	jQuery("#applyFiltersSearch").click(newFilterSearch);
	
	jQuery("#showFeatured").click(showPinnedResources);
	
	jQuery(".resourceUpload").click(function (){
		
		emptySearchBox();
		
		let url = BioDiv.root + "&view=resourceupload&format=raw";
		jQuery('#uploadArea').load(url, setUploadButton);
		
	});
	
	jQuery(".setBadge").click(displayReadOnlyBadgeArticle);
	
	jQuery(".newPage").click(newPage);
	
	setsLoaded ();

});
