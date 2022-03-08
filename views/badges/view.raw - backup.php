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
* Return badge data as JSON eg for display on student dashboard
*
* @since 0.0.1
*/
class BioDivViewBadges extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "Badges display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("badges");
	
		$this->personId = (int)userID();
		
		$this->data = array();

		if ( !$this->personId ) {
			
			error_log("Badges view: no person id" );
			
		}
		else {
			
			$this->statusIcons = array ( "fa-lock", "fa-unlock", "fa-clock-o", "fa-check", "fa-check" );
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			// Get badge group (pillar)
			$this->badgeGroupId = $input->getInt('complete', 0);
			
			$this->badgeGroupId = $input->getInt('group', 0);
			
			$this->badgeGroupName = codes_getName ( $this->badgeGroupId, "badgegroup" );
			
			error_log ( "Badge group = " . $this->badgeGroupId );

			$badgeColorArray = getOptionData ( $this->badgeGroupId, "color" ); 

			if ( count($badgeColorArray) > 0 ) {
				$this->badgeColor = $badgeColorArray[0];
			}
				
				
			// Get badge results for this group for this user
			// May extend to cover school badges or more likely have separate view.
			$badgeGroup = new Biodiv\BadgeGroup ( $this->badgeGroupId );
			
			error_log ( "BadgeGroup created" );
			
			$this->data = $badgeGroup->getAllBadgesJSON();
			
			error_log ( "Got badges from BadgeGroup class" );
			
			
		}

		// Display the view
		parent::display($tpl);
    }
}



?>