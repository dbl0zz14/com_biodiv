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
class BioDivViewDashCharts extends JViewLegacy
{
 
  
   /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  public function display($tpl = null) 
  {
    error_log ( "ChartDash view display called" );
		
	$this->personId = (int)userID();
	
	
	if ( $this->personId ) {
		
		error_log ( "DashCharts called" );
		
		$app = JFactory::getApplication();	
		
		$input = $app->input;
		
		$this->siteId = $input->get('site', 0, 'INT');
		error_log ( "DashCharts view.  Site id = " . $this->siteId );

		
		$db = JDatabase::getInstance(dbOptions());
		
		// Filter by site
		$query = $db->getQuery(true);
		$query->select("distinct S.site_name, S.site_id" )
			->from("Site S")
			->innerJoin("Photo P on P.site_id = S.site_id and P.person_id = " . $this->personId)
			->innerJoin("Animal A on A.photo_id = P.photo_id")
			->where("A.species != 97")
			->order("S.site_name");
			
		$db->setQuery($query);
		
		//error_log("Site select query created: " . $query->dump());
				
		$this->siteSelect = $db->loadAssocList("site_id", "site_name");
		
		// Count the user's sites
		$this->numSites = count($this->siteSelect);
		
		// And add the All option
		$this->siteSelect = array(JText::_("COM_BIODIV_DASHCHARTS_ALL")) + $this->siteSelect;
		
		// Does the user have any non-like classifications?
		$query = $db->getQuery(true);
		$query->select("count(*)" )
			->from("Animal A")
			->where("A.species != 97 and A.person_id = " . $this->personId);
			
		$db->setQuery($query);
		
		$this->numAnimals = $db->loadResult();
		
		//$errMsg = print_r ( $this->siteSelect, true );
		//error_log ( "siteSelect: " . $errMsg );
		
		
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>