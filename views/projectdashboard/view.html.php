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
	
	// Get all the projects that this user is admin for
	$this->projects = myAdminProjects();
	
	// Remove any previous report for this user - page has been reloaded and we don't keep them around
	BiodivReport::removeExistingReports( $this->personId );
	
	$this->reports = BiodivReport::listReports();
	
	$this->reportText = array();
	foreach ( $this->reports as $report ) {
		$helpId = getOptionData($report[0], 'help');
		//error_log ( "report id = " . $report[0] );
		//error_log ( "helpId = " . print_r ( $helpId, true ) );
		if ( count($helpId) > 0 ) {
			$this->reportText[$report[0]] = codes_getName($helpId[0], 'helptran');
		}
	}
	
	$this->optInReports = array();
	// Get the opt-in reports for each project.
	foreach ( array_keys($this->projects) as $projectId ) {
		$optInRpts = BiodivReport::listOptInReports( $projectId );
		if ( count($optInRpts) > 0 ) {
			$this->optInReports[$projectId] = BiodivReport::listOptInReports( $projectId );
		}
	}
	
	
	$this->waitText = JText::_("COM_BIODIV_PROJECTDASHBOARD_WAIT_TEXT");
	$this->doneText = JText::_("COM_BIODIV_PROJECTDASHBOARD_DONE_TEXT");
	$this->genText = JText::_("COM_BIODIV_PROJECTDASHBOARD_GEN_TEXT");

    // Display the view
    parent::display($tpl);
  }
}



?>