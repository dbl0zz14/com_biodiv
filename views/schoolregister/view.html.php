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
class BioDivViewSchoolRegister extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    
	$this->helpOption = codes_getCode ( "schoolregister", "beshelp" );
	
	$recapOpts = recaptchaOptions();
	if ( $recapOpts ) {
		
		$this->recaptchaRequired = true;
		$this->recaptchaSiteKey = $recapOpts["sitekey"];
	}
	else {
		$this->recaptchaRequired = false;
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

