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
* HTML View class for the Kiosk Map class - ajax called from Kiosk home screen map button
*
*/
class BioDivViewKioskMap extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
	  
	error_log("KioskMap view called");
	  
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("kioskmap");
	
	error_log("KioskMap view got translations");
	
	$app = JFactory::getApplication();
	$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

	if ( !$this->projectId ) die ("no project id given" );
		
		
	// Get the map settings for the project
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true)
			->select("OD.data_type, OD.value")
			->from("OptionData OD")
			->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
			->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type in ('mapcentre', 'mapzoom')")
			->where("PO.project_id = " . $this->projectId);
	$db->setQuery($query); 
	
	$this->projectMapSettings = $db->loadAssocList('data_type', 'value');
	
	$errStr = print_r ( $this->projectMapSettings, true );
	error_log ( "KioskMap settings: " . $errStr );
		
	// All kiosks are UK based so for now set initial area to UK
	$this->initialMapSettings = array (
		"mapcentre" => "[55,-5]",
		"mapzoom" => "6"
	);
	
		
	$this->showSitesOnLoad = false;
	
	  
	// Get the species to include in dropdown.
	$this->speciesList = getFeatureSpecies();
	
	error_log("KioskMap view got species list");
	

    // Display the view
    parent::display($tpl);
  }
}



?>