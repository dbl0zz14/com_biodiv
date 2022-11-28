<?php

namespace Biodiv;

// Class to represent the school community with functions to get user related details.


// No direct access to this file
defined('_JEXEC') or die;

class SchoolCommunity {
	
	// Correspond to access_levels in db table
	const PERSON		= 0;
	const SCHOOL		= 1;
	const COMMUNITY		= 2;
	const ECOLOGISTS	= 3;
	
	// Correspond to roles in db table
	const ECOLOGIST_ROLE	= 3;
	const TEACHER_ROLE		= 4;
	const STUDENT_ROLE		= 5;
	const ADMIN_ROLE		= 7;
	const SUPPORTER_ROLE	= 6;
	
	private $schools;
	
	function __construct( )
	{
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		/*


		$maxSelect = "select SA.school_id, max(seq) as seq from Award A inner join SchoolAwards SA using (award_id) group by SA.school_id";

		$query = $db->getQuery(true)
			->select("S.school_id as schoolId, S.name as schoolName, A.seq as seq, A.award_id as awardId, A.award_type as awardType, A.award_name as awardName from School S")
			->leftJoin("(".$maxSelect.") SA on S.school_id = SA.school_id")
			->leftJoin("SchoolAwards SA2 on S.school_id = SA2.school_id")
			->leftJoin("Award A on A.award_id = SA2.award_id and A.award_winner = 'SCHOOL'")
			->where("A.seq = SA.seq");
			
		$query2 = $db->getQuery(true)
			->select("school_id as schoolId, name as schoolName, 0 as seq, NULL as awardId, NULL as awardType, NULL as awardName from School S")
			->where("school_id not in ( select school_id from SchoolAwards )");
		
		$query->union($query2)->order("schoolName");
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity constructor select query created: " . $query->dump());
		
		$this->schools = $db->loadObjectList();
		*/
		
		$query = $db->getQuery(true)
			->select("S.school_id as schoolId, S.name as schoolName from School S")->order("schoolName");
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity constructor select query created: " . $query->dump());
		
		$this->schools = $db->loadObjectList();
		
	}
	
	
	public function getSchools () {
		return $this->schools;
	}
	
	
	public static function getAllSchools () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("S.school_id as id, S.name as name from School S")->order("name");
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity constructor select query created: " . $query->dump());
		
		$schools = $db->loadObjectList();
		
