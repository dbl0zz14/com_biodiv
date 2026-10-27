<?php
namespace MammalWeb\Component\Biodiv\Site\View\RequestAI;

 
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
 * View for the manual queue script 
 */
class HtmlView extends BaseHtmlView {
	
	// const TO_SEND			= 0;
	// const SEND_SUCCESS		= 1;
	// const PROCESSING		= 2;
	// const PROCESSING_ERROR	= 3;
	// const SEND_ERROR		= 4;
	// const FILETYPE_ERROR	= 5;
	// const LONG_SEQUENCE		= 6;
	// const MANUAL_UNQUEUE	= 7;
	
	const MAX_NUM = 30;
	const NUM_REQUESTS = 15;
	
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
			
		$this->repeat = $input->getInt('repeat', 0);
		
		if ( !$this->repeat ) {
			$this->repeat = self::NUM_REQUESTS;
		}
		
		$this->number = $input->getInt('number', 0);
		
		if ( !$this->number ) {
			$this->number = self::MAX_NUM;
		}
		
		$this->aiType = $input->getString('ai', 'CAI');
		
		$this->aiModel = $this->getModel();
		
		$this->photos = array();
		$this->siteIds = array();
		
		if ( $this->aiModel->canRunScripts() ) {

			$this->canRun = $this->aiModel->setRunning ( 'requestai', 1 );
		
			if ( $this->canRun ) {
				
				for ( $i=0; $i < $this->repeat; $i++ ) {
					
					$sitePriorityArray = $this->aiModel->getNextSiteId ( $this->aiType );
				
					$this->siteIds[$i] = $sitePriorityArray[0];
					$lowestPriority = $sitePriorityArray[1];
					$siteId = $this->siteIds[$i];
					
					if ( $siteId ) {
						$this->photos[$i] = $this->aiModel->getPhotoDetails ( $this->aiType, $siteId, $this->number, $lowestPriority);
						$theseIds = array_keys($this->photos[$i]);
						$this->aiModel->setProcessingFlags ( $this->aiType, $theseIds );
						//$this->aiModel->setQueueStatusMultiple ( $this->aiType, $theseIds, BioDivViewRequestAI::PROCESSING );
					}
				}
			}
		}
			
		parent::display($tpl);
	}
	
	
	
	
	
}



?>
