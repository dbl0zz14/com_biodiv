<?php

namespace MammalWeb\Component\Biodiv\Site\View\SurveyDebrief;

 
// No direct access to this file
defined('_JEXEC') or die;
 

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use MammalWeb\Component\Biodiv\Site\Library\Core\Survey;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the trapper page
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
    
	$this->surveyId = 
	    (int)$app->getUserStateFromRequest('com_biodiv.survey', 'survey', 0);
		
	$survey = new Survey ( $this->surveyId );
	
	$this->debriefArticle = $survey->getDebriefArticle();
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>