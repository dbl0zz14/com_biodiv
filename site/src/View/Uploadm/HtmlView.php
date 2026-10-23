<?php

namespace MammalWeb\Component\Biodiv\Site\View\Uploadm;

 
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
 * View for the uploadm page
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
		
		$this->uploadId = $app->getUserState('com_biodiv.upload_id');
		$this->badge = $input->getInt('badge', 0);
		$this->classId = $input->getInt('class_id', 0);

		if(!$this->uploadId){
			die("No upload id");
		}
		
		$trapperModel = $this->getModel();

		$this->root = Uri::root() . "?option=com_biodiv";

		$uploadDetails = codes_getDetails($this->uploadId, 'upload');
		
		$this->siteId = $uploadDetails['site_id'];
		$this->siteName = codes_getName($this->siteId, "site");
		
		if($uploadDetails['person_id'] != $trapperModel->userID()){
			die("You cannot upload here: should be");
		}
		
		$this->prevPhotos = $trapperModel->getPrevPhotos ( $this->uploadId );
		
		// Display the view
		parent::display($tpl);
		
    }
}



?>