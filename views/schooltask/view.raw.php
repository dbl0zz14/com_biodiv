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
* Return badge data as JSON eg for display on student dashboard
*
* @since 0.0.1
*/
class BioDivViewSchoolTask extends JViewLegacy
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
		
		$this->allTasks = array();

		if ( !$this->personId ) {
			
			error_log("SchooolTask view: no person id" );
			
		}
		else {
			
			$this->schoolRoles = Biodiv\SchoolCommunity::getSchoolRoles();
			
			$this->statusIcons = array ( "fa-lock", "fa-unlock", "fa-clock-o", "fa-check", "fa-check" );
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			// Get badge group (pillar), if no group get all
			$badgeGroupId = $input->getInt('group', 0);
			
			$this->isTeacher = Biodiv\SchoolCommunity::isTeacher();
			
			if ( $this->isTeacher ) {
				
				$this->allTasks = Biodiv\Task::getAllSchoolTasks();
				
				$this->myStudents = Biodiv\SchoolCommunity::getMyStudents();
				
			}
		}

		// Display the view
		parent::display($tpl);
    }
}



?>