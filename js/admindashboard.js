

addPlayMedia = function () {
	
	/*
	jQuery('.media-btn').click(function (){
		
		// Clear media carousel
		jQuery('#media_carousel').empty();
	
		jQuery('#carousel_modal').modal('show');
		
		
		let sequence_id = jQuery(this).attr('data-seq_id');
		
		var url = BioDiv.root + "&view=mediacarousel&format=raw&sequence_id=" + sequence_id;
		
		jQuery.ajax(url, {'success': function(data) {
			//window.location.reload(true);
			// Try all three media types
			jQuery('#media_carousel').append(data);
			
			addFullScreenFnly();
			//jQuery('#carousel_modal').modal('show');
			
			jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
			jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});
		}
		});
		
	});
	*/
	
	jQuery('.report_select').change(function (){
		
		let allSelects = jQuery('.report_select');
		
		const filterObj = {};
		
		jQuery('.report_select').each( () => {
			
			let id = jQuery(this).attr("id");
			const idbits = id.split("_");
			let filterType = idbits.pop();
		
			filterObj[filterType] = jQuery(this).val();
		});
		
		let url = BioDiv.root + "&view=report&format=raw";
	
	});
}

// addViewSet = function () {
	// jQuery('.resource-set-btn').click(function (){
		
		// let set_id = jQuery(this).attr('data-set_id');
		
		// var url = BioDiv.root + "&view=mediacarousel&format=raw&sequence_id=" + sequence_id;
		
		// jQuery.ajax(url, {'success': function(data) {
			// //window.location.reload(true);
			// // Try all three media types
			// jQuery('#media_carousel').append(data);
			
			// addFullScreenFnly();
			// //jQuery('#carousel_modal').modal('show');
			
			// jQuery('#fullscreen-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Full screen', 'placement': 'top'});
			// jQuery('#fullscreen-exit-button').tooltip({'delay': {'show':1000, 'hide': 10}, 'title': 'Exit full screen', 'placement': 'top'});
		// }
		// });
		
	// });
// }



