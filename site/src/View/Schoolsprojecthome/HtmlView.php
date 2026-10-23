<?php

namespace MammalWeb\Component\Biodiv\Site\View\SchoolsProjectHome;

 
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
 * View for the project home page
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
		
		$this->projectId = $input->getInt('project_id', 0);
		$this->includePrivate = $input->getInt('private', 0);
		
		// Remove any stored photo id or animal ids on project load.
		$app->setUserState('com_biodiv.photo_id', null);
		$app->setUserState('com_biodiv.animal_ids', 0);

		//$this->project_id =
		//	(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		$this->projectModel = $this->getModel();

		// Determine whether audio site
		$isCamera = $this->projectModel->getSetting("camera") == "yes";
		$this->classifyView = $isCamera ? "classify" : "classifybirds";


		$displayOptionArray = $this->projectModel->getSingleProjectOptions($this->projectId, 'projectdisplay');

		// Just get the option names.
		$this->displayOptions = array_column($displayOptionArray, 'option_name');

		if ( $this->includePrivate ) {
			$this->subProjects = $this->projectModel->getSubProjectsById($this->projectId, true);
		}
		else {
			$this->subProjects = $this->projectModel->getSubProjectsById($this->projectId, false);
		}

		// Remove this project from the sub projects list...
		unset($this->subProjects[$this->projectId]);

		$this->projectTree = $this->projectModel->getProjectTree($this->projectId);

		$this->childProjects = $this->projectModel->getChildProjectsById ( $this->projectId );
		
		$this->project = codes_getDetails($this->projectId, "project");

		$article = $this->projectModel->getArticleById ( $this->project['article_id'] );

		$this->title = $article->title;
		$this->introtext = $article->introtext;
		$this->fulltext = $article->fulltext;


		$this->access_level = $this->project['access_level'];

		// Check whether there's a survey or pop up needed on the project page
		$this->generateSurvey = false;
		if ( $this->projectModel->getSetting("survey") === "yes" ) {
			
			$surveyOpts = $this->projectModel->getSingleProjectOptions($this->projectId, 'projectsurvey');
			
			if ( count($surveyOpts) > 0 ) {
				$this->generateSurvey = true;
			}
		}

		// Display the view
		parent::display($tpl);
	}
}



?>
