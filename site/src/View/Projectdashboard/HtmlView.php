<?php

namespace MammalWeb\Component\Biodiv\Site\View\ProjectDashboard;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;


 /**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the Project dashboard (admin) landing page 
 */
class HtmlView extends BaseHtmlView {

	/**
	*
	* @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	*
	* @return  voidz
	*/

	public function display($tpl = null) 
	{

		$reportModel = $this->getModel();

		$this->personId = $reportModel->userID();

		$app = Factory::getApplication();

		// Get all the projects that this user is admin for
		$this->projects = $reportModel->getAdminProjects();

		// Remove any previous report for this user - page has been reloaded and we don't keep them around
		//BiodivReport::removeExistingReports( $this->personId );
		$reportModel->removeExistingReports( $this->personId );

		//$this->reports = BiodivReport::listReports();
		$this->reports = $reportModel->listReports();

		$this->reportText = $reportModel->getReportHelpText ( $this->reports );
		
		$this->optInReports = $reportModel->getAllProjectOptInReports ( array_keys($this->projects) );
		// $this->optInReports = array();
		// // Get the opt-in reports for each project.
		// foreach ( array_keys($this->projects) as $projectId ) {
			// //$optInRpts = BiodivReport::listOptInReports( $projectId );
			// $optInRpts = $reportModel->listOptInReports( $projectId );
			// if ( count($optInRpts) > 0 ) {
				// //$this->optInReports[$projectId] = BiodivReport::listOptInReports( $projectId );
				// $this->optInReports[$projectId] = $reportModel->listOptInReports( $projectId );
			// }
		// }

		$this->waitText = Text::_("COM_BIODIV_PROJECTDASHBOARD_WAIT_TEXT");
		$this->doneText = Text::_("COM_BIODIV_PROJECTDASHBOARD_DONE_TEXT");
		$this->genText = Text::_("COM_BIODIV_PROJECTDASHBOARD_GEN_TEXT");

		// Display the view
		parent::display($tpl);
	}
}



?>