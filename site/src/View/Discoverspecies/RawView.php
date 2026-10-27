<?php

namespace MammalWeb\Component\Biodiv\Site\View\DiscoverSpecies;

 
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
 * View for project data, used in charts
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
		
		$this->species_id =
		(int)$app->getUserStateFromRequest('com_biodiv.species', 'species');
		$this->year =
		(int)$app->getUserStateFromRequest('com_biodiv.year', 'year');
				
		$statsModel = $this->getModel();
		
		$this->data = $statsModel->discoverSpecies ( $this->species_id, $this->year );
		
		$this->colormap = $statsModel->getSetting('colormap');
		
		$this->data["colormap"] = json_decode($this->colormap);
		
		// Display the view
		parent::display($tpl);
    }
}



?>