<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskStart;

 
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
 * View used as kiosk home page
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
		
		$app = Factory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		if ( !$this->projectId ) die ("no project id given" );

		$kioskModel = $this->getModel();

		$this->project = $kioskModel->getProjectDetails($this->projectId);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		//error_log("Kiosk View: user_key = " . $this->user_key);
		
		// Make sure there are no stray animals
		$app->setUserState('com_biodiv.all_animal_ids', 0);

		// get the url for the project image
		$this->projectImageUrl = $kioskModel->projectImageURL($this->projectId);
		//error_log ( "Project image url: " . $this->projectImageUrl );

		// Get the logos to be displayed for this project
		$this->logos = $kioskModel->getLogos($this->projectId);
		
		$this->birdsOnly = $kioskModel->getSetting("birds_only") == "yes";

		
		// Display the view
		parent::display($tpl);
    }
}



?>