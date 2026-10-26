<?php

namespace MammalWeb\Component\Biodiv\Site\View\DashAnalysis;
  
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
 * View for analysis user input eg density request in user dashboard
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
		$this->analysisModel = $this->getModel();

		$this->personId = $this->analysisModel->userID();
			
		if ( $this->personId ) {
			
			$app = Factory::getApplication();	
			
			$input = $app->input;
			
			$this->analysisType = $input->getInt('type', 0);
			
		}

		// Display the view
		parent::display($tpl);
	}
}



?>