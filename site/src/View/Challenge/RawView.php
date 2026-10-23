<?php

namespace MammalWeb\Component\Biodiv\Site\View\Challenge;

 
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
 * View for when a challenge has been added
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
	  
		$this->text = Text::_("COM_BIODIV_TRAINING_CHALLENGE_REC");

		// Display the view
		parent::display($tpl);
    }
}



?>