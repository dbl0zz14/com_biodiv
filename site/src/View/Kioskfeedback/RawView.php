<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskFeedback;

 
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
 * View used as kiosk classify feedback page
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

		// Set the user state so doesn't pick up secomdary project unless asked to
		$app->setUserState('com_biodiv.classify_second_project', 0);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		$this->person_id = $kioskModel->userID();

		if ( !$this->person_id ) {
		  error_log("Feedback view: no person id" );
		  die("Feedback view please log in");
		}
		
		$this->all_animal_ids =
		$app->getUserStateFromRequest('com_biodiv.all_animal_ids', 'all_animal_ids', 0);

		$this->all_animals = 0;

		if ( $this->all_animal_ids ) {
			
			$allAnimalsArray = explode("_", $this->all_animal_ids);
			
			$this->all_animals = $kioskModel->getKioskAllAnimals ( $allAnimalsArray );
			
		}
		
		// get the url for the project image
		$this->projectImageUrl = $kioskModel->projectImageURL($this->projectId);

		// Unset the animal ids as this ends the classify session
		$app->setUserState('com_biodiv.all_animal_ids', 0);


		// Display the view
		parent::display($tpl);
    }
}



?>