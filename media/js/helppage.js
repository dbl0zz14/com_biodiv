

jQuery(document).ready(function(){
	
	if ( BioDiv.helptype ) {
		
		let url = BioDiv.root + "&view=helparticle&format=raw&type=" + BioDiv.helptype;
		jQuery('#displayArea').load(url, setReloadPage);
		
	}
	
});



