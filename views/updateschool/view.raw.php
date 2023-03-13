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
class BioDivViewUpdateSchool extends JViewLegacy
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
	
			$input = JFactory::getApplication()->input;
			
			$this->schoolSetupComplete = $input->getInt('school', 0);
			
			$this->teacherSetupComplete = $input->getInt('teacher', 0);
			
			$this->classSetupComplete = $input->getInt('class', 0);
			
			$this->studentSetupComplete = $input->getInt('student', 0);
			
			$this->updated = 0;
			$this->data = new StdClass();
			
			if ( $this->schoolSetupComplete ) {
				$this->updated = Biodiv\SchoolCommunity::schoolSetupComplete ( $this->schoolUser );
			}
			else if ( $this->teacherSetupComplete ) {
				$this->updated = Biodiv\SchoolCommunity::teacherSetupComplete ( $this->schoolUser );
			}
			else if ( $this->classSetupComplete ) {
				$this->updated = Biodiv\SchoolCommunity::classSetupComplete ( $this->schoolUser );
			}
			else if ( $this->studentSetupComplete ) {
				$this->updated = Biodiv\SchoolCommunity::studentSetupComplete ( $this->schoolUser );
			}
			
			$allSetUp = Biodiv\SchoolCommunity::getSetupComplete ( $this->schoolUser );
			
			$this->data->updated = $this->updated;
			if ( $this->updated ) {
				
				$this->data->icon = '<i class="fa fa-check-square-o fa-lg"></i>'; 
				$this->data->buttonText = JText::_("COM_BIODIV_UPDATESCHOOL_DONE");
				$this->data->allSetUp  = $allSetUp;
			}
			else {
				
				$this->data->icon = '<i class="fa fa-square-o fa-lg"></i>'; 
				$this->data->buttonText = JText::_("COM_BIODIV_UPDATESCHOOL_SET_UP");
				$this->data->allSetUp  = $allSetUp;
			}
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>