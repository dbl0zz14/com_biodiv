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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewRptFileDownload extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

    public function display($tpl = null) 
    {
		error_log ( "RptFileDownload view display called" );
		
		($person_id = (int)userID()) or die("No person_id");
		
		$app = JFactory::getApplication();
		
		$this->reportId =
		(int)$app->getUserStateFromRequest('com_biodiv.report_id', 'report_id');
		error_log ( "ReportDownload view.  Report_id = " . $this->reportId );
		
		// Check user is project admin for this project
		$allProjects = myAdminProjects();
		$err_msg = print_r ( $allProjects, true );
		error_log ( $err_msg );
		
		$allIds = array_column ( $allProjects, 'project_id' );
		
		$err_msg = print_r ( $allIds, true );
		error_log ( $err_msg );
		
		$details = codes_getDetails($this->reportId, 'report');
		$this->projectId = $details['project_id'];
		
		
		$this->reportURL = null;
		
		if ( $this->projectId == 0 && $person_id == $details['person_id']) {
			error_log ( "valid user project, creating report" );
			
			$biodivReport = BiodivReport::createFromId ( $this->reportId );
			
			$biodivReport->createDownloadFile();
			
			$this->reportURL = $biodivReport->reportURL();
			
			$this->reportName = $biodivReport->getFilename();
			
			$this->headings = $biodivReport->headings();
		}
		else if ( in_array ($this->projectId, $allIds ) ) {
			
			error_log ( "valid project, creating report" );
			
			$biodivReport = BiodivReport::createFromId ( $this->reportId );
			
			$biodivReport->createDownloadFile();
			
			$this->reportURL = $biodivReport->reportURL();
			
			$this->reportName = $biodivReport->getFilename();
			
			$this->headings = $biodivReport->headings();
		}
		else {
			$this->reportURL = "Sorry you do not have access";
		}
		
		
		error_log ( "About to call display" );
		
		

		// Display the view
		parent::display($tpl);
		
		
		
    }
}



?>