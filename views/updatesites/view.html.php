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
class BioDivViewUpdatesites extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
			// Create a list of all sites where lat and long are both 0
			$db = JDatabase::getInstance(dbOptions());
			$query = $db->getQuery(true);
			$query->select("site_id, grid_ref, latitude, longitude")
				->from("Site")
				->where("latitude=0 and longitude=0 and grid_ref != ''");

			$db->setQuery($query);
			$this->sites = $db->loadAssocList("site_id");
		
			parent::display($tpl);
        }
}



?>
