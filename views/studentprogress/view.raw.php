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
class BioDivViewStudentProgress extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "StudentProgress display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("studentprogress");
		
		$this->personId = userID();
		
		if ( $this->personId ) {
			
			$this->isTeacher = Biodiv\SchoolCommunity::isTeacher();
	
			if ( $this->isTeacher ) {
				
				$this->modules = Biodiv\Module::getModules();
				$this->moduleIds = array_keys($this->modules);
				
				$this->badgeGroups = array();
			
				$badgeGroups = codes_getList ( "badgegroup" );
				foreach ( $badgeGroups as $bg ) {
					$this->badgeGroups[$bg[0]] = $bg[1];
				}
				
				$this->badgeGroupColour = array();
					
				foreach ( $this->badgeGroups as $groupId=>$badgeGroupName ) {
					
					$badgeColorArray = getOptionData ( $groupId, "color" ); 

					if ( count($badgeColorArray) > 0 ) {
						$this->badgeGroupColour[$groupId] = $badgeColorArray[0];
					}
				}
				
				$this->availablePoints = Biodiv\Task::getAvailablePointsByGroup( Biodiv\SchoolCommunity::STUDENT_ROLE );
				
				$this->students = Biodiv\SchoolCommunity::getMyStudentsProgress();
				
			}
		
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>