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
* HTML View class for the Biodiversity Monitoring component
* Display task details and article
*
* @since 0.0.1
*/
class BioDivViewAddSchoolUser extends JViewLegacy
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
		$this->personId = $this->schoolUser->person_id;
		
		if ( $this->schoolUser ) {
			
			if ( Biodiv\SchoolCommunity::isTeacher() ) {
		
				$app = JFactory::getApplication();
				$input = $app->input;
					
				$roleId = $input->getInt('suRoleId', 0);
				$name = $input->getString("suName", 0);
				$classId = $input->getString("suClassId", 0);
				$username = $input->getString("suUsername", 0);
				$email = $input->getString("suEmail", 0);
				$password = $input->getString("suPassword", 0);
				
				// Sanitize and double check matching
				
				error_log ( "roleId = " . $roleId );
				
				$this->addUserResult = Biodiv\SchoolCommunity::addSchoolUser ( $this->schoolUser, $roleId, $name, $classId, $username, $email, $password );
				
				$this->printTeachers = $roleId == Biodiv\SchoolCommunity::TEACHER_ROLE;
				
				error_log ( "printTeachers = " . $this->printTeachers );
				
				$this->printStudents = $roleId == Biodiv\SchoolCommunity::STUDENT_ROLE;
				
				error_log ( "printStudents = " . $this->printStudents );
				
				
			}
		}
	  
		parent::display($tpl);
		
    }
}



?>