<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskMap;

 
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
 * View used as kiosk intro page
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

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		$this->projectMapSettings = $kioskModel->getMapSettings ( $this->projectId );
		
		// // Get the map settings for the project
		// $db = JDatabase::getInstance(dbOptions());
		// $query = $db->getQuery(true)
				// ->select("OD.data_type, OD.value")
				// ->from("OptionData OD")
				// ->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				// ->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type in ('mapcentre', 'mapzoom')")
				// ->where("PO.project_id = " . $this->projectId);
		// $db->setQuery($query); 

		// $this->projectMapSettings = $db->loadAssocList('data_type', 'value');


		// All kiosks are UK based so for now set initial area to UK
		$this->initialMapSettings = array (
			"mapcentre" => "[55,-5]",
			"mapzoom" => "6"
		);

			
		$this->showSitesOnLoad = false;

		  
		// Get the species to include in dropdown.
		$this->speciesList = $kioskModel->getFeatureSpecies();


		// Display the view
		parent::display($tpl);
	}
}



?>