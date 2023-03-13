

// function searchWork () {
	
	// let searchStr = jQuery("#searchWork").val();
	// let searchOrFilter = '';
	// let pageStr = '';
	
	// if ( searchStr.length > 0 ) {
		
		// searchOrFilter = '?search=' + searchStr;
		
		// if ( page > 1 ) {
			// pageStr = '&page=' + page;
		// }
	// }
	// else if ( page > 1 ) {
		// pageStr = '?page=' + page;
	// }
	
	// window.location.href = 'bes-search-resources' + searchOrFilter + pageStr;
		
// }

// function emptySearchWorkBox () {
	
	// jQuery("#searchWork").val("");
	
// }

function showAllWork () {
	
	window.location.href = 'bes-school-work';
}

function newWorkPage () {
	
	let pageBtnId = this.id;
	let idbits = pageBtnId.split("_");
	let pageNum = idbits.pop();
	
	let searchStr = jQuery("#searchWork").val();
	let searchOrFilter = '';
	let pageStr = '';
	
	if ( searchStr.length > 0 ) {
		
		searchOrFilter = '?search=' + searchStr;
		
		if ( pageNum > 1 ) {
			pageStr = '&page=' + pageNum;
		}
	}
	else if ( pageNum > 1 ) {
		pageStr = '?page=' + pageNum;
	}
	
	window.location.href = 'bes-school-work' + searchOrFilter + pageStr;
}


jQuery(document).ready(function(){
	
	jQuery(".showAllWork").click( showAllWork );
	
	jQuery(".newPage").click(newWorkPage);
	
});
