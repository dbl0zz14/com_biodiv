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
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		if ( $this->schoolUser ) {
			$this->personId = $this->schoolUser->person_id;
		}
		else {
			$this->personId = (int)userID();
		}
	
		if ( $this->personId && ($this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE) ) {
			
			$this->helpOption = codes_getCode ( "studentprogress", "beshelp" );
		
			$this->educatorPage = "bes-educator-zone";
			
			$awards = Biodiv\Award::getCollectedAwards ( $this->schoolUser );
			
			$this->award1 = $awards[Biodiv\SchoolCommunity::STUDENT_ROLE][1];
			$this->award2 = $awards[Biodiv\SchoolCommunity::STUDENT_ROLE][2];
			$this->award3 = $awards[Biodiv\SchoolCommunity::STUDENT_ROLE][3];
			
			$this->levels = array ( 
						1=>JText::_("COM_BIODIV_STUDENTPROGRESS_1"),
						2=>JText::_("COM_BIODIV_STUDENTPROGRESS_2"),
						3=>JText::_("COM_BIODIV_STUDENTPROGRESS_3") );
						
			$this->maxBadgeIds = array (
						1=>8,
						2=>16,
						3=>28 );
						
		
			$this->students = Biodiv\SchoolCommunity::getMyStudentsProgress();
		
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>