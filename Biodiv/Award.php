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
				->select("A.*, AO.threshold_per_user as override,  IFNULL(SA.sa_id,0) as existing_award, M.name as module, SA.timestamp as award_time from Award A")
				->innerJoin("Module M on A.module_id = M.module_id" )
				->leftJoin("SchoolAwards SA on SA.award_id = A.award_id and SA.school_id = " . $schoolId )
				->leftJoin("AwardOverride AO on AO.school_id = " . $schoolId . " and AO.award_id = A.award_id")
				->where("A.module_id = " . $module )
				->where("award_winner = " . $db->quote("SCHOOL") )
				->order("A.seq");		
			
			$db->setQuery($query);
			
			//error_log("Award::getSchoolAwards  select query created: " . $query->dump());
			
			$schoolAwards = $db->loadObjectList();
		}
		
		return $schoolAwards;
	}
	
	
	public static function getSchoolModuleAwards ( $schoolId ) {
		
		$personId = userID();
		
		$schoolAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());


			$maxSelect = "select A.module_id, max(seq) as seq from Award A inner join SchoolAwards SA using (award_id) where SA.school_id = ".$schoolId." group by A.module_id";

			$query = $db->getQuery(true)
				->select("A.module_id as module_id, A.seq as seq, A.award_id as awardId, A.award_type as awardType, A.award_name as awardName from Award A")
				->innerJoin("(".$maxSelect.") SA on A.module_id = SA.module_id and A.seq = SA.seq")
				->where("A.award_winner = 'SCHOOL'");
				
			
			$db->setQuery($query);
			
			//error_log("SchoolCommunity constructor select query created: " . $query->dump());
			
			$moduleAwards = $db->loadObjectList("module_id");
			
		}
		
		return $moduleAwards;
	}
	
	
	public static function getMaxSchoolModuleAwards () {
		
		$personId = userID();
		
		$schoolAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());


			$maxSelect = "select SA.school_id, A.module_id, max(seq) as seq from Award A inner join SchoolAwards SA using (award_id) group by SA.school_id, A.module_id";

			$query = $db->getQuery(true)
				->select("A.module_id as moduleId, SA.school_id as schoolId, S.name as schoolName, A.seq as seq, A.award_id as awardId, A.award_type as awardType, A.award_name as awardName from Award A")
				->innerJoin("(".$maxSelect.") SA on A.module_id = SA.module_id and A.seq = SA.seq")
				->innerJoin("School S on S.school_id = SA.school_id")
				->where("A.award_winner = 'SCHOOL'")
				->order("seq DESC, schoolName");
				
			
			$db->setQuery($query);
			
			//error_log("SchoolCommunity constructor select query created: " . $query->dump());
			
			$moduleAwards = $db->loadObjectList();
			
			$schoolAwards = array();
			foreach ($moduleAwards as $moduleAward) {
				$schoolName = $moduleAward->schoolName;
				$moduleId = $moduleAward->moduleId;
				if ( !array_key_exists( $schoolName, $schoolAwards ) ) {
					
					$schoolAwards[$schoolName] = array();
				}
				if ( !array_key_exists( $moduleId, $schoolAwards[$schoolName] ) ) {
					
					$schoolAwards[$schoolName][$moduleId] = $moduleAward;
				}
			}
			
		}
		
		return $schoolAwards;
	}
	
	
	
	public static function getSchoolTargetAwards ( $schoolId, $module=1 ) {
		
		$personId = userID();
		
		$schoolAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("A.*, M.name as module from Award A")
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
				->select("A.*, O.option_name as badge_group, M.name as module from Award A")
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
	
	
	
	public static function getTotalStars ( $module = null ) {
		
		$personId = userID();
		
		$moduleStr = "";
		
		if ( $module ) {
			$moduleStr = "A.module_id = " . $module . " and ";
		}
		
		$starAwards = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("COUNT(A.seq) as num_stars from Award A")
				->innerJoin("UserAwards UA on UA.award_id = A.award_id and UA.person_id = ". $personId )
				->where( $moduleStr . "A.award_type = 'STAR' and A.award_winner = " . $db->quote("STUDENT") );
				
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
				->select("A.*, O.option_name as badge_group, M.name as module from Award A")
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
				
				// $errMsg = print_r ( $userPoints, true );
				// error_log ( "User points : " . $errMsg );
				
				foreach ( $possibleAwards as $award ) {
					
					//if ( array_key_exists($award->badge_group_id, $userPoints) and $userPoints[$award->badge_group_id] >= $award->threshold_per_user ) {
						
					$moduleId = $award->module_id;
					$groupId = $award->badge_group_id;
					
					if ( array_key_exists($moduleId, $userPoints) and array_key_exists($groupId, $userPoints[$moduleId]) and $userPoints[$moduleId][$groupId] >= $award->threshold_per_user ) {
						
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

