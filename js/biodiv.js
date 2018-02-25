jQuery(document).ready(function(){

	jQuery.fn.editable.defaults.disabled = true;
	jQuery.fn.editable.defaults.mode = 'inline';
	jQuery.fn.editableform.buttons =
'<button type="submit" class="btn btn-success editable-submit btn-mini"><i class="fa fa-check"></i></button>' +
	    '<button type="button" class="btn editable-cancel btn-mini"><i class="fa fa-times"></i></button>';

	BioDiv.ajaxRoot = BioDiv.root + '&task=ajax&format=raw';

	BioDiv.add = function (struc, fields){
	    jQuery.post(BioDiv.ajaxRoot, 
	{'action': 'add',
	 'fields': fields,
	 'struc': struc},
			function(data){
			    alert("Done add");});
	};

	// x-editable set up
	jQuery('.biodiv_editable').editable();

	// x-editable for the site projects checklist
	// Reduce this to simpler version.....
	jQuery('.biodiv_editable_checklist').editable(
	{
	params: function (params) {
	var someObject = {};
	someObject.name = params.name;
	someObject.pk = params.pk;
	someObject.value = params.value.join(",");
	return someObject;
	}}
	);
	

	// editing enable
	jQuery('.biodiv_edit_enable').click(function (){
		//		jQuery('.biodiv_editable').editable('disable');
		var id = jQuery(this).attr('id');
		jQuery('.' + id).editable('toggleDisabled');
	    });

	jQuery('[data-toggle="tooltip"]').tooltip({'delay': {'show': 1000, 'hide': 10}});
});
