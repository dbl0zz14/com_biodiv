<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Award {
	
	private $personId;
	private $awardId;
	private $awardName;
	private $awardType;
	private $moduleId;
	private $badgeGroupId;
	private $threshold;
	
	
	function __construct( $awardId, $awardType, $awardName, $moduleId, $badgeGroupId, $threshold )
	{
		
		$this->personId = userID();
		$this->awardId = $awardId;
		$this->awardName = $awardName;
		$this->awardType = $awardType;
		$this->moduleId = $moduleId;
		$this->badgeGroupId = $badgeGroupId;
		$this->threshold = $threshold;
	}
	
	public function newAwards () {
		
	}
	
	
	public static function getSchoolAwards ( $schoolId, $module=1 ) {
		
		$personId = userID();
		
		$schoolAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("A.*, IFNULL(SA.sa_id,0) as existing_award, M.module_name as module from Award A")
				->innerJoin("Module M on A.module_id = M.module_id and M.module_id = " . $module )
				->leftJoin("SchoolAwards SA on SA.award_id = A.award_id and SA.school_id = " . $schoolId )
				->where("award_winner = " . $db->quote("SCHOOL") )
				->order("A.seq");		
			
			$db->setQuery($query);
			
			//error_log("Award::getSchoolAwards  select query created: " . $query->dump());
			
			$schoolAwards = $db->loadObjectList();
		}
		
		return $schoolAwards;
	}
	
	
	public static function getSchoolTargetAwards ( $schoolId, $module=1 ) {
		
		$personId = userID();
		
		$schoolAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("A.*, M.module_name as module from Award A")
				->innerJoin("Module M on A.module_id = M.module_id and M.module_id = " . $module )
				->where("A.award_winner = " . $db->quote("SCHOOL") )
				->where("A.award_id not in (select award_id from SchoolAwards where school_id = ". $schoolId . ")" )
				->order("M.module_id, A.seq");		
			
			$db->setQuery($query);
			
			//error_log("Award::getSchoolAwards  select query created: " . $query->dump());
			
			$schoolAwards = $db->loadObjectList();
		}
		
		return $schoolAwards;
	}
	
	
	public static function getStudentTargetAwards () {
		
		$personId = userID();
		
		$schoolAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("A.*, O.option_name as badge_group, M.module_name as module from Award A")
				->innerJoin("Options O on O.option_id = A.badge_group_id and O.struc = " . $db->quote("badgegroup"))
				->innerJoin("Module M on M.module_id = A.module_id ")
				->where("A.award_winner = " . $db->quote("STUDENT") )
				->where("A.award_id not in (select award_id from UserAwards where person_id = ". $personId . ")" )
				->order("A.seq");		
			
			$db->setQuery($query);
			
			//error_log("Award::getSchoolAwards  select query created: " . $query->dump());
			
			$schoolAwards = $db->loadObjectList();
		}
		
		return $schoolAwards;
	}
	
	
	
	public static function getStudentStars ( $module = 1 ) {
		
		$personId = userID();
		
		$starAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("A.badge_group_id as group_id, MAX(A.seq) as num_stars from Award A")
				->innerJoin("UserAwards UA on UA.award_id = A.award_id and UA.person_id = ". $personId )
				->where("A.module_id = " . $module . " and A.award_type = 'STAR' and A.award_winner = " . $db->quote("STUDENT") )
				->group("group_id");		
			
			$db->setQuery($query);
			
			//error_log("Award::getStudentStars  select query created: " . $query->dump());
			
			$starAwards = $db->loadObjectList("group_id");
		}
		
		return $starAwards;
	}
	
	
	
	public static function getTotalStars ( $module = 1 ) {
		
		$personId = userID();
		
		$starAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("COUNT(A.seq) as num_stars from Award A")
				->innerJoin("UserAwards UA on UA.award_id = A.award_id and UA.person_id = ". $personId )
				->where("A.module_id = " . $module . " and A.award_type = 'STAR' and A.award_winner = " . $db->quote("STUDENT") );
				
			$db->setQuery($query);
			
			//error_log("Award::getTotalStars  select query created: " . $query->dump());
			
			$starAwards = $db->loadResult();
		}
		
		return $starAwards;
	}
	
	
	
	public static function updateAwards () {
		
		$personId = userID();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("A.*, O.option_name as badge_group, M.module_name as module from Award A")
				->innerJoin("Options O on O.option_id = A.badge_group_id and O.struc = " . $db->quote("badgegroup"))
				->innerJoin("Module M on M.module_id = A.module_id ")
				->innerJoin("SchoolUsers SU on SU.role_id = A.role_id and SU.person_id = " . $personId )
				->where("A.award_id not in (select award_id from UserAwards where person_id = ". $personId . ")" );		
			
			$db->setQuery($query);
			
			//error_log("Award::updateAwards  select query created: " . $query->dump());
			
			$possibleAwards = $db->loadObjectList();
			
			if ( count($possibleAwards) > 0 ) {
				
				$roleId = $possibleAwards[0]->role_id;
			
				$userPoints = Task::getUserPointsByGroup($roleId);
				
				foreach ( $possibleAwards as $award ) {
					
					if ( array_key_exists($award->badge_group_id, $userPoints) and $userPoints[$award->badge_group_id] >= $award->threshold_per_user ) {
						
						$query = $db->getQuery(true);
						
						$awardFields = (object) [
							'person_id' => $personId,
							'award_id' => $award->award_id
						];		
						
						$result = $db->insertObject("UserAwards", $awardFields);
								
						if ( !$result ) {
							error_log ( "Award::updateAwards failed to write user award" );
						}
						else {
							$awardTitle = $award->award_name . ' ' . $award->module . ' ' . $award->badge_group;
							SchoolCommunity::addNotification("Well done. You achieved " . $awardTitle . "!");
							SchoolCommunity::logEvent ( false, SchoolCommunity::SCHOOL, "achieved " . $awardTitle  );
						}
					}
				}
			}
		}
	}
	
	
	
}



?>

