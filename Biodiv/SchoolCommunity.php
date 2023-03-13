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
	
	public static function getSchoolStatusOrig ( $schoolId ) {
		
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
	
	
	public static function atMySchool ( $userId ) {
		
		if ( !$userId ) {
			return null;
		}
		
		$personId = userID();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id from SchoolUsers SU")
			->where("SU.person_id = " . $userId )
			->where("SU.school_id in (SELECT school_id from SchoolUsers where person_id = " . $personId . ")" );
			
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::isMyStudent select query created: " . $query->dump());
		
		$matchingSchool = $db->loadAssocList();
		
		if ( count($matchingSchool) > 0 ) {
			return true;
		}
		else {
			return false;
		}
				
	}
	
	
	public static function checkMyClass ( $schoolUser, $classId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$personId = userID();
		$isMyClass = false;
		
		if ( $personId == $schoolUser->person_id ) {
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SC.school_id from SchoolClass SC")
				->where("SC.class_id = " . $classId );
				
			$db->setQuery($query);
			
			//error_log("SchoolCommunity::isMyStudent select query created: " . $query->dump());
			
			$schoolId = $db->loadResult();
			
			if ( $schoolId == $schoolUser->school_id ) {
				$isMyClass = true;
			}
		}
		
		return $isMyClass;
		
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
		
		$currentUser = userID();
		
		if ( !$currentUser ) {
			return null;
		}
		
		$schoolUser = null;
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		if ( $currentUser == $personId ) {
			$nameIfMine = "U.name";
		}
		else {
			$nameIfMine = $db->quote("name");
		}
			
		
		$query = $db->getQuery(true)
			->select("SU.person_id, SU.school_id, S.name as school, S.image as school_logo, S.project_id, SU.role_id, SU.new_user, U.username, ".$nameIfMine." as name, A.image as avatar from SchoolUsers SU")
			->innerJoin($userDb . "." . $prefix ."users U on U.id = SU.person_id")
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->where("SU.person_id = " . $personId );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$userDetails = $db->loadObjectList();
		
		if ( count($userDetails) > 0 ) {
			
			$schoolUser = $userDetails[0];
			
			// if ( $schoolUser->role_id == self::ECOLOGIST_ROLE ) {
				
				// $schoolUser->schoolArray = array();
				// foreach ( $userDetails as $u ) {
					
					// $schoolUser->schoolArray[$u->school_id] = $u->school;
				// }
			// }
			
		}
		else {
			$query = $db->getQuery(true)
				->select("SU.person_id, SU.school_id, SU.role_id, SU.new_user, U.username, A.image as avatar from SchoolUsers SU")
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
	
	
	public static function getSchoolLogoPath () {
		return "biodivimages/projects";
	}
	
	
	public static function updateSchoolLogo ( $schoolId, $newFullName ) {
		
		$result = false;
		
		if ( self::canEditSchool($schoolId) ) {
			
			$options = dbOptions();
				
			$db = \JDatabaseDriver::getInstance(dbOptions());

			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName('image') . ' = ' . $db->quote($newFullName)
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('school_id') . ' = ' . $schoolId
			);
			
			$query->update('School')->set($fields)->where($conditions);
			
			$db->setQuery($query);
			
			$result = $db->execute();
		}
		
		return $result;
	}
	
	
	public static function canEditSchool ( $schoolId ) {
		
		$schoolUser = self::getSchoolUser();
		
		if ( $schoolUser && ($schoolUser->role_id == self::ADMIN_ROLE) ) {
			return true;
		}
		else if ( $schoolUser && ($schoolUser->role_id == self::TEACHER_ROLE) && ($schoolUser->school_id == $schoolId) ) {
			return true;
		}
		else {
			return false;
		}
	
	}
	
	public static function getSchoolDetails ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		$schoolDetails = null;
		
		if ( $schoolUser->person_id == $personId ) {
		
			$db = \JDatabaseDriver::getInstance(dbOptions());
				
			$query = $db->getQuery(true)
				->select("* from School where school_id = " . $schoolUser->school_id);
				
			$db->setQuery($query);
			
			//error_log("getSchoolDetails select query created: " . $query->dump());
			
			$schoolDetails = $db->loadObject();
		}
		
		return $schoolDetails;
	}
	
	
	public static function getSchoolAccounts ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		$returnObj = new \StdClass();
		$returnObj->educators = array();
		$returnObj->classes = array();
		$returnObj->students = array();
		if ( $schoolUser ) {
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SC.*, A.image from SchoolClass SC")
				->innerJoin( "Avatar A on A.avatar_id = SC.avatar" )
				->where("SC.school_id = " . $schoolUser->school_id)
				->order("SC.is_active DESC, class_id DESC");
				
			
			$db->setQuery($query);
			
			//error_log("getSchoolAccounts select query created: " . $query->dump());
			
			$schoolClasses = $db->loadObjectList();
			
			$returnObj->classes = $schoolClasses;
			
			foreach ( $schoolClasses as $schoolClass ) {
				$returnObj->students[$schoolClass->name] = array();
			}
			$returnObj->students["no_class"] = array();
			
			$query = $db->getQuery(true)
				->select("SU.role_id, SU.person_id, SU.class_id, SC.name as class_name, U.name, U.username, A.image, SU.include_points from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->leftJoin( "SchoolClass SC on SC.class_id = SU.class_id" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.school_id = " . $schoolUser->school_id);
				
			
			$db->setQuery($query);
			
			//error_log("getSchoolAccounts select query created: " . $query->dump());
			
			$schoolUsers = $db->loadObjectList();
			
			foreach ( $schoolUsers as $nextUser ) {
				
				if ( $nextUser->role_id == self::TEACHER_ROLE ) {
					$returnObj->educators[] = $nextUser;
				}
				else if ( $nextUser->role_id == self::STUDENT_ROLE ) {
					if ( $nextUser->class_name ) {
						$returnObj->students[$nextUser->class_name][] = $nextUser;
					}
					else {
						$returnObj->students["no_class"][] = $nextUser;
					}
				}
			}
			
			
		}
		
		return $returnObj;
	}
	
	
	public static function getSchoolClasses ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$schoolClasses = array();
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
		
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SC.*, A.avatar_id, A.image from SchoolClass SC")
				->innerJoin( "Avatar A on A.avatar_id = SC.avatar" )
				->where("SC.school_id = " . $schoolUser->school_id)
				->order("SC.is_active DESC, class_id DESC");
				
			
			$db->setQuery($query);
			
			//error_log("getSchoolAccounts select query created: " . $query->dump());
			
			$schoolClasses = $db->loadObjectList();
			
		}
		
		return $schoolClasses;
	}
	
	
	private static function getSchoolAccountsByRole ( $schoolUser, $roleId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$schoolAccounts = array();
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
		
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SU.role_id, SU.person_id, SU.class_id, SC.name as class_name, U.name, U.username, A.image, SU.include_points from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->leftJoin( "SchoolClass SC on SC.class_id = SU.class_id" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.school_id = " . $schoolUser->school_id)
				->where("SU.role_id =" . $roleId);
				
			
			$db->setQuery($query);
			
			//error_log("getSchoolAccounts select query created: " . $query->dump());
			
			$schoolAccounts = $db->loadObjectList();
			
						
		}
		
		return $schoolAccounts;
	}
	
	
	private static function getStudentAccountsByClass ( $schoolUser ) {
		
		error_log ( "getStudentAccountsByClass called" );
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$schoolAccounts = array();
		
		$personId = userID();
		
		$students = array();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SC.*, A.image from SchoolClass SC")
				->innerJoin( "Avatar A on A.avatar_id = SC.avatar" )
				->where("SC.school_id = " . $schoolUser->school_id)
				->order("SC.is_active DESC, class_id DESC");
				
			
			$db->setQuery($query);
			
			error_log("getStudentAccountsByClass select classes query created: " . $query->dump());
			
			$schoolClasses = $db->loadObjectList();
			
			$students["no_class"] = array();
			foreach ( $schoolClasses as $schoolClass ) {
				$students[$schoolClass->name] = array();
			}
			
			
			$query = $db->getQuery(true)
				->select("SU.role_id, SU.person_id, SU.class_id, SC.name as class_name, U.name, U.username, A.image, SU.include_points from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->leftJoin( "SchoolClass SC on SC.class_id = SU.class_id" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.school_id = " . $schoolUser->school_id)
				->where("SU.role_id = " . self::STUDENT_ROLE);
				
			
			$db->setQuery($query);
			
			error_log("getStudentAccountsByClass select students query created: " . $query->dump());
			
			$allStudents = $db->loadObjectList();
			
			foreach ( $allStudents as $nextUser ) {
				
				if ( $nextUser->class_name ) {
					$students[$nextUser->class_name][] = $nextUser;
				}
				else {
					$students["no_class"][] = $nextUser;
				}
			}
		}
		
		return $students;

	}
	
	
	public static function printSchoolAdminSchool ( $schoolUser ) {
		
		$schoolUser = self::getSchoolUser ();

		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) )
		{
			print '<div class="adminSchoolGrid">';
			print '<div class="adminSchoolImage">';
			print '<img class="img-responsive" src="'.$schoolUser->school_logo.'">';
			print '</div>'; // adminSchoolImage
			print '<div class="adminSchoolName">';
			print '<div class="h3">'.$schoolUser->school.'</div>';
			print '</div>'; // adminSchoolName
			print '<div class="adminSchoolEdit">';
			print '<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#editSchoolModal">'.\JText::_("COM_BIODIV_SCHOOLADMIN_EDIT").'</button>';
			print '</div>'; // adminSchoolEdit
			print '</div>'; // adminSchoolGrid
		}
		
	}
	
	
	public static function printSchoolAccountTeachers ( $schoolUser ) {
		
		error_log ( "printSchoolAccountTeachers called" );
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			$teachers = self::getSchoolAccountsByRole ( $schoolUser, self::TEACHER_ROLE );
			
			self::printSchoolAccountHeadings ( $schoolUser );
			
			foreach ( $teachers as $user ) {
				self::printSchoolAccountTeacher ( $schoolUser, $user );
			}
		}
		
	}
	
	
	public static function printSchoolAccountStudents ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			$students = self::getStudentAccountsByClass ( $schoolUser );
			
			$i=1;
			foreach ( $students as $className=>$classStudents ) {
				print '<div href="#studentAccounts_'.$i.'" class="studentAccountsClassGrid" role="button" data-toggle="collapse">';
				if ( $className == "no_class" ) {
					print '<div class="h3 vSpaced studentAccountsClassName">'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_NO_CLASS").
						'</div>';
				}
				else {
					print '<div href="#studentAccounts_'.$i.'"  class="h3 vSpaced studentAccountsClassName" role="button" data-toggle="collapse">'.$className.
						'</div>';
				}
				print '<div class="h3 vSpaced studentAccountsToggle">';
				if ( count($classStudents) > 0 ) {
					print '<i class="fa fa-lg fa-angle-down"></i>';
				}
				print '</div>';
				print '</div>'; // studentAccountsClassGrid
				
				print '<div id="studentAccounts_'.$i.'" class="collapse">';
				
				// Headings
				self::printSchoolAccountHeadings( $schoolUser );
				
				foreach ( $classStudents as $user ) {
					
					self::printSchoolAccountStudent( $schoolUser, $user );
					
				}
				
				print '</div>';
				$i++;
			}
		}
		
	}
	
	
	public static function printSchoolAccountClasses ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			$users = self::getSchoolClasses ( $schoolUser );
			
			error_log ( "Got " . count($users) . " class accounts" );
			
			self::printSchoolClassHeadings ( $schoolUser );
			
			foreach ( $users as $user ) {
				self::printSchoolAccountClass ( $schoolUser, $user );
			}
		}
		
	}
	
	
	public static function printSchoolAccountHeadings ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) )
		{
			print '<div class="schoolAccountGrid headingGrid">';
			print '<div class="schoolAccountImage text-center">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_AVATAR");
			print '</div>'; // schoolAccountImage
			print '<div class="schoolAccountUsername">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_USERNAME");
			print '</div>'; // schoolAccountUsername
			print '<div class="schoolAccountName">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_NAME");
			print '</div>'; // schoolAccountName
			print '<div class="schoolAccountIsActive">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_IS_ACTIVE");
			print '</div>'; // schoolAccountIsActive
			print '</div>'; // schoolAccountGrid
		}
	}
	
	
	public static function printSchoolClassHeadings ( $schoolUser ) {
		
		error_log ( "printSchoolClassHeadings called" );
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) )
		{
		
			print '<div class="schoolAccountGrid headingGrid">';
			print '<div class="schoolAccountImage text-center">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_AVATAR");
			print '</div>'; // schoolAccountImage
			print '<div class="schoolAccountUsername">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_NAME");
			print '</div>'; // schoolAccountUsername
			print '<div class="schoolAccountIsActive">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_IS_ACTIVE");
			print '</div>'; // schoolAccountIsActive
			print '</div>'; // schoolAccountGrid
		}
		
		error_log ( "printSchoolClassHeadings complete" );
	
	}
	
	
	public static function printSchoolAccountClass ( $schoolUser, $user ) {
		
		error_log ( "printSchoolAccountClass called" );
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) )
		{
			print '<div class="schoolAccountGrid">';
			print '<div class="schoolAccountImage">';
			print '<img id="classAvatar_'.$user->class_id.'" class="img-responsive" src="'.$user->image.'" data-avatar="'.$user->avatar_id.'">';
			print '</div>'; // schoolAccountImage
			print '<div class="schoolAccountUsername">';
			print '<div id="className_'.$user->class_id.'">'.$user->name.'</div>';
			print '</div>'; // schoolAccountUsername
			$icon = '';
			$isActive = 0;
			if ( $user->is_active == 1 ) {
				$icon = '<i class="fa fa-check"></i>';
				$isActive = 1;
			}
			print '<div class="schoolAccountIsActive">';
			print '<div id="classActive_'.$user->class_id.'" data-isActive="'.$isActive.'">'.$icon.'</div>';
			print '</div>'; // schoolAccountIsActive
			print '<div class="schoolAccountEdit">';
			print '<div id="editClass_'.$user->class_id.'" class="btn btn-info editClass" role="button" data-toggle="modal" data-target="#editClassModal">'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_EDIT").'</div>';
			print '</div>'; // schoolAccountEdit
			print '</div>'; // schoolAccountGrid
		}
	}
	
	
	public static function printSchoolAccountTeacher ( $schoolUser, $user ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) )
		{
			print '<div class="schoolAccountGrid">';
			print '<div class="schoolAccountImage">';
			print '<img class="img-responsive" src="'.$user->image.'">';
			print '</div>'; // schoolAccountImage
			print '<div class="schoolAccountUsername">';
			print '<div id="teacherUsername_'.$user->person_id.'">'.$user->username.'</div>';
			print '</div>'; // schoolAccountUsername
			print '<div class="schoolAccountName">';
			print '<div id="teacherName_'.$user->person_id.'">'.$user->name.'</div>';
			print '</div>'; // schoolAccountName
			$icon = '';
			$isActive = 0;
			if ( $user->include_points == 1 ) {
				$icon = '<i class="fa fa-check"></i>';
				$isActive = 1;
			}
			print '<div class="schoolAccountIsActive">';
			print '<div id="teacherActive_'.$user->person_id.'" data-isActive="'.$isActive.'">'.$icon.'</div>';
			print '</div>'; // schoolAccountIsActive
			print '<div class="schoolAccountEdit">';
			print '<div id="editAccount_'.$user->person_id.'" class="btn btn-info editTeacher" role="button" data-toggle="modal" data-target="#editTeacherModal">'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_EDIT").'</div>';
			print '</div>'; // schoolAccountEdit
			print '</div>'; // schoolAccountGrid
		}
	}
	
	
	public static function printSchoolAccountStudent ( $schoolUser, $user ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser ();
		}
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) )
		{
			print '<div class="schoolAccountGrid">';
			print '<div class="schoolAccountImage">';
			print '<img class="img-responsive" src="'.$user->image.'">';
			print '</div>'; // schoolAccountImage
			print '<div class="schoolAccountUsername">';
			print '<div id="studentUsername_'.$user->person_id.'">'.$user->username.'</div>';
			print '</div>'; // schoolAccountUsername
			print '<div class="schoolAccountName">';
			print '<div id="studentName_'.$user->person_id.'"  data-classid="'.$user->class_id.'">'.$user->name.'</div>';
			print '</div>'; // schoolAccountName
			$icon = '';
			$isActive = 0;
			if ( $user->include_points == 1 ) {
				$icon = '<i class="fa fa-check"></i>';
				$isActive = 1;
			}
			print '<div class="schoolAccountIsActive">';
			print '<div id="studentActive_'.$user->person_id.'" data-isActive="'.$isActive.'">'.$icon.'</div>';
			print '</div>'; // schoolAccountIsActive
			print '<div class="schoolAccountEdit">';
			print '<div id="editAccount_'.$user->person_id.'" class="btn btn-info editStudent" role="button" data-toggle="modal" data-target="#editStudentModal">'.\JText::_("COM_BIODIV_SCHOOLADMIN_EDIT").'</div>';
			print '</div>'; // schoolAccountEdit
			print '</div>'; // schoolAccountGrid
		}
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
	
	
	public static function getMySchoolPerson ( $schoolUser, $studentId ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		if ( $personId != $schoolUser->person_id ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, SU.role_id, SU.person_id, U.name, U.username, A.image, SU.include_points from SchoolUsers SU")
			->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id = " . self::TEACHER_ROLE )
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
			->where("SU.person_id = " . $studentId )
			->where("SU2.person_id = " . $personId);
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$student = $db->loadObject("person_id");
		
		return $student;
		
				
	}
	
	
	public static function getMyStudentsProgress () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		
		$query = $db->getQuery(true)
			->select("SU.school_id, SU.person_id, U.username, U.name, A.image as avatar, IFNULL(MAX(SA.level),0) as max_level, GROUP_CONCAT(SB.badge_id ORDER BY SB.badge_id SEPARATOR ',' ) as badges from SchoolUsers SU")
			->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id = " . self::TEACHER_ROLE )
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->leftJoin("StudentAwards SA on SA.person_id = SU.person_id")
			->leftJoin("StudentBadges SB on SB.person_id = SU.person_id and SB.status > 2")
			->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
			->where("SU.role_id = " . self::STUDENT_ROLE )
			->where("SU2.person_id = " . $personId)
			->group("SU.person_id");
			
		
		$db->setQuery($query);
		
		error_log("getMyStudentsProgress select query created: " . $query->dump());
		
		$students = $db->loadObjectList("person_id");
		
		return $students;
				
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
		
		return $users;
				
	}
	
	
	public static function getFeaturedSpecies () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
			
		$query = $db->getQuery(true)
			->select("* from FeaturedSpecies")
			->where("week = WEEK(CURDATE())" );
		
		$db->setQuery($query);
		
		//error_log("getFeaturedSpecies select query created: " . $query->dump());
	
		$species = $db->loadObject();
		
		return $species;
	}
	
	
	public static function addClass ( $schoolUser, $className, $avatar ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		if ( $schoolUser->role_id == self::TEACHER_ROLE ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
			$query->select("count(*)")
				->from( "SchoolClass" )
				->where( "school_id = " . $schoolUser->school_id )
				->where( "name = " . $db->quote($className) );
			$db->setQuery($query);
			
			$existingClass = $db->loadResult(); 
			
			if ( $existingClass > 0 ) {
				error_log ( "Class " . $className . " already in use, cannot create" );
				$result["errors"][] = array("error"=>"Class ".$className." already exists - cannot create");
			}
			else {
		
				$fields = new \StdClass();
				$fields->name = $className;
				$fields->avatar = $avatar;
				$fields->school_id = $schoolUser->school_id;
				
				$success = $db->insertObject("SchoolClass", $fields);
				if(!$success){
					error_log ( "SchoolClass insert failed" );
					$result["errors"][] = array("error"=>"Class ".$className." - problem creating");
				}	
			}
		}
		
		return $result;
		
	}
	
	
	public static function registerNewSchool ( $name, $username, $email, $password, 
												$schoolName, $postcode, $website, $wherehear, $terms ) {
		
		$result = array();
		$result["errors"] = array();
		
		$nameF = filter_var($name, FILTER_SANITIZE_STRING);
		$usernameF = filter_var($username, FILTER_SANITIZE_STRING);
		$emailF = filter_var($email, FILTER_SANITIZE_STRING);
		$passwordF = filter_var($password, FILTER_SANITIZE_STRING);
		$schoolNameF = filter_var($schoolName, FILTER_SANITIZE_STRING);
		$postcodeF = filter_var($postcode, FILTER_SANITIZE_STRING);
		$websiteF = filter_var($website, FILTER_SANITIZE_STRING);
		$wherehearF = filter_var($wherehear, FILTER_SANITIZE_STRING);
		
		$helper = new \BiodivHelper();
		
		$existingUserEmail = $helper->getUser ( $emailF );
		
		if ( $existingUserEmail ) {
			error_log ( "Email " . $emailF . " already in use, cannot create" );
			$result["errors"][] = array("error"=>"Email ".$emailF." already exists - cannot create");
			
		}
		else if ( \JUserHelper::getUserId($usernameF) ) {
			error_log ( "username " . $usernameF . " already in use, cannot create" );
			$result["errors"][] = array("error"=>"Username ".$usernameF." already exists - cannot create");
		}
		else {
			
			$profileMW = array( 
				'tos'=>$terms,
				'wherehear'=>"BES signup: " . $wherehearF,	
				'subscribe'=>0
				);
			
			// Add to Registered group
			//$groups = array("2"=>"2");
			
			$config = \JComponentHelper::getParams('com_users');
			$defaultUserGroup = $config->get('new_usertype', 2);
			$groups = array($defaultUserGroup);
			
			
			$data = array(
			'name'=>$nameF,
			'username'=>$usernameF,
			'password'=>$passwordF,
			'email'=>$emailF,
			'sendEmail'=>1,
			'block'=>1,
			'profileMW'=>$profileMW,
			'groups'=>$groups,
			);
			
			$hash = \JApplicationHelper::getHash(\JUserHelper::genRandomPassword());
			$data['activation'] = $hash;
			$data['block'] = 1;
			
			$user = new \JUser;
			
			$userCreated = false;

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
					
					$userCreated = true;
				}
				
			}
			catch(Exception $e){
				$result["errors"][] = array("error"=>"Username ".$usernameF." - problem creating user");
			}
			
			if ( $userCreated ) {
				$addSignupResult = self::addSignup ( $user->id, $nameF, $schoolNameF, $postcodeF, $websiteF, $terms );
			}
			else {
				$result["errors"][] = array("error"=>"Username ".$usernameF." - problem creating user");
			}
			if ( $addSignupResult && (count($addSignupResult["errors"]) == 0) ) {
				$subject = \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SIGNUP_SUBJECT");
				$msg = \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SIGNUP_MESSAGE") . ' <a href="'.\JURI::root().'/bes-schools-admin">'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SIGNUP_LINK_TEXT").'</a>';
				self::notifyAdmins ( $subject, $msg );
			}
			else if ( $addSignupResult ){
				foreach ( $addSignupResult["errors"] as $nextError ) {
					$result["errors"][] = $nextError;
				}
			}
			else {
				$result["errors"][] = array("error"=>"Error signing up school");
			}
		}	
		return $result;
	}
		

	private static function notifyAdmins ( $subject, $msg ) {
		
		try {
			
			$mailer = \JFactory::getMailer();
			
			$config = \JFactory::getConfig();
			$sender = array( 
				$config->get( 'mailfrom' ),
				$config->get( 'fromname' ) 
			);

			$mailer->setSender($sender);
			
			$emails = json_decode(getSetting("bes_emails"));
			
			$recipient = array();
			foreach ( $emails as $email ) {
				$recipient[] = $email;
			}
			
			$mailer->addRecipient($recipient);
			
			$body   = $msg;
			
			$mailer->isHtml(true);
			$mailer->Encoding = 'base64';

			$mailer->setSubject($subject);
			$mailer->setBody($body);
			
			$send = $mailer->Send();
			if ( $send !== true ) {
				error_log ( 'Error sending email: ' . $send->getMessage() );
			} else {
				error_log ( 'Mail sent' );
			}			
				
			// foreach ( $emails as $email ) {
				// mail( $email, $subject, $msg );
			// }
		} catch ( \Exception $e ) {
			error_log ( "notifyAdmins exception caught: " . $e->getMessage() );
		}
	}
	
	
	public static function notifyUser ( $personId, $subject, $msg) {
		
		error_log ( "notifyUser called" );
		
		try {
			
			$mailer = \JFactory::getMailer();
			$config = \JFactory::getConfig();
			$sender = array( 
				$config->get( 'mailfrom' ),
				$config->get( 'fromname' ) 
			);
			$mailer->setSender($sender);
			
			$newUser = \JFactory::getUser ( $personId );
			$email = $newUser->email;
			
			$mailer->addRecipient($email);
			
			$body   = $msg;
			
			$mailer->isHtml(true);
			$mailer->Encoding = 'base64';

			$mailer->setSubject($subject);
			$mailer->setBody($body);
			error_log ( "About to send mail" );
			$send = $mailer->Send();
			if ( $send !== true ) {
				error_log ( 'Error sending email: ' . $send->getMessage() );
			} else {
				error_log ( 'Mail sent' );
			}			
			
		} catch ( \Exception $e ) {
			error_log ( "notifyAdmins exception caught: " . $e->getMessage() );
		}

		// if ( $newUser ) {
					// try {
						// $email = $newUser->email;
						// mail( $email, $subject, $msg );	
						
					// } catch ( \Exception $e ) {
						// error_log ( "Email notification to new user failed" );
					// }
				// }
	}
	
																		
	public static function addSchoolUser ( $schoolUser, $roleId, $name, $classId, $username, $email, $password ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		if ( $schoolUser->role_id == self::TEACHER_ROLE ) {
			
			$schoolName = preg_replace('/\s+/u', '', $schoolUser->school);
			$schoolStem = substr($schoolName, 0, 20) . $schoolUser->school_id;
			$emailDomain = "mammalweb.org";
			if ( !$email ) {
				$email = $username . '.' . $schoolStem . '@' . $emailDomain;
			}
			
			$helper = new \BiodivHelper();
			
			$existingUserEmail = $helper->getUser ( $email );
			
			if ( $existingUserEmail ) {
				error_log ( "Email " . $email . " already in use, cannot create" );
				$result["errors"][] = array("error"=>"Email ".$email." already exists - cannot create");
				
			}
			else if ( \JUserHelper::getUserId($username) ) {
				error_log ( "username " . $username . " already in use, cannot create" );
				$result["errors"][] = array("error"=>"Username ".$username." already exists - cannot create");
			}
			else {
				
				$besGroupId = null;
				if ( $roleId == self::TEACHER_ROLE ) {
					$besGroupId = $helper->getUserGroupId ( "School Teacher" );
				}
				else if ( $roleId == self::STUDENT_ROLE ) {
					$besGroupId = $helper->getUserGroupId ( "School Student" );
				}
				
				if ( !$besGroupId ) {
					error_log ( "Cannot get group id for role id " . $roleId );
					$result["errors"][] = array("error"=>"Cannot get group id for role id " . $roleId);
				}
				else {
			
					$profileMW = array( 
						'tos'=>0,
						'wherehear'=>"British Ecological Society",	
						'subscribe'=>0
						);
					
					// Add to Registered group
					$groups = array("2"=>"2");
					
					
					$data = array(
					'name'=>$name,
					'username'=>$username,
					'password'=>$password,
					'email'=>$email,
					'sendEmail'=>0,
					'block'=>0,
					'profileMW'=>$profileMW,
					'groups'=>$groups,
					);
					
					$user = new \JUser;
					
					$userCreated = false;

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
							
							$userCreated = true;
						}
						
					}
					catch(Exception $e){
						error_log($e->getMessage());
						$result["errors"][] = array("error"=>"Username ".$username." - problem creating user");
					}
					
					if ( $userCreated ) {
						
						if ( $besGroupId > 0 ) {
							\JUserHelper::addUserToGroup ( $user->id, $besGroupId );
						}
						
						$db = \JDatabaseDriver::getInstance(dbOptions());
						
						// Link to school project
						$fields = new \StdClass();
						$fields->person_id = $user->id;
						$fields->project_id = $schoolUser->project_id;
						$fields->role_id = 2;
						
						$success = $db->insertObject("ProjectUserMap", $fields);
						if(!$success){
							error_log ( "ProjectUserMap insert failed" );
						}	
		
						
						// Link to school in BES
						$fields = new \StdClass();
						$fields->person_id = $user->id;
						$fields->school_id = $schoolUser->school_id;
						$fields->class_id = $classId;
						$fields->role_id = $roleId;
						
						$success = $db->insertObject("SchoolUsers", $fields);
						if(!$success){
							error_log ( "SchoolUsers insert failed" );
						}	
						else {
							$result["user"][] = array("personId"=>$user->id);
														
						}
					}
					else {
						$result["errors"][] = array("error"=>"Username ".$username." - problem creating user");
					}
				}
			}	
		}
		
		return $result;
	}
	
	private static function addUserToSchool ( $schoolUser, $roleId, $userId, $schoolId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
			
			
			$helper = new \BiodivHelper();
			
				
			$besGroupId = null;
			if ( $roleId == self::TEACHER_ROLE ) {
				$besGroupId = $helper->getUserGroupId ( "School Teacher" );
			}
			else if ( $roleId == self::STUDENT_ROLE ) {
				$besGroupId = $helper->getUserGroupId ( "School Student" );
			}
				
			if ( !$besGroupId ) {
				error_log ( "Cannot get group id for role id " . $roleId );
				$result["errors"][] = array("error"=>"Cannot get group id for role id " . $roleId);
			}
			else {
				
				\JUserHelper::addUserToGroup ( $userId, $besGroupId );
							
				$db = \JDatabaseDriver::getInstance(dbOptions());
				
				// Link to school in BES
				$fields = new \StdClass();
				$fields->person_id = $userId;
				$fields->school_id = $schoolId;
				$fields->role_id = $roleId;
				
				$success = $db->insertObject("SchoolUsers", $fields);
				
				if(!$success){
					error_log ( "SchoolUsers insert failed" );
					$result["errors"][] = array("error"=>"SchoolUsers insert failed for user " . $userId);
				}	
			}	
		}
		error_log ("returning result from addUserToSchool");
		return $result;
	}
	
	public static function editTeacher ( $schoolUser, $teacherId, $teacherName, $isActive, $password = null ) {
		
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			if ( self::atMySchool ( $teacherId ) ) {
		
				$data = array(
					'name'=>$teacherName
					);
				
				if ( $password ) {
					error_log ( "Got password" );
					$data['password'] = $password;
					$data['password2'] = $password;
				}
				
				$user = null;
				
				try {
					$user = new \JUser ( $teacherId );
				} 
				catch ( Exception $e ) {
					error_log($e->getMessage());
					$result["errors"][] = array("error"=>$e->getMessage());
				}
				
				$userUpdated = false;

				try {
					
					if (!$user->bind($data)){
						error_log($user->getError());
						$result["errors"][] = array("error"=>$user->getError());
					}
					
					// if ( $password ) {
						// if ( !$user->setParam('password', $password ) ) {
							// error_log("User set password param returned false");
							// error_log($user->getError());
						// }
					// }
					
					if (!$user->save()) {
						error_log($user->getError());
						$result["errors"][] = array("error"=>$user->getError());
						
					}
					
					if ( !$user->getError() ) {
						$userUpdated = true;
					}
					
				}
				catch(Exception $e){
					error_log($e->getMessage());
					$result["errors"][] = array("error"=>$e->getMessage());
				}
				
				if ( $userUpdated ) {
					
					$options = dbOptions();
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
		
					$query = $db->getQuery(true);
							
					$fields = array(
						$db->quoteName('include_points') . ' = ' . $isActive
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('person_id') . ' = ' . $teacherId
					);

					$query->update('SchoolUsers')->set($fields)->where($conditions);
					
					$db->setQuery($query);
					$updateResult = $db->execute();
					
					if ( !$updateResult ) {
						$result["errors"][] = array("failed to update db for school user");
					}
					
				}
				else {
					$result["errors"][] = array("failed to update user");
				}
			}
			else {
				$result["errors"][] = array("schools do not match");
			}
		}
		return $result;
	}

	public static function editSchool ( $schoolUser, $schoolId, $schoolName ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) && ($schoolUser->school_id == $schoolId) ) {
			
			$options = dbOptions();
			
			$db = \JDatabaseDriver::getInstance(dbOptions());

			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName('name') . ' = ' . $db->quote($schoolName)
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('school_id') . ' = ' . $schoolId
			);
			
			$query->update('School')->set($fields)->where($conditions);
			
			$db->setQuery($query);
			
			$updateResult = $db->execute();
			
			if ( !$updateResult ) {
				$result["errors"][] = array("error"=>"failed to update class");
			}
		}
		else {
			$result["errors"][] = array("error"=>"no access to update school");
		}
		return $result;
	}

	public static function editClass ( $schoolUser, $classId, $className, $avatar, $isActive ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			if ( self::checkMyClass ( $schoolUser, $classId ) ) {
				
				$options = dbOptions();
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
	
				$query = $db->getQuery(true);
						
				$fields = array(
					$db->quoteName('name') . ' = ' . $db->quote($className),
					$db->quoteName('avatar') . ' = ' . $avatar,
					$db->quoteName('is_active') . ' = ' . $isActive
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('class_id') . ' = ' . $classId
				);
				
				$query->update('SchoolClass')->set($fields)->where($conditions);
				
				$db->setQuery($query);
				
				$updateResult = $db->execute();
				
				if ( !$updateResult ) {
					$result["errors"][] = array("error"=>"failed to update class");
				}
				
			}
			else {
				$result["errors"][] = array("error"=>"no access to update class");
			}
		}
		else {
			$result["errors"][] = array("error"=>"no access to update class");
		}
		return $result;
	}


	public static function editStudent ( $schoolUser, $studentId, $studentName, $studentClass, $includePoints, $password = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$result = array();
		$result["errors"] = array();
		
		$personId = userID();
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
		
			if ( self::isMyStudent ( $studentId ) ) {
		
				$data = array(
					'name'=>$studentName
					);
				
				if ( $password ) {
					$data['password'] = $password;
				}
				
				$user = null;
				
				try {
					$user = new \JUser ( $studentId );
				} 
				catch ( Exception $e ) {
					error_log($e->getMessage());
					$result["errors"][] = array("error"=>$e->getMessage());
				}
				
				$userUpdated = false;

				try{
					
					if (!$user->bind($data)){
						error_log("User bind returned false");
						error_log($user->getError());
						$result["errors"][] = array("error"=>$user->getError());
					}
					
					if (!$user->save()) {
						error_log("User save returned false");
						error_log($user->getError());
						$result["errors"][] = array("error"=>$user->getError());
					}
					
					if ( !$user->getError() ) {
						
						$userUpdated = true;
					}
					
				}
				catch(Exception $e){
					error_log($e->getMessage());
					$result["errors"][] = array("error"=>$e->getMessage());
				}
				if ( $userUpdated ) {
					
					$options = dbOptions();
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
		
					$query = $db->getQuery(true);
							
					$fields = array(
						$db->quoteName('include_points') . ' = ' . $includePoints,
						$db->quoteName('class_id') . ' = ' . $studentClass
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('person_id') . ' = ' . $studentId
					);

					$query->update('SchoolUsers')->set($fields)->where($conditions);
					
					$db->setQuery($query);
					$updateResult = $db->execute();
					
					if ( !$updateResult ) {
						$result["errors"][] = array("error"=>"failed to update user in db");
					}
				}
				else {
					$result["errors"][] = array("error"=>"failed to update user");
				}
			}
			else {
				$result["errors"][] = array("error"=>"no access to update user");
			}
		}
		return $result;
	}
	
	
	public static function addSignup ( $personId, $personName, $schoolName, $postcode, $website, $terms ) {
		
		$result = array();
		$result["errors"] = array();
		
		$user = \JFactory::getUser($personId);
		
		if ( $user ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$fields = new \StdClass();
			$fields->person_id = $personId;
			$fields->person_name = $personName;
			$fields->school_name = $schoolName;
			$fields->postcode = $postcode;
			$fields->website = $website;
			$fields->terms_agreed = $terms;
			
			$success = $db->insertObject("SchoolSignup", $fields);
			if(!$success){
				error_log ( "SchoolSignup insert failed" );
				$result["errors"][] = array("error"=>"School ".$schoolName.", teacher " . $name . " - problem creating");
			}	
		}
		
		error_log ( "addSignup returning" );
		return $result;
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
			->select("B.lock_level, count(*) as numBadges from StudentBadges SB")
			->innerJoin("Badge B on B.badge_id = SB.badge_id")
			->where("SB.status >= " . Badge::COMPLETE )
			->group("B.lock_level");	
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$studentBadges = $db->loadAssocList("lock_level", "numBadges");
		
		
		$query = $db->getQuery(true)
			->select("B.lock_level, count(*) as numBadges from TeacherBadges TB")
			->innerJoin("Badge B on B.badge_id = TB.badge_id")
			->where("TB.status >= " . Badge::COMPLETE )
			->group("B.lock_level");	
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$teacherBadges = $db->loadAssocList("lock_level", "numBadges");
		
		
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
	
	
	public static function getUnapprovedSchools( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$unapproved = array();
		
		if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("* from SchoolSignup where approved = 0");	
			
			$db->setQuery($query);
			
			//error_log("Set id select query created: " . $query->dump());
			
			$unapproved = $db->loadObjectList( "signup_id" );
		}
		
		return $unapproved;
	}
	
	
	public static function approveSchool ( $schoolUser, $signupId, $comment ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
			$db = \JDatabaseDriver::getInstance(dbOptions());
			$query = $db->getQuery(true);
				
			$fields = array(
				$db->quoteName('approved') . ' = 1',
				$db->quoteName('comment') . ' = ' . $db->quote($comment),
				$db->quoteName('approved_by') . ' = ' . $schoolUser->person_id,
				$db->quoteName('approve_timestamp') . ' = CURRENT_TIMESTAMP'
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('signup_id') . ' = ' . $signupId
			);
			
			$query->update('SchoolSignup')->set($fields)->where($conditions);
			
			$db->setQuery($query);
			$result = $db->execute();
			
			if ( $result ) {
				self::createSchool ( $schoolUser, $signupId );
			}
		}
	}
	
	
	public static function rejectSchool ( $schoolUser, $signupId, $comment ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			$query = $db->getQuery(true);
				
			$fields = array(
				$db->quoteName('approved') . ' = -1',
				$db->quoteName('comment') . ' = ' . $db->quote($comment),
				$db->quoteName('approved_by') . ' = ' . $schoolUser->person_id,
				$db->quoteName('approve_timestamp') . ' = CURRENT_TIMESTAMP'
			);
			
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('signup_id') . ' = ' . $signupId
			);

			$query->update('SchoolSignup')->set($fields)->where($conditions);
			
			$db->setQuery($query);
			$result = $db->execute();	
			
		}
	}
	
	
	public static function createSchool ( $schoolUser, $signupId ) {
		
		error_log ( "createSchool called" );
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
			
			error_log ( "createSchool got admin role" );
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("* from SchoolSignup where signup_id = " . $signupId);	
			
			$db->setQuery($query);
			
			$newSchoolSignup = $db->loadObject();
			
			
			$projectName = str_replace(' ', '_', $newSchoolSignup->school_name);
			$prettyName = $newSchoolSignup->school_name;
			$projectDescription = \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_BES_PROJECT");
			
			
			$projectSettings = getSetting ( "school_project" );
			$projectSettingsObj = json_decode ( $projectSettings );
			
			$parentProject = null;
			$accessLevel  = 1;
			$imageDir = null;
			$imageFile = null;
			$articleId = 0;
			$listingLevel = 1000;
			$priority = 0;
			$displayOptions = array();
			$speciesLists = array();
			
			if (property_exists($projectSettingsObj, 'parent_project')) {
				$parentProject = $projectSettingsObj->parent_project;
			}
			if (property_exists($projectSettingsObj, 'access_level')) {
				$accessLevel = $projectSettingsObj->access_level;
			}
			if (property_exists($projectSettingsObj, 'image_dir')) {
				$imageDir = $projectSettingsObj->image_dir;
			}
			if (property_exists($projectSettingsObj, 'image_file')) {
				$imageFile = $projectSettingsObj->image_file;
			}
			if (property_exists($projectSettingsObj, 'article_id')) {
				$articleId = $projectSettingsObj->article_id;
			}
			if (property_exists($projectSettingsObj, 'listing_level')) {
				$listingLevel = $projectSettingsObj->listing_level;
			}
			if (property_exists($projectSettingsObj, 'priority')) {
				$priority = $projectSettingsObj->priority;
			}
			if (property_exists($projectSettingsObj, 'display_options')) {
				$displayOptions = $projectSettingsObj->display_options;
			}
			if (property_exists($projectSettingsObj, 'species_lists')) {
				$speciesLists = $projectSettingsObj->species_lists;
			}
			
			$projectAdmins = array($newSchoolSignup->person_id);
			$isSchoolProject = 2;
			$existingSchoolId = 0;
			$newSchoolName = $newSchoolSignup->school_name;
			
			error_log ( "createSchool creating project" );
			
			$projectAndSchool = createProject( $projectName, $prettyName, $projectDescription, $accessLevel, $parentProject,
						$imageDir, $imageFile, $articleId, $listingLevel, $priority, $displayOptions,
						$speciesLists, $projectAdmins, $isSchoolProject, $existingSchoolId, $newSchoolName );
			
			error_log ( "createSchool project created" );
			
			$newSchoolId = $projectAndSchool->schoolFields->school_id;
			$newProjectId = $projectAndSchool->projectFields->project_id;
			
			$addResult = null;
			
			if ( $newSchoolId ) {
				
				$addResult = self::addUserToSchool ( $schoolUser, self::TEACHER_ROLE, $newSchoolSignup->person_id, $newSchoolId );
			}
			
			if ( $addResult && count($addResult["errors"]) == 0 ) {
				
				$kioskSettings = getSetting ( "school_kiosk" );
				$kioskSettingsObj = json_decode ( $kioskSettings );
			
				$kiosk = self::createSchoolKiosk ( $newProjectId, $kioskSettingsObj );
			}
			if ( $kiosk ) {
				
				$subject = \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_READY_SUBJECT");
				$msg = \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_READY_MESSAGE") . ' <a href="'.\JURI::root().'/bes-school-dashboard">'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_READY_LINK_TEXT").'</a>';
				
				self::notifyUser ( $newSchoolSignup->person_id, $subject, $msg );
				
			}
		}
	}
	
	
	private static function createSchoolKiosk ( $projectId, $kioskSettingsObj ) {
		
		$schoolUser = self::getSchoolUser();	
		
		if ( $schoolUser->role_id == self::ADMIN_ROLE ) {
			
			$db = \JDatabase::getInstance(dbOptions());	
			
			// Check there's no existing kiosk
			$query = $db->getQuery(true);
			
			$query->select("O.option_id")
				->from("ProjectOptions PO")
				->innerJoin("Options O on O.option_id = PO.option_id")
				->where("PO.project_id = " . $projectId )
				->where("O.struc = " . $db->quote("kiosk") );
			$db->setQuery($query);
			
			$existingKiosks = $db->loadColumn();
			
			if ( count($existingKiosks) > 0 ) {
				return $existingKiosks[0];
			}
			
			$errMsg = print_r ( $kioskSettingsObj, true );
			error_log ("kiosk settings = " . $errMsg );
			
			if (property_exists($kioskSettingsObj, 'kiosk')) {
				$kioskName = $kioskSettingsObj->kiosk;
			}
			
			
			$kioskOption = new \StdClass();
			$kioskOption->struc = "kiosk";
			$kioskOption->option_name = $kioskName . "?project_id=" . $projectId;
			$kioskOption->seq = 1;
			$kioskOption->article_id = 0;
			
			$success = $db->insertObject("Options", $kioskOption, 'option_id');
			if(!$success){
				error_log ( "kioskOption kiosk option failed" );
				return null;
			}
			
			$kioskOptionId = $kioskOption->option_id;
			
			
			if (property_exists($kioskSettingsObj, 'topics')) {
				$topics = $kioskSettingsObj->topics;
			}
			if (property_exists($kioskSettingsObj, 'species_lists')) {
				$speciesLists = $kioskSettingsObj->species_lists;
			}
			if (property_exists($kioskSettingsObj, 'tutorial')) {
				$tutorial = $kioskSettingsObj->tutorial;
			}
			if (property_exists($kioskSettingsObj, 'optiondata')) {
				$optionData = $kioskSettingsObj->optiondata;
			}
		
			$tutorialProjectOptions = array($tutorial);
			$kioskProjectOptions = array_merge_recursive($tutorialProjectOptions, $topics, $speciesLists);
			
			$errMsg = print_r ( $kioskProjectOptions, true );
			error_log ("kiosk project options = " . $errMsg );
			
			
			$projectOptionFields = new \StdClass();
			$projectOptionFields->project_id = $projectId;
			$projectOptionFields->option_id = $kioskOptionId;
			
			$success = $db->insertObject("ProjectOptions", $projectOptionFields);
			if(!$success){
				error_log ( "ProjectOptions kiosk option failed" );
			}
			

			// Set project options
			foreach ( $kioskProjectOptions as $optionId ) {
				$projectOptionFields = new \StdClass();
				$projectOptionFields->project_id = $projectId;
				$projectOptionFields->option_id = $optionId;
				
				$success = $db->insertObject("ProjectOptions", $projectOptionFields);
				if(!$success){
					error_log ( "ProjectOptions kiosk option failed" );
				}
			}
			
			// Add OptionData
			foreach ($optionData as $key => $value) {
				
				$optionDataFields = new \StdClass();
				$optionDataFields->option_id = $projectId;
				$optionDataFields->option_id = $kioskOptionId;
				$optionDataFields->data_type = $key;
				$optionDataFields->value = $value;
				
				$success = $db->insertObject("OptionData", $optionDataFields);
				if(!$success){
					error_log ( "OptionData kiosk option failed" );
				}
			}
			
			error_log ( "createSchoolKiosk returning kioskoptionId: " . $kioskOptionId );
		
			return $kioskOptionId;
		}
		return null;
	}

	
	public static function getSetupComplete ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("school_setup, teacher_setup, class_setup, student_setup from School" )
			->where("school_id = " . $schoolUser->school_id );
			
		
		$db->setQuery($query);
		
		error_log("Set id select query created: " . $query->dump());
		
		$setup = $db->loadObject();
		
		return ($setup->school_setup &&  $setup->teacher_setup &&  $setup->class_setup &&  $setup->student_setup);
	}
	
	
	public static function schoolSetupComplete ( $schoolUser = null ) {
		
		return self::updateSchoolSetupComplete ( $schoolUser, 'school_setup' );
	}
	
	
	public static function teacherSetupComplete ( $schoolUser = null ) {
		
		return self::updateSchoolSetupComplete ( $schoolUser, 'teacher_setup' );
	}
	
	
	public static function classSetupComplete ( $schoolUser = null ) {
		
		return self::updateSchoolSetupComplete ( $schoolUser, 'class_setup' );
	}
	
	
	public static function studentSetupComplete ( $schoolUser = null ) {
		
		return self::updateSchoolSetupComplete ( $schoolUser, 'student_setup' );
	}
	
	
	private static function updateSchoolSetupComplete ( $schoolUser, $column ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$personId = userID();
		
		$result = null;
		
		if ( ($personId == $schoolUser->person_id) && ($schoolUser->role_id == self::TEACHER_ROLE) ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName($column) . ' = 1' 
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('school_id') . ' = ' . $schoolUser->school_id
			);

			$query->update('School')->set($fields)->where($conditions);
			
			error_log("updateSchoolSetupComplete select query created: " . $query->dump());
			
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		return $result;
	}
	
	
	
	public static function countProjectUnclassified ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$personId = userID();
		
		$result = null;
		
		$numUnclassified = 0;
		
		if ( $personId == $schoolUser->person_id ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true)
				->select("count(*) from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id and PSM.project_id = " . $schoolUser->project_id)
				->where("P.photo_id >= PSM.start_photo_id and ((P.photo_id <= PSM.end_photo_id) or (PSM.end_photo_id is NULL))")
				->where("P.sequence_num = 1")
				->where("P.photo_id not in (select photo_id from Animal where person_id = " . $schoolUser->person_id . ")");
				
			
			$db->setQuery($query);
			
			error_log("countProjectUnclassified select query created: " . $query->dump());
			
			$numUnclassified = $db->loadResult();
		}
		
		error_log ( "Got " . $numUnclassified . " unclassified sequences" );
		return $numUnclassified;
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
	
	
	
	public static function getClasses ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		
		if ( !$schoolUser ) {
			return null;
		}
		
		$schoolId = $schoolUser->school_id;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SC.*, A.image from SchoolClass SC")
			->innerJoin("Avatar A on SC.avatar = avatar_id")
			->where("SC.school_id = " . $schoolId)
			->order("SC.is_active DESC, SC.timestamp DESC");
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$classes = $db->loadObjectList("class_id");
		
		return $classes;
	}
	
	public static function getClassDetails ( $schoolUser, $classId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( !$schoolUser ) {
			return null;
		}
		
		$schoolId = $schoolUser->school_id;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SC.*, A.image from SchoolClass SC")
			->innerJoin("Avatar A on A.avatar_id = SC.avatar")
			->where("SC.class_id = " . $classId);
			
		
		$db->setQuery($query);
		
		//error_log("getClassDetails select query created: " . $query->dump());
		
		$classDetails = $db->loadObject();
		
		return $classDetails;
	}
	
	
	public static function getCertificateData ( $schoolUser, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( $classId ) {
			$classDetails = self::getClassDetails ( $schoolUser, $classId );
			$certificateName = $classDetails->name;
		}
		else {
			$certificateName = $schoolUser->name;
		}
		$certificateDate = date("jS F Y");
		
		return (object)array("name"=>$certificateName, "date"=>$certificateDate, "school"=>$schoolUser->school);
	}
	
	
	public static function getClassStatus ( $schoolUser, $schoolId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( !$schoolUser ) {
			return null;
		}
		
		if ( ($schoolUser->role_id == self::ADMIN_ROLE) or ($schoolId == $schoolUser->school_id) ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());

			$query = $db->getQuery(true)
				->select("SC.*, A.image as avatar, " .
						"count(TB.tb_id) as num_badges from SchoolClass SC" )
				->leftJoin("TeacherAwards TA on SC.class_id = TA.class_id and TA.collected = 1")
				->innerJoin("Avatar A on A.avatar_id = SC.avatar")
				->leftJoin("TeacherBadges TB on TB.class_id = SC.class_id and TB.status = " . Badge::COLLECTED )
				->where("SC.school_id = " . $schoolId)
				->where("SC.is_active = 1")
				->group("SC.class_id");
			
			$db->setQuery($query);
			
			error_log("getClassStatus select query created: " . $query->dump());
			
			$classStatus = $db->loadObjectList("class_id");
			
			
			foreach ( array_keys($classStatus) as $classId ) {
				
				$awardsPlusBlanks = Award::getAwardsPlusBlanks ( $schoolUser, $classId );
				$classStatus[$classId]->awards = $awardsPlusBlanks;
			}
			return $classStatus;
			
		}
		else {
			return null;
		}
	}
	
	
	public static function getSchoolStatus ( $schoolUser, $schoolId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( !$schoolUser ) {
			return null;
		}
		
		if ( ($schoolUser->role_id == self::ADMIN_ROLE) or ($schoolId == $schoolUser->school_id) ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());

			$query = $db->getQuery(true)
				->select("count(TB.tb_id) as class_badges from TeacherBadges TB" )
				->innerJoin("SchoolUsers SU on TB.person_id = SU.person_id and SU.school_id = ".$schoolId)
				->where("TB.status = " . Badge::COLLECTED);
			
			$db->setQuery($query);
			
			error_log("getSchoolStatus select query created: " . $query->dump());
			
			$schoolStatus = $db->loadObject();
			
			
			$query = $db->getQuery(true)
				->select("count(SB.sb_id) as student_badges from StudentBadges SB" )
				->innerJoin("SchoolUsers SU on SB.person_id = SU.person_id and SU.school_id = ".$schoolId)
				->where("SB.status = " . Badge::COLLECTED);
			
			$db->setQuery($query);
			
			error_log("getSchoolStatus select query created: " . $query->dump());
			
			$numStudentBadges = $db->loadResult();
			
			$schoolStatus->student_badges = $numStudentBadges;
			
			$awards = Award::getSchoolAwards($schoolUser);
			
			$schoolStatus->class_awards = array();
			$schoolStatus->class_awards[1] = 0;
			$schoolStatus->class_awards[2] = 0;
			$schoolStatus->class_awards[3] = 0;
			foreach ( $awards->classAwards as $award ) {
				$schoolStatus->class_awards[$award->level] += 1;
			}
			
			$schoolStatus->student_awards = array();
			$schoolStatus->student_awards[1] = 0;
			$schoolStatus->student_awards[2] = 0;
			$schoolStatus->student_awards[3] = 0;
			foreach ( $awards->studentAwards as $award ) {
				$schoolStatus->student_awards[$award->level] += 1;
			}	
			
			return $schoolStatus;
			
		}
		else {
			return null;
		}
	}
	
	
	public static function getStudentStatus ( $schoolUser, $schoolId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		if ( !$schoolUser ) {
			return null;
		}
		
		if ( ($schoolUser->role_id == self::ADMIN_ROLE) or ($schoolId == $schoolUser->school_id) ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$query = $db->getQuery(true)
				->select("SU.*, A.image as avatar, U.username as name, " .
						"count(SB.sb_id) as num_badges, ".
						"IF(ISNULL(SA.sa_id),0,1) as level1, ".
						"IF(ISNULL(SA2.sa_id),0,1) as level2, ".
						"IF(ISNULL(SA3.sa_id),0,1) as level3 from SchoolUsers SU" )
				->leftJoin("StudentBadges SB on SB.person_id = SU.person_id and SB.status = " . Badge::COLLECTED )
				->leftJoin("StudentAwards SA on SA.person_id = SU.person_id and SA.level = 1 and SA.collected = 1")
				->leftJoin("StudentAwards SA2 on SA2.person_id = SU.person_id and SA2.level = 2 and SA.collected = 1")
				->leftJoin("StudentAwards SA3 on SA3.person_id = SU.person_id and SA3.level = 3 and SA.collected = 1")
				->innerJoin("Avatar A on A.avatar_id = SU.avatar")
				->innerJoin($userDb . "." . $prefix ."users U on U.id = SU.person_id" )
				->where("SU.school_id = " . $schoolId )
				->where("SU.role_id = " . self::STUDENT_ROLE)
				->group("SU.person_id")
				->order("num_badges DESC");
			
			$db->setQuery($query);
			
			//error_log("getStudentStatus select query created: " . $query->dump());
			
			$studentStatus = $db->loadObjectList("person_id");
			
			return $studentStatus;
			
		}
		else {
			return null;
		}
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
	
	
	public static function generateNonUserHeader ( $helpOptionId = 0 ) {
		
		$personId = userID();
		
		if ( $personId ) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			print '<div class="row studentMastheadRow">';
			
			print '<div class="col-md-2 col-sm-3 col-xs-4" >';
			
			print '<img src="'.$logoPath.'" class="img-responsive brandLogo" />';
			
			print '</div>'; // col-2
			
			print '<div class="col-md-2 col-md-offset-8 col-sm-3 col-sm-offset-6 col-xs-4 col-xs-offset-4 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h4" data-toggle="modal" data-target="#helpModal">';
				print ' <i class="fa fa-info"></i> ';
				print '</div>';
			}
			
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'" class="btn btn-success" >';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			print '</a>';
			
			print '</div>'; // col-2
			
			print '</div>';
		}
		else {
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			print '<div class="row studentMastheadRow">';
			
			print '<div class="col-md-2 col-sm-3 col-xs-4" >';
			
			print '<img src="'.$logoPath.'" class="img-responsive brandLogo" />';
			
			print '</div>'; // col-2
			
			print '<div class="col-md-2 col-md-offset-8 col-sm-3 col-sm-offset-6 col-xs-4 col-xs-offset-4 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h4" data-toggle="modal" data-target="#helpModal">';
				print ' <i class="fa fa-info"></i> ';
				print '</div>';
			}
			
			print '</div>'; // col-2
			
			print '</div>';
		}
	}
	
	public static function generateNav ( $schoolUser = null, $classId = null, $activeItem = null, $helpOptionId = 0 ) {
		
		if ( !$schoolUser ) {
			$schoolUser = self::getSchoolUser();
		}
		
		$totalPointsByModule = 0;
		
		if ( $schoolUser) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			$roleId = $schoolUser->role_id;
			
			print '<nav class="navbar navbar-default">';
			//print '<div class="container-fluid staffNav">';
			print '<div class="staffNav">';
			
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
			
			
			
			print '<div class="col-lg-6 col-lg-offset-1 col-md-8 col-sm-7 col-xs-12 text-center" >';
			
			print '<table class="table statusBar" >';
			
			print '<tbody>';
			print '<tr>';
			

			if ( $classId ) {
				
				$classDetail = self::getClassDetails ( $schoolUser, $classId );
				
				print '<td class="statusBarNarrowElement" ><img src="'.$classDetail->image.'" class="img-responsive avatar statusBarIcon" /></td>';
				
				print '<td class="text-center statusBarUsername hidden-xs" >'.$classDetail->name.'</td>';
				
			}
			else {
				
				print '<td class="statusBarNarrowElement" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar statusBarIcon" /></td>';
			
				print '<td class="text-center statusBarUsername hidden-xs" >'.$schoolUser->username.'</td>';
				
			}
			
			if ( $roleId == self::ADMIN_ROLE )
				{
				print '<td class="statusBarElement statusBarElement statusBarLeft statusBarRight text-center"> ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_ADMIN_USER") . ' </td>';
			}
			else if ( ($roleId == self::TEACHER_ROLE) and !$classId ) {
				print '<td class="statusBarElement statusBarElement statusBarLeft statusBarRight text-center"> ' . \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TEACHER_USER") . ' </td>';
			}
			else {
				
				$numBadges = Badge::countComplete($schoolUser, $classId);
				$badgePath = $settingsObj->badge_icon;
				print '<td class="statusBarElement statusBarNarrowElement statusBarLeft"><img class="img-responsive statusBarIcon" src="'.$badgePath.'" > </td>';
				print '<td class="statusBarElement statusBarCount text-left">' . $numBadges . ' </td>';
				
				$awards = Award::getAwardsPlusBlanks($schoolUser, $classId);
				$numAwards = count($awards);
				$i = 1;
				foreach ( $awards as $award ) {
					if ( $i == $numAwards ) {
						print '<td class="statusBarElement statusBarNarrowElement statusBarRight"><img class="img-responsive statusBarIcon" src="'.$award->getDisplayImage().'" ></td>';
					}
					else {
						print '<td class="statusBarElement statusBarNarrowElement "><img class="img-responsive statusBarIcon" src="'.$award->getDisplayImage().'" ></td>';
					}
					$i += 1;
				}  
			}
			
			print '</tr>';
			print '</tbody>';
			
			print '</table>'; 
			
			print '</div>'; // col-8
			
			print '<div class="col-lg-2 col-lg-offset-1 col-md-2 col-sm-2 col-xs-2 text-right">';
			
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
			
			
			// ------------------------------------------ school page
			if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::STUDENT_ROLE) or ($roleId == self::ADMIN_ROLE) ) {
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
			
			if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::STUDENT_ROLE) ) {
				$activeClass = "";
				if ( $activeItem == "badges" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_BADGES_LINK").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_BADGES_PAGE");
				print '</a>';
				print '</li>';
			}
			
			// if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::ECOLOGIST_ROLE)) {
				
				// $activeClass = "";
				// if ( $activeItem == "managetasks" ) {
					// $activeClass = "active";
				// }
				// print '<li class="besNavbarItem '.$activeClass.'">';
				// print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_TASKS_LINK").'" class="manageTasks">';
				// print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MANAGE_TASKS");
				// print '</a>';
				// print '</li>';
				
			// }
			
			
			// if ( $roleId == self::TEACHER_ROLE ) {
				
				// $numToApprove = Task::countMyStudentsTasks ( Badge::PENDING );
	
				// $activeClass = "";
				// if ( $activeItem == "students" ) {
					// $activeClass = "active";
				// }
				// print '<li class="besNavbarItem '.$activeClass.'">';
				// print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENTS_LINK").'" class="students">';
				// print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_STUDENTS");
				// if ( $numToApprove > 0 ) {
					// print ' <span id="studentsBadge" class="badge notifyBadge">'.$numToApprove.'</span>';
				// }
				// print '</a>';
				// print '</li>';
			
			
			// }
			
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
				if ( $activeItem == "teacherzone" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_EDU_ZONE_LINK").'">';
				print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_EDU_ZONE");
				print '</a>';
				print '</li>';
				
				
				// $activeClass = "";
				// if ( $activeItem == "resourcehub" ) {
					// $activeClass = "active";
				// }
				// print '<li class="besNavbarItem '.$activeClass.'">';
				// print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HUB_LINK").'">';
				// print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_RESOURCE_HUB");
				// print '</a>';
				// print '</li>';
			
			
				// $messageList = new MessageList();
				// $numNewMessages = $messageList->newMessageCount();
	
				// $activeClass = "";
				// if ( $activeItem == "messages" ) {
					// $activeClass = "active";
				// }
				// print '<li class="besNavbarItem '.$activeClass.'">';
				// print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MESSAGES_LINK").'">';
				// print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MESSAGES");
				// if ( $numNewMessages > 0 ) {
					// print ' <span id="messageBadge" class="badge notifyBadge">'.$numNewMessages.'</span>';
				// }
				// print '</a>';
				// print '</li>';
			
			}
			
			// ------------------------ help page
			
			$activeClass = "";
			if ( $activeItem == "help" ) {
				$activeClass = "active";
			}
			print '<li class="besNavbarItem '.$activeClass.'">';
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HELP_LINK").'">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HELP");
			print '</a>';
			print '</li>';
				
				
				
			print '<li class="besNavbarItem hidden-md hidden-lg">';
			print '<a href="'.\JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT_LINK").'">';
			print \JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGOUT");
			print '</a>';
			print '</li>';
			
			print '</ul>';
			
			
			//print '</div>';
			
			print '</div>'; // nav collapse
			
			print '</div>'; // container-fluid
			print '</nav>'; // navbar
			
			
			//print '</div>';
			
			print '<div id="navEnd"></div>';
		
		}
	}
	
}



?>

