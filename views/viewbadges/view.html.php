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
class BioDivViewViewBadges extends JViewLegacy
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
		
		$this->badgeGroupId = 0;
		$this->data = "";

		if ( !$this->personId ) {
			
			error_log("BrowseBadges view: no person id" );
			
		}
		else {
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			$this->teacher = $input->getInt('teacher', 0);
			
			$this->moduleId = $input->getInt('module', 0);
			
			$this->helpOption = codes_getCode ( "viewbadges", "beshelp" );
		
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
			// Get the pillars: Quizzer etc
			$this->badgeGroups = codes_getList ( "badgegroup" );
			
			$this->allModules = Biodiv\Module::getModules();
			
			
			$this->stars = Biodiv\Award::getStudentStars();
			
			$this->badgeColorClasses = array();
			$this->badgeColors = array();
			$this->badgeImages = array();
			$this->badgeIcons = array();
			$this->badgeNoStars = array();
			$this->badgeStarImages = array();
			
			
			$this->badgeGroupSummary = array();
			
			foreach ( $this->badgeGroups as $badgeGroup ) {
				$groupId = $badgeGroup[0];
				
				// --------------------------- Color classes
				$colorClassArray = getOptionData ( $groupId, "colorclass" ); 

				$colorClass = "";
			
				if ( count($colorClassArray) > 0 ) {
					$colorClass = $colorClassArray[0];
				}
				//error_log ( "color class = " . $colorClass );
			
				$this->badgeColorClasses[$groupId] = $colorClass;
				
				// --------------------------- Colors
				$colorArray = getOptionData ( $groupId, "color" ); 

				$color = "";
			
				if ( count($colorArray) > 0 ) {
					$color = $colorArray[0];
				}
				error_log ( "color class = " . $colorClass );
			
				$this->badgeColors[$groupId] = $color;
				
				// ----------------------------- Images
				$imageArray = getOptionData ( $groupId, "image" ); 

				$image = "";
			
				if ( count($imageArray) > 0 ) {
					$image = $imageArray[0];
				}
				
				$this->badgeImages[$groupId] = $image;
				
				
				$badgeGroup = new Biodiv\BadgeGroup ( $groupId, $this->moduleId );
				
				$imageData = $badgeGroup->getImageData();
				
				$this->badgeIcons[$groupId] = $imageData->icon;
				$this->badgeNoStars[$groupId] = $imageData->no_stars;
				
				// $this->badgeStarImages[$groupId] = array();
				// $this->badgeStarImages[$groupId][0] = $imageData->zero_star;
				// $this->badgeStarImages[$groupId][1] = $imageData->one_star;
				// $this->badgeStarImages[$groupId][2] = $imageData->two_star;
				// $this->badgeStarImages[$groupId][3] = $imageData->three_star;
				
				// $this->badgeGroupSummary[$groupId] = $badgeGroup->getSummary();
		
			}
		}

		// Display the view
		parent::display($tpl);
    }
}



?>