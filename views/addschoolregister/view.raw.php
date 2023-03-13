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
class BioDivViewAddSchoolRegister extends JViewLegacy
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
		
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$this->result = array();
		$this->result["errors"] = array();
				
		// Check recaptcha
		$recaptchaResponse = $input->getString ( "g-recaptcha-response", 0 );
		$passedRecaptcha = BiodivRecaptcha::checkUserRecaptcha ( $recaptchaResponse );
		
		if ( !$passedRecaptcha ) {
			$this->result["errors"][] = array("error"=>JText::_("COM_BIODIV_ADDSCHOOLREGISTER_RECAPTCHA_FAIL"));
		}
		else {
			$name = $input->getString("name", 0);
			$username = $input->getString("username", 0);
			$email = $input->getString("email", 0);
			$password = $input->getString("password", 0);
			$password2 = $input->getString("password2", 0);
			$schoolName = $input->getString("schoolName", 0);
			$postcode = $input->getString("postcode", 0);
			$website = $input->getString("website", 0);
			$wherehear = $input->getString("wherehear", 0);
			$terms = $input->getInt("terms", 0);
			
			if ( !$terms ) {
				
				$this->result["errors"][] = array("error"=>JText::_("COM_BIODIV_ADDSCHOOLREGISTER_TERMS_FAIL"));
				
			}
			else {
				
				$this->result["success"] = Biodiv\SchoolCommunity::registerNewSchool ( $name, $username, $email, $password, 
																		$schoolName, $postcode, $website, $wherehear, $terms );
																		
			}
		}
																	
		parent::display($tpl);
		
    }
}



?>