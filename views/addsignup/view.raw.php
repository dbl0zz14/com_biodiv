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
class BioDivViewAddSignup extends JViewLegacy
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
		$this->personId = userID();
		$this->addSignupResult = null;
		
		if ( !$this->schoolUser and $this->personId ) {
			
			$app = JFactory::getApplication();
			$input = $app->input;
				
			$personName = $input->getString("name", 0);
			$schoolName = $input->getString("schoolName", 0);
			$postcode = $input->getString("postcode", 0);
			$website = $input->getString("website", 0);
			$terms = $input->getInt("terms", 0);
			
			$this->addSignupResult = Biodiv\SchoolCommunity::addSignup ( $this->personId, $personName, $schoolName, $postcode, $website, $terms );
			
			if ( count($this->addSignupResult["errors"]) == 0 ) {
				
				$subject = JText::_("COM_BIODIV_ADDSIGNUP_SUBJECT");
				$msg = JText::_("COM_BIODIV_ADDSIGNUP_MESSAGE") . ' <a href="bes-schools-admin">'.JText::_("COM_BIODIV_ADDSIGNUP_LINK_TEXT").'</a>';
				
				Biodiv\SchoolCommunity::notifyAdmins ( $subject, $msg )
			}
			
		}
	  
		parent::display($tpl);
		
    }
}



?>