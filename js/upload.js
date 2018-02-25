jQuery(document).ready(function(){
	var pickerOptions = {"format": "yyyy-mm-dd",
			     "startDate": BioDiv.min_date,
			     "endDate": BioDiv.max_date};

	var deploymentOptions = jQuery.extend({"todayHighlight": true}, pickerOptions);

	jQuery('#deployment_date').datepicker(deploymentOptions);
	jQuery('#deployment_date').datepicker('setDate', BioDiv.min_date);
	jQuery('#deployment_hours').val(BioDiv.min_hours);
	jQuery('#deployment_mins').val(BioDiv.min_mins);
	jQuery('#collection_date').datepicker(pickerOptions);
    });
