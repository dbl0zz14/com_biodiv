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
class BioDivViewApproveSchool extends JViewLegacy
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
		
		if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::ADMIN_ROLE ) {
			
			$app = JFactory::getApplication();
			$input = $app->input;
				
			$signupId = $input->getInt("signUpId", 0);
			$approveSchool = $input->getInt("approve", 0);
			$comment = $input->getString("comment", 0);
			
			if ( $approveSchool == 1 ) {
				error_log ( "Approving school" );
				Biodiv\SchoolCommunity::approveSchool ( $this->schoolUser, $signupId, $comment );
			}
			else if ( $approveSchool == -1 ) {
				Biodiv\SchoolCommunity::rejectSchool ( $this->schoolUser, $signupId, $comment );
			}
		}
	
		parent::display($tpl);
		
    }
}



?>