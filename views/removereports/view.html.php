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
class BioDivViewRemoveReports extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
			
			// Get all users who have generated reports
			$db = JDatabase::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
			$query->select("distinct person_id")
				->from("Report R");
				
			$db->setQuery($query);
			$this->reportPeople = $db->loadColumn();
		
			parent::display($tpl);
        }
}



?>
