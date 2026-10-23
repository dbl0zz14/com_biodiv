<?php

namespace MammalWeb\Component\Biodiv\Site\View\Trapper;

 
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
 * View for the trapper page
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
		
		$this->trapperModel = $this->getModel();
	  
		//$isCamera = $trapperModel->getSetting("camera") == "yes";
		
		$this->fields = $this->trapperModel->getFieldsArray();
		$this->help = $this->trapperModel->getHelpArray();

		$this->sites = $this->trapperModel->getSites();
		
		$this->siteCount = $this->trapperModel->getSitePhotoCount();

		// Projects additions.
		$this->projecthelp = "All projects which this site and this user are members of.";

		$this->userprojects = $this->trapperModel->getUserProjects();
		
		// For each user project get any additional data required
		$this->projectsitedata = $this->trapperModel->getProjectSiteData();

		$this->projectsitedataJSON = $this->trapperModel->getProjectSiteDataJSON();
		
		$this->siteData = $this->trapperModel->getSiteData();

		$this->projects = $this->trapperModel->getProjects();
		
		// Check for BES user 
		$this->besProjectId = $this->trapperModel->getBesProjectId();
		
		// $schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
		// $this->besProjectId = null;
		// if ( $schoolUser ) {
			// $this->besProjectId = $schoolUser->project_id;
		// }
		

		// Display the view
		parent::display($tpl);

	}
}



?>
