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
class BioDivViewUserDashboard extends JViewLegacy
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
	$this->translations = getTranslations("userdashboard");
	
	// Get all the projects that this user is a spotter for
	$this->spotterProjects = mySpottingProjects();
	
	// Get all the projects that this user is a spotter for
	$this->trapperProjects = myTrappingProjects();
	
	// get all the users sites
	//$this->sites = mySites();
	
	// Remove any previous report for this user - page has been reloaded and we don't keep them around
	BiodivReport::removeExistingReports( $this->personId );
	
	$this->reports = BiodivReport::listUserReports();
	
	$err_msg = print_r ( $this->reports , true );
	error_log ( "reports: " . $err_msg );
	
	$this->reportText = array();
	foreach ( $this->reports as $report ) {
		$helpId = getOptionData($report[0], 'help');
		error_log ( "report id = " . $report[0] );
		error_log ( "helpId = " . print_r ( $helpId, true ) );
		if ( count($helpId) > 0 ) {
			$this->reportText[$report[0]] = codes_getName($helpId[0], 'helptran');
		}
	}
	
	$this->waitText = $this->translations['wait_text']['translation_text'];
	$this->doneText = $this->translations['done_text']['translation_text'];
	$this->genText = $this->translations['gen_text']['translation_text'];

    // Display the view
    parent::display($tpl);
  }
}



?>