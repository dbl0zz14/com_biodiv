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
class BioDivViewAddClass extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		error_log ( "addclass view called" );
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		$this->personId = $this->schoolUser->person_id;
		
		if ( $this->schoolUser ) {
			
			if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		
				$app = JFactory::getApplication();
				$input = $app->input;
					
				$className = $input->getString("className", 0);
				$avatar = $input->getInt("classAvatar", 1);
				
				error_log ( "Adding class " . $className );
				$this->addClassResult = Biodiv\SchoolCommunity::addClass ( $this->schoolUser, $className, $avatar );
			}
		}
	  
		parent::display($tpl);
		
    }
}



?>