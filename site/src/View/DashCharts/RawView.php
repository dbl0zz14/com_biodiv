<?php
namespace MammalWeb\Component\Biodiv\Site\View\DashCharts;
  
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
    
	$trapperModel = $this->getModel();

	$this->personId = $trapperModel->userID();
		
	if ( $this->personId ) {
		
		$app = Factory::getApplication();	
		
		$input = $app->input;
		
		$this->siteId = $input->get('site', 0, 'INT');
		
		$this->siteSelect = $trapperModel->getSiteSelect();
		
		// Count the user's sites
		$this->numSites = count($this->siteSelect);
		
		// And add the All option
		$this->siteSelect = array(Text::_("COM_BIODIV_DASHCHARTS_ALL")) + $this->siteSelect;
		
		$this->numAnimals = $trapperModel->getNumAnimals();
		
	}
	
    // Display the view
    parent::display($tpl);
  }
}



?>