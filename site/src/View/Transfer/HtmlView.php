<?php
namespace MammalWeb\Component\Biodiv\Site\View\Transfer;

 
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
 * View for the transfer of images to S3 script 
 */
class HtmlView extends BaseHtmlView {
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 	{
		
		$this->trapperModel = $this->getModel();

		if ( $this->trapperModel->canRunScripts() ) {
			
			$this->photos = $this->trapperModel->getPhotosToTransfer();
			
			$this->originalFiles = $this->trapperModel->getOriginalFilesToTransfer();
			
			$this->largeFiles = $this->trapperModel->getLargeFilesToTransfer();
			
		}
		else {
			$this->photos = null;
			$this->originalFiles = null;
			$this->largeFiles = null;
		}
		
		parent::display($tpl);
	}
}



?>
