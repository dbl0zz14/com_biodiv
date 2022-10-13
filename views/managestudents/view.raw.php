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
class BioDivViewManageStudents extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("managestudents");
		
		$this->personId = userID();
		
		if ( $this->personId ) {
			
			$this->isTeacher = Biodiv\SchoolCommunity::isTeacher();
	
			if ( $this->isTeacher ) {
				
				$this->students = Biodiv\SchoolCommunity::getMyStudents();
				
				$this->tasks = Biodiv\Task::getAllDoneTasks ();
				
			}
		
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>