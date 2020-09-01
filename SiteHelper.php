<?php

// No direct access to this file
defined('_JEXEC') or die;

// Class to hold generic site stuff including generating creation wizard
class SiteHelper {
	
	// An array of the children of this project.
	private $translations;
	private $userprojects;
	private $projectsitedata;
	private $fields;
	private $help;
	private $sites;
	private $isCamera;
	
	function __construct( $isCamera = true)
	{
		$this->translations = getTranslations("trapper");
		
		$this->isCamera = $isCamera;
		$this->userprojects = myTrappingProjects();
		$this->projectsitedata = getSiteDataStrucs(array_keys($this->userprojects));
		
		$this->setFieldsArray($isCamera);
		$this->setHelpArray($isCamera);
		
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$dbFields = array_keys($this->fields);
		$dbFields[] = "site_id";
		$query->select($db->quoteName($dbFields));
		$query->from("Site");
		$query->where("person_id = " . (int)userID());

		$db->setQuery($query);
		$this->sites = $db->loadAssocList("site_id");
		
	}
	
	public function getUserProjects () {
		return $this->userprojects;
	}
	
	public function getProjectSiteData () {
		return $this->projectsitedata;
	}
	
	public function getHelpArray() {
		
		return $this->help;
	}
	
	public function setHelpArray( $isCamera = true ) {
		
		
		$this->help = array("site_name" => $this->translations['site_help']['translation_text'],
				"grid_ref" => $this->translations['loc_help']['translation_text']);

		$this->help['habitat_id'] = $this->translations['hab_help']['translation_text'];
		
		if ( $isCamera ) $this->help['purpose_id'] = $this->translations['pur_help']['translation_text'];
		
		$this->help['camera_id'] = str_replace("'", "&apos;", $this->translations['cam_help']['translation_text']);

		$this->help['water_id'] = $this->translations['water_help']['translation_text'];
		
		if ( $isCamera ) $this->help["camera_height"] = $this->translations['height_help']['translation_text'];
		
		$this->help["notes"] = "Notes: Please note any other information pertinent to this location, such as \"there is a bird feeder nearby\".";

	}
	
	public function getFieldsArray() {
		
		return $this->fields;
	}
	
	public function setFieldsArray( $isCamera = true ) {
		
		$this->fields = array("site_name" => $this->translations['site_name']['translation_text'],
				"grid_ref" => $this->translations['lat_lon']['translation_text']);
		
		$this->fields['habitat_id'] = $this->translations['habitat']['translation_text'];
		
		if ( $isCamera ) $this->fields['purpose_id'] = $this->translations['purpose']['translation_text'];
		
		$this->fields['camera_id'] = $this->translations['camera']['translation_text'];
		
		
		$this->fields['water_id'] = $this->translations['see_water']['translation_text'];
		
		if ( $isCamera ) $this->fields["camera_height"] = $this->translations['cam_height']['translation_text'];
		
		$this->fields["notes"] = $this->translations['notes']['translation_text'];
		
	}
	
	public function getSites () {
				
		return $this->sites;
	}
	
	public function getSitePhotoCount () {
		$siteCount = array();

		$db = JDatabase::getInstance(dbOptions());
		
		foreach($this->sites as $site_id => $site){
			$query = $db->getQuery(true);
			$query->select("COUNT(*)");
			$query->from("Photo");
			$query->where("site_id = " . $site_id);
			$db->setQuery($query);
			$numPhotos = $db->loadResult();
			$siteCount[$site_id] = $numPhotos;
		}
		
		return $siteCount;
	}
	
	public function getProjects() {
		
		$projects = array();
		
		$db = JDatabase::getInstance(dbOptions());
		
		foreach($this->sites as $site_id => $site){
			// Get list of projects this site is part of and which this user is a user of
			$query = $db->getQuery(true);
			$query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname");
			$query->from("Project P");
			$query->innerJoin("ProjectSiteMap PSM ON P.project_id = PSM.project_id");
			$query->where("PSM.site_id = " . $site_id);
			$query->where("PSM.end_time is NULL" );
			$db->setQuery($query);
			$siteprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
			
			// Remove any siteprojects where this user is not a member of the project.
			$intersectprojects = array_intersect_key($this->userprojects, $siteprojects);
			
			$projects[$site_id] = array_keys($intersectprojects);
			
		 }
		 
		 return $projects;
	}
	
