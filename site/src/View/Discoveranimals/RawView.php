<?php

namespace MammalWeb\Component\Biodiv\Site\View\DiscoverAnimals;

 
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
		
		$this->lat_start =
		(float)$app->getUserStateFromRequest('com_biodiv.latstart', 'latstart');
		$this->lat_end =
		(float)$app->getUserStateFromRequest('com_biodiv.latend', 'latend');
		$this->lon_start =
		(float)$app->getUserStateFromRequest('com_biodiv.lonstart', 'lonstart');
		$this->lon_end =
		(float)$app->getUserStateFromRequest('com_biodiv.lonend', 'lonend');
		
		
		$statsModel = $this->getModel();
		
		$this->data = $statsModel->discoverAnimals ( $this->lat_start, $this->lat_end, $this->lon_start, $this->lon_end, 7 );
		
		$this->colormap = $statsModel->getSetting('colormap');
		$this->data["colormap"] = json_decode($this->colormap);
		

		// Display the view
		parent::display($tpl);
    }
}



?>