

// Filter the resources list on search
function filterResourcesByType () {
	//var value = jQuery(this).find(".filterText").val().toLowerCase();
	
	let value = jQuery(this).find(".filterText")[0].innerHTML;
		
	jQuery(".resourceCardType").filter(function() {
		
		let thisText = this.innerHTML;
		
		let isMatch = thisText.indexOf(value) > -1;
	  
		if ( isMatch ) {
			jQuery(this).parents(".resourceCardColumn").show();
		}
		else {
			let thisPanel = jQuery(this).parents(".resourceCardColumn");
			thisPanel.hide();
		}
	});
	
}


function filterResourcesByGroup () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let groupType = idbits.pop();
	
	let groupTypeClass = "is" + groupType;
		
	jQuery(".resource_panel_body").filter(function() {
		
		let isMatch = jQuery(this).hasClass(groupTypeClass);
	  
		if ( isMatch ) {
			jQuery(this).parents(".resourceCardColumn").show();
		}
		else {
			let thisPanel = jQuery(this).parents(".resourceCardColumn");
			thisPanel.hide();
		}
	});
	
}

function filterResourcesAny () {
	
	jQuery(".resourceCardColumn").hide();
	
	jQuery(".resource_panel_body").each(function() {
		
		let isMatch = jQuery(this).hasClass("filterMatch");
	  
		if ( isMatch ) {
			jQuery(this).parents(".resourceCardColumn").show();
		}
		
	});
	
}

function filterResourcesAll () {
	
	jQuery(".resourceCardColumn").hide();
	
	let checkedClasses = jQuery(".checked").map(function() { 
		
		return jQuery(this).val(); 
	});
			
	jQuery(".resource_panel_body").each(function() {
		
		let thisPanelBody = jQuery(this);
		let matches = checkedClasses.filter ( function ( a, b ) {
			return thisPanelBody.hasClass(b);
		} );
		
		if ( matches.length == checkedClasses.length ) {
			thisPanelBody.parents(".resourceCardColumn").show();
		}
		
	});
	
}

function searchWithFilters () {
	
	const checkedClasses = [];
	
	jQuery(".checked").each( function () {
		
		let val = jQuery(this).val();
	
		checkedClasses.push(val);
	} );
	
	let url = window.location.href;
	
	let hasParam = url.indexOf('?') >= 0;
	
	let filterString = "";
	if ( hasParam ) {
		filterString = '&filter=' + JSON.stringify(checkedClasses);
	}
	else {
		filterString = '?filter=' + JSON.stringify(checkedClasses);
	}
	
	let filterPos = url.indexOf('&filter');
	
	if ( filterPos > 0 ) {
		url = url.substring(0, filterPos);
	}
	
	filterPos = url.indexOf('?filter');
	
	if ( filterPos > 0 ) {
		url = url.substring(0, filterPos);
	}
	
	filterPos = url.indexOf('&page');
	
	if ( filterPos > 0 ) {
		url = url.substring(0, filterPos);
	}
	
	window.location.href = url+filterString;
		
}


function showAllFilters () {
	jQuery("#filterMenu").show();
}

function showAllResources () {
	
	let url = window.location.href;
	
	let filterPos = url.indexOf('&filter');
	
	if ( filterPos > 0 ) {
		url = url.substring(0, filterPos);
	}
	
	filterPos = url.indexOf('?filter');
	
	if ( filterPos > 0 ) {
		url = url.substring(0, filterPos);
	}
	
	filterPos = url.indexOf('&page');
	
	if ( filterPos > 0 ) {
		url = url.substring(0, filterPos);
	}
	
	window.location.href = url;
}

function filterCheckboxChange () {
	
	let filterClass = "." + jQuery(this).val();
    if (jQuery(this).is(':checked')) {
        jQuery(this).addClass("checked");
        jQuery(filterClass).addClass("filterMatch");
    } 
	else {
        jQuery(this).removeClass("checked");
        jQuery(filterClass).removeClass("filterMatch");
    }
}



jQuery(document).ready(function(){

	jQuery(".filterByType").click(filterResourcesByType);
	jQuery(".filterByGroup").click(filterResourcesByGroup);
	jQuery("#clearFilters").click(showAllResources);
	
	jQuery("#moreFilters").click(showAllFilters);
	jQuery(".hideMiniMenu").click(hideMiniMenu);
	
	jQuery(":checkbox").change( filterCheckboxChange );
	
	jQuery("#applyFiltersAll").click(filterResourcesAll);
	jQuery("#applyFiltersAny").click(filterResourcesAny);
	jQuery("#applyFiltersSearch").click(searchWithFilters);
	
	
});
