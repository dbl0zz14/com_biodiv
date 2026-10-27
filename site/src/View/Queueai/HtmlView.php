<?php

namespace MammalWeb\Component\Biodiv\Site\View\QueueAI;

 
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

	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		// // Live $this->minPhotoId = 21100;
		// $this->minPhotoId = 500; // Dev

		$app = Factory::getApplication();
		
		$input = $app->input;
			
		$this->uploadId = $input->getInt('upload_id', 0);
		$this->projectId = $input->getInt('project_id', 0);
		$this->excludeSubs = $input->getInt('exclude_subs', 0);
		$this->aiType = $input->getString('ai_type', 0);
		$this->priority = $input->getInt('priority', 5);
		$this->latest = $input->getInt('latest', 0);
		$this->test = $input->getInt('test', 0);
		$this->testTag = $input->getString('tag', 0);
		
		$this->aiModel = $this->getModel();
		
		
		if ( !$this->aiType ) {
			
			$this->aiType = 'MEGA';
			
		}
		
		$this->photoIds = null;
		
		if ( $this->test ) {
			
			$this->photoIds = $this->aiModel->getTestPhotoIds ( $this->testTag );
				
		}
		else if ( !$this->uploadId ) {
			
			$this->uploadId = $this->aiModel->getUploadId ( $this->aiType,
													$this->projectId,
													$this->excludeSubs,
													$this->latest );
			
		}
		
		// Display the view
		parent::display($tpl);
    }
}



?>
