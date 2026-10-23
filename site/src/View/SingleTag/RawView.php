<?php

namespace MammalWeb\Component\Biodiv\Site\View\SingleTag;

 
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
      
		$spotterModel = $this->getModel();

		// Assign data to the view
		$this->personId = $spotterModel->userID();

		if ( $this->personId ) {
		  
			$app = Factory::getApplication();

			//$this->photo_id = (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id');

			$this->animalId =
			(int)$app->getUserStateFromRequest('com_biodiv.animal_id', 'animal_id');


			if ( $this->animalId ) {
			  
				$this->animal = $spotterModel->getAnimal( $this->animalId );
			  
			}
		}
		// Display the view
		parent::display($tpl);
    }
}



?>