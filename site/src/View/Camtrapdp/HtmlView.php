<?php
namespace MammalWeb\Component\Biodiv\Site\View\CamtrapDp;

 
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
 * View for the CamtrapDP extract script 
 */
class HtmlView extends BaseHtmlView {

	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$app = Factory::getApplication();
		
		$input = $app->input;
			
		$this->startDate = $input->getString('start', 0);
		$this->endDate = $input->getString('end', 0);
        $this->zipFileTag = $input->getString('tag', '');
		
		if ( !$this->startDate ) {
			
			$this->startDate = date('Ymd', strtotime("last year January 1st"));
			//error_log ( "Using start date " . $this->startDate );
		}
		if ( !$this->endDate ) {
			
			$this->endDate = date('Ymd', strtotime("last year December 31st"));
			//error_log ( "Using end date " . $this->endDate );
		}
		
		print ( "<br>Using start date " . $this->startDate );
		print ( "<br>Using end date " . $this->endDate );
		
		$this->reportModel = $this->getModel();
		
		$this->filesToTransfer = $this->reportModel->generateCamtrapDp ( $this->startDate, 
																		$this->endDate,
																		$this->zipFileTag );
		
		// Display the view
		parent::display($tpl);
    }
}



?>
