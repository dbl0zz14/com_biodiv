<?php

namespace MammalWeb\Component\Biodiv\Site\View\UploadErrors;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
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
 * View for the list of uploaded files page
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

		$this->root = Uri::root() . "?option=com_biodiv";
		$this->uploadId = $app->input->getInt('upload_id',0);
		$this->siteId = $app->getUserStateFromRequest('com_biodiv.site_id', 'site_id',0);
		$this->siteName = codes_getName($this->siteId, "site");
		$this->start_date = "";
		$this->end_date = "";

		$this->trapperModel = $this->getModel();
		
		if ( !$this->siteId ) {
			
			$details = codes_getDetails($this->uploadId, 'upload');
			if ( $details ) {
				
				$this->siteId = $details['site_id'];
			}
		}

		if(!$this->trapperModel->canEdit($this->siteId, "site")){
			die("No permission for site " . $this->siteId);
		}
		
		$this->photos = $this->trapperModel->getUploadErrors ( $this->uploadId );
		
		if ( !$this->photos ) {
			$this->photos = array();
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>