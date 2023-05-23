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
class BioDivViewEditSchoolUser extends JViewLegacy
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
				
				$this->schoolId = $input->getInt("schoolId", 0);
				$schoolName = $input->getString("schoolName", 0);
		
				$this->teacherId = $input->getInt("teacherId", 0);
				$teacherName = $input->getString("teacherName", 0);
				$teacherActive = $input->getInt('teacherActive', 0);
				
				$this->classId = $input->getInt("classId", 0);
				$className = $input->getString("editClassName", 0);
				$classAvatar = $input->getInt("editClassAvatar", 0);
				$isActive = $input->getInt('classActive', 0);
				
				$this->studentId = $input->getInt("studentId", 0);
				$studentName = $input->getString("studentName", 0);
				$studentActive = $input->getInt('studentActive', 0);
				$studentClassId = $input->getInt("studentClass", 0);
				$includePoints = $input->getInt("studentActive", 0);
				//$password = $input->getInt('password', 0);
		
		
				$password = $input->getString('password', 0);
				$password2 = $input->getString('password2', 0);
				
				if ( $password && (strlen($password) == 0) ) $password = null;
				
				$passwordProblem = false;
				if ( $password ) {
					
					if ( strcmp($password, $password2) !== 0 ) {
						
						error_log ( "Passwords don't match" );
						$passwordProblem = true;
					}
				}
				if ( !$passwordProblem ) {
					
					if ( $this->schoolId ) {
						error_log ( "calling editSchool" );
						$this->editUserResult = Biodiv\SchoolCommunity::editSchool ( $this->schoolUser, $this->schoolId, $schoolName );
						error_log ( "editSchool complete" );
					}
					else if ( $this->teacherId ) {
						error_log ( "calling editTeacher" );
						$this->editUserResult = Biodiv\SchoolCommunity::editTeacher ( $this->schoolUser, $this->teacherId, $teacherName, $teacherActive, $password );
						error_log ( "editTeacher complete" );
					}
					else if ( $this->classId ) {
						error_log ( "calling editClass" );
						$this->editUserResult = Biodiv\SchoolCommunity::editClass ( $this->schoolUser, $this->classId, $className, $classAvatar, $isActive );
						error_log ( "editClass complete" );
					}
					else if ( $this->studentId ) {
						error_log ( "calling editStudent" );
						$this->editUserResult = Biodiv\SchoolCommunity::editStudent ( $this->schoolUser, $this->studentId, $studentName, $studentClassId, $includePoints, $password, $password2 );
						error_log ( "editStudent complete" );
					}
				}
			}
		}
	  
		parent::display($tpl);
		
    }
}



?>