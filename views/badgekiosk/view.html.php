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
class BioDivViewBadgeKiosk extends JViewLegacy
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
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
		if ( $this->schoolUser ) {

			$this->projectId = $this->schoolUser->project_id;
			
			//error_log ( "Project id = " . $this->projectId );
			
			if ( !$this->projectId ) {
				error_log ( "BadgeKiosk no project id" );
			}

			if ( !$this->projectId ) die ("no project id given" );
			
			//error_log ( "Project id found" );

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
			
			// Allow for loading quiz or classify directly for badges
			$input = JFactory::getApplication()->input;
			
			$this->badgeId = $input->getInt('badge', 0);
			$this->classId = $input->getInt('class_id', 0);
			
			$this->badge = Biodiv\Badge::createFromId ( $this->schoolUser, $this->classId, $this->badgeId );
			$this->kioskParams = $this->badge->getKioskParams();
			
			$kioskParamsObj = json_decode ( $this->kioskParams );
			
			if ( property_exists( $kioskParamsObj, "systemType" ) ) {
				
				$systemBadgeType = $kioskParamsObj->systemType;
				if ( $systemBadgeType == "CLASSIFY" or $systemBadgeType == "CLASSCLASSIFY" ) {
					
					// Check there are enough sequences and widen to parent project if not
					$numUnclassified = Biodiv\SchoolCommunity::countProjectUnclassified ( $this->schoolUser );
					if ( $numUnclassified > $kioskParamsObj->threshold ) {
						$kioskParamsObj->enoughToClassify = true;
					}
					else {
						$kioskParamsObj->enoughToClassify = false;
					}
					
					$this->kioskParams = json_encode ( $kioskParamsObj );
				}
			}
			
			$schoolSettings = getSetting ( "school_icons" );
			$settingsObj = json_decode ( $schoolSettings );
			$this->logoPath = $settingsObj->logo;
			
		}
		// Display the view
		parent::display($tpl);
    }
}



?>