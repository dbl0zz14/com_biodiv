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
	  
	// Get the area covered setting, currently UK or Europe, default to Europe
	$area = getSetting("area_covered");
	
	if ( $area == null ) $area = "europe";
	
	// Handle different cases
	$this->areaCovered = strtolower($area);
	
	// Should we display sites when discover page is loaded
	$show = getSetting("show_sites");
	
	$this->showSitesOnLoad = $show == "yes";
	
	  
	// Get the species to include in dropdown.
	$this->speciesList = getFeatureSpecies();
	
	error_log("Discover view got species list");
	

    // Display the view
    parent::display($tpl);
  }
}



?>