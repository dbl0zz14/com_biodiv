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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewDashboard extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $query->select("DISTINCT person_id");
	  $query->from("Photo");
	  $db->setQuery($query);
	  $this->spotters = $db->loadAssocList();
	  $this->spotterFields = array("person_id"=> "Spotter ID");

	  $query = $db->getQuery(true);
	  $query->select("DISTINCT person_id");
	  $query->from("Site");
	  $db->setQuery($query);
	  $this->trappers= $db->loadAssocList();
	  $this->trapperFields = array("person_id"=> "Trapper ID");

	  // Display the view
	  parent::display($tpl);
        }
}



?>