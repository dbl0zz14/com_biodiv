<?php
namespace MammalWeb\Component\Biodiv\Site\View\DiscoverUserAnimals;
  
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
 * View for charts raw data, used eg in user dashboard
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
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		
		$statsModel = $this->getModel();
		
		$app = Factory::getApplication();
		
		$input = $app->input;
		
		$this->siteId = $input->get('site', 0, 'INT');
		
		$this->rare = $input->get('rare', 0, 'INT');
		
		$this->colormap = $statsModel->getSetting('colormap');
		
		$this->data = $statsModel->discoverUserAnimals ( $this->siteId, $this->rare == 0, 10, true );
		
		$this->data["colormap"] = json_decode($this->colormap);
		
		// Display the view
		parent::display($tpl);
    }
}



?>