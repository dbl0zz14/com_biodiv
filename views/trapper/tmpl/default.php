<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

print '<h1>' . $this->translations['cam_sites']['translation_text']. '</h1>';

print "<h5 class='bg-warning highlighted add-padding-all'>".$this->translations['site_warn']['translation_text']."</h5>\n";
//print "<div class='spacer-1em'></div>\n";

print '<button type="button" id="add_site" class="btn btn-primary">'.$this->translations['add_site']['translation_text'].'</button>';


if(count($this->sites) == 0){
  print "<p class='bg-warning'>".$this->translations['no_sites']['translation_text']."</p>\n";
 }
 else{
   print "<table class='table tablehover'>\n";
   print "<thead><tr>\n";
   foreach($this->fields as $field => $fieldTitle){
     print "<th data-toggle='tooltip' data-placement='top' title='".$this->help[$field]."'>$fieldTitle</th>\n";
   }
   print "<th>".$this->translations['ph_upl']['translation_text']."</th>\n";
   print "<th data-toggle='tooltip' data-placement='top' title='".$this->projecthelp."'>".$this->translations['projects']['translation_text']."</th>\n";
   print "<th>".$this->translations['upload']['translation_text']."</th>\n";
   print "<th>".$this->translations['edit']['translation_text']."</th>\n";
   print "</tr></thead>\n";

   print "<tbody>\n";


   foreach($this->sites as $site_id => $site){
     print "<tr>";
	 // Certain fields are no longer editable after photos are uploaded
	 if ( $this->siteCount[$site_id] > 0 ) {
		 foreach($this->fields as $field => $fieldTitle){
			$fieldVal = $site[$field];
	   
			switch($field){
				case "grid_ref":
				print "<td>".$fieldVal." <i class='fa fa-map-marker'/></td>";
				break;
		   
				case "camera_height":
				print "<td>".$fieldVal."</td>\n";
				break;
			
				case "landuse_id":
				case "habitat_id":
				case "purpose_id":
				case "camera_id":
				case "placement_id":
				case "water_id":
				$bits = explode("_", $field);
				$struc = array_shift($bits);
				if($fieldVal == 0){
					$fieldVal = '';
				}
				print "<td>".codes_getName($fieldVal, $struc."tran")."</td>";
				break;
		   
				case "site_name":
				$editLink = "<a href='#'";
				$editLink .= " data-url='" . BIODIV_ROOT . "&task=ajax_update_site&format=raw'";
				$editLink .= " data-pk='" . (int)$site_id . "'";
				$editLink .= " data-name='$field'";
				$editLink .= " class='biodiv_editable biodiv_edit_site_${site_id}'";
				print "<td>";
				print $editLink. " data-type='text'>" . $fieldVal . '</a></td>';
				break;
     
				case "notes":
				$editLink = "<a href='#'";
				$editLink .= " data-url='" . BIODIV_ROOT . "&task=ajax_update_site&format=raw'";
				$editLink .= " data-pk='" . (int)$site_id . "'";
				$editLink .= " data-name='$field'";
				$editLink .= " class='biodiv_editable biodiv_edit_site_${site_id}'";
				print "<td>";
				print $editLink. " data-type='textarea'>" . $fieldVal . '</a></td>';
				break;
			}
		}
	}
	else {	// If no photos have been loaded then everything is editable
		foreach($this->fields as $field => $fieldTitle){
			$fieldVal = $site[$field];
			if($field == "grid_ref"){
				$editLink="<a href='" . BIODIV_ROOT . "&view=mapselect&site_id=${site_id}'";
			}
			else{
				// set up for x-editable
				// see http://vitalets.github.io/x-editable/docs.html
				$editLink = "<a href='#'";
				$editLink .= " data-url='" . BIODIV_ROOT . "&task=ajax_update_site&format=raw'";
				$editLink .= " data-pk='" . (int)$site_id . "'";
				$editLink .= " data-name='$field'";
				$editLink .= " class='biodiv_editable biodiv_edit_site_${site_id}'";
			}


			print "<td>";

			switch($field){
				case "grid_ref":
				print $editLink.">" . $fieldVal . " <i class='fa fa-map-marker'/></a>";
				break;

				case "site_name":
				case "camera_height":
				print $editLink. " data-type='text'>" . $fieldVal . '</a>';
				break;

				case "notes":
				print $editLink. " data-type='textarea'>" . $fieldVal . '</a>';
				break;

				case "landuse_id":
				case "habitat_id":
				case "purpose_id":
				case "camera_id":
				case "placement_id":
				case "water_id":
				$bits = explode("_", $field);
				$struc = array_shift($bits);
				if($fieldVal == 0){
					$fieldVal = '';
				}
				print $editLink;
				print " data-type='select'";
				print " data-defaultValue='?'";
				print " data-source='" . codes_getListJSON($struc."tran") . "'>";
				print codes_getName($fieldVal, $struc."tran") . "</a>";
				break;
			}
			print "</td>\n";

		}
	 }
     print "<td>" . $this->siteCount[$site_id] . "</td>\n";
	 
	 // Projects additions
	 $userProjects = addCSlashes(json_encode($this->userprojects),"'");
	 $siteProjects = json_encode($this->projects[$site_id]);
     $editLink = "<a href='#'";
	 $editLink .= " data-url='" . BIODIV_ROOT . "&task=ajax_update_site_projects&format=raw'";
	 $editLink .= " data-pk='" . (int)$site_id . "'";
	 $editLink .= " data-name='projects'";
	 $editLink .= " data-source='" . $userProjects . "'";
	 $editLink .= " data-value='" . $siteProjects . "'";
	 $editLink .= " class='biodiv_editable_checklist biodiv_edit_site_${site_id}'";
	 $editLink .= " data-type='checklist'></a>";
	 print "<td>" . $editLink . "</td>\n";
     print "<td><a href='". BIODIV_ROOT . "&view=upload&site_id=$site_id'>" . biodiv_label_icons("upload", $this->translations['upload']['translation_text']) . "</a></td>\n";
     print "<td data-toggle='tooltip' data-placement='top' title='".$this->translations['edit_help']['translation_text']."'><a class='biodiv_edit_enable' id='biodiv_edit_site_${site_id}'>" . biodiv_label_icons("edit", $this->translations['edit']['translation_text']) . "</a></td>\n";
	 
     print "</tr>";
   }

   print "<tbody>\n";
   print "</table>\n";
}

