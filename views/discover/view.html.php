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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewDiscover extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
	  
	error_log("Discover view called");
	  
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("discover");
	
	error_log("Discover view got translations");
	  
	// Get the species to include in dropdown.
	$this->speciesList = getFeatureSpecies();
	
	error_log("Discover view got species list");
	

    // Display the view
    parent::display($tpl);
  }
}



?>