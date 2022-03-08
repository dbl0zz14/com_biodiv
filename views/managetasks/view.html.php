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
class BioDivViewManageTasks extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "ManageStudents display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("managetasks");
		
		$this->personId = userID();
		
		if ( $this->personId ) {
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
			
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