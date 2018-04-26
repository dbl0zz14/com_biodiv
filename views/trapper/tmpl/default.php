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

print "<form action='". BIODIV_ROOT . "&task=add_site' method='post'>\n";
print JHtml::_('form.token');

?>
<button type='submit' id='add_site' class='btn btn-primary'><i class='fa fa-plus'></i> Add site</button></p>

</form>
<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", true, true);
JHTML::script("com_biodiv/trapper.js", true, true);
?>



