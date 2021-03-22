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
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("dashcharts");
	
	
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
		
		error_log("Site select query created: " . $query->dump());
				
		$this->siteSelect = $db->loadAssocList("site_id", "site_name");
		$this->siteSelect = array($this->translations['all']['translation_text']) + $this->siteSelect;
		
		//$errMsg = print_r ( $this->siteSelect, true );
		//error_log ( "siteSelect: " . $errMsg );
		
		
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>