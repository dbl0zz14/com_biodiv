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
class BioDivViewSite extends JViewLegacy
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
	  $this->fields = array("site_name" => "Site",
				"camera_id" => "Camera Type",
				"camera_height" => "Camera Height",
				"placement_id" => "Placement");

	  
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
        }
}



?>