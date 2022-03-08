




jQuery(document).ready(function(){
	
	let url = BioDiv.root + "&view=badges&format=raw&collect=1";
	jQuery("#displayArea").load(url, tasksLoaded);
	
});
