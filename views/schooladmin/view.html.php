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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewSchoolAdmin extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
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
		
	if ( $this->schoolUser and ($this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE) ) {
		
		$this->helpOption = codes_getCode ( "schooladmin", "beshelp" );
			
		$this->educatorPage = "bes-educator-zone";
		
		$allDefaults = json_decode(getSetting("bes_defaults"));
		$this->domainDefault = $allDefaults->email_domain;
		
		$joomlaDb = JFactory::getDbo();
        $joomlaDb->setQuery( 'SELECT id' .
                        ' FROM `#__usergroups`' .
						' WHERE title = "School Student"' );
                        
        $this->studentGroup = $joomlaDb->loadResult();
			
		$input = JFactory::getApplication()->input;
			
		$this->help = $input->getInt('help', 0);
		$this->checklist = $input->getInt('checklist', 0);
		$this->setup = $input->getString('setup', 0);
		
		$this->schoolSetup = 0;
		$this->teacherSetup = 0;
		$this->classSetup = 0;
		$this->studentSetup = 0;
		if ( $this->setup ) {
			if ( strcmp($this->setup, "school") == 0 ) {
				$this->schoolSetup = 1;
			}
			else if ( strcmp($this->setup, "teacher") == 0 ) {
				$this->teacherSetup = 1;
			}
			else if ( strcmp($this->setup, "class") == 0 ) {
				$this->classSetup = 1;
			}
			else if ( strcmp($this->setup, "student") == 0 ) {
				$this->studentSetup = 1;
			}
		}
		
		$this->schoolDetails = Biodiv\SchoolCommunity::getSchoolDetails ( $this->schoolUser );
		
		$this->schoolSetupDone = $this->schoolDetails->school_setup;
		$this->teacherSetupDone = $this->schoolDetails->teacher_setup;
		$this->classSetupDone = $this->schoolDetails->class_setup;
		$this->studentSetupDone = $this->schoolDetails->student_setup;
		
		if ( !$this->schoolSetupDone or !$this->teacherSetupDone or !$this->classSetupDone or !$this->studentSetupDone ) {
			$this->checklist = 1;
		}
		
		$this->allDone = 0;
		if ( $this->schoolSetupDone and $this->teacherSetupDone and $this->classSetupDone and $this->studentSetupDone ) {
			$this->allDone = 1;
		}
			
		//$this->schoolAccounts = Biodiv\SchoolCommunity::getSchoolAccounts ( $this->schoolUser );							
		$this->classes = Biodiv\SchoolCommunity::getSchoolClasses ( $this->schoolUser );
		$this->avatars = Biodiv\SchoolCommunity::getAvatars ();
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

