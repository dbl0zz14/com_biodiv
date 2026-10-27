<?php

namespace MammalWeb\Component\Biodiv\Site\View\ReportDownload;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View used to download reports
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

		($person_id = $reportModel->userID()) or die("Please log in");
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->reportId =
		(int)$app->getUserStateFromRequest('com_biodiv.report_id', 'report_id');
		
		$this->filter = $input->getString('filter', 0);
			
		$biodivReport = $reportModel->getReportForDownload ( $this->reportId );
		
		if ( $biodivReport ) {
			
			$this->reportName = $biodivReport->getFilename();
			$this->headings = $biodivReport->headings();
			$this->data = $biodivReport->rows(0, $biodivReport->totalRows(), $this->filter);
		}
		else {
			$this->data = "Sorry you do not have access";
		}
		
		
		
		// // Check user is project admin for this project or if no project that users match
		// $details = codes_getDetails($this->reportId, 'report');
		// $this->projectId = $details['project_id'];
		
		
		// $allProjects =   myAdminProjects();
		
		// $allIds = array_keys ( $allProjects );
		
		// $this->reportURL = null;
		
		// if ( $this->projectId == 0 && $person_id == $details['person_id']) {
			// error_log ( "valid user project, creating report" );
			
			// $biodivReport = BiodivReport::createFromId ( $this->reportId );
			
			// $this->reportName = $biodivReport->getFilename();
			// $this->headings = $biodivReport->headings();
			// $this->data = $biodivReport->rows(0, $biodivReport->totalRows(), $this->filter);
		// }
		// else if ( $this->projectId && in_array ($this->projectId, $allIds ) ) {
			
			// error_log ( "valid project, creating report" );
			
			// $biodivReport = BiodivReport::createFromId ( $this->reportId );
			
			// $this->reportName = $biodivReport->getFilename();
			// $this->headings = $biodivReport->headings();
			// $this->data = $biodivReport->rows(0, $biodivReport->totalRows(), $this->filter);
			
		// }
		// else {
			// $this->data = "Sorry you do not have access";
		// }
		
		// Display the view
		parent::display($tpl);
		
    }
}



?>