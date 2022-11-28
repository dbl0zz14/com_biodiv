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
		$this->personId = userID();
		
		if ( $this->personId ) {
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
			
			$this->isTeacher = $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE;
			$this->isEcologist = $this->schoolUser->role_id == Biodiv\SchoolCommunity::ECOLOGIST_ROLE;
			
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>