<?php

namespace MammalWeb\Component\Biodiv\Site\View\Sequence;

 
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
 * View for the sequence process page
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
		$input = $app->getInput();
		$this->sequenceThreshold = $input->getInt("threshold", 10);

		$this->trapperModel = $this->getModel();

		$this->canRun = $this->trapperModel->setRunning ( 'sequence', 1 );
		
		$this->uploadDetails = null;
		if ( $this->canRun ) {
			$this->uploadDetails = $this->trapperModel->getUnsequencedUploads();
		}

		// Display the view
		parent::display($tpl);
    }
}



?>
