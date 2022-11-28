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
class BioDivViewTaskUpload extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->personId = (int)userID();

		if ( !$this->personId ) {
			error_log("TaskUpload view: no person id" );
		}
		else {
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			// Get badge group (pillar)
			$this->taskId = $input->getInt('task', 0);
			$this->schoolTask = $input->getInt('school', 0);
			
			$this->task = new Biodiv\Task ( $this->taskId );
			
			$this->badgeGroup = $this->task->getBadgeGroupName();
			$this->badgeName = $this->task->getBadgeName();
			$this->lockLevel = $this->task->getLockLevel();
			$this->taskName = $this->task->getTaskName();
			$this->moduleTagId = $this->task->getModuleTagId();
			
			//error_log ( "Got task details" );
			
			$this->uploadName = $this->badgeName;
			
			// if ( strlen($this->uplo
			// $this->uploadName = $this->badgeGroup . ': ' . $this->badgeName;
			
			//error_log ( "Upload name set to " . $this->uploadName );
			
			// What school id to use?
			$this->schoolRoles = Biodiv\SchoolCommunity::getSchoolRoles();
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>