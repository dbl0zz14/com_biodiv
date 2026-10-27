<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskLearn;

 
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

		$this->kioskSpecies = $kioskModel->getKioskSpecies($this->projectId);
		
		$this->maxSpeciesDisplayed = $this->kioskSpecies->getMaxSpeciesDisplayed();
		
		// Get these keyed on id
		$this->commonMammals = array_column($this->kioskSpecies->getCommonMammals(), NULL, "id");
		$this->allMammals = array_column($this->kioskSpecies->getAllMammals(), NULL, "id");
		$this->commonBirds = array_column($this->kioskSpecies->getCommonBirds(), NULL, "id");
		$this->allBirds = array_column($this->kioskSpecies->getAllBirds(), NULL, "id");
		
		$this->commonMammalIds = array_keys($this->commonMammals);
		$this->commonBirdIds = array_keys($this->commonBirds);
		$this->allMammalIds = array_keys($this->allMammals);
		$this->allBirdIds = array_keys($this->allBirds);
		
		// Need one long list to display, will filter using other lists
		$this->allSpecies = $this->commonMammals + $this->commonBirds;
		
		$keys = array_column($this->allSpecies, 'name');
		array_multisort($keys, SORT_ASC, $this->allSpecies);
		
		// Display the view
		parent::display($tpl);
    }
}



?>