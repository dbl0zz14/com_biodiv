




jQuery(document).ready(function(){
	
	let url = BioDiv.root + "&view=resourcelist&format=raw&student=1";
	jQuery("#displayArea").load(url, resourceListLoaded);
	
});
