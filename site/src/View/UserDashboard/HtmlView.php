<?php

namespace MammalWeb\Component\Biodiv\Site\View\UserDashboard;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;


 /**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the spotter classify page
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
	$biodivModel = $this->getModel();
		
	$this->personId = $biodivModel->userID();
    
    $app = Factory::getApplication();
	
	// Get all the projects that this user is a spotter for
	$this->spotterProjects = $biodivModel->getSpottingProjects();
	
	// Get all the projects that this user is a spotter for
	$this->trapperProjects = $biodivModel->getTrappingProjects();
	
	// Remove any previous report for this user - page has been reloaded and we don't keep them around
	$biodivModel->removeExistingReports( $this->personId );
	
	$this->optInAnalysis = $biodivModel->listUserAnalysis();
	
	$this->reports = $biodivModel->listUserReports();
	
	$this->optInReports = $biodivModel->listUserOptInReports();
	
	$this->reportText = $biodivModel->getReportHelpText ( $this->reports );
	
	$this->dashUri = Uri::root().'/'.Text::_("COM_BIODIV_USERDASHBOARD_DASH_PAGE");
	
	// $this->reportText = array();
	// foreach ( $this->reports as $report ) {
		// $helpId = getOptionData($report[0], 'help');
		// if ( count($helpId) > 0 ) {
			// $this->reportText[$report[0]] = codes_getName($helpId[0], 'helptran');
		// }
	// }
	
	$this->waitText = Text::_("COM_BIODIV_USERDASHBOARD_WAIT_TEXT");
	$this->doneText = Text::_("COM_BIODIV_USERDASHBOARD_DONE_TEXT");
	$this->genText = Text::_("COM_BIODIV_USERDASHBOARD_GEN_TEXT");

    // Display the view
    parent::display($tpl);
  }
}



?>