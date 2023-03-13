

function setHelpButtons () {
	
	jQuery(".nextHelp").click( function () {
		
		let btnId = this.id;
		let idbits = btnId.split("_");
		let helpId = idbits.pop();
		let nextHelpId = parseInt(helpId) + 1;
		
		jQuery(".help_" + helpId).addClass("hidden");
		jQuery(".help_" + nextHelpId).removeClass("hidden");
		
		jQuery("#t3-mainbody")[0].scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
		
		
	});
	
	jQuery(".skipHelp").click( function () {
		
		jQuery(".instructions").hide();
		jQuery(".mockModal").hide();
		jQuery(".helpOverlay").hide();
		
	});
	
	
}



jQuery(document).ready(function(){
	
	setHelpButtons ();
	
});



