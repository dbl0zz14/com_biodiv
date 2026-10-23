<?php

namespace MammalWeb\Component\Biodiv\Site\View\Uploaded;

 
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
		$this->siteId = $app->getUserStateFromRequest('com_biodiv.site_id', 'site_id',0);
		$this->siteName = codes_getName($this->siteId, "site");
		$this->start_date = "";
		$this->end_date = "";

		$this->trapperModel = $this->getModel();

		if(!$this->trapperModel->canEdit($this->siteId, "site")){
			die("No permission for site " . $this->siteId);
		}
		
		$this->photos = $this->trapperModel->getSitePhotos ( $this->siteId );
		$this->tosplit = $this->trapperModel->getPhotosToSplit ( $this->siteId );
		$this->toresize = $this->trapperModel->getPhotosToResize ( $this->siteId );
		$this->latestUploads = $this->trapperModel->getLatestUploads ( $this->siteId );
		
		if ( !$this->photos ) {
			$this->photos = array();
		}

		if ( !$this->tosplit ) {
			$this->tosplit = array();
		}

		if ( !$this->toresize ) {
			$this->toresize = array();
		}

		if ( !$this->latestUploads ) {
			$this->latestUploads = array();
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>