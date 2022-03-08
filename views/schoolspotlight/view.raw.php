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
* Return school summary data for display in charts 
*
* @since 0.0.1
*/
class BioDivViewSchoolSpotlight extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "SchoolSpotlight display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("schoolspotlight");
	
		$this->personId = (int)userID();
		
		$this->schoolData = array();

		if ( !$this->personId ) {
			
			error_log("SchoolPoints view: no person id" );
			
		}
		else {
			
			$input = JFactory::getApplication()->input;
	
			$this->schoolId = $input->getInt('id', 0);
			
			$this->displayName = $input->getInt('name', 0);
			
			$this->defaultSchool = false;
			if ( !$this->schoolId ) {
				$schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
				$this->schoolId = $schoolUser->school_id;
				$this->defaultSchool = true;
			}
			
			$this->school = Biodiv\SchoolCommunity::getSchool ( $this->schoolId );
			
			$this->badgeGroups = codes_getList ( "badgegroup" );
			
			foreach ( $this->badgeGroups as $badgeGroup ) {
				
				$badgeGroupId = $badgeGroup[0];
				$badgeGroupName = $badgeGroup[1];
				
				$this->schoolData[$badgeGroupId] = array();
				
				$schoolSummary = Biodiv\BadgeGroup::getSchoolSummary ( $this->schoolId, $badgeGroupId );
				$this->data[$badgeGroupId] = $schoolSummary;
				
			}
			
					
		}

		// Display the view
		parent::display($tpl);
    }
}



?>