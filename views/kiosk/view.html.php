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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewKiosk extends JViewLegacy
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

		// Take the first kiosk option for the project 
		$kioskRows = getSingleProjectOptions ( $this->projectId, 'kiosk'  );
		$this->kiosk = $kioskRows[0]['option_name'];
		
		$this->user_key = 
			$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		$this->isSchoolUser = Biodiv\SchoolCommunity::isSchoolUser();
		$this->logoPath = null;
		if ( $this->isSchoolUser ) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$this->logoPath = $settingsObj->logo;
		}
		
		// Display the view
		parent::display($tpl);
    }
}



?>