	public function getProjectSiteDataJSON() {
		
		// For each user project get any additional data required
		$projectsitedataJSON = array();

		$project_ids = array_keys($this->userprojects);
		foreach ($project_ids as $project_id ) {
		  $strucs = array_column( array_filter($this->projectsitedata, function ($element) use ($project_id) {
				return ($element["project_id"] == $project_id);
			}), "struc");
			if (count($strucs) > 0 ) {
				$projectsitedataJSON[$project_id] = json_encode($strucs);
			}
		}
		
		return $projectsitedataJSON;

	}
	
	public function generateSiteCreationModal( $withUpload = false , $defaultProjectId = 1 ) {
		
		
		// Set up the meta data needed for the modal
		$help = $this->getHelpArray( $this->isCamera );

		$projectsitedataJSON = $this->getProjectSiteDataJSON();
		
		$task = 'add_site';
		if ( $withUpload ) $task = 'add_site_and_upload';
		
		print '<div id="add_site_modal" class="modal fade" role="dialog" aria-hidden="true" >';
		print '  <div class="modal-dialog modal-sm">';

		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		print '      <div class="modal-header">';
		print '      </div>';
		print '      <div class="modal-body">';
		print '        <form id="siteForm" action="'. BIODIV_ROOT . '&task=' . $task . '" method="post">';
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
		
		if ( $this->isCamera ) $strucArray = array("habitat", "water", "purpose");
		else $strucArray = array("habitat", "water");
		foreach($strucArray as $struc){
		  $struc_key = $struc.'_id';
		  print '          <h5>'.$help[$struc_key].'</h5>';
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
		print '          <h5>'.$help[$struc_key].'</h5>';
		print '          <select name = "'.$struc_key.'" class = "form-control required" >';
		 print '            <option value="" disabled selected hidden>'.$this->translations['pls_select']['translation_text'].'</option>';
		 foreach ( codes_getList($struc."tran") as $thing ) {
			list($code, $name) = $thing;
			print "<option value='$code'>$name</option>";
		}
		print '          </select>';
		
		if ( $this->isCamera ) {
			print '          <h5>'.$help["camera_height"].'</h5>';
			//print '          <p><input name="camera_height" id="camera_height" class="checkint" placeholder="Height in cm..." oninput="this.className = \'\'"></p>';
			print '          <input name="camera_height" id="camera_height" class="checkint" placeholder="'.$this->translations['ht_cm']['translation_text'].'" oninput="this.className = \'\'">';
		}
		
		print '        </div>';

		// --------------------------------------------- Projects ------------------------------------------------------
		print '        <div id="projecttab" class="tab"><h2>'.$this->translations['which_prj']['translation_text'].'<h2>';
		print '        <h5>'.$this->translations['ctrl_cmd']['translation_text'].'<h5>';
		print '          <select id="projectselect" name = "project_ids[]" class = "form-control" size="15" multiple>';
			
		foreach($this->userprojects as $proj_id=>$proj_name){
		  $strucs_attribute = "";
		  if ( array_key_exists($proj_id, $projectsitedataJSON)) $strucs_attribute = $projectsitedataJSON[$proj_id];
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
					if ( $code == $defaultProjectId ) {
						print "<option value='$code' selected>$name</option>";
					}
					else {
						print "<option value='$code'>$name</option>";
					}
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
	}


public function generateSiteSelectionModal( $defaultProjectId = 1 ) {
		
		
		// Set up the meta data needed for the modal
		
		$sites = $this->getSites();

		print '<div id="select_site_modal" class="modal fade" role="dialog" aria-hidden="true" >';
		print '  <div class="modal-dialog modal-sm">';

		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		
		print '        <form id="uploadForm" action="'. BIODIV_ROOT . '&task=add_upload" method="POST">';
		
		print '      <div class="modal-header">';
		print '      </div>';
		print '      <div class="modal-body">';
		print JHtml::_('form.token');
		
		print '        <div><h2>'.$this->translations['select_site']['translation_text'].'</h2>';
		print '        </div>';
		
		print '          <select name = "site_id" class = "form-control required" >';
		  print '            <option value="" disabled selected hidden>'.$this->translations['pls_select']['translation_text'].'</option>';
		  foreach ( $sites as $site ) {
		  print "<option value='".$site['site_id']."'>".$site['site_name']."</option>";
		}
		print '          </select>';

		print '      </div>';
		print '      <div class="modal-footer">';
		print '        <button type="submit" class="btn btn-primary" >'.$this->translations['upload']['translation_text'].'</button>'.'<button type="button" class="btn btn-primary" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
		print '      </div>';
		print '        </form>';
		
		print '    </div>'; // modal-content

		print '  </div>';
		print '</div>';
	}
}



?>

