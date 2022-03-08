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
* Browse tasks by badge group
*
* @since 0.0.1
*/
class BioDivViewCollection extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("collection");
	
		$this->personId = (int)userID();
		
		$this->badgeGroupId = 0;
		$this->data = "";

		if ( !$this->personId ) {
			
			error_log("BrowseBadges view: no person id" );
			
		}
		else {
			
			$this->helpOption = codes_getCode ( "collection", "beshelp" );
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
			$app = JFactory::getApplication();
			
			/*
			// Get the pillars: Quizzer etc
			$this->badgeGroups = codes_getList ( "badgegroup" );
			
			$this->badgeColorClasses = array();
			$this->badgeImages = array();
			$this->badgeIcons = array();
			
			$this->badgeGroupSummary = array();
			
			foreach ( $this->badgeGroups as $badgeGroup ) {
				$groupId = $badgeGroup[0];
				
				// --------------------------- Colors
				//$badgeColorArray = getOptionData ( $groupId, "color" ); 
				$colorClassArray = getOptionData ( $groupId, "colorclass" ); 

				// if ( count($badgeColorArray) > 0 ) {
					// $badgeColor = $badgeColorArray[0];
				// }
				// error_log ( "group color = " . $badgeColor );
				
				$colorClass = "";
			
				if ( count($colorClassArray) > 0 ) {
					$colorClass = $colorClassArray[0];
				}
				//error_log ( "color class = " . $colorClass );
			
				$this->badgeColorClasses[$groupId] = $colorClass;
				
				// ----------------------------- Images
				$imageArray = getOptionData ( $groupId, "image" ); 

				$image = "";
			
				if ( count($imageArray) > 0 ) {
					$image = $imageArray[0];
				}
				
				$this->badgeImages[$groupId] = $image;
				
				
				// ----------------------------- Icons
				$iconArray = getOptionData ( $groupId, "icon" ); 

				$icon = "";
			
				if ( count($iconArray) > 0 ) {
					$icon = $iconArray[0];
				}
				
				$this->badgeIcons[$groupId] = $icon;
				
				
				$badgeResults = new Biodiv\BadgeGroup ( $groupId );
		
				$this->badgeGroupSummary[$groupId] = $badgeResults->getSummary();
		
			}
			
			*/
		}

		// Display the view
		parent::display($tpl);
    }
}



?>