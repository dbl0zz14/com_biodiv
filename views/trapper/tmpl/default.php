<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>
<h1>Camera sites</h1>
<?php
if(count($this->sites) == 0){
  print "<p class='bg-warning'>No camera sites active</p>\n";
 }
 else{
   print "<table class='table tablehover'>\n";
   print "<thead><tr>\n";
   foreach($this->fields as $field => $fieldTitle){
     print "<th data-toggle='tooltip' data-placement='top' title='".$this->help[$field]."'>$fieldTitle</th>\n";
   }
   print "<th>Photos uploaded</th>\n";
   print "<th data-toggle='tooltip' data-placement='top' title='".$this->projecthelp."'>Projects</th>\n";
   print "<th>Upload</th>\n";
   print "<th>Edit</th>\n";
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
				print "<td>".codes_getName($fieldVal, $struc)."</td>";
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
				print " data-source='" . codes_getListJSON($struc) . "'>";
				print codes_getName($fieldVal, $struc) . "</a>";
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
     print "<td><a href='". BIODIV_ROOT . "&view=upload&site_id=$site_id'>" . biodiv_label("upload") . "</a></td>\n";
     print "<td data-toggle='tooltip' data-placement='top' title='Click Edit then click one of the editable fields to make a change'><a class='biodiv_edit_enable' id='biodiv_edit_site_${site_id}'>" . biodiv_label("edit") . "</a></td>\n";
	 
     print "</tr>";
   }

   print "<tbody>\n";
   print "</table>\n";
}
print "<h5 class='bg-warning highlighted add-padding-all'>Please take care when entering site details.  Most cannot be amended once photos are uploaded.  
If you need to make a change please contact us on info@mammalweb.org.</h5>\n";
print "<div class='spacer-1em'></div>\n";

//print "<form action='". BIODIV_ROOT . "&task=add_site' method='post'>\n";
//print JHtml::_('form.token');

?>
<!-- button type='submit' id='add_site' class='btn btn-primary'><i class='fa fa-plus'></i> Add site</button></p  -->

</ >
<?php
//"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'>".$imageText.
print '<button type="button" id="add_site" class="btn btn-primary">Add site</button>';

print '<div id="add_site_modal" class="modal fade" role="dialog" aria-hidden="true">';
print '  <div class="modal-dialog modal-sm">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '      </div>';
print '      <div class="modal-body">';
print '        <form id="siteForm" action="'. BIODIV_ROOT . '&task=add_site" method="post">';
print JHtml::_('form.token');

print '        <div class="tab"><h2>Please enter a name for your site:</h2>';
print '          <p><input name="site_name" id="sitename" class="required" placeholder="Site name..." oninput="this.className = \'\'"></p>';
print '        </div>';

print '        <div class="tab"><h2>Where is your site located?</h2>';
print '         <p>';
print '			<div id="map_canvas" style="width:400px;height:400px;"></div>';
print '			<div class="input-group" style="width:400px">';
print '			    <span class="input-group-addon" id="basic-addon2" style="width:80px">Latitude</span>';
print '			    <input type="text" class="form-control required" id="latitude" name="latitude"/>';
print '			    <span class="input-group-addon" id="basic-addon2" style="width:80px">Longitude</span>';
print '			    <input type="text" class="form-control" id="longitude" name="longitude"/>';
print '			</div>';
print '			<div class="input-group" style="width:400px"> ';
print '			    <span class="input-group-addon" id="basic-addon1" style="width:100px">Grid Reference</span>';
print '			    <input type="text" class="form-control required" id="grid_ref" name="grid_ref"/>';
print '			</div>';
print '         </p>';

print '        </div>';

print '        <div class="tab"><h2>Can you enter extra detail about the site?</h2>';
print '          <p>';
foreach(array("habitat", "water", "purpose") as $struc){
  $struc_key = $struc.'_id';
  print '          <h5>'.$this->help[$struc_key].'</h5>';
  print '          <select name = "'.$struc_key.'" class = "form-control" >';
  foreach ( codes_getList($struc) as $thing ) {
	  list($code, $name) = $thing;
	  print "<option value='$code'>$name</option>";
  }
  print '          </select>';
}
print '          </p>';
print '        </div>';

print '        <div class="tab"><h2>Which camera are you using?</h2>';

$struc = "camera";
$struc_key = $struc.'_id';
print '          <h5>'.$this->help[$struc_key].'</h5>';
print '          <select name = "'.$struc_key.'" class = "form-control" >';
foreach ( codes_getList($struc) as $thing ) {
	list($code, $name) = $thing;
	print "<option value='$code'>$name</option>";
}
print '          </select>';

print '          <h5>'.$this->help["camera_height"].'</h5>';
//print '          <p><input name="camera_height" id="camera_height" class="checkint" placeholder="Height in cm..." oninput="this.className = \'\'"></p>';
print '          <input name="camera_height" id="camera_height" class="checkint" placeholder="Height in cm..." oninput="this.className = \'\'">';
print '        </div>';

// --------------------------------------------- Projects ------------------------------------------------------
print '        <div id="projecttab" class="tab"><h2>Which projects does this site belong to?<h2>';
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
	print '        <div id="projectdatatab" class="tab"><h2>Please enter the following for your site:</h2>';
	print '        <p>';
	$unique_strucs = array_unique(array_column($this->projectsitedata, 'struc'));
	foreach ( $unique_strucs as $struc ) {
		print '<div id="'.$struc.'_section" class="struc_section">';
		$meta = codes_getMeta($struc);
		print '          <h5>'.$meta["helptext"].'</h5>';
		print '          <select name = "'.$struc.'_id" class = "form-control sitedata" >';
		foreach ( codes_getList($struc) as $thing ) {
			list($code, $name) = $thing;
			print "<option value='$code'>$name</option>";
		}
		print '          </select>';
		print '</div>';
		
	}
	print '        <div id="noprojectdata">No additional data is needed for the projects you have selected, please choose Next</div>';
	print '        </p>';
	print '        </div>';
}

// ----------------------------- Notes -----------------------------------
print '        <div class="tab"><h2>Add any other information about the site here:</h2>';
print '          <p><input name="notes" placeholder="Notes..." oninput="this.className = \'\'"></p>';
print '        </div>';

print '        <div style="overflow:auto;">';
print '          <div style="float:right;">';
print '            <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>';
print '            <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>';
print '          </div>';
print '        </div>';

print '        <div style="text-align:center;margin-top:40px;">';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
print '          <span class="step"></span>';
if ( count($this->projectsitedata) > 0 ) {
	print '          <span id="projectdatastep" class="step"></span>';
}
print '          <span class="step"></span>';
print '        </div>';

print '        </form>';

print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>';
print '      </div>';
print '    </div>';

print '  </div>';
print '</div>';





?>
<?php
// Live site or test env JHTML::script("https://maps.googleapis.com/maps/api/js?key=AIzaSyAEq1lqv5U0cu2NObRiHnSlbkkynsiRcHY");
JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
JHTML::script("com_biodiv/geodesy-master/vector3d.js", true, true);
JHTML::script("com_biodiv/geodesy-master/latlon-ellipsoidal.js", true, true);
JHTML::script("com_biodiv/geodesy-master/osgridref.js", true, true);
JHTML::script("com_biodiv/geodesy-master/dms.js", true, true);

JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/trapper.js", true, true);
JHTML::script("com_biodiv/mapselect.js", true, true);
?>



