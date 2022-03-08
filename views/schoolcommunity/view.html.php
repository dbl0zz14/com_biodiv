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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewSchoolCommunity extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    $this->personId = (int)userID();
    
    $app = JFactory::getApplication();
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("schoolcommunity");
	
	// Check whether set id 
			
	$input = JFactory::getApplication()->input;
	
	if ( $this->personId ) {
		
		$this->helpOption = codes_getCode ( "schoolcommunity", "beshelp" );
			
		$this->myTotalPoints = Biodiv\Task::getTotalUserPoints();
		
		$schoolRoles = Biodiv\SchoolCommunity::getSchoolRoles();
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
		$this->schoolPoints = 0;
		$this->mySchoolId = 0;
		$this->mySchoolName = "";
		$this->mySchoolRole = 0;
		
		if ( $this->schoolUser ) {
			$this->mySchoolId = $this->schoolUser->school_id;
			$this->mySchoolName = $this->schoolUser->school;
			$this->mySchoolRole = $this->schoolUser->role_id;
		}
		
		
		// Sort the schools from highest to lowest points
		function school_sort($a,$b)
		{
			if ($a->school->weightedPoints==$b->school->weightedPoints) return 0;
			return ($a->school->weightedPoints<$b->school->weightedPoints)?1:-1;
		}
		
		// Sort the schools on award, then alphabetical
		function school_sort_award($a,$b)
		{
			if ($a->awardSeq == $b->awardSeq) {
				return ( $a->schoolName > $b->schoolName );
			}
			return ($a->awardSeq < $b->awardSeq)?1:-1;
		}
		
		$this->awardIcons = array( 'NONE'=>'',
									'SCHOOL_BRONZE'=>'<span class="bronze"><i class="fa fa-trophy"></i></span>',
									'SCHOOL_SILVER'=>'<span class="bronze"><i class="fa fa-trophy"></i></span><span class="silver"><i class="fa fa-trophy"></i></span>',
									'SCHOOL_GOLD'=>'<span class="bronze"><i class="fa fa-trophy"></i></span><span class="silver"><i class="fa fa-trophy"></i></span><span class="gold"><i class="fa fa-trophy"></i></span>');
		
		// Get all schools and summaries for each.
		$this->community = new Biodiv\SchoolCommunity();
		$this->schools = $this->community->getSchools();
		
		$errMsg = print_r ( $this->schools, true );
		error_log ( "schools: " . $errMsg );
		
		$this->badgeGroups = codes_getList ( "badgegroup" );
		$this->badgeColorClasses = array();
		$this->badgeIcons = array();
		
		foreach ( $this->badgeGroups as $badgeGroup ) {
				
			$badgeGroupId = $badgeGroup[0];
			$badgeGroupName = $badgeGroup[1];
			
			$maxSchoolPoints = 0;
			
			$this->data[$badgeGroupId] = array("schools"=>array());
			
			// --------------------------- Colors
			$colorClassArray = getOptionData ( $badgeGroupId, "colorclass" ); 

			$colorClass = "";
		
			if ( count($colorClassArray) > 0 ) {
				$colorClass = $colorClassArray[0];
			}
			
			$this->badgeColorClasses[$badgeGroupId] = $colorClass;
				
				
			// ----------------------------- Icons
			// $iconArray = getOptionData ( $badgeGroupId, "icon" ); 

			// $icon = "";
		
			// if ( count($iconArray) > 0 ) {
				// $icon = $iconArray[0];
			// }
			
			// $this->badgeIcons[$badgeGroupId] = $icon;
			
			
			$badgeGroup = new Biodiv\BadgeGroup ( $badgeGroupId );
				
			$imageData = $badgeGroup->getImageData();
			
			$this->badgeIcons[$badgeGroupId] = $imageData->icon;
			
				
			
			$groupSchools = array();
			foreach ( $this->schools as $school ) {
				
				$schoolId = $school->schoolId;
				$schoolName = $school->schoolName;
				$schoolAward = $school->awardId;
				$schoolSummary = Biodiv\BadgeGroup::getSchoolSummary ( $schoolId, $badgeGroupId );
				
				$schoolSummary->schoolName = $schoolName;
				$schoolSummary->schoolId = $schoolId;
				$schoolSummary->awardType = $school->awardType;
				$schoolSummary->awardName = $school->awardName;
				$schoolSummary->awardSeq = $school->seq ? $school->seq : 0;
				
				$errMsg = print_r ( $schoolSummary, true );
				error_log ( "Got school summary:" );
				error_log ( $errMsg );
				
				$groupSchools[] = $schoolSummary;
				
				if ( $schoolSummary->school->weightedPoints > $maxSchoolPoints ) $maxSchoolPoints = $schoolSummary->school->weightedPoints;
			}
			
			

			uasort($groupSchools,"school_sort_award");
			
			$this->data[$badgeGroupId]["schools"] = $groupSchools;
			$this->data[$badgeGroupId]["maxPoints"] = $maxSchoolPoints;
		}
		
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

