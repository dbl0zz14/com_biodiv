jQuery(document).ready(function(){
	
	jQuery('.topic-btn').click(function (){
		let topic_id = jQuery(this).attr("data-topic");
		jQuery('#topic_helplet_' + topic_id).empty();
		jQuery('#intro_modal_'+topic_id).modal('show');
		let url = BioDiv.root + "&view=ajax&format=raw&option_id=" + topic_id;
		jQuery('#topic_helplet_' + topic_id).load(url);
	});
	
});
