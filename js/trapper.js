
var currentTab = 0; // Current tab is set to be the first tab (0)

function resetTabs () {
	currentTab = 0;
	showTab(0);
}

function showTab(n) {
  // This function will display the specified tab of the form ...
  var x = document.getElementsByClassName("tab");
  
  // Set all to not display, then display the chosen one
  for (i = 0; i < x.length; i++) {
	 x[i].style.display = "none"; 
  }
  x[n].style.display = "block";
  
  // If this is the additional site data tab, set the strucs to display or not depending on projects selected.
  if ( x[currentTab].id == "projectdatatab" ) {
	  var selectedProjectIds = jQuery('#projectselect').val();
	  
	  var strucs = [];

      for (proj_id of selectedProjectIds) {
		let newstrucs = document.getElementById("select_proj_" + proj_id).dataset.strucs;
        if ( newstrucs ) {
			strucs = strucs.concat(JSON.parse(newstrucs));
		}
	  }
	  let strucs_unique = [...new Set(strucs)];
	  
	  // Set everything else to display none
	  if ( strucs_unique.length == 0 ) {
		  jQuery('#noprojectdata').show();
	  }
	  else {
		  jQuery('#noprojectdata').hide();
	  }
	  // Hide everything then make selected shown and required.
	  jQuery('.struc_section').hide();
	  jQuery('.sitedata').removeClass("required");
	  for (struc of strucs_unique) {
		  jQuery('#'+ struc + '_section').show();
		  let strucidname = "" + struc + '_id';
		  jQuery("[name='" + strucidname + "']").addClass("required");
	  }
  }
  
  // ... and fix the Previous/Next buttons:
  let nextBtn = document.getElementById("nextBtn");
  if (n == 0) {
	document.getElementById("prevBtn").style.display = "none";
  } else {
	document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
	nextBtn.innerHTML = nextBtn.dataset.submit;
  } else {
	nextBtn.innerHTML = nextBtn.dataset.next;
	//document.getElementById("nextBtn").innerHTML = "Next";
  }
  // ... and run a function that displays the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  
 // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form... :
  if (currentTab >= x.length) {
	//...the form gets submitted:
	//document.getElementById("siteForm").submit();
	jQuery("#siteForm").trigger("submit");
	return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}


function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByClassName("required");
  // A loop that checks every required input field in the current tab:
  for (i = 0; i < y.length; i++) {
	// If a field is empty...
	if (y[i].value == "") {
	  // add an "invalid" class to the field:
	  y[i].className += " invalid";
	  // and set the current valid status to false:
	  valid = false;
	}
  }
  // Validate any integer field
  y = x[currentTab].getElementsByClassName("checkint");
  // A loop that checks every checkint input field is an integer:
  for (i = 0; i < y.length; i++) {
	// If a field is empty...
	if (!Number.isInteger(y[i].value)) {
	  // add an "invalid" class to the field:
	  y[i].className += " invalid";
	  // and set the current valid status to false:
	  valid = false;
	}
  }
  // Validate lat/long is not the default value if we are on that tab.
  lat = document.getElementById("latitude");
  lon = document.getElementById("longitude");
  if (x[currentTab].contains(lat)) {
	  if ( lat.value == 54.763213 && lon.value == -1.581919 ) {
		  lat.className += " invalid";
		  lon.className += " invalid";
		  let latlonhelp = document.getElementById("latlonhelp")
		  latlonhelp.innerHTML = latlonhelp.dataset.help;
		  valid = false;
	  }
	  else {
		  lat.className = "form-control required";
		  lon.className = "form-control required";
		  
		  document.getElementById("latlonhelp").innerHTML = "";
	  }
  }
  
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
	document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
	x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
  x[n].className += " active";
}

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
	//jQuery('.biodiv_edit_enable').click(function (){
		// //		jQuery('.biodiv_editable').editable('disable');
		// var id = jQuery(this).attr('id');
		// jQuery('.' + id).editable('toggleDisabled');
	    //});

	jQuery('[data-toggle="tooltip"]').tooltip({'delay': {'show': 1000, 'hide': 10}});
	
	// On Add Site click, show the modal
	jQuery('#add_site').click(function (){
		currentTab = 0; // Current tab is set to be the first tab (0)
		showTab(currentTab); // Display the current tab		
		jQuery('#add_site_modal').modal('show');
		updateMap ( null );
		
	});
	
});
   
