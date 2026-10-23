<?php

namespace MammalWeb\Component\Biodiv\Site\View\ProjectAnimals;

 
/// No direct access to this file
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
 * View for project animals data, used in charts
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
		$input = $app->input;		

		$this->projectId = $input->getInt('project_id', 0);
		
		$projectModel = $this->getModel();
				
		$this->data = $projectModel->projectAnimals ( $this->projectId, 8 );
		
		$this->colormap = $projectModel->getSetting('colormap');
		$this->data["colormap"] = json_decode($this->colormap);
		
		// Display the view
		parent::display($tpl);
    }
	
}



?>