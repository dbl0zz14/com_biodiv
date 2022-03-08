
	
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

jQuery(document).ready(function(){
	
	jQuery("#searchSchools").on ("keyup", searchSchools);
	
	jQuery(".school_btn").click( function () {
		
		jQuery(".displaySelectedCharts").empty();
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let schoolId = idbits.pop();
		
		let progressClass = ".schoolProgress_" + schoolId;
		// let positionClass = ".schoolPosition_" + schoolId;
		// let progressAmountClass = ".schoolProgressAmount_" + schoolId;
		
		let displayParents = jQuery(progressClass).parents(".displaySchoolCharts");
		
		displayParents.each( function() {
			let displayParentId = jQuery(this).attr("id");
			let dpIdbits = displayParentId.split("_");
			let groupId = dpIdbits.pop();
		
			let selectedClass = "#displaySelectedCharts_" + groupId;
			
			let progressChild = jQuery(this).find(progressClass);
		
			//progressChild.clone().appendTo(selectedClass);
			let rowHtml = progressChild.html();
			jQuery(selectedClass).html(rowHtml);
			
			
			// let selectedPositionClass = "#displaySelectedPosition_" + groupId;
			// let selectedProgressClass = "#displaySelectedProgress_" + groupId;
			
			// let positionChild = jQuery(this).find(positionClass);
			// let progressAmountChild = jQuery(this).find(progressAmountClass);
		
			// positionChild.clone().appendTo(selectedPositionClass);
			// progressAmountChild.clone().appendTo(selectedProgressClass);
		});
		
		// let progressRow = anime({
			// targets: [".displaySelectedCharts"],
			// // scale: [{value:1}, {value:1.3}, {value:1, delay: 250} ]
			// translateY: "2vh",
			// duration: 800,
		// });
		
		let url = BioDiv.root + "&view=schoolspotlight&format=raw&name=1&id=" + schoolId;
	
		jQuery("#schoolSpotlight").load(url);
	
	});
	
	
	let url = BioDiv.root + "&view=schoolspotlight&format=raw&name=1";
	
	jQuery("#schoolSpotlight").load(url);
	
});



