<?php

namespace MammalWeb\Component\Biodiv\Site\View\DashTrapper;
  
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
    $trapperModel = $this->getModel();

	$this->personId = $trapperModel->userID();
		
	if ( $this->personId ) {
		
		
		$app = Factory::getApplication();	
		
		$this->statRows = $trapperModel->getTrapperStatistics();
		
		$this->numSites = $trapperModel->getNumSites();
		
		// // Check number of sites
		// $db = JDatabase::getInstance(dbOptions());
		
		// $query = $db->getQuery(true);
		// $query->select("count(*)" )
			// ->from("Site S")
			// ->where("person_id = " . $this->personId);
			
		// $db->setQuery($query);
		
		// $this->numSites = $db->loadResult();
		
		//$errMsg = print_r ( $this->statRows, true );
		//error_log ( "DashTrapper trapper statRows = " . $errMsg );
		
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>