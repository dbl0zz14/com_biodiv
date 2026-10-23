
var addCurrentTab = 0; // Current tab for add is set to be the first tab (0)
var editCurrentTab = 0; // Current tab for edit is set to be the first tab (0)
var parteditCurrentTab = 0; // Current tab for partial edit is set to be the first tab (0)

// function resetTabs () {
	// currentTab = 0;
	// showTab(0);
// }step

function showTab(prefix, n) {
  // This function will display the specified tab of the form ...
  var tabclass = prefix + "tab";
  var x = document.getElementsByClassName(tabclass);
  
  // Set all to not display, then display the chosen one
  for (i = 0; i < x.length; i++) {
	 x[i].style.display = "none"; 
  }
  x[n].style.display = "block";
  
  // If this is the additional site data tab, set the strucs to display or not depending on projects selected.
  var currentTab = addCurrentTab;
  if ( prefix == 'edit' ) currentTab = editCurrentTab;
  else if ( prefix == 'partedit' ) currentTab = parteditCurrentTab;
  if ( x[currentTab].id == prefix+"_projectdatatab" ) {
	  var selectedProjectIds = jQuery('#'+prefix+'_projectselect').val();
	  
	  var strucs = [];

      for (proj_id of selectedProjectIds) {
		let newstrucs = document.getElementById(prefix+"_select_proj_" + proj_id).dataset.strucs;
        if ( newstrucs ) {
			strucs = strucs.concat(JSON.parse(newstrucs));
		}
	  }
	  let strucs_unique = [...new Set(strucs)];
	  
	  // Set everything else to display none
	  if ( strucs_unique.length == 0 ) {
		  jQuery('#'+prefix+'_noprojectdata').show();
	  }
	  else {
		  jQuery('#'+prefix+'_noprojectdata').hide();
	  }
	  // Hide everything then make selected shown and required.
	  jQuery('.'+prefix+'_struc_section').hide();
	  //jQuery('.'+prefix+'sitedata').removeClass("required");
	  jQuery('#'+prefix+"_projectdatatab").find('select').removeClass("required");
	  for (struc of strucs_unique) {
		  jQuery('#'+prefix+'_'+ struc + '_section').show();
		  let strucidname = prefix+'_' + struc + '_id';
		  jQuery("[name='" + strucidname + "']").addClass("required");
	  }
  }
  
  // ... and fix the Previous/Next buttons:
  let nextBtn = document.getElementById(prefix+"_nextBtn");
  if (n == 0) {
	document.getElementById(prefix+"_prevBtn").style.display = "none";
  } else {
	document.getElementById(prefix+"_prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
	nextBtn.innerHTML = nextBtn.dataset.submit;
  } else {
	nextBtn.innerHTML = nextBtn.dataset.next;
	//document.getElementById("nextBtn").innerHTML = "Next";
  }
  // ... and run a function that displays the correct step indicator:
  fixStepIndicator(prefix, n)
}

function nextPrev(prefix, n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName(prefix+"tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm(prefix)) return false;
  
 // Hide the current tab:
  var currentTab = addCurrentTab;
  if ( prefix == 'edit' ) currentTab = editCurrentTab;
  else if ( prefix == 'partedit' ) currentTab = parteditCurrentTab;
  
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  
  // update the counters
  if ( prefix == 'add' ) addCurrentTab = currentTab;
  else if ( prefix == 'edit' ) editCurrentTab = currentTab;
  else if ( prefix == 'partedit' ) parteditCurrentTab = currentTab;
  
  // if you have reached the end of the form... :
  if (currentTab >= x.length) {
	
	// Hide the modal
	currentElement = x[currentTab-1];
	const modalElement = currentElement.closest('.modal');
	modalElement.style.display = "none";
	
	//...the form gets submitted:
	//document.getElementById("siteForm").submit();
	jQuery("#"+prefix+"SiteForm").trigger("submit");
	return false;
  }
  // Otherwise, display the correct tab:
  showTab(prefix, currentTab);
}


function validateForm(prefix) {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  var currentTab = addCurrentTab;
  if ( prefix == 'edit' ) currentTab = editCurrentTab;
  else if ( prefix == 'partedit' ) currentTab = parteditCurrentTab;
  
  x = document.getElementsByClassName(prefix+"tab");
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
  lat = document.getElementById(prefix+"latitude");
  lon = document.getElementById(prefix+"longitude");
  if (x[currentTab].contains(lat)) {
	  if ( lat.value == 54.763213 && lon.value == -1.581919 ) {
		  lat.className += " invalid";
		  lon.className += " invalid";
		  let latlonhelp = document.getElementById(prefix+"latlonhelp")
		  latlonhelp.innerHTML = latlonhelp.dataset.help;
		  valid = false;
	  }
	  else {
		  lat.className = "form-control required";
		  lon.className = "form-control required";
		  
		  document.getElementById(prefix+"latlonhelp").innerHTML = "";
	  }
  }
  
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
	document.getElementsByClassName(prefix+"step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(prefix,n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName(prefix+"step");
  for (i = 0; i < x.length; i++) {
	x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
  x[n].className += " active";
}

jQuery(document).ready(function(){
	
	// jQuery.fn.editable.defaults.disabled = true;
	// jQuery.fn.editable.defaults.mode = 'inline';
	// jQuery.fn.editableform.buttons =
// '<button type="submit" class="btn btn-success editable-submit btn-mini"><i class="fa fa-check"></i></button>' +
	    // '<button type="button" class="btn editable-cancel btn-mini"><i class="fa fa-times"></i></button>';

	// BioDiv.ajaxRoot = BioDiv.root + '&task=ajax&format=raw';

	// BioDiv.add = function (struc, fields){
	    // jQuery.post(BioDiv.ajaxRoot, 
	// {'action': 'add',
	 // 'fields': fields,
	 // 'struc': struc},
			// function(data){
			    // alert("Done add");});
	// };

	// x-editable set up
	// jQuery('.biodiv_editable').editable();

	// // x-editable for the site projects checklist
	// // Reduce this to simpler version.....
	// jQuery('.biodiv_editable_checklist').editable(
	// {
		// params: function (params) {
		// var someObject = {};
		// someObject.name = params.name;
		// someObject.pk = params.pk;
		// someObject.value = params.value.join(",");
		// return someObject;
		// }}
	// );
	

	// // editing enable
	// jQuery('.biodiv_edit_enable').click(function (){
		// //		jQuery('.biodiv_editable').editable('disable');
		// var id = jQuery(this).attr('id');
		// jQuery('.' + id).editable('toggleDisabled');
	    // });

	// jQuery('[data-toggle="tooltip"]').tooltip({'delay': {'show': 1000, 'hide': 10}});
	
	// On Add Site click, show the modal
	jQuery('#add_site').click(function (){
		addCurrentTab = 0; // Current tab is set to be the first tab (0)
		showTab('add', addCurrentTab); // Display the current tab		
		let addSiteModal = new bootstrap.Modal(document.getElementById('add_site_modal'), {
			keyboard: false
		});
		addSiteModal.show();
		updateMap ( null, 'add' );
		
	});
	
	jQuery('.biodiv_edit_enable').click(function (){
		
		editCurrentTab = 0; // Current tab is set to be the first tab (0)
		showTab('edit', editCurrentTab); // Display the current tab		
		let editSiteModal = new bootstrap.Modal(document.getElementById('edit_site_modal'), {
			keyboard: false
		});
		
		// populate with current values
		const id = jQuery(this).attr("id");
		const idbits = id.split("_");
		const siteId = idbits.pop();
		
		jQuery("#edit_site_id").val(siteId);
		
		let qStr = "a.biodiv_edit_site_"+siteId+"[data-name='site_name']";
		let aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let textValue = aElement.text().trim(); // Trim whitespace
			jQuery("#edit_sitename").val(textValue);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='grid_ref']";
		aElement = jQuery(qStr);
		let gridRef = '';
		if (aElement.length > 0) {
			gridRef = aElement.text().trim(); // Trim whitespace
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='habitat_id']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let dataSelected = aElement.data('selected');
			jQuery("#edit_habitat_id").val(dataSelected);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='water_id']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let dataSelected = aElement.data('selected');
			jQuery("#edit_water_id").val(dataSelected);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='purpose_id']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let dataSelected = aElement.data('selected');
			jQuery("#edit_purpose_id").val(dataSelected);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='camera_id']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let dataSelected = aElement.data('selected');
			jQuery("#edit_camera_id").val(dataSelected);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='camera_height']";
		aElement = jQuery(qStr);
		let camHeight = '';
		if (aElement.length > 0) {
			camHeight = aElement.text().trim(); // Trim whitespace
			jQuery("#edit_camera_height").val(camHeight);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='projects']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let dataSelected = aElement.data('value');
			jQuery("#edit_projectselect").val(dataSelected);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='projectdata']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			const dataSelected = aElement.data('value');
			//const dataVals = JSON.parse(dataSelected[0]);
			const dataVals = dataSelected;
			dataVals.forEach(function(selected) {
				const projectDataStruc = selected["struc"];
				const optionId = selected["option_id"];
				jQuery("#edit_"+projectDataStruc+"_id").val(optionId.toString());
			});
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='notes']";
		aElement = jQuery(qStr);
		let notes = '';
		if (aElement.length > 0) {
			notes = aElement.text().trim(); // Trim whitespace
			jQuery("#edit_notes").val(notes);
		}
		
		editSiteModal.show();
		updateMapFromGridRef ( gridRef, 'edit' );
		
	});
	
	jQuery('.biodiv_partedit_enable').click(function (){
		
		parteditCurrentTab = 0; // Current tab is set to be the first tab (0)
		showTab('partedit', parteditCurrentTab); // Display the current tab		
		let parteditSiteModal = new bootstrap.Modal(document.getElementById('partedit_site_modal'), {
			keyboard: false
		});
		// populate with current values
		const id = jQuery(this).attr("id");
		const idbits = id.split("_");
		const siteId = idbits.pop();
		
		jQuery("#partedit_site_id").val(siteId);
		
		let qStr = "a.biodiv_edit_site_"+siteId+"[data-name='site_name']";
		let aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let textValue = aElement.text().trim(); // Trim whitespace
			jQuery("#partedit_sitename").val(textValue);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='projects']";
		aElement = jQuery(qStr);
		if (aElement.length > 0) {
			let dataSelected = aElement.data('value');
			jQuery("#partedit_projectselect").val(dataSelected);
		}
		
		qStr = "a.biodiv_edit_site_"+siteId+"[data-name='notes']";
		aElement = jQuery(qStr);
		let notes = '';
		if (aElement.length > 0) {
			notes = aElement.text().trim(); // Trim whitespace
			jQuery("#partedit_notes").val(notes);
		}
		
		parteditSiteModal.show();
		
	});
	
	
	
});
   
