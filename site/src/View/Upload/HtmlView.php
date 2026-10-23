<?php

namespace MammalWeb\Component\Biodiv\Site\View\Upload;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Date\Date;

 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the upload page
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
		$this->badge = $input->getInt('badge', 0);
		$this->classId = $input->getInt('class_id', 0);
		$this->siteId = $app->getUserStateFromRequest('com_biodiv.site_id', 'site_id',0);
		
		$this->trapperModel = $this->getModel();
		//$this->uploadModel = $this->getModel('Upload');


		// Get setting to determine whether camera deployment (or audio)
		$this->isCamera = $this->trapperModel->isCamera();

		// Set the help article 
		$help_option = codes_getCode("upload", "help");
		$help_details = codes_getDetails($help_option, "help");
		$article_id = $help_details["article_id"];
		
		$article = $this->trapperModel->getArticleById ( $article_id );
		
		// Default the title and introtext
		$this->title = "Help";
		$this->introtext = 0;

		if ( $article->title ) {
			$this->title = $article->title;
		}
		if ( $article->introtext ) {
			$this->introtext = $article->introtext;
		}

		$this->root = Uri::root() . "?option=com_biodiv";
		
		if ( $this->siteId != 0 ) {
		
			$this->siteName = codes_getName($this->siteId, "site");
			$this->start_date = "";
			$this->end_date = "";
			
			$this->previousUploadId = null;
			$this->previousDeploymentDate = null;
			$this->previousCollectionDate = null;
			$this->previousUploadDate = null;
			$this->previousCameraTz = null;
			$this->previousIsDst = 0;
			
			$uploadDetails = $this->trapperModel->getUploadDetails ( $this->siteId );

			if ( $uploadDetails ) {
			  
				$this->previousUploadId = $uploadDetails['upload_id'];
				$this->previousDeploymentDate = new Date($uploadDetails['deployment_date']);
				$this->previousCollectionDate = new Date($uploadDetails['collection_date']);
				$this->previousUploadDate = new Date($uploadDetails['timestamp']);
				$this->previousCameraTz = $uploadDetails['camera_tz'];
				$this->previousIsDst = $uploadDetails['is_dst'];
				
				$uploadCount = $this->trapperModel->getUploadCounts ( $this->previousUploadId );
				$this->previousNumUploaded = $uploadCount->numUploaded;
				$this->previousNumLarge = $uploadCount->numLarge;
				$this->previousNumOriginal = $uploadCount->numOriginal;
				$this->previousNumFailed = $uploadCount->numFailed;
				
			}
		}
		
		//$this->uploadForm = $this->uploadModel->getForm();
		
		// Display the view
		parent::display($tpl);
		
	}
}



?>