		return $schools;
	}
	
	public static function getSchoolRoles () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, S.name, SU.role_id, R.role_name, R.display_text from SchoolUsers SU")
			->innerJoin("School S on SU.school_id = S.school_id" )
			->innerJoin("Role R on SU.role_id = R.role_id")
			->where("person_id = " . $personId);
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::getSchoolRoles select query created: " . $query->dump());
		
		$schoolRoles = $db->loadAssocList();
		
		return $schoolRoles;
		
				
	}
	
	public static function getSchoolName ( $schoolId ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("name from School S")
			->where("school_id = " . $schoolId);
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::getSchoolName select query created: " . $query->dump());
		
		$schoolName = $db->loadResult();
		
		return $schoolName;
		
				
	}
	
	public static function getUserName ( $personId = null ) {
		
		if ( !$personId ) {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("U.username from " . $userDb . "." . $prefix ."users U where U.id = " . $personId );
		
		$db->setQuery($query);
		
		//error_log("getUsername select query created: " . $query->dump());
		
		$username = $db->loadResult();
		
		return $username;
		
				
	}
	
	public static function getRoleText () {
		
		$schoolRoles = self::getSchoolRoles ();
		$roleText = null;
		
		if ( count($schoolRoles) > 0 ) {
			$roleText = $schoolRoles[0]['display_text'];
		}
		
		return $roleText;
	}
	
	
	public static function getSchoolUsers ( $schoolId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, S.name, SU.role_id, R.role_name from SchoolUsers SU")
			->innerJoin("School S on SU.school_id = S.school_id" )
			->innerJoin("Role R on SU.role_id = R.role_id")
			->where("SU.school_id = " . $schoolId);
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::getSchoolUsers select query created: " . $query->dump());
		
		$schoolUsers = $db->loadObjectList();
		
		return $schoolUsers;
		
				
	}
	
	public static function getSchoolUserCount ( $schoolId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.role_id, R.role_name, count(*) as num_users from SchoolUsers SU")
			->innerJoin("School S on SU.school_id = S.school_id" )
			->innerJoin("Role R on SU.role_id = R.role_id")
			->where("SU.school_id = " . $schoolId . " and SU.include_points = 1")
			->group("SU.role_id");
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::getSchoolUserCount select query created: " . $query->dump());
		
		$schoolUsers = $db->loadObjectList();
		
		return $schoolUsers;
		
				
	}
	
	public static function getSchool ( $schoolId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$taskTypeId = codes_getCode ( "Task", "resource" );
	
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$sitesQueryString = "select count(*) from Site S where person_id in (select person_id from SchoolUsers where school_id = " . $schoolId . ")";
		$uploadsQueryString = "select count(*) from Photo P where sequence_num = 1 and person_id in (select person_id from SchoolUsers where school_id = " . 
								$schoolId . ")";
		$classnsQueryString = "select count(distinct A.photo_id) from Animal A " .
								"where person_id in (select person_id from SchoolUsers where school_id = " . 
								$schoolId . ")";
		$resourcesQueryString = "select count(*) from Resource R where resource_type != " . $taskTypeId . 
								" and person_id in (select person_id from SchoolUsers where school_id = " . $schoolId . ")";
		
		
		$query = $db->getQuery(true)
			->select("S.*, (".$sitesQueryString.") as numSites, (".$uploadsQueryString.") as numUploaded, ("
			. $classnsQueryString . ") as numClassified, (".$resourcesQueryString.") as numResources from School S")
			->where("school_id = " . $schoolId);
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::getSchool select query created: " . $query->dump());
		
		$school = $db->loadObject();
		
		$school->userCount = self::getSchoolUserCount( $schoolId );
		
		return $school;
		
				
	}
	
	public static function getSchoolTargetOrig ( $schoolId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$school = new \StdClass();
		$school->teacherCount = 0;
		$school->studentCount = 0;
		
		$userCount = self::getSchoolUserCount( $schoolId );
		
		foreach ( $userCount as $roleCount ) {
			if ( $roleCount->role_id == self::TEACHER_ROLE ) {
				$school->teacherCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::STUDENT_ROLE ) {
				$school->studentCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::ECOLOGIST_ROLE ) {
				$school->ecologistCount = $roleCount->num_users;
			}
		}
		
		//$possibleAwards = Award::getSchoolAwards( $schoolId );
		
		$numUsers = $school->teacherCount + $school->studentCount + $school->ecologistCount;
		
		// $modules = Biodiv\Module::getModules();
		// $moduleIds = array_keys ( $this->modules );
		
		
		$school->totalTeacherPoints = Task::getSchoolTeacherPoints( $schoolId );
		
		$school->totalStudentPoints = Task::getSchoolStudentPoints( $schoolId );
		
		$school->totalEcologistPoints = Task::getSchoolEcologistPoints( $schoolId );
		
		$school->totalUserPoints = $school->totalTeacherPoints + $school->totalStudentPoints + $school->totalEcologistPoints;
		
		$school->targetFound = false;
		
		$awards = self::checkSchoolAwards ( $schoolId, $school->totalUserPoints );
		$target = $awards->targetAward;
		
		if ( $target ) {
			$school->awardName = $target->award_name;
			$school->pointsNeeded = $target->pointsNeeded;
			$school->targetFound = true;
			$school->isLatest = false;
		}
		else {
			$latest = $awards->existingAward;
			if ( $latest ) {
				$school->awardName = $latest->award_name;
				$school->pointsNeeded = 0;
				$school->isLatest = true;
			}
			
		}
		
		
		
		return $school;
		
				
	}
	
	public static function getSchoolStatus ( $schoolId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$school = new \StdClass();
		$school->teacherCount = 0;
		$school->studentCount = 0;
		$school->points = array();
		
		$userCount = self::getSchoolUserCount( $schoolId );
		
		foreach ( $userCount as $roleCount ) {
			if ( $roleCount->role_id == self::TEACHER_ROLE ) {
				$school->teacherCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::STUDENT_ROLE ) {
				$school->studentCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::ECOLOGIST_ROLE ) {
				$school->ecologistCount = $roleCount->num_users;
			}
		}
		
		//$possibleAwards = Award::getSchoolAwards( $schoolId );
		
		//$numUsers = $school->teacherCount + $school->studentCount + $school->ecologistCount;
		
		$modules = Module::getModules();
		$moduleIds = array_keys ( $modules );
		
		// Get the pillars: Quizzer etc
		$badgeGroups = codes_getList ( "badgegroup" );
		
		// Get the current status for each badge group.
		$badgeGroupSummary = array();
		
		$existingModuleAward = array();
		$newModuleAward = array();
		$targetModuleAward = array();
		
		$existingAward = null;
		$newAward = null;
		$targetAward = null;
	
		foreach ( $moduleIds as $moduleId ) {
				
			$badgeGroupSummary[$moduleId] = array();
			$schoolPoints[$moduleId] = 0;
			
			foreach ( $badgeGroups as $badgeGroup ) {
				
				$groupId = $badgeGroup[0];
			
				$badgeGroupSummary[$moduleId][$groupId] = BadgeGroup::getSchoolSummary ( $schoolId, $groupId, $moduleId );
				
				$schoolPoints[$moduleId] += $badgeGroupSummary[$moduleId][$groupId]->school->weightedPoints;
			}
			
			$targetAwards = self::checkSchoolAwards($schoolId, $schoolPoints[$moduleId], $moduleId);
			
			if ( property_exists ( $targetAwards, "existingAward" ) ) {
				$existingModuleAward[$moduleId] = $targetAwards->existingAward;
				
				if ( $targetAwards->existingAward ) {
					if ( $existingAward == null ) {
						$existingAward = $targetAwards->existingAward;
					}
					else if ( $targetAwards->existingAward->award_time > $existingAward->award_time  ) {
						$existingAward = $targetAwards->existingAward;
					}
				}
			}
			
			if ( property_exists ( $targetAwards, "latestAward" ) ) {
				$newModuleAward[$moduleId] = $targetAwards->latestAward;
				
				if ( $targetAwards->latestAward ) {
					if ( $newAward == null ) {
						$newAward = $targetAwards->latestAward;
					}
					else if ( $targetAwards->latestAward->award_time > $newAward->award_time  ) {
						$newAward = $targetAwards->latestAward;
					}
				}
			}
			
			if ( property_exists ( $targetAwards, "targetAward" ) ) {
				$targetModuleAward[$moduleId] = $targetAwards->targetAward;
				
				if ( $targetAwards->targetAward ) {
					if ( $targetAward == null ) {
						$targetAward = $targetAwards->targetAward;
					}
					else if ( $targetAwards->targetAward->threshold_per_user < $targetAward->threshold_per_user  ) {
						$targetAward = $targetAwards->targetAward;
					}
				}
			}
			$school->points[$moduleId] = $schoolPoints[$moduleId];
		}
		
		$school->badgeGroupSummary = $badgeGroupSummary;
		$school->existingAward = $existingAward;
		$school->newAward = $newAward;
		$school->targetAward = $targetAward;
		
		
		return $school;
	}
	
	/*
	public static function getSchoolTarget ( $schoolId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$school = new \StdClass();
		$school->teacherCount = 0;
		$school->studentCount = 0;
		
		$userCount = self::getSchoolUserCount( $schoolId );
		
		foreach ( $userCount as $roleCount ) {
			if ( $roleCount->role_id == self::TEACHER_ROLE ) {
				$school->teacherCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::STUDENT_ROLE ) {
				$school->studentCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::ECOLOGIST_ROLE ) {
				$school->ecologistCount = $roleCount->num_users;
			}
		}
		
		//$possibleAwards = Award::getSchoolAwards( $schoolId );
		
		$numUsers = $school->teacherCount + $school->studentCount + $school->ecologistCount;
		
		$modules = Biodiv\Module::getModules();
		$moduleIds = array_keys ( $this->modules );
		
		// Get the pillars: Quizzer etc
		$badgeGroups = codes_getList ( "badgegroup" );
		
		// Get the current status for each badge group.
		$badgeGroupSummary = array();
		$badgeColorClasses = array();
		$badgeIcons = array();
		
		$existingModuleAward = array();
		$newModuleAward = array();
		$targetModuleAward = array();
		
		$existingAward = null;
		$newAward = null;
		$targetAward = null;
	
		foreach ( $moduleIds as $moduleId ) {
				
			$badgeGroupSummary[$moduleId] = array();
			$schoolPoints[$moduleId] = 0;
			
			foreach ( $badgeGroups as $badgeGroup ) {
				
				$groupId = $badgeGroup[0];
			
				// $newBadgeGroup = new Biodiv\BadgeGroup ( $groupId, $moduleId );
				
				// if ( !array_key_exists ( $groupId, $this->badgeIcons ) ) {
					// $imageData = $newBadgeGroup->getImageData();
				
					// $this->badgeIcons[$groupId] = $imageData->icon;
				// }
				
				// //$this->badgeGroupSummary[$moduleId][$groupId] = $badgeGroup->getSummary();
				
				$badgeGroupSummary[$moduleId][$groupId] = Biodiv\BadgeGroup::getSchoolSummary ( $schoolId, $groupId, $moduleId );
				
				$schoolPoints[$moduleId] += $badgeGroupSummary[$moduleId][$groupId]->school->weightedPoints;
			}
			
			$targetAwards = Biodiv\SchoolCommunity::checkSchoolAwards($schoolId, $schoolPoints[$moduleId], $moduleId);
			
			$errMsg = print_r ( $targetAwards, true );
			error_log ( "Target awards for module " . $moduleId . ": " . $errMsg );
			
			if ( property_exists ( $targetAwards, "existingAward" ) ) {
				$existingModuleAward[$moduleId] = $targetAwards->existingAward;
				
				if ( $targetAwards->existingAward ) {
					if ( $existingAward == null ) {
						$existingAward = $targetAwards->existingAward;
					}
					else if ( $targetAwards->existingAward->award_time > $existingAward->award_time  ) {
						$existingAward = $targetAwards->existingAward;
					}
				}
			}
			
			if ( property_exists ( $targetAwards, "latestAward" ) ) {
				$newModuleAward[$moduleId] = $targetAwards->latestAward;
				
				if ( $targetAwards->latestAward ) {
					if ( $newAward == null ) {
						$newAward = $targetAwards->latestAward;
					}
					else if ( $targetAwards->latestAward->award_time > $newAward->award_time  ) {
						$newAward = $targetAwards->latestAward;
					}
				}
			}
			
			if ( property_exists ( $targetAwards, "targetAward" ) ) {
				$targetModuleAward[$moduleId] = $targetAwards->targetAward;
				
				if ( $targetAwards->targetAward ) {
					if ( $targetAward == null ) {
						$targetAward = $targetAwards->targetAward;
					}
					else if ( $targetAwards->targetAward->threshold_per_user < $targetAward->threshold_per_user  ) {
						$targetAward = $targetAwards->targetAward;
					}
				}
			}
		}
		
		if ( $target ) {
			$school->awardName = $target->award_name;
			$school->pointsNeeded = $target->pointsNeeded;
			$school->targetFound = true;
			$school->isLatest = false;
		}
		else {
			$latest = $awards->existingAward;
			if ( $latest ) {
				$school->awardName = $latest->award_name;
				$school->pointsNeeded = 0;
				$school->isLatest = true;
			}
			
		}
		
		
		// $school->totalTeacherPoints = Task::getSchoolTeacherPoints( $schoolId );
		
		// $school->totalStudentPoints = Task::getSchoolStudentPoints( $schoolId );
		
		// $school->totalEcologistPoints = Task::getSchoolEcologistPoints( $schoolId );
		
		// $school->totalUserPoints = $school->totalTeacherPoints + $school->totalStudentPoints + $school->totalEcologistPoints;
		
		// $school->targetFound = false;
		
		// $awards = self::checkSchoolAwards ( $schoolId, $school->totalUserPoints );
		// $target = $awards->targetAward;
		
		// if ( $target ) {
			// $school->awardName = $target->award_name;
			// $school->pointsNeeded = $target->pointsNeeded;
			// $school->targetFound = true;
			// $school->isLatest = false;
		// }
		// else {
			// $latest = $awards->existingAward;
			// if ( $latest ) {
				// $school->awardName = $latest->award_name;
				// $school->pointsNeeded = 0;
				// $school->isLatest = true;
			// }
			
		// }
		
		
		
		
		
		
		return $school;
				
	}
	*/
	
	public static function checkSchoolAwards($schoolId, $schoolPoints, $moduleId) {
		
		//$possibleAwards = Award::getSchoolTargetAwards( $schoolId, $moduleId );
		
		$teacherCount = 0;
		$studentCount = 0;
		$ecologistCount = 0;
		
		$userCount = self::getSchoolUserCount( $schoolId );
		
		foreach ( $userCount as $roleCount ) {
			if ( $roleCount->role_id == self::TEACHER_ROLE ) {
				$teacherCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::STUDENT_ROLE ) {
				$studentCount = $roleCount->num_users;
			}
			else if ( $roleCount->role_id == self::ECOLOGIST_ROLE ) {
				$ecologistCount = $roleCount->num_users;
			}
		}
		
		$numUsers = $teacherCount + $studentCount + $ecologistCount;
		
		$possibleAwards = Award::getSchoolAwards( $schoolId, $moduleId );
		
		$returnAwards = new \StdClass();
		
		$returnAwards->latestAward = null;
		$returnAwards->targetAward = null;
		$returnAwards->existingAward = null;
		
		foreach ( $possibleAwards as $award ) {
			
			if ( $award->existing_award > 0 ) {
				$returnAwards->existingAward = $award;
				$returnAwards->existingAward->awardName = $award->award_name;
				$returnAwards->existingAward->awardType = $award->award_type;
			}
			else {
				// Check and update where achieved. 
				if ( $award->override ) {
					$schoolThreshold = $award->override * $numUsers;
				}
				else {
					$schoolThreshold = $award->threshold_per_user * $numUsers;
				}
				if ( $schoolPoints >= $schoolThreshold ) {
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
			
					$fields = new \StdClass();
					$fields->school_id = $schoolId;
					$fields->award_id = $award->award_id;
					
					$success = $db->insertObject("SchoolAwards", $fields);
					if(!$success){
						error_log ( "SchoolAwards insert failed" );
					}			
					$returnAwards->latestAward = $award;
					$returnAwards->latestAward->awardName = $award->award_name;
					$returnAwards->latestAward->awardType = $award->award_type;
					
				}
				else if ( !$returnAwards->targetAward ) {
					$returnAwards->targetAward = $award;
					$returnAwards->targetAward->awardName = $award->award_name;
					$returnAwards->targetAward->awardType = $award->award_type;
					$returnAwards->targetAward->pointsNeeded = $schoolThreshold - $schoolPoints;
				}
			}
		}
		
		return $returnAwards;
	}
	
	
	public static function getStudentTarget () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$badgeGroupTargets = array();
		
		$userPointsByGroup = Task::getUserPointsByGroup( self::STUDENT_ROLE );
		
		$possibleAwards = Award::getStudentTargetAwards();
		
		if ( count ( $possibleAwards ) > 0 ) {
			foreach ( $possibleAwards as $award ) {
				
				$errMsg = print_r ( $award, true );
				
				// Check them in order and find the first one not yet achieved for each badge group. 
				$badgeGroupId = $award->badge_group_id;
				$alreadyFound = array_key_exists( $badgeGroupId, $badgeGroupTargets );
				$awardThreshold = $award->threshold_per_user;
				if ( array_key_exists( $badgeGroupId, $userPointsByGroup ) ) {
					$userPoints = $userPointsByGroup[$badgeGroupId];
				}
				else {
					$userPoints = 0;
				}
				
				if ( !$alreadyFound and ( $userPoints < $awardThreshold)  ) {
					
					$pointsNeeded = $awardThreshold - $userPoints;
					
					if ($pointsNeeded > 0 ) {
						
						$target = new \StdClass();
						$target->awardName = $award->award_name;
						$target->awardType = $award->award_type;
						$target->seq = $award->seq;
						$target->pointsNeeded = $pointsNeeded;
						$target->badgeGroup = $award->badge_group;
						$target->module = $award->module;
						
						$badgeGroupTargets[$badgeGroupId] = $target;
					}
				}
			}
			
			// Pick a random badge group to use
			$badgeGroups = array_keys($badgeGroupTargets);
			shuffle($badgeGroups);
			$targetGroup = $badgeGroups[0];
			
			return $badgeGroupTargets[$targetGroup];
		}
		else {
			error_log ( "No possible awards found" );
			return null;
		}
			
	}
	

	
	public static function getCelebration ( $level ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$celebration = null;
		
		if ( $level == self::SCHOOL ) {
			
		}
		else if ( $level == self::PERSON and self::isStudent() ) {
			$doneTasks = Task::getAllDoneStudentTasks();
			if ( count($doneTasks) > 0 ) {
				// Most recent task will be first
				$celebration = $doneTasks[0];
				$celebration->type = "badge";
			}
			else {
				$celebration = new \StdClass();
				$celebration->type = "welcome";
			}
		}
		
		return $celebration;
	}
	
	public static function isSchoolUser ( $userId = null ) {
		
		
		if ( $userId ) {
			$personId = $userId;
		}
		else {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers")
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$countRoles = $db->loadResult();
		
		return $countRoles > 0;
				
	}
	
	public static function isEcologist ( $userId = null ) {
		
		if ( $userId ) {
			$personId = $userId;
		}
		else {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers")
			->where("role_id = " . self::ECOLOGIST_ROLE )
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$countEcolRoles = $db->loadResult();
		
		return $countEcolRoles > 0;
				
	}
	
	public static function isAdmin () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers")
			->where("role_id = " . self::ADMIN_ROLE )
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$countAdminRoles = $db->loadResult();
		
		return $countAdminRoles > 0;
				
	}
	
	public static function isStudent () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers")
			->where("role_id = " . self::STUDENT_ROLE )
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("isStudent select query created: " . $query->dump());
		
		$countStudentRoles = $db->loadResult();
		
		return $countStudentRoles > 0;
				
	}
	
	public static function isStaff () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers")
			->where("role_id != " . self::STUDENT_ROLE )
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("isStudent select query created: " . $query->dump());
		
		$countNonStudentRoles = $db->loadResult();
		
		return $countNonStudentRoles > 0;
				
	}
	
	public static function isTeacher ( $userId = null ) {
		
		if ( $userId ) {
			$personId = $userId;
		}
		else {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers")
			->where("role_id = " . self::TEACHER_ROLE )
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("isTeacher select query created: " . $query->dump());
		
		$countTeacherRoles = $db->loadResult();
		
		return $countTeacherRoles > 0;
				
	}
	
	public static function isMyStudent ( $studentId ) {
		
		if ( !$studentId ) {
			return null;
		}
		
		$personId = userID();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, SU.new_user from SchoolUsers SU")
			->innerJoin("SchoolUsers SU2 on SU.school_id = SU2.school_id")
			->where("SU2.role_id = " . self::TEACHER_ROLE . " and SU2.person_id = " . $personId )
			->where("SU.role_id = " . self::STUDENT_ROLE . " and SU.person_id = " . $studentId );
			
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::isMyStudent select query created: " . $query->dump());
		
		$studentDetails = $db->loadAssocList();
		
		if ( count($studentDetails) > 0 ) {
			return true;
		}
		else {
			return false;
		}
				
	}
	
	
	
	public static function isNewUser ( $personId = null ) {
		
		if ( !$personId ) {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("new_user from SchoolUsers")
			->where("person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("isNewUser select query created: " . $query->dump());
		
		$newUser = $db->loadResult();
		
		return $newUser == 1;
				
	}
	
	public static function setNewUser ( $newValue ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
				
		$fields = array(
			$db->quoteName('new_user') . ' = ' . $newValue
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('person_id') . ' = ' . $personId
		);

		$query->update('SchoolUsers')->set($fields)->where($conditions);
		
		$db->setQuery($query);
		$result = $db->execute();
				
	}
	
	public static function addNotification ( $message, $personId = null, $is_positive = true ) {
		
		$loggedInPerson = userID();
		
		if ( $personId == null ) {
			$personId = $loggedInPerson;
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$fields = new \StdClass();
		$fields->person_id = $personId;
		$fields->message = $message;
		$fields->is_positive = $is_positive == true ? 1 : 0;
		
		$success = $db->insertObject("Notification", $fields);
		if(!$success){
			error_log ( "Notification insert failed" );
		}				
	}
	
	
	public static function getNotifications () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("message, is_positive from Notification")
			->where("person_id = " . $personId )
			->where("is_seen = 0" );
			
		
		$db->setQuery($query);
		
		//error_log("getNotifications select query created: " . $query->dump());
		
		$notifications = $db->loadObjectList();
		
		if ( count($notifications) > 0 ) {
			return $notifications;
		}
		else {
			return null;
		}
				
	}
	
	
	public static function notificationsSeen () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
				
		$fields = array(
			$db->quoteName('is_seen') . ' = 1' 
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('person_id') . ' = ' . $personId
		);

		$query->update('Notification')->set($fields)->where($conditions);
		
		$db->setQuery($query);
		$result = $db->execute();
				
	}
	
	
	public static function getStudentDetails ( $userId = null ) {
		
		if ( $userId ) {
			$personId = $userId;
		}
		else {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, S.name as school, SU.new_user, A.image from SchoolUsers SU")
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->where("SU.role_id = " . self::STUDENT_ROLE )
			->where("SU.person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$studentDetails = $db->loadAssocList();
		
		if ( count($studentDetails) > 0 ) {
			return $studentDetails;
		}
		else {
			return null;
		}
				
	}
	
	
	public static function getAvatars () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("* from Avatar");
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$avatars = $db->loadObjectList("avatar_id");
		
		return $avatars;
		
	}
	
	
	public static function getAvatar () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.* from Avatar A")
			->innerJoin("SchoolUsers SU on A.avatar_id = SU.avatar and SU.person_id = " . $personId);
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$avatar = $db->loadObject();
		
		return $avatar;
		
	}
	
	
	public static function setAvatar ( $avatarId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
				
		$fields = array(
			$db->quoteName('avatar') . ' = ' . $avatarId,
			$db->quoteName('timestamp') . ' = ' . "NOW()"
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('person_id') . ' = ' . $personId
		);

		$query->update("SchoolUsers")->set($fields)->where($conditions);
		
		$db->setQuery($query);
		$result = $db->execute();
	
		return self::getAvatar();
	}
	
	
	public static function getSchoolUser ( $userId = null ) {
		
		if ( $userId ) {
			$personId = $userId;
		}
		else {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return null;
		}
		
		$schoolUser = null;
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, S.name as school, S.project_id, SU.role_id, SU.new_user, U.username, A.image as avatar from SchoolUsers SU")
			->innerJoin($userDb . "." . $prefix ."users U on U.id = SU.person_id")
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->where("SU.person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$userDetails = $db->loadObjectList();
		
		if ( count($userDetails) > 0 ) {
			
			$schoolUser = $userDetails[0];
			
			if ( $schoolUser->role_id == self::ECOLOGIST_ROLE ) {
				
				$schoolUser->schoolArray = array();
				foreach ( $userDetails as $u ) {
					
					$schoolUser->schoolArray[$u->school_id] = $u->school;
				}
			}
			
		}
		else {
			$query = $db->getQuery(true)
				->select("SU.school_id, SU.role_id, SU.new_user, U.username, A.image as avatar from SchoolUsers SU")
				->innerJoin($userDb . "." . $prefix ."users U on U.id = SU.person_id")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->where("SU.person_id = " . $personId );
				
			
			$db->setQuery($query);
			
			//error_log("Set id select query created: " . $query->dump());
			
			$userDetails = $db->loadObjectList();
			
			if ( count($userDetails) > 0 ) {
			
				$schoolUser = $userDetails[0];
			
				if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
					
					$schoolUser->schoolArray = array();
					// add schools
				}
			}
		}
		return $schoolUser;
				
	}
	
	
	
	public static function getMyStudents () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, SU.person_id, U.name, U.username, A.image, SU.include_points from SchoolUsers SU")
			->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id = " . self::TEACHER_ROLE )
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
			->where("SU.role_id = " . self::STUDENT_ROLE )
			->where("SU2.person_id = " . $personId);
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$students = $db->loadObjectList("person_id");
		
		return $students;
		
				
	}
	
	
	public static function getMyStudentsProgress () {
		
		//error_log ( "SchoolCommunity::getMyStudentsProgress called" );
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		
		$query = $db->getQuery(true)
			->select("SU.school_id, SU.person_id, U.username, U.name, A.image as avatar, M.module_id, B.badge_group, SUM(T.points) as num_points from SchoolUsers SU")
			->innerJoin("StudentTasks ST on ST.person_id = SU.person_id")
			->innerJoin("Task T on T.task_id = ST.task_id and ST.status > " . Badge::PENDING)
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id = " . self::TEACHER_ROLE )
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
			->where("SU.role_id = " . self::STUDENT_ROLE )
			->where("SU2.person_id = " . $personId)
			->group("SU.school_id, SU.person_id, U.username, U.name, A.image, M.module_id, B.badge_group");
			
		
		$db->setQuery($query);
		
		//error_log("getMyStudentsProgress select query created: " . $query->dump());
		
		$students = $db->loadObjectList();
		
		$studentProgress = array();
		
		foreach ( $students as $student ) {
			if ( array_key_exists ( $student->person_id, $studentProgress ) ) {
				
				$st = $studentProgress[$student->person_id];
				
				if ( !array_key_exists($student->badge_group, $st->progress) ) {
					$st->progress[$student->badge_group] = array();
				}
				
				
				$st->progress[$student->badge_group][$student->module_id] = $student->num_points;
				if ( !array_key_exists($student->module_id, $st->totalPoints) ) {
					$st->totalPoints[$student->module_id] = $student->num_points;
				}
				else {
					$st->totalPoints[$student->module_id] += $student->num_points;
				}
				$st->grandTotal += $student->num_points;
				
			}
			else {
				
				$st = new \StdClass();
				$st->personId = $student->person_id;
				$st->username = $student->username;
				$st->name = $student->name;
				$st->avatar = $student->avatar;
				$st->grandTotal = 0;
				
				$st->progress = array();
				$st->progress[$student->badge_group] = array();
				$st->progress[$student->badge_group][$student->module_id] = $student->num_points;
				$st->totalPoints = array();
				$st->totalPoints[$student->module_id] = $student->num_points;
				$st->grandTotal += $student->num_points;
				$studentProgress[$student->person_id] = $st;
			}
		}
		
		return $studentProgress;
		
				
	}
	
	
	
	public static function getAdults () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		if ( self::isAdmin() ) {
			
			$query1 = $db->getQuery(true)
				->select("SU.school_id, SU.person_id, U.username, U.name, A.image from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.role_id in (" . self::TEACHER_ROLE . ", " . self::ECOLOGIST_ROLE . ", " . self::ADMIN_ROLE . ")" );
		}
		else {
			
			$query1 = $db->getQuery(true)
				->select("SU.school_id, SU.person_id, U.username, U.name, A.image from SchoolUsers SU")
				->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id in (" . 
						self::TEACHER_ROLE . ", " . self::ECOLOGIST_ROLE . ")" )
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.role_id in (" . self::TEACHER_ROLE . ", " . self::ECOLOGIST_ROLE . ")" );
		}
		
		$db->setQuery($query1);
		
		
		$adults = $db->loadObjectList("person_id");
		
		return $adults;
		
				
	}
	
	
	public static function pairEcologist ( $ecologistId, $schoolIds ) {
		
		if ( self::isAdmin() ) {
			
			$schoolStr = "";
		
			if ( $schoolIds ) {
				$schoolStr = implode ( ',', $schoolIds );
			}
			
			$db = \JDatabase::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
			$query->select("count(*)")
				->from( "SchoolUsers" )
				->where( "person_id = " . $ecologistId )
				->where( "role_id != " . self::ECOLOGIST_ROLE );
			$db->setQuery($query);
			
			$otherRoles = $db->loadResult(); 
			
			if ( $otherRoles > 0 ) {
				$err_str = "Cannot add ecologist as user exists with other role";
				error_log ( "SchoolUsers insert failed: " . $err_str );
			}
			else {
			
				$query = $db->getQuery(true);
				$query->select("*")
					->from( "SchoolUsers" )
					->where( "person_id = " . $ecologistId )
					->where( "school_id in ( " . $schoolStr . ' )' )
					->where( "role_id = " . self::ECOLOGIST_ROLE );
				$db->setQuery($query);
				
				$existingPairs = $db->loadObjectList("school_id");
				
				
				$existingSchoolIds = array_keys ( $existingPairs );
			
				
				foreach ( $schoolIds as $schoolId ) {
					
					if ( !in_array( $schoolId, $existingSchoolIds ) ) {
						
						error_log ( "Adding school " . $schoolId );
						$fields = new \stdClass();
						$fields->school_id = $schoolId;
						$fields->person_id = $ecologistId;
						$fields->role_id = self::ECOLOGIST_ROLE;
						
						$success = $db->insertObject("SchoolUsers", $fields);
						
						if(!$success){
							$err_str = print_r ( $fields, true );
							error_log ( "SchoolUsers insert failed: " . $err_str );
						}
				
					}
				}
				
				$conditions = array('person_id = ' . $ecologistId,
						 'school_id not in ( ' . $schoolStr . ' )',
						 'role_id = ' . self::ECOLOGIST_ROLE);

				$query = $db->getQuery(true);
				$query->delete($db->quoteName('SchoolUsers'));
				$query->where($conditions);
			 
				$db->setQuery($query);
				$result = $db->execute();
			}
		}
	}
	
	
	public static function getEcologists () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$users = array();
		
		if ( self::isAdmin($personId) ) {
		
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query1 = $db->getQuery(true)
				->select("SU.person_id, U.username, U.name, A.image, SU.school_id, S.name as school_name from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->innerJoin( "School S on S.school_id = SU.school_id" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.role_id = " . self::ECOLOGIST_ROLE );
			
			$db->setQuery($query1);
		
		
			$rows = $db->loadObjectList();
			
			foreach ( $rows as $row ) {
				
				if ( array_key_exists ( $row->person_id, $users ) ) {
					
					$existingUser = $users[$row->person_id];
					$existingUser->schools[$row->school_id] = $row->school_name;
					
				}
				else {
					$newUser = new \StdClass();
					$newUser->personId = $row->person_id;
					$newUser->name = $row->name;
					$newUser->username = $row->username;
					$newUser->avatar = $row->image;
					$newUser->schools = array();
					$newUser->schools[$row->school_id] = $row->school_name;
					
					$users[$row->person_id] = $newUser;
				}
			}
			
		}
		
		$errMsg = print_r ( $users, true );
		error_log ( "Ecologists: " . $errMsg );
		
		return $users;
				
	}
	
	
	public static function editStudent ( $studentId, $studentName, $includePoints ) {
		
		if ( self::isTeacher() ) {
			
			if ( self::isMyStudent ( $studentId ) ) {
		
				$data = array(
					'name'=>$studentName
					);
				
				//$user = new \Joomla\CMS\User ( $studentId );
				$user = null;
				
				try {
					//$user = new \Joomla\CMS\User ( $studentId );
					$user = new \JUser ( $studentId );
				} 
				catch ( Exception $e ) {
					error_log($e->getMessage());
				}
				
				$userUpdated = false;

				try{
					
					if (!$user->bind($data)){
						error_log("User bind returned false");
						error_log($user->getError());
						
					}
					
					if (!$user->save()) {
						error_log("User save returned false");
						error_log($user->getError());
						
					}
					
					if ( !$user->getError() ) {
						error_log("User saved");
						
						$userUpdated = true;
					}
					
				}
				catch(Exception $e){
					error_log($e->getMessage());
				}
				
				if ( $userUpdated ) {
					
					$options = dbOptions();
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
		
					$query = $db->getQuery(true);
							
					$fields = array(
						$db->quoteName('include_points') . ' = ' . $includePoints
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('person_id') . ' = ' . $studentId
					);

					$query->update('SchoolUsers')->set($fields)->where($conditions);
					
					$db->setQuery($query);
					$result = $db->execute();
					
					
				}
			}
		}
	}
	
	
	public static function getUsersByRole ( $roleId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$users = array();
		
		if ( self::isAdmin($personId) ) {
		
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query1 = $db->getQuery(true)
				->select("SU.school_id, SU.person_id, U.username, U.name, A.image from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.role_id = " . $roleId );
			
			$db->setQuery($query1);
		
		
			$users = $db->loadObjectList("person_id");
		}
		
		return $users;
		
				
	}
	
	
	public static function getTeacherDetails () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, S.name as school, SU.new_user, A.image from SchoolUsers SU")
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->where("SU.role_id = " . self::TEACHER_ROLE )
			->where("SU.person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$teacherDetails = $db->loadAssocList();
		
		if ( count($teacherDetails) > 0 ) {
			return $teacherDetails;
		}
		else {
			return null;
		}
				
	}
	
	public static function getAdminSummary () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		if ( !self::isAdmin() ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from Resource where deleted = 0");	
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$numResources = $db->loadResult();
		
		
		$query = $db->getQuery(true)
			->select("B.module_id, count(*) as numTasks from StudentTasks ST")
			->innerJoin("Task T on T.task_id = ST.task_id")
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->where("ST.status >= " . Badge::COMPLETE )
			->group("B.module_id");	
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$studentBadges = $db->loadAssocList("module_id", "numTasks");
		
		
		$query = $db->getQuery(true)
			->select("B.module_id, count(*) as numTasks from TeacherTasks TT")
			->innerJoin("Task T on T.task_id = TT.task_id")
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->where("TT.status >= " . Badge::COMPLETE )
			->group("B.module_id");	
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$teacherBadges = $db->loadAssocList("module_id", "numTasks");
		
		
		$query = $db->getQuery(true)
			->select("count(*) from SchoolUsers where new_user = 0 and include_points = 1");
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$numActiveUsers = $db->loadResult();
		
		
		$summary = new \StdClass();
		$summary->numResources = $numResources;
		$summary->studentBadges = $studentBadges;
		$summary->teacherBadges = $teacherBadges;
		$summary->numActiveUsers = $numActiveUsers;
		
		return $summary;
	}
	
	public static function logEvent ( $isSchoolEvent, $accessLevel, $message ) {
		
		$personId = userID();
		$schoolUser = self::getSchoolUser();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
		
			$eventFields = (object) [
				'school_id' => $schoolUser->school_id,
				'access_level' => $accessLevel,
				'school_event' => $isSchoolEvent,
				'person_id' => $personId,
				'message' => $message
			];
								
			
			$result = $db->insertObject("EventLog", $eventFields);
								
		}
		else {
			error_log ("Cannot log event as no person logged in");
		}
	}
	
	
	public static function getEncouragement () {
		
		$encourageList = codes_getList('encourage');
		
		shuffle ( $encourageList );
		return  $encourageList[0][1];
	}
	
	
	public static function generateUserBox ( $boxName, $param = null, $selected = false ) {
		
		$selectedBoxClass = "";
		$activeClass = "";
		if ( $selected ) {
			$selectedBoxClass = "btn-primary";
			$activeClass = "active";
		}
		
		
		if ( $boxName == "totalPointsBox" ) {
			if ( $param ) {
				if ( $param->points ) {
					$points = $param->points;
				}
				else {
					$points = 0;
				}
				if ( $param->slogan ) {
					$slogan = $param->slogan;
				}
				else {
					$slogan = "";
				}
			}
			else {
				$points = 0;
				$slogan = "";
			}
			
			$htmlStr = '<div class="dashboardBox totalPointsBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="totalPoints h5">';			
			$htmlStr .=  $points . ' ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_POINTS");
			$htmlStr .= '</div>';
			$htmlStr .= '<div class="userSlogan h5">';
			$htmlStr .= $slogan;
			$htmlStr .= '</div>'; // userSlogan
			$htmlStr .= '</div>'; // totalPointsBox
		}
		if ( $boxName == "sloganBox" ) {
			if ( $param ) {
				if ( property_exists ( $param, "slogan" ) ) {
					$slogan = $param->slogan;
				}
				else {
					$slogan = "";
				}
			}
			else {
				$slogan = "";
			}
			
			$htmlStr = '<div class="sloganBox text-center">';
			$htmlStr .= '<div class="userSlogan h3">';
			$htmlStr .= $slogan;
			$htmlStr .= '</div>'; // userSlogan
			$htmlStr .= '</div>'; // sloganBox
			
			/*
			$htmlStr .= '<div class="slogan">';
			$htmlStr .= '<svg viewBox="0 0 500 150">';
			$htmlStr .= '	<path id="curve" d="M73.2,148.6c4-6.1,65.5-96.8,178.6-95.6c111.3,1.2,170.8,90.3,175.1,97" />';
			$htmlStr .= '	<text id="avatarSlogan" width="500">';
			$htmlStr .= '	  <textPath xlink:href="#curve">';
			$htmlStr .= $slogan;
			$htmlStr .= '	  </textPath>';
			$htmlStr .= '	</text>';
			$htmlStr .= '</svg>';
			$htmlStr .= '</div>'; // slogan
			*/
		}
		else if ( $boxName == "avatarBox" ) {
			if ( $param ) {
				if ( property_exists ( $param, "avatar" ) ) {
					$avatar = $param->avatar;
				}
				else {
					$avatar = "";
				}
				if ( property_exists ( $param, "points" ) ) {
					$points = $param->points;
				}
				else {
					$points = 0;
				}
				if ( property_exists ( $param, "username" ) ) {
					$username = $param->username;
				}
				else {
					$username = "";
				}				
				if ( property_exists ( $param, "school" ) ) {
					$school = $param->school;
				}
				else {
					$school = "";
				}				
			}
			else {
				$avatar = "";
				$points = 0;
				$username = "";
				$school = "";
			}
			
			
			$htmlStr = '<div class="avatarBox">';
			
			$htmlStr .= '<div class="row">';
			$htmlStr .= '<div class="col-md-12 col-xs-6 text-center">';
			$htmlStr .= '<div class="schoolName">'.$school.'</div>';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '</div>'; // row
			
			$htmlStr .= '<div class="row">';
			
			$htmlStr .= '<div class="col-md-4 col-md-offset-3 col-sm-2 col-xs-6 text-center">';
			
			$htmlStr .= '<div class="row">';
			$htmlStr .= '<div class="col-md-8 col-md-offset-2">';
			$htmlStr .= '<img src="'.$avatar.'" class="img-responsive" />';
			$htmlStr .= '</div>'; // col-8-2
			$htmlStr .= '</div>'; // row
			
			$htmlStr .= '<div class="row">';
			$htmlStr .= '<div class="col-md-12">';
			$htmlStr .=  '<div class="username">'.$username.'</div>';
			$htmlStr .= '<p><small>' . $points . ' ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_POINTS_CONTRIB") . '</small></p>';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '</div>'; // row
			
			$htmlStr .=  '</div>'; // col-7
			
			$htmlStr .= '</div>'; // row
			
			$htmlStr .= '</div>'; // avatarBox
			
		}
		else if ( $boxName == "schoolPageBox" ) {
			
			$htmlStr = '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SCHOOL_LINK").'">';
			$htmlStr .= '<div class="dashboardBox schoolPageBox ' . $selectedBoxClass . '">';	
			$htmlStr .= '<div class="schoolPage h5">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SCHOOL_PAGE");
			$htmlStr .= '</div>'; // schoolPage
			$htmlStr .= '</div>'; // schoolPageBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "logoutBox" ) {
			
			$htmlStr = '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'">';
			$htmlStr .= '<div class="dashboardBox logoutBox ">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // logoutBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "resourceHubBox" ) {
			
			$htmlStr = '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HUB_LINK").'">';
			$htmlStr .= '<div class="dashboardBox resourceHubBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="resourceHub h5">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_RESOURCE_HUB");
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // resourceHubBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "communityBox" ) {
			
			$htmlStr = '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COMMUNITY_LINK").'">';
			$htmlStr .= '<div class="dashboardBox communityBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COMMUNITY");
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // communityBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "teacherDashBox" ) {
			
			$htmlStr = '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TEACHER_DASH").'">';
			$htmlStr .= '<div class="dashboardBox teacherDashBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TEACHER_PAGE");
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // teacherDashBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "studentDashBox" ) {
			
			$htmlStr = '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_DASH").'">';
			$htmlStr .= '<div class="dashboardBox studentDashBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_PAGE");
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // communityBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "avatarMenuItem" ) {
			
			if ( $param ) {
				if ( property_exists ( $param, "avatar" ) ) {
					$avatar = $param->avatar;
				}
				else {
					$avatar = "";
				}
				if ( property_exists ( $param, "points" ) ) {
					$points = $param->points;
				}
				else {
					$points = 0;
				}
				if ( property_exists ( $param, "username" ) ) {
					$username = $param->username;
				}
				else {
					$username = "";
				}				
				if ( property_exists ( $param, "school" ) ) {
					$school = $param->school;
				}
				else {
					$school = "";
				}				
			}
			else {
				$avatar = "";
				$points = 0;
				$username = "";
				$school = "";
			}
			
			
			$htmlStr = '<li>';
			
			
			
			$htmlStr .= '<div class="row">';
			$htmlStr .= '<div class="col-md-12">';
			$htmlStr .= '<img src="'.$avatar.'" class="img-responsive menuAvatar" />';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '<div class="col-md-12">';
			$htmlStr .=  '<strong>' . $username . '</strong>';
			$htmlStr .=  ' <small>' . $points . ' ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_POINTS_CONTRIB") . '</small>';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '</div>'; // row
			
			
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "studentAvatarMenuItem" ) {
			
			if ( $param ) {
				if ( property_exists ( $param, "avatar" ) ) {
					$avatar = $param->avatar;
				}
				else {
					$avatar = "";
				}
				if ( property_exists ( $param, "points" ) ) {
					$points = $param->points;
				}
				else {
					$points = 0;
				}
				if ( property_exists ( $param, "username" ) ) {
					$username = $param->username;
				}
				else {
					$username = "";
				}				
				if ( property_exists ( $param, "school" ) ) {
					$school = $param->school;
				}
				else {
					$school = "";
				}				
			}
			else {
				$avatar = "";
				$points = 0;
				$username = "";
				$school = "";
			}
			
			
			$htmlStr = '<li>';
			
						
			$htmlStr .= '<div class="row">';
			$htmlStr .= '<div class="col-md-4">';
			$htmlStr .= '<img src="'.$avatar.'" class="img-responsive menuAvatar" />';
			$htmlStr .= '</div>'; // col-4
			$htmlStr .= '<div class="col-md-8">';
			$htmlStr .=  $username;
			$htmlStr .= '</div>'; // col-8
			$htmlStr .= '<div class="col-md-4">';
			$htmlStr .= $points. ' ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_POINTS_CONTRIB");
			$htmlStr .= '</div>'; // col-4
			$htmlStr .= '</div>'; // row
			
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "schoolPageMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SCHOOL_LINK").'">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SCHOOL_PAGE");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "logoutMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'" class="h4" >';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "resourceHubMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HUB_LINK").'">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_RESOURCE_HUB");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "communityMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COMMUNITY_LINK").'">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COMMUNITY");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "teacherDashMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TEAHER_DASH").'">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TEACHER_PAGE");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "studentDashMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_DASH").'">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_PAGE");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "ecologistDashMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ECOL_DASH").'">';
			$htmlStr .= \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ECOL_PAGE");
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "projectLogo" ) {
			
			$htmlStr = '<img class="img-responsive" src="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_PROJECT_LOGO").'" />';
			
		}
		
		return $htmlStr;
		
	}
	
	
	public static function calcUserStatus ( $roleId ) {
		
		$personId = userID();
		
		$userTaskTable = "StudentTasks";
		if ( $roleId != self::STUDENT_ROLE ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$selectStars = "select count(*) from UserAwards UA inner join Award A on A.award_id = UA.award_id and A.award_type = 'STAR' where UA.person_id = " . $personId;
		
		$query = $db->getQuery(true)
			->select("SUM(T.points) as numPoints, count(*) as numBadges, (".$selectStars.") as numStars from Task T" )
			->innerJoin($userTaskTable . " UT on T.task_id = UT.task_id and UT.person_id = " . $personId)
			->where("UT.status >= " . Badge::COMPLETE );
			
		
		$db->setQuery($query);
		
		error_log("Task getTotalUserPoints select query created: " . $query->dump());
		
		$userStatus = $db->loadObject();
		
		return $userStatus;
		
	}
	
	
	
	public static function generateStudentMasthead ( $helpOptionId = 0, $slogan = null, $totalPoints = 0, $numBadges = 0, $numStars = 0, $backButtonLink = null, $calcStatus = false ) {
		
		$schoolUser = self::getSchoolUser();
		
		$totalPointsByModule = Task::getTotalUserPointsByModule();
		
		
		if ( $schoolUser ) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			$roleId = $schoolUser->role_id;
			
			$modules = Module::getModules();
			$moduleIds = array_keys($modules);
			
			if ( $calcStatus ) {
				$userStatus = self::calcUserStatus ( $roleId );
				$totalPoints = $userStatus->numPoints;
				$numBadges = $userStatus->numBadges;
				$numStars = $userStatus->numStars;
			}
			
			print '<div class="row studentMastheadRow">';
			
			print '<div class="col-md-2 col-sm-3 col-xs-5" >';
			
			print '<img src="'.$logoPath.'" class="img-responsive brandLogo" />';
			
			print '</div>'; // col-2
			
			print '<div class="col-lg-2 col-lg-push-8 col-md-2 col-md-push-8 col-sm-9 col-xs-7 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h4" data-toggle="modal" data-target="#helpModal">';
				print ' <i class="fa fa-info"></i> ';
				print '</div>';
			}
			
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'" class="btn btn-success" >';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			print '</a>';
			
			print '</div>'; // col-3
			
			
			//print '<div class="col-md-6 col-md-offset-1 col-md-pull-3 col-sm-12 col-xs-12 text-center" >';
			print '<div class="col-lg-8 col-lg-pull-2 col-md-8 col-md-pull-2 col-sm-12 col-xs-12 text-center" >';
			
			print '<table class="table statusBar" >';
			
			print '<tbody>';
			print '<tr>';
			

			print '<td class="statusBarElement statusBarAvatar" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" /></td>';
			
			
			print '<td class="text-center statusBarElement statusBarUsername" ><span class="hidden-xs">'.$schoolUser->username.'</span></td>';
			
			
			print '<td class="statusBarElement statusBarStars" ><i class="fa fa-lg fa-star statusIcon"></i><span class="label label-primary statusBadge">' . $numStars .  '</span></td>';
	
			
			print '<td class="statusBarElement statusBarBadges" ><i class="fa fa-lg fa-circle statusIcon"></i><span class="label label-primary statusBadge">' . $numBadges . '</span></td>';
			
			
			
			$numModules = count($moduleIds);
			$moduleNum = 1;
			foreach ( $moduleIds as $moduleId ) {
			
				if ( array_key_exists( $moduleId, $totalPointsByModule ) ){
					$modulePoints = $totalPointsByModule[$moduleId];
				}
				else {
					$modulePoints = (object)array("points" => 0);
				}
				$extraClass = "";
				if ( $moduleNum == $numModules ) {
					$extraClass .= " statusBarTeacherPointsRight";
				}
				
				print '<td class="statusBarElement statusBarTeacherPoints '.$extraClass.'"><img class="img-responsive statusModuleIcon'.$modules[$moduleId]->name.'" src="'.$modules[$moduleId]->icon.'" > ' . $modulePoints->points . ' </td>';
				
				$moduleNum++;
			}
			
			
			print '</tr>';
			print '</tbody>';
			
			print '</table>'; 
			
			print '</div>'; // col-8
			
			print '</div>'; // row
			
			if ( $backButtonLink ) {
				print '<div class="row">';
				
				print '<div class="col-md-2 col-sm-4 col-xs-4">';
			
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_DASH").'" class="btn btn-primary homeBtn" >';
				print '<i class="fa fa-arrow-left"></i> ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_BACK");
				print '</a>';
				
				print '</div>'; // col-1
			
				print '</div>'; // row
			}
			
			print '<div id="navEnd"></div>';
		}
			
	}
	
	
	public static function generateBackAndLogout ( $helpOptionId = 0, $slogan = null, $totalPoints = 0 ) {
		
		$schoolUser = self::getSchoolUser();
		
		if ( $schoolUser ) {
			
			$roleId = $schoolUser->role_id;
			print '<div class="row studentBackRow">';
			print '<div class="col-md-2 col-sm-4 col-xs-4">';
			
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_DASH").'" class="btn btn-default" >';
			print '<i class="fa fa-arrow-left"></i> ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENT_BACK");
			print '</a>';
			
			print '</div>'; // col-1
			
			print '<div class="col-md-2 col-md-offset-8 col-sm-4 col-sm-offset-4 col-xs-4 col-xs-offset-4 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h3" data-toggle="modal" data-target="#helpModal">';
				print '<i class="fa fa-info"></i>';
				print '</div>';
			}
			
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'" class="btn btn-default" >';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			print '</a>';
			
			print '</div>'; //col-2
			print '</div>'; // row
	
		}
	}
	
	
	public static function generateNav ( $activeItem = null, $helpOptionId = 0 ) {
		
		$schoolUser = self::getSchoolUser();
		
		$totalPointsByModule = Task::getTotalUserPointsByModule();
		
		if ( $schoolUser) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			$roleId = $schoolUser->role_id;
			
			$modules = Module::getModules();
			$moduleIds = array_keys($modules);
						
			print '<nav class="navbar navbar-default">';
			print '<div class="container-fluid staffNav">';
			
			print '<div class="navbar-header">';
			
			
			print '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#besNavbarCollapse" aria-expanded="false">';
			print '<span class="sr-only">Toggle navigation</span>';
			print '<i class="fa fa-3x fa-bars "></i>';
			print '</button>';

			print '</div>'; // navbar-header
			
			print '<div class="row studentMastheadRow">';
			
			print '<div class="col-md-2 col-sm-3 col-xs-4" >';
			
			print '<img src="'.$logoPath.'" class="img-responsive brandLogo" />';
			
			print '</div>'; // col-2
			
			
			
			print '<div class="col-lg-6 col-lg-offset-2 col-md-7 col-md-offset-1 col-sm-7 col-xs-12 text-center" >';
			
			print '<table class="table statusBar" >';
			
			print '<tbody>';
			print '<tr>';
			

			print '<td class="statusBarElement statusBarAvatar" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" /></td>';
			
			
			print '<td class="text-center statusBarElement statusBarUsername" ><span class="hidden-xs">'.$schoolUser->username.'</span></td>';
			
			if ( $roleId == self::ADMIN_ROLE ) {
				print '<td class="statusBarElement statusBarTeacherPoints statusBarTeacherPointsLeft statusBarTeacherPointsRight text-center"> ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ADMIN_USER") . ' </td>';
			}
			else {
				$numModules = count($moduleIds);
				$moduleNum = 1;
				foreach ( $moduleIds as $moduleId ) {
				
					if ( array_key_exists( $moduleId, $totalPointsByModule ) ){
						$modulePoints = $totalPointsByModule[$moduleId];
					}
					else {
						$modulePoints = (object)array("points" => 0);
					}
					$extraClass = "";
					if ( $moduleNum == 1 ) {
						$extraClass .= "statusBarTeacherPointsLeft";
					}
					if ( $moduleNum == $numModules ) {
						$extraClass .= " statusBarTeacherPointsRight";
					}
					
					print '<td class="statusBarElement statusBarTeacherPoints '.$extraClass.'"><img class="img-responsive statusModuleIcon'.$modules[$moduleId]->name.'" src="'.$modules[$moduleId]->icon.'" > ' . $modulePoints->points . ' </td>';
					
					$moduleNum++;
				}
			}
			
			print '</tr>';
			print '</tbody>';
			
			print '</table>'; 
			
			print '</div>'; // col-8
			
			print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h4" data-toggle="modal" data-target="#helpModal">';
				print ' <i class="fa fa-info"></i> ';
				print '</div>';
			}
			
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'" class="btn btn-success hidden-xs hidden-sm" >';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			print '</a>';
			
			print '</div>'; // col-2
			
			print '</div>'; // row

			print '<div class="collapse navbar-collapse" id="besNavbarCollapse">';
			print '<ul class="nav navbar-nav">';
			
			
			
			if ( $roleId == self::ECOLOGIST_ROLE ) {
				
				// ------------------------ ecologist dash
				
				$activeClass = "";
				if ( $activeItem == "ecologistdashboard" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ECOLOGIST_DASH").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ECOLOGIST_PAGE");
				print '</a>';
				print '</li>';
			
			}
			
			if ( $roleId == self::ADMIN_ROLE ) {
				
				// ------------------------ admin dash
				
				$activeClass = "";
				if ( $activeItem == "admindashboard" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ADMIN_DASH").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ADMIN_PAGE");
				print '</a>';
				print '</li>';
			
			}
			
			// ------------------------------------------ school page
			if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::STUDENT_ROLE) ) {
				$activeClass = "";
				if ( $activeItem == "schooldashboard" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SCHOOL_LINK").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SCHOOL_PAGE");
				print '</a>';
				print '</li>';
			}
			
			if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::ECOLOGIST_ROLE)) {
				
				$activeClass = "";
				if ( $activeItem == "managetasks" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TASKS_LINK").'" class="manageTasks">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MANAGE_TASKS");
				print '</a>';
				print '</li>';
				
			}
			
			
			if ( $roleId == self::TEACHER_ROLE ) {
				
				$numToApprove = Task::countMyStudentsTasks ( Badge::PENDING );
	
				$activeClass = "";
				if ( $activeItem == "students" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENTS_LINK").'" class="students">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENTS");
				if ( $numToApprove > 0 ) {
					print ' <span id="studentsBadge" class="badge notifyBadge">'.$numToApprove.'</span>';
				}
				print '</a>';
				print '</li>';
			
			
			}
			
			// ---------------------------------------- community page
			$activeClass = "";
			if ( $activeItem == "schoolcommunity" ) {
				$activeClass = "active";
			}
			print '<li class="besNavbarItem '.$activeClass.'">';
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COMMUNITY_LINK").'">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COMMUNITY");
			print '</a>';
			print '</li>';
			
			// ------------------------------------------ resource hub
			if ( $roleId != self::STUDENT_ROLE ) {
				$activeClass = "";
				if ( $activeItem == "resourcehub" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HUB_LINK").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_RESOURCE_HUB");
				print '</a>';
				print '</li>';
			
			
				$messageList = new MessageList();
				$numNewMessages = $messageList->newMessageCount();
	
				$activeClass = "";
				if ( $activeItem == "messages" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MESSAGES_LINK").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MESSAGES");
				if ( $numNewMessages > 0 ) {
					print ' <span id="messageBadge" class="badge notifyBadge">'.$numNewMessages.'</span>';
				}
				print '</a>';
				print '</li>';
			
			}
			
			print '<li class="besNavbarItem hidden-md hidden-lg">';
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			print '</a>';
			print '</li>';
			
			print '</ul>';
			
			
			print '</div>';
			
			print '</div>'; // nav collapse
			
			print '</div>'; // container-fluid
			print '</nav>'; // navbar
			
			
			print '</div>';
			
			print '<div id="navEnd"></div>';
		
		}
	}
	
}



?>

