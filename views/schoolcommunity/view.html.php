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
	
	$input = JFactory::getApplication()->input;
	
	if ( $this->personId ) {
		
		$this->helpOption = codes_getCode ( "schoolcommunity", "beshelp" );
			
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
			if ( $a->totalPointsAvail > 0 ) $aAvg = round((100*$a->totalPoints)/$a->totalPointsAvail, 1);
			else $aAvg = 0;
			
			if ( $b->totalPointsAvail > 0 ) $bAvg = round((100*$b->totalPoints)/$b->totalPointsAvail, 1);
			else $bAvg = 0;
			
			if ($aAvg==$bAvg) return 0;
			return ($aAvg<$bAvg)?1:-1;
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
		
		$this->modules = Biodiv\Module::getModules();
		$this->moduleIds = array_keys ( $this->modules );
			
		// Get all schools and summaries for each.
		$this->community = new Biodiv\SchoolCommunity();
		$this->schools = $this->community->getSchools();
		$this->schoolAwards = Biodiv\Award::getMaxSchoolModuleAwards();
		
		
		$this->badgeGroups = codes_getList ( "badgegroup" );
		$this->badgeColorClasses = array();
		$this->badgeIcons = array();
		
		foreach ( $this->badgeGroups as $badgeGroup ) {
				
			$badgeGroupId = $badgeGroup[0];
			$badgeGroupName = $badgeGroup[1];
			
			
			// --------------------------- Colors
			$colorClassArray = getOptionData ( $badgeGroupId, "colorclass" ); 

			$colorClass = "";
		
			if ( count($colorClassArray) > 0 ) {
				$colorClass = $colorClassArray[0];
			}
			
			$this->badgeColorClasses[$badgeGroupId] = $colorClass;
		
		}
		
		//foreach ( $this->moduleIds as $moduleId ) {
			
		$this->data = array();
			
		foreach ( $this->badgeGroups as $badgeGroup ) {
			
			$badgeGroupId = $badgeGroup[0];
			$badgeGroupName = $badgeGroup[1];
			
			$this->data[$badgeGroupId] = array("schools"=>array());
			$maxSchoolPoints = 0;
		
					
			// ----------------------------- Icons
			// $iconArray = getOptionData ( $badgeGroupId, "icon" ); 

			// $icon = "";
		
			// if ( count($iconArray) > 0 ) {
				// $icon = $iconArray[0];
			// }
			
			// $this->badgeIcons[$badgeGroupId] = $icon;
			
			$groupSchools = array();
			foreach ( $this->schools as $school ) {
				
				$newSchool = clone $school;
				
				$newSchool->modules = array();
				
				$newSchool->totalPoints = 0;
				$newSchool->totalPointsAvail = 0;
									
				foreach ( $this->moduleIds as $moduleId ) {
					
					if ( !array_key_exists($badgeGroupId, $this->badgeIcons) ) {
						$badgeGroup = new Biodiv\BadgeGroup ( $badgeGroupId, $moduleId );
						
						$imageData = $badgeGroup->getImageData();
					
						$this->badgeIcons[$badgeGroupId] = $imageData->icon;
					
					}
			
					$moduleSummary = Biodiv\BadgeGroup::getSchoolSummary ( $newSchool->schoolId, $badgeGroupId, $moduleId );
				
					//$schoolSummary->awardType = $school->awardType;
					//$schoolSummary->awardName = $school->awardName;
					//$schoolSummary->awardSeq = $school->seq ? $school->seq : 0;
									
					//if ( $schoolSummary->school->weightedPoints > $maxSchoolPoints ) $maxSchoolPoints = $schoolSummary->school->weightedPoints;
					
					$newSchool->modules[$moduleId] = $moduleSummary;
					$newSchool->totalPoints += $moduleSummary->school->weightedPoints;
					$newSchool->totalPointsAvail += $moduleSummary->school->pointsAvailable;
				}
				
				$groupSchools[] = $newSchool;
			}
			
			uasort($groupSchools,"school_sort");
			
			$this->data[$badgeGroupId]["schools"] = $groupSchools;
			//$this->data[$badgeGroupId]["maxPoints"] = $maxSchoolPoints;
		}
		//}
		
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

