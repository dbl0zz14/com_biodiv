<?php

namespace MammalWeb\Component\Biodiv\Site\View\Retire;

 
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
 * View for processing analysis requests eg density calcs
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
		
		$input = $app->input;
			
		$this->projectId = $input->getInt("project_id", 0);
		
		$this->unretire = $input->getInt("unretire", 0);
		
		$projectModel = $this->getModel();
		
		$this->retired = null;
		$this->unretired = null;
		
		if ( $this->unretire ) {
			$this->unretired = $projectModel->unretire( $this->projectId );
		}
		else {
			$this->retired = $projectModel->retire( $this->projectId );
		}

		parent::display($tpl);
		
	}
	
}



?>
