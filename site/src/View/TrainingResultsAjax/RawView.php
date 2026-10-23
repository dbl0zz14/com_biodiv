<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
namespace MammalWeb\Component\Biodiv\Site\View\TrainingResultsAjax;
  
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
 * View for trapper raw data, used eg in user dashboard
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

	$personId = $spotterModel->userID();
		
	$personId or die("Please log in");
	
	$this->helper = $spotterModel->getTraining ();
	
	$this->helper->calculateScores();
	
	$this->helper->writeScores();
		
    // Display the view
    parent::display($tpl);
  }
}



?>