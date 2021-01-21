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
class BioDivViewReport extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

    public function display($tpl = null) 
    {
		error_log ( "Report view display called" );
		
		$this->personId = (int)userID();
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("report");
		
		
		if ( $this->personId ) {
		
			$app = JFactory::getApplication();
			$this->project_id =
			(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id');
			error_log ( "Report view.  Project id = " . $this->project_id );
			
			//$this->report_id =
			//(int)$app->getUserStateFromRequest('com_biodiv.report_id', 'report_id');
			//error_log ( "Report view.  Report_id = " . $this->report_id );
			
			$input = $app->input;
			$this->report_id = $input->get('report_id', 0, 'INT');
			error_log ( "Report view.  Report_id = " . $this->report_id );

			$this->report_type =
			(int)$app->getUserStateFromRequest('com_biodiv.report_type', 'report_type');
			error_log ( "Report view.  Report_type = " . $this->report_type );
			
			$this->page =
			(int)$app->getUserStateFromRequest('com_biodiv.page', 'page');
			error_log ( "Report view.  Page = " . $this->page );
			
			/* refinement
			$this->pageLength =
			(int)$app->getUserStateFromRequest('com_biodiv.page_len', 'page');
			error_log ( "Report view.  Page = " . $this->pageLength );
			
			if ( $this->pageLength == 0 ) $this->pageLength = BiodivReport::PAGE_LENGTH;
			*/
			
				
			// Check user is project admin for this project
			$allProjects = myAdminProjects();
			$err_msg = print_r ( $allProjects, true );
			error_log ( $err_msg );
			
			$allIds = array_column ( $allProjects, 'project_id' );
			
			$err_msg = print_r ( $allIds, true );
			error_log ( $err_msg );
			
			if ( in_array ($this->project_id, $allIds ) ) {
				
				error_log ( "valid project, creating report" );
				
				$biodivReport = null;
				
				// Could be a new report or a new page of existing report
				if ( $this->report_id == 0 ) {
					$biodivReport = new BiodivReport( $this->project_id, $this->report_type, $this->personId );
					$this->report_id = $biodivReport->getReportId();
				}
				else {
					$biodivReport = BiodivReport::createFromId ( $this->report_id );
				}
				
				error_log ("Getting report data");
				
				$this->headings = $biodivReport->headings();
				$this->totalRows = $biodivReport->totalRows();
				$this->pageLength = $biodivReport->pageLength();
				
				//$this->data = $biodivReport->getData( $this->page );
				$this->rows = $biodivReport->rows( $this->page );
				
				error_log ("Got rows");
			}
			else {
				$this->data = "Sorry you do not have access";
			}
		}

		// Display the view
		parent::display($tpl);
    }
}



?>