//print "<form action='". BIODIV_ROOT . "&task=add_site' method='post'>\n";
//print JHtml::_('form.token');

?>
<!-- button type='submit' id='add_site' class='btn btn-primary'><i class='fa fa-plus'></i> Add site</button></p  -->

</ >
<?php
//"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'>".$imageText.
$this->siteHelper->generateSiteCreationModal();
/*
print '<div id="add_site_modal" class="modal fade" role="dialog" aria-hidden="true" >';
print '  <div class="modal-dialog modal-sm">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '      </div>';
print '      <div class="modal-body">';
print '        <form id="siteForm" action="'. BIODIV_ROOT . '&task=add_site" method="post">';
print JHtml::_('form.token');

print '        <div class="tab"><h2>'.$this->translations['enter_site']['translation_text'].'</h2>';
print '          <p><input name="site_name" id="sitename" class="required" placeholder="'.$this->translations['site_name']['translation_text'].'..." oninput="this.className = \'\'"></p>';
print '        </div>';

print '        <div class="tab"><h2>'.$this->translations['site_loc']['translation_text'].'</h2>';
print '        <h5>'.$this->translations['marker']['translation_text'].'<h5>';
print '         <p>';


print '			<div id="map_canvas" style="width:400px;height:400px;"></div>';
print '			<div class="input-group" style="width:400px">';
print '			    <span class="input-group-addon" id="basic-addon2" style="width:80px">'.$this->translations['lat']['translation_text'].'</span>';
print '			    <input type="text" class="form-control required" id="latitude" name="latitude"/>';
print '			    <span class="input-group-addon" id="basic-addon2" style="width:80px">'.$this->translations['lon']['translation_text'].'</span>';
print '			    <input type="text" class="form-control required" id="longitude" name="longitude"/>';
print '			</div>';
print '			<div class="input-group" style="width:400px"> ';
print '			    <span class="input-group-addon" id="basic-addon1" style="width:100px">'.$this->translations['grid']['translation_text'].'</span>';
print '			    <input type="text" class="form-control" id="grid_ref" name="grid_ref"/>';
print '			</div>';


print '         <div id="latlonhelp" data-help="'.$this->translations['lat_lon_help']['translation_text'].'"></div>';
print '         </p>';

print '        </div>';

print '        <div class="tab"><h2>'.$this->translations['extra_det']['translation_text'].'</h2>';
print '          <p>';
foreach(array("habitat", "water", "purpose") as $struc){
  $struc_key = $struc.'_id';
  print '          <h5>'.$this->help[$struc_key].'</h5>';
  print '          <select name = "'.$struc_key.'" class = "form-control required" >';
  print '            <option value="" disabled selected hidden>'.$this->translations['pls_select']['translation_text'].'</option>';
  foreach ( codes_getList($struc."tran") as $thing ) {
	  list($code, $name) = $thing;
	  print "<option value='$code'>$name</option>";
  }
  print '          </select>';
}
print '          </p>';
print '        </div>';

print '        <div class="tab"><h2>'.$this->translations['which_cam']['translation_text'].'</h2>';

$struc = "camera";
$struc_key = $struc.'_id';
print '          <h5>'.$this->help[$struc_key].'</h5>';
print '          <select name = "'.$struc_key.'" class = "form-control required" >';
 print '            <option value="" disabled selected hidden>'.$this->translations['pls_select']['translation_text'].'</option>';
 foreach ( codes_getList($struc."tran") as $thing ) {
	list($code, $name) = $thing;
	print "<option value='$code'>$name</option>";
}
print '          </select>';

print '          <h5>'.$this->help["camera_height"].'</h5>';
//print '          <p><input name="camera_height" id="camera_height" class="checkint" placeholder="Height in cm..." oninput="this.className = \'\'"></p>';
print '          <input name="camera_height" id="camera_height" class="checkint" placeholder="'.$this->translations['ht_cm']['translation_text'].'" oninput="this.className = \'\'">';
print '        </div>';

// --------------------------------------------- Projects ------------------------------------------------------
print '        <div id="projecttab" class="tab"><h2>'.$this->translations['which_prj']['translation_text'].'<h2>';
print '        <h5>'.$this->translations['ctrl_cmd']['translation_text'].'<h5>';
print '          <select id="projectselect" name = "project_ids[]" class = "form-control" size="15" multiple>';
    
foreach($this->userprojects as $proj_id=>$proj_name){
  $strucs_attribute = "";
  if ( array_key_exists($proj_id, $this->projectsitedataJSON)) $strucs_attribute = $this->projectsitedataJSON[$proj_id];
  if ( $proj_name == "MammalWeb UK" ) {
	  print "<option id='select_proj_$proj_id' value='$proj_id' data-strucs='". $strucs_attribute ."'selected>$proj_name</option>";
  }
  else {
	  print "<option id='select_proj_$proj_id' value='$proj_id' data-strucs='". $strucs_attribute ."'>$proj_name</option>";
  }
}
		
print '          </select>';
print '        </div>';

// ----------------------------- Project specific data -----------------------------------
if ( count($this->projectsitedata) > 0 ) {
	print '        <div id="projectdatatab" class="tab"><h2>'.$this->translations['pls_enter']['translation_text'].'</h2>';
	print '        <p>';
	$unique_strucs = array_unique(array_column($this->projectsitedata, 'struc'));
	foreach ( $unique_strucs as $struc ) {
		print '<div id="'.$struc.'_section" class="struc_section">';
		//$meta = codes_getMeta($struc);
		//print '          <h5>'.$meta["helptext"].'</h5>';
		print '          <h5>'.$this->translations[$struc.'_help']['translation_text'].'</h5>';
		print '          <select name = "'.$struc.'_id" class = "form-control sitedata required" >';
		print '            <option value="" disabled selected hidden>'.$this->translations['pls_select']['translation_text'].'</option>';
		foreach ( codes_getList($struc."tran") as $thing ) {
			list($code, $name) = $thing;
			print "<option value='$code'>$name</option>";
		}
		print '          </select>';
		print '</div>';
		
	}
	print '        <div id="noprojectdata">'.$this->translations['no_addit']['translation_text'].'</div>';
	print '        </p>';
	print '        </div>';
}

// ----------------------------- Notes -----------------------------------
print '        <div class="tab"><h2>'.$this->translations['add_any']['translation_text'].'</h2>';
print '          <p><input name="notes" placeholder="'.$this->translations['notes']['translation_text'].'..." oninput="this.className = \'\'"></p>';
print '        </div>';

print '        <div style="overflow:auto;">';
print '          <div style="float:right;">';
print '            <button type="button" id="prevBtn" onclick="nextPrev(-1)">'.$this->translations['prev']['translation_text'].'</button>';
print '            <button type="button" id="nextBtn" onclick="nextPrev(1)" data-next="'.$this->translations['next']['translation_text'].'" data-submit="'.$this->translations['submit']['translation_text'].'">'.$this->translations['next']['translation_text'].'</button>';
print '          </div>';
print '        </div>';

print '        <div style="text-align:center;margin-top:40px;">';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span id="projectdatastep" class="step"></span>';
print '          <span class="step"></span>';
print '        </div>';

print '        </form>';

print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
print '      </div>';
print '    </div>';

print '  </div>';
print '</div>';
*/




?>
<?php
$mapOptions = mapOptions();
$key = $mapOptions['key'];

JHTML::script("https://maps.googleapis.com/maps/api/js?key=" . $key);
//JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
JHTML::script("com_biodiv/geodesy-master/vector3d.js", true, true);
JHTML::script("com_biodiv/geodesy-master/latlon-ellipsoidal.js", true, true);
JHTML::script("com_biodiv/geodesy-master/osgridref.js", true, true);
JHTML::script("com_biodiv/geodesy-master/dms.js", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/trapper.js", true, true);
JHTML::script("com_biodiv/mapselect.js", true, true);
?>



