

function suggestedLoaded () {
	
	tasksLoaded();
}


jQuery(document).ready(function(){
	
	let url = BioDiv.root + "&view=badges&format=raw&suggest=1";
	jQuery("#displayArea").load(url, suggestedLoaded);
	
});
