<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewTrapper extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
                // Assign data to the view
	  $this->fields = array("site_name" => "Site Name",
				"grid_ref" => "OS grid reference or lat long");
	  $this->help = array("site_name" => "Site name: Something specific that reminds you of where the camera trap is. E.g. \"backyard\" or \"woods next to my house\".",
				"grid_ref" => "The location of the site: Please note this cannot be changed once photos are uploaded.");
	  foreach(array("habitat", "purpose", "camera") as $struc){
	    $this->fields[$struc . "_id"] = codes_getTitle($struc);
	    $meta = codes_getMeta($struc);
	    $this->help[$struc . "_id"] = $meta['helptext'];
	  }
	  $this->fields['water_id'] = "Can you/the camera see water?";
	  $this->help['water_id'] = "Can you/the camera see water? This means if any bodies of water (e.g. ponds, streams, ocean) are in the camera trap's view.";
	  $this->fields["camera_height"] = "Camera Height (cm)";
	  $this->help["camera_height"] = "Camera height: Please enter the approximate height of the camera off the ground. The unit is centimetres.";
	  $this->fields["notes"] = "Notes";
	  $this->help["notes"] = "Notes: Please note any other information pertinent to this location, such as \"there is a bird feeder nearby\".";
	  
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $dbFields = array_keys($this->fields);
	  $dbFields[] = "site_id";
	  $query->select($db->quoteName($dbFields));
	  $query->from("Site");
	  $query->where("person_id = " . (int)userID());

	  $db->setQuery($query);
	  $this->sites = $db->loadAssocList("site_id");

	  $this->siteCount = array();

	  foreach($this->sites as $site_id => $site){
	    $query = $db->getQuery(true);
	    $query->select("COUNT(*)");
	    $query->from("Photo");
	    $query->where("site_id = " . $site_id);
	    $db->setQuery($query);
	    $numPhotos = $db->loadResult();
	    $this->siteCount[$site_id] = $numPhotos;
	  }
	  
	  // Projects additions.
	  $this->projecthelp = "All projects which this site and this user are members of.";
	  $this->projects = array();
	  
	  $this->userprojects = myTrappingProjects();
	  
	  // For each user project get any additional data required
	  $this->projectsitedata = getSiteDataStrucs(array_keys($this->userprojects));
	  
	  $this->projectsitedataJSON = array();
	  
	  $project_ids = array_keys($this->userprojects);
	  foreach ($project_ids as $project_id ) {
		  $strucs = array_column( array_filter($this->projectsitedata, function ($element) use ($project_id) {
				return ($element["project_id"] == $project_id);
			}), "struc");
			if (count($strucs) > 0 ) {
				$this->projectsitedataJSON[$project_id] = json_encode($strucs);
			}
	  }
	    
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
		
		$this->projects[$site_id] = array_keys($intersectprojects);
		
	  }

	  // Display the view
	  parent::display($tpl);

	  // make sure sequences are up-to-date
/*
	  $query = $db->getQuery(true);
	  $query->select("distinct upload_id");
	  $query->from("Photo");
	  $query->where("person_id = " . (int)userID());
	  $query->where("sequence_id = 0");
	  $db->setQuery($query);
	  $upload_ids = $db->loadColumn();
	  foreach($upload_ids as $upload_id){
	    sequencePhotos($upload_id);
	  }
*/
        }
}



?>
