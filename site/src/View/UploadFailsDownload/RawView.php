<?php

namespace MammalWeb\Component\Biodiv\Site\View\UploadFailsDownload;

 
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
		
		$trapperModel = $this->getModel();

		($person_id = $trapperModel->userID()) or die("Please log in");
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->uploadId =
		(int)$app->getUserStateFromRequest('com_biodiv.upload_id', 'upload_id');
		
		$this->fails = $trapperModel->getUploadFailsForDownload ( $this->uploadId );
		
		// Display the view
		parent::display($tpl);
		
    }
}



?>