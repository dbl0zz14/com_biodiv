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
		$this->personId = (int)userID();
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("report");
		
		
		if ( $this->personId ) {
		
			$app = JFactory::getApplication();
			$this->project_id =
			(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id');
			
			$input = $app->input;
			
			$this->report_id = $input->get('report_id', 0, 'INT');
			
			$this->filter = $input->getString('filter', 0);
			
			$this->report_type =
			(int)$app->getUserStateFromRequest('com_biodiv.report_type', 'report_type');
			
			$this->page =
			(int)$app->getUserStateFromRequest('com_biodiv.page', 'page');
			
				
			// Check user is project admin for this project
			$allProjects = myAdminProjects();
			
			$allIds = array_keys ( $allProjects );
			
			
			if ( $this->project_id == 0 ) {
				
				$biodivReport = null;
				
				// Could be a new report or a new page of existing report
				if ( $this->report_id == 0 ) {
					$biodivReport = new BiodivReport( null, $this->report_type, $this->personId, null, $this->filter );
					$this->report_id = $biodivReport->getReportId();
				}
				else {
					$biodivReport = BiodivReport::createFromId ( $this->report_id );
				}
				
				$this->reportTitle = $biodivReport->getReportTypeText();
				$this->headings = $biodivReport->headings();
				$this->totalRows = $biodivReport->totalRows( $this->filter );
				$this->pageLength = $biodivReport->pageLength();
				$this->filterColumns = $biodivReport->getReportFilters();
				
				$this->filterValues = array();
				if ( $this->filterColumns ) {
					foreach ( $this->filterColumns as $id=>$type ) {
						$this->filterValues[$id] = BiodivReport::getFilterValues($type);
						$this->filterSelected[$id] = BiodivReport::getSelectedValue ( $type, $this->filter );
					}
				}
				
				$this->rows = $biodivReport->rows( $this->page, null, $this->filter );
				
			}
			else if ( in_array ($this->project_id, $allIds ) ) {
				
				$biodivReport = null;
				
				// Could be a new report or a new page of existing report
				if ( $this->report_id == 0 ) {
					$biodivReport = new BiodivReport( $this->project_id, $this->report_type, $this->personId );
					$this->report_id = $biodivReport->getReportId();
				}
				else {
					$biodivReport = BiodivReport::createFromId ( $this->report_id );
				}
				
				$this->reportTitle = $biodivReport->getReportTypeText();
				$this->headings = $biodivReport->headings();
				$this->totalRows = $biodivReport->totalRows( $this->filter );
				$this->pageLength = $biodivReport->pageLength();
				$this->filterColumns = $biodivReport->getReportFilters();
				
				$this->filterValues = array();
				if ( $this->filterColumns ) {
					foreach ( $this->filterColumns as $filterCol ) {
						$this->filterValues[$filterCol->id] = BiodivReport::getFilterValues($filterCol->type);
					}
				}
				
				$this->rows = $biodivReport->rows( $this->page, null, $this->filter );
				
				
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