jQuery(document).ready(function(){
	
	
	const classifyProject = BioDiv.classifyProject;
	
	// if ( classifyProject ) {
		
		// const url = BioDiv.root + "&task=add_user_choice&format=raw&choice=classifyproject&value=" + classifyProject;
		// jQuery.ajax(url);
	// }

	jQuery('#classify-save').click(function (){
		
		if ( classifyProject ) {
		
			const url = BioDiv.root + "&task=add_user_choice&format=raw&choice=classifyproject&value=" + classifyProject;
			jQuery.ajax(url);
		}
		
	});
	
});

	
    
