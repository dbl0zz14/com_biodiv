<?php

// No direct access to this file
defined('_JEXEC') or die;

// Class to hold generic site stuff including generating creation wizard
class SiteHelper {
	
	// An array of the children of this project.
	private $userprojects;
	private $projectsitedata;
	private $fields;
	private $help;
	private $sites;
	private $isCamera;
	private $justRecorded;
	
	function __construct( $isCamera = true )
	{
		$this->isCamera = $isCamera;
		$this->justRecorded = false;
		
		$trappingProjects = myTrappingProjects();
		$this->userprojects = array();
		foreach ( $trappingProjects as $id=>$projectString ) {
			$this->userprojects[$id] = str_replace('\'', '&#39', $projectString);
		}
		
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
	
	// Set to true or false to indicate if we will be uploading a file recorded within the website
	public function setJustRecorded ( $justRecorded ) {
		$this->justRecorded = $justRecorded;
	}
	
	public function getHelpArray() {
		
		return $this->help;
	}
	
	public function setHelpArray( $isCamera = true ) {
		
		
		$this->help = array("site_name" => JText::_("COM_BIODIV_TRAPPER_SITE_HELP"),
				"grid_ref" => JText::_("COM_BIODIV_TRAPPER_LOC_HELP"));


		$this->help['habitat_id'] = JText::_("COM_BIODIV_TRAPPER_HAB_HELP");
		
		if ( $isCamera ) $this->help['purpose_id'] = JText::_("COM_BIODIV_TRAPPER_PUR_HELP");
		
		$this->help['camera_id'] = str_replace("'", "&apos;", JText::_("COM_BIODIV_TRAPPER_CAM_HELP"));

		$this->help['water_id'] = JText::_("COM_BIODIV_TRAPPER_WATER_HELP");
		
		if ( $isCamera ) $this->help["camera_height"] = JText::_("COM_BIODIV_TRAPPER_HEIGHT_HELP");
		
		$this->help["notes"] = JText::_("COM_BIODIV_TRAPPER_HEIGHT_HELP");

	}
	
	public function getFieldsArray() {
		
		return $this->fields;
	}
	
	public function setFieldsArray( $isCamera = true ) {
		
		$this->fields = array("site_name" => JText::_("COM_BIODIV_TRAPPER_SITE_NAME"),
				"grid_ref" => JText::_("COM_BIODIV_TRAPPER_LAT_LON"));
		
		$this->fields['habitat_id'] = JText::_("COM_BIODIV_TRAPPER_HABITAT");
		
		if ( $isCamera ) $this->fields['purpose_id'] = JText::_("COM_BIODIV_TRAPPER_PURPOSE");
		
		$this->fields['camera_id'] = JText::_("COM_BIODIV_TRAPPER_CAMERA");
		
		$this->fields['water_id'] = JText::_("COM_BIODIV_TRAPPER_SEE_WATER");
		
		if ( $isCamera ) $this->fields["camera_height"] = JText::_("COM_BIODIV_TRAPPER_CAM_HEIGHT");
		
		$this->fields["notes"] = JText::_("COM_BIODIV_TRAPPER_NOTES");
		
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
	
	public function generateSiteCreationModal( $withUpload = false , $defaultProjectId = 1, $badgeId = null, $classId = null ) {
		
		
		// Set up the meta data needed for the modal
		$help = $this->getHelpArray( $this->isCamera );

		$projectsitedataJSON = $this->getProjectSiteDataJSON();
		
		$task = 'add_site';
		if ( $withUpload ) $task = 'add_site_and_upload';
		if ( $this->justRecorded ) $view = 'record'; 
		
		print '<div id="add_site_modal" class="modal fade" role="dialog" aria-hidden="true" >';
		print '  <div class="modal-dialog modal-sm">';

		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		print '      <div class="modal-header">';
		print '      </div>';
		print '      <div class="modal-body">';
		
		if ( $this->justRecorded ) {
			print '        <form id="siteForm" action="'. BIODIV_ROOT . '&view=' . $task . '" method="post">';
		}
		else if ( $badgeId && $classId ) {
			print '        <form id="siteForm" action="'. BIODIV_ROOT . '&task=' . $task . ' &badge=' . $badgeId . '&class_id='. $classId . '" method="post">';
		}
		else {
			print '        <form id="siteForm" action="'. BIODIV_ROOT . '&task=' . $task . '" method="post">';
		}
		print JHtml::_('form.token');

		print '        <div class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_ENTER_SITE").'</h2>';
		print '          <p><input name="site_name" id="sitename" class="required" placeholder="'.JText::_("COM_BIODIV_TRAPPER_SITE_NAME").'..." oninput="this.className = \'\'"></p>';
		print '        </div>';

		print '        <div class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_SITE_LOC").'</h2>';
		print '        <h5>'.JText::_("COM_BIODIV_TRAPPER_MARKER").'<h5>';
		print '         <p>';


		print '			<div id="map_canvas" style="width:100%;height:400px"></div>';
		print '			<div class="input-group" style="width:100%">';
		print '			    <span class="input-group-addon" id="basic-addon2" style="width:20%">'.JText::_("COM_BIODIV_TRAPPER_LAT").'</span>';
		print '			    <input type="text" class="form-control required" id="latitude" name="latitude"/>';
		print '			    <span class="input-group-addon" id="basic-addon2" style="width:20%">'.JText::_("COM_BIODIV_TRAPPER_LON").'</span>';
		print '			    <input type="text" class="form-control required" id="longitude" name="longitude"/>';
		print '			</div>';
		print '			<div class="input-group" style="width:100%"> ';
		print '			    <span class="input-group-addon" id="basic-addon1" style="width:25%">'.JText::_("COM_BIODIV_TRAPPER_GRID").'</span>';
		print '			    <input type="text" class="form-control" id="grid_ref" name="grid_ref"/>';
		print '			</div>';
		

		print '         <div id="latlonhelp" data-help="'.JText::_("COM_BIODIV_TRAPPER_LAT_LON_HELP").'"></div>';
		
		print '         </p>';

		print '        </div>'; // tab

		print '        <div class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_EXTRA_DET").'</h2>';
		print '          <p>';
		
		if ( $this->isCamera ) $strucArray = array("habitat", "water", "purpose");
		else $strucArray = array("habitat", "water");
		foreach($strucArray as $struc){
		  $struc_key = $struc.'_id';
		  print '          <h5>'.$help[$struc_key].'</h5>';
		  print '          <select name = "'.$struc_key.'" class = "form-control required" >';
		  print '            <option value="" disabled selected hidden>'.JText::_("COM_BIODIV_TRAPPER_PLS_SELECT").'</option>';
		  foreach ( codes_getList($struc."tran") as $thing ) {
			  list($code, $name) = $thing;
			  print "<option value='$code'>$name</option>";
		  }
		  print '          </select>';
		}
		print '          </p>';
		print '        </div>';

		print '        <div class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_WHICH_CAM").'</h2>';

		$struc = "camera";
		$struc_key = $struc.'_id';
		print '          <h5>'.$help[$struc_key].'</h5>';
		print '          <select name = "'.$struc_key.'" class = "form-control required" >';
		print '            <option value="" disabled selected hidden>'.JText::_("COM_BIODIV_TRAPPER_PLS_SELECT").'</option>';
		foreach ( codes_getList($struc."tran") as $thing ) {
			list($code, $name) = $thing;
			print "<option value='$code'>$name</option>";
		}
		print '          </select>';
		
		if ( $this->isCamera ) {
			print '          <h5>'.$help["camera_height"].'</h5>';
			print '          <input name="camera_height" id="camera_height" class="checkint" placeholder="'.JText::_("COM_BIODIV_TRAPPER_HT_CM").'" oninput="this.className = \'\'">';
		}
		
		print '        </div>';

		// --------------------------------------------- Projects ------------------------------------------------------
		print '        <div id="projecttab" class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_WHICH_PRJ").'<h2>';
		print '        <h5>'.JText::_("COM_BIODIV_TRAPPER_CTRL_CMD").'<h5>';
		print '          <select id="projectselect" name = "project_ids[]" class = "form-control" size="15" multiple>';
		
		$default_project = getSetting('default_project');
			
		foreach($this->userprojects as $proj_id=>$proj_name){
		  $strucs_attribute = "";
		  if ( array_key_exists($proj_id, $projectsitedataJSON)) $strucs_attribute = $projectsitedataJSON[$proj_id];
		  if ( $proj_id == $default_project ) {
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
			print '        <div id="projectdatatab" class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_PLS_ENTER").'</h2>';
			print '        <p>';
			$unique_strucs = array_unique(array_column($this->projectsitedata, 'struc'));
			foreach ( $unique_strucs as $struc ) {
				print '<div id="'.$struc.'_section" class="struc_section">';
				$tranStr = "COM_BIODIV_TRAPPER_" . strtoupper($struc) . "_HELP";
				print '          <h5>'.JText::_($tranStr).'</h5>';
				print '          <select name = "'.$struc.'_id" class = "form-control sitedata required" >';
				print '            <option value="" disabled selected hidden>'.JText::_("COM_BIODIV_TRAPPER_PLS_SELECT").'</option>';
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
			print '        <div id="noprojectdata">'.JText::_("COM_BIODIV_TRAPPER_NO_ADDIT").'</div>';
			print '        </p>';
			print '        </div>';
		}

		// ----------------------------- Notes -----------------------------------
		print '        <div class="tab"><h2>'.JText::_("COM_BIODIV_TRAPPER_ADD_ANY").'</h2>';
		print '          <p><input name="notes" placeholder="'.JText::_("COM_BIODIV_TRAPPER_NOTES").'..." oninput="this.className = \'\'"></p>';
		print '        </div>';

		print '        <div style="overflow:auto;">';
		print '          <div style="float:right;">';
		print '            <button type="button" id="prevBtn" class="btn btn-success btn-lg" onclick="nextPrev(-1)">'.JText::_("COM_BIODIV_TRAPPER_PREV").'</button>';
		print '            <button type="button" id="nextBtn" class="btn btn-success btn-lg" onclick="nextPrev(1)" data-next="'.JText::_("COM_BIODIV_TRAPPER_NEXT").'" data-submit="'.JText::_("COM_BIODIV_TRAPPER_SUBMIT").'">'.JText::_("COM_BIODIV_TRAPPER_NEXT").'</button>';
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
		print '        <button type="button" class="btn btn-primary btn-lg" data-dismiss="modal">'.JText::_("COM_BIODIV_TRAPPER_CANCEL").'</button>';
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
		
		print '        <form id="uploadForm" action="'. BIODIV_ROOT . '&view=upload" method="POST">';
		
		print '      <div class="modal-header">';
		print '      </div>';
		print '      <div class="modal-body">';
		print JHtml::_('form.token');
		
		print '        <div><h2>'.JText::_("COM_BIODIV_TRAPPER_SELECT_SITE").'</h2>';
		print '        </div>';
		
		print '          <select name = "site_id" class = "form-control required" >';
		print '            <option value="" disabled selected hidden>'.JText::_("COM_BIODIV_TRAPPER_PLS_SELECT").'</option>';
		foreach ( $sites as $site ) {
		  print "<option value='".$site['site_id']."'>".$site['site_name']."</option>";
		}
		print '          </select>';

		print '      </div>';
		print '      <div class="modal-footer">';
		print '        <button type="submit" class="btn btn-success btn-lg" >'.JText::_("COM_BIODIV_TRAPPER_UPLOAD").'</button>'.'<button type="button" class="btn btn-primary btn-lg" data-dismiss="modal">'.JText::_("COM_BIODIV_TRAPPER_CANCEL").'</button>';
		print '      </div>';
		print '        </form>';
		
		print '    </div>'; // modal-content

		print '  </div>';
		print '</div>';
	}
	
	
}



?>

