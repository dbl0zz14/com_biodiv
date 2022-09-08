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
class BioDivViewReportDownload extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

    public function display($tpl = null) 
    {
		error_log ( "ReportDownload view display called" );
		
		($person_id = (int)userID()) or die("No person_id");
		
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$this->reportId =
		(int)$app->getUserStateFromRequest('com_biodiv.report_id', 'report_id');
		
		$this->filter = $input->getString('filter', 0);
			
		
		// Check user is project admin for this project or if no project that users match
		$details = codes_getDetails($this->reportId, 'report');
		$this->projectId = $details['project_id'];
		
		$allProjects = myAdminProjects();
		
		$allIds = array_keys ( $allProjects );
		
		$this->reportURL = null;
		
		if ( $this->projectId == 0 && $person_id == $details['person_id']) {
			error_log ( "valid user project, creating report" );
			
			$biodivReport = BiodivReport::createFromId ( $this->reportId );
			
			$this->reportName = $biodivReport->getFilename();
			$this->headings = $biodivReport->headings();
			$this->data = $biodivReport->rows(0, $biodivReport->totalRows(), $this->filter);
		}
		else if ( $this->projectId && in_array ($this->projectId, $allIds ) ) {
			
			error_log ( "valid project, creating report" );
			
			$biodivReport = BiodivReport::createFromId ( $this->reportId );
			
			$this->reportName = $biodivReport->getFilename();
			$this->headings = $biodivReport->headings();
			$this->data = $biodivReport->rows(0, $biodivReport->totalRows(), $this->filter);
			
		}
		else {
			$this->data = "Sorry you do not have access";
		}
		
		error_log ( "About to call display" );
		
		// Display the view
		parent::display($tpl);
		
    }
}



?>