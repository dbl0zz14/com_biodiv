<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');

 
/**
* Ajax HTML View class for the Spotter status on User Dashboard 
*
* @since 0.0.1
*/
class BioDivViewDashTrapper extends JViewLegacy
{
 
  
   /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  public function display($tpl = null) 
  {
    $this->personId = (int)userID();
	
	if ( $this->personId ) {
		
		
		$app = JFactory::getApplication();	
		
		$this->statRows = getTrapperStatistics();
		
		// Check number of sites
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		$query->select("count(*)" )
			->from("Site S")
			->where("person_id = " . $this->personId);
			
		$db->setQuery($query);
		
		$this->numSites = $db->loadResult();
		
		//$errMsg = print_r ( $this->statRows, true );
		//error_log ( "DashTrapper trapper statRows = " . $errMsg );
		
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>