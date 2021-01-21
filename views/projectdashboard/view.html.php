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
class BioDivViewProjectDashboard extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    $this->personId = (int)userID();
    
    $app = JFactory::getApplication();
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("project");
	
	// Get all the projects that this user is admin for
	$this->projects = myAdminProjects();
	
	// Remove any previous report for this user - page has been reloaded and we don't keep them around
	BiodivReport::removeExistingReports( $this->personId );
	
	$this->reports = BiodivReport::listReports();
	
	$this->reportText = array();
	foreach ( $this->reports as $report ) {
		$this->reportText[$report[0]] = getOptionData($report[0], 'help');
	}
	
	

    // Display the view
    parent::display($tpl);
  }
}



?>