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
* Kiosk Classify top level
*
* @since 0.0.1
*/
class BioDivViewKioskAbout extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
				->select("OD.value")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'aboutcategory'")
				->where("PO.project_id = " . $this->projectId);
		$db->setQuery($query); 
		
		$this->categoryId = $db->loadResult();
		
		
		$dbo = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__content')
			->where('catid = ' . $this->categoryId)
			->order('ordering');
		
		$dbo->setQuery((string)$query);
		$this->articles = $dbo->loadObjectList();
		
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>