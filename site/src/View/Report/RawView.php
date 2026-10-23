<?php

namespace MammalWeb\Component\Biodiv\Site\View\Report;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for project data, used in charts
 */
class RawView extends BaseHtmlView {

        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

    public function display($tpl = null) 
    {
		$reportModel = $this->getModel();
	
		$this->personId = $reportModel->userID();
	
		if ( $this->personId ) {
		
			$app = Factory::getApplication();
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
			$allProjects = $reportModel->getAdminProjects();
			
			$allIds = array_keys ( $allProjects );
			
			$this->jsonThreshold = $reportModel->getJsonThreshold();
			
			
			if ( $this->project_id == 0 ) {
				
				$biodivReport = null;
				
				// Could be a new report or a new page of existing report
				if ( $this->report_id == 0 ) {
					//$biodivReport = new BiodivReport( null, $this->report_type, $this->personId, null, $this->filter );
					$biodivReport = $reportModel->getNewReport( $this->report_type );
					$this->report_id = $biodivReport->getReportId();
				}
				else {
					//$biodivReport = BiodivReport::createFromId ( $this->report_id );
					$biodivReport = $reportModel->getReport ( $this->report_id );
				}
				
				$this->reportTitle = $biodivReport->getReportTypeText();
				$this->headings = $biodivReport->headings();
				$this->totalRows = $biodivReport->totalRows( $this->filter );
				$this->pageLength = $biodivReport->pageLength();
				$this->filterColumns = $biodivReport->getReportFilters();
				
				$this->filterValues = array();
				if ( $this->filterColumns ) {
					foreach ( $this->filterColumns as $id=>$type ) {
						$this->filterValues[$id] = $biodivReport->getFilterValues($type);
						$this->filterSelected[$id] = $biodivReport->getSelectedValue ( $type, $this->filter );
						// $this->filterValues[$id] = BiodivReport::getFilterValues($type);
						// $this->filterSelected[$id] = BiodivReport::getSelectedValue ( $type, $this->filter );
					}
				}
				
				$this->rows = $biodivReport->rows( $this->page, null, $this->filter );
				
			}
			else if ( in_array ($this->project_id, $allIds ) ) {
				
				$biodivReport = null;
				
				// Could be a new report or a new page of existing report
				if ( $this->report_id == 0 ) {
					//$biodivReport = new BiodivReport( $this->project_id, $this->report_type, $this->personId );
					$biodivReport = $reportModel->getNewReport($this->report_type, $this->project_id  );
					$this->report_id = $biodivReport->getReportId();
				}
				else {
					//$biodivReport = BiodivReport::createFromId ( $this->report_id );
					$biodivReport = $reportModel->getReport ( $this->report_id );
				}
				
				$this->reportTitle = $biodivReport->getReportTypeText();
				$this->headings = $biodivReport->headings();
				$this->totalRows = $biodivReport->totalRows( $this->filter );
				$this->pageLength = $biodivReport->pageLength();
				$this->filterColumns = $biodivReport->getReportFilters();
				$this->filterValues = array();
				if ( $this->filterColumns ) {
					foreach ( $this->filterColumns as $id=>$type ) {
						$this->filterValues[$id] = $biodivReport->getFilterValues($type);
						$this->filterSelected[$id] = $biodivReport->getSelectedValue ( $type, $this->filter );
					}
				}

				// if ( $this->filterColumns ) {
					// foreach ( $this->filterColumns as $filterCol ) {
						// //$this->filterValues[$filterCol->id] = $biodivReport->getFilterValues($filterCol->type);
						// $this->filterValues[$filterCol->id] = $biodivReport->getFilterValues($filterCol->type);
					// }
				// }
				
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