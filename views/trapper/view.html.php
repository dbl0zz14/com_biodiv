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
				"grid_ref" => "OS grid reference");
	  $this->help = array("site_name" => "Site name: Something specific that reminds you of where the camera trap is. E.g. \"backyard\" or \"woods next to my house\".",
				"grid_ref" => "");
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

	  // Display the view
	  parent::display($tpl);

	  // make sure sequences are up-to-date
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
        }
}



?>