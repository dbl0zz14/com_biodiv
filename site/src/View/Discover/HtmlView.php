<?php

namespace MammalWeb\Component\Biodiv\Site\View\Discover;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
// use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
// use Joomla\CMS\Uri\Uri;
// use Joomla\CMS\Session\Session;


 /**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the discover page - map and related charts
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

		$discoverModel = $this->getModel();

		// Get the area covered setting, currently UK or Europe, default to Europe
		$area = $discoverModel->getSetting("area_covered");
		
		if ( $area == null ) $area = "europe";
		
		// Handle different cases
		$this->areaCovered = strtolower($area);
		
		// Should we display sites when discover page is loaded
		$show = $discoverModel->getSetting("show_sites");
		
		$this->showSitesOnLoad = $show == "yes";
		 
		// Get the species to include in dropdown.
		$this->speciesList = $discoverModel->getFeatureSpecies();
		
		// Display the view
		parent::display($tpl);
	}
}



?>