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
	const ADMIN_ROLE		= 6;
	const SUPPORTER_ROLE	= 7;
	
	private $schools;
	
	function __construct( )
	{
		$db = \JDatabaseDriver::getInstance(dbOptions());


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
		
	}
	
	
	public function getSchools () {
		return $this->schools;
	}
	
	
	public static function getSchoolRoles () {
		
		//error_log ( "SchoolCommunity::getSchoolRoles called" );
		
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SU.school_id, S.name, SU.role_id, R.role_name from SchoolUsers SU")
			->innerJoin("School S on SU.school_id = S.school_id" )
			->innerJoin("Role R on SU.role_id = R.role_id")
			->where("person_id = " . $personId);
			
		
		$db->setQuery($query);
		
		//error_log("SchoolCommunity::getSchoolRoles select query created: " . $query->dump());
		
		$schoolRoles = $db->loadAssocList();
		
		return $schoolRoles;
		
				
	}
	
	public static function getSchoolName ( $schoolId ) {
		
		//error_log ( "SchoolCommunity::getSchoolName called" );
		
		
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
		
		//error_log ( "SchoolCommunity::getUserName called" );
		
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
	
	
	public static function getSchoolUsers ( $schoolId ) {
		
		//error_log ( "SchoolCommunity::getSchoolUsers called" );
		
		
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
	
	public static function checkSchoolAwards($schoolId, $schoolPoints) {
		
		$possibleAwards = Award::getSchoolTargetAwards( $schoolId );
		
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
		
		$possibleAwards = Award::getSchoolAwards( $schoolId );
		
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
				$schoolThreshold = $award->threshold_per_user * $numUsers;
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
	
	public static function isEcologist ( $userId = null ) {
		
		//error_log ( "SchoolCommunity::isEcologist called" );
		
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
		
		//error_log ( "SchoolCommunity::isAdmin called" );
		
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
		
		//error_log ( "SchoolCommunity::isStudent called" );
		
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
	
	public static function isTeacher ( $userId = null ) {
		
		//error_log ( "SchoolCommunity::isTeacher called" );
		
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
		
		//error_log ( "SchoolCommunity::isMyStudent called" );
		
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
		
		//error_log ( "SchoolCommunity::isNewUser called" );
		
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
		
		//error_log ( "SchoolCommunity::setNewUser called" );
		
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
		
		//error_log ( "SchoolCommunity::addNotification called" );
		
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
			->select("SU.school_id, SU.person_id, U.username, A.image from SchoolUsers SU")
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
			->select("SU.school_id, SU.person_id, U.username, U.name, A.image as avatar, B.badge_group, SUM(T.points) as num_points from SchoolUsers SU")
			->innerJoin("StudentTasks ST on ST.person_id = SU.person_id")
			->innerJoin("Task T on T.task_id = ST.task_id and ST.status > " . Badge::PENDING)
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id = " . self::TEACHER_ROLE )
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
			->where("SU.role_id = " . self::STUDENT_ROLE )
			->where("SU2.person_id = " . $personId)
			->group("SU.school_id, SU.person_id, U.username, U.name, A.image, B.badge_group");
			
		
		$db->setQuery($query);
		
		//error_log("getMyStudentsProgress select query created: " . $query->dump());
		
		$students = $db->loadObjectList();
		
		$studentProgress = array();
		
		foreach ( $students as $student ) {
			if ( array_key_exists ( $student->person_id, $studentProgress ) ) {
				
				$st = $studentProgress[$student->person_id];
				$st->progress[$student->badge_group] = $student->num_points;
				$st->totalPoints += $student->num_points;
			}
			else {
				
				$st = new \StdClass();
				$st->personId = $student->person_id;
				$st->username = $student->username;
				$st->name = $student->name;
				$st->avatar = $student->avatar;
				
				$st->progress = array();
				$st->progress[$student->badge_group] = $student->num_points;
				$st->totalPoints = $student->num_points;
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
		
		$query1 = $db->getQuery(true)
			->select("SU.school_id, SU.person_id, U.username, U.name, A.image from SchoolUsers SU")
			->innerJoin( "SchoolUsers SU2 on SU2.school_id = SU.school_id and SU2.person_id = " . $personId . " and SU2.role_id in (" . 
					self::TEACHER_ROLE . ", " . self::ECOLOGIST_ROLE . ")" )
			->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
			->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
			->where("SU.role_id in (" . self::TEACHER_ROLE . ", " . self::ECOLOGIST_ROLE . ")" );
		
		$db->setQuery($query1);
		
		/*
		if ( self::isEcologist() ) {
			$query2 = $db->getQuery(true)
				->select("SU.school_id, SU.person_id, U.username, U.name, A.image from SchoolUsers SU")
				->innerJoin( "Avatar A on A.avatar_id = SU.avatar" )
				->innerJoin($userDb . "." . $prefix ."users U on SU.person_id = U.id")
				->where("SU.role_id = " . self::ECOLOGIST_ROLE );
			
			$db->setQuery ( $query1->union($query2) );
			//error_log("getAdults select query created: " . $query->dump());
		}
		else {
			$db->setQuery($query1);
			//error_log("getAdults select query created: " . $query1->dump());
		}
		*/
		
		
		$adults = $db->loadObjectList("person_id");
		
		return $adults;
		
				
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
		
		$translations = getTranslations("schoolcommunity");
		
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
			$htmlStr .=  $points . ' ' . $translations['points']['translation_text'];
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
			$htmlStr .= '<p><small>' . $points . ' ' . $translations['points_contrib']['translation_text'] . '</small></p>';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '</div>'; // row
			
			$htmlStr .=  '</div>'; // col-7
			
			// $htmlStr .= '<div class="col-md-5 col-sm-2 col-xs-6 text-center">';
			// $htmlStr .= '<a href="'.$translations['logout_link']['translation_text'].'">';
			// $htmlStr .= '<div class="dashboardBox logoutBox ">';
			// $htmlStr .= $translations['logout']['translation_text'];
			// $htmlStr .= '</div>'; // logoutBox
			// $htmlStr .= '</a>';
			
			// $htmlStr .= '</div>'; // col-5
			
			$htmlStr .= '</div>'; // row
			
			$htmlStr .= '</div>'; // avatarBox
			
		}
		else if ( $boxName == "schoolPageBox" ) {
			
			$htmlStr = '<a href="'.$translations['school_link']['translation_text'].'">';
			$htmlStr .= '<div class="dashboardBox schoolPageBox ' . $selectedBoxClass . '">';	
			$htmlStr .= '<div class="schoolPage h5">';
			$htmlStr .= $translations['school_page']['translation_text'];
			$htmlStr .= '</div>'; // schoolPage
			$htmlStr .= '</div>'; // schoolPageBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "logoutBox" ) {
			
			$htmlStr = '<a href="'.$translations['logout_link']['translation_text'].'">';
			$htmlStr .= '<div class="dashboardBox logoutBox ">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= $translations['logout']['translation_text'];
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // logoutBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "resourceHubBox" ) {
			
			$htmlStr = '<a href="'.$translations['hub_link']['translation_text'].'">';
			$htmlStr .= '<div class="dashboardBox resourceHubBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="resourceHub h5">';
			$htmlStr .= $translations['resource_hub']['translation_text'];
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // resourceHubBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "communityBox" ) {
			
			$htmlStr = '<a href="'.$translations['community_link']['translation_text'].'">';
			$htmlStr .= '<div class="dashboardBox communityBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= $translations['community']['translation_text'];
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // communityBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "teacherDashBox" ) {
			
			$htmlStr = '<a href="'.$translations['teacher_dash']['translation_text'].'">';
			$htmlStr .= '<div class="dashboardBox teacherDashBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= $translations['teacher_page']['translation_text'];
			$htmlStr .= '</div>'; // h5
			$htmlStr .= '</div>'; // teacherDashBox
			$htmlStr .= '</a>';
			
		}
		else if ( $boxName == "studentDashBox" ) {
			
			$htmlStr = '<a href="'.$translations['student_dash']['translation_text'].'">';
			$htmlStr .= '<div class="dashboardBox studentDashBox ' . $selectedBoxClass . '">';
			$htmlStr .= '<div class="h5">';
			$htmlStr .= $translations['student_page']['translation_text'];
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
			
			//$htmlStr .= '<div class="schoolName">'.$school.'</div>';
			
			
			
			//$htmlStr = '<div class="avatarBox">';
			
			// $htmlStr .= '<div class="row">';
			// $htmlStr .= '<div class="col-md-12 col-xs-6 text-center">';
			// $htmlStr .= '<div class="schoolName">'.$school.'</div>';
			// $htmlStr .= '</div>'; // col-12
			// $htmlStr .= '</div>'; // row
			
			//$htmlStr .= '<div class="row">';
			
			//$htmlStr .= '<div class="col-md-5 col-md-offset-col-sm-2 col-xs-6 text-center">';
			
			
			$htmlStr .= '<div class="row">';
			$htmlStr .= '<div class="col-md-12">';
			$htmlStr .= '<img src="'.$avatar.'" class="img-responsive menuAvatar" />';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '<div class="col-md-12">';
			$htmlStr .=  '<strong>' . $username . '</strong>';
			$htmlStr .=  ' <small>' . $points . ' ' . $translations['points_contrib']['translation_text'] . '</small>';
			$htmlStr .= '</div>'; // col-12
			$htmlStr .= '</div>'; // row
			
			/*
			$htmlStr .= '<div style="display:inline">';
			$htmlStr .= '<img src="'.$avatar.'" class="img-responsive menuAvatar" />';
			$htmlStr .=  $username;
			$htmlStr .=   ' <span class="badge">'.$points.'</span>';
			$htmlStr .= '</div>';
			*/
			
			
			
			//$htmlStr .=  '<div class="username">'.$username.'</div>';
			//$htmlStr .= '<p><small>' . $points . ' ' . $translations['points_contrib']['translation_text'] . '</small></p>';
			
			//$htmlStr .=  '</div>'; // col-7
			
			//$htmlStr .= '</div>'; // row
			
			//$htmlStr .= '</div>'; // avatarBox
			
			// $htmlStr = '<div class="avatarBox">';
			
			// $htmlStr .= '<div class="row">';
			// $htmlStr .= '<div class="col-md-12 col-xs-6 text-left">';
			// $htmlStr .= '<div class="schoolName">'.$school.'</div>';
			// $htmlStr .= '</div>'; // col-12
			// $htmlStr .= '</div>'; // row
			
			// $htmlStr .= '<div class="row">';
			
			// $htmlStr .= '<div class="col-md-5 col-sm-2 col-xs-6 text-center">';
			
			// $htmlStr .= '<div class="row">';
			// $htmlStr .= '<div class="col-md-8 col-md-offset-2">';
			// $htmlStr .= '<img src="'.$avatar.'" class="img-responsive" />';
			// $htmlStr .= '</div>'; // col-8-2
			// $htmlStr .= '</div>'; // row
			
			// $htmlStr .= '<div class="row">';
			// $htmlStr .= '<div class="col-md-12">';
			// $htmlStr .=  '<div class="username">'.$username.'</div>';
			// $htmlStr .= '<p><small>' . $points . ' ' . $translations['points_contrib']['translation_text'] . '</small></p>';
			// $htmlStr .= '</div>'; // col-12
			// $htmlStr .= '</div>'; // row
			
			// $htmlStr .=  '</div>'; // col-7
			
			// $htmlStr .= '</div>'; // row
			
			// $htmlStr .= '</div>'; // avatarBox
			
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
			$htmlStr .= $points. ' ' . $translations['points_contrib']['translation_text'];
			$htmlStr .= '</div>'; // col-4
			$htmlStr .= '</div>'; // row
			
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "schoolPageMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.$translations['school_link']['translation_text'].'">';
			$htmlStr .= $translations['school_page']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "logoutMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a href="'.$translations['logout_link']['translation_text'].'" class="h4" >';
			$htmlStr .= $translations['logout']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "resourceHubMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.$translations['hub_link']['translation_text'].'">';
			$htmlStr .= $translations['resource_hub']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "communityMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.$translations['community_link']['translation_text'].'">';
			$htmlStr .= $translations['community']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "teacherDashMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.$translations['teacher_dash']['translation_text'].'">';
			$htmlStr .= $translations['teacher_page']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "studentDashMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.$translations['student_dash']['translation_text'].'">';
			$htmlStr .= $translations['student_page']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "ecologistDashMenuItem" ) {
			
			$htmlStr = '<li>';
			$htmlStr .= '<a class="'.$activeClass.' h4" href="'.$translations['ecol_dash']['translation_text'].'">';
			$htmlStr .= $translations['ecol_page']['translation_text'];
			$htmlStr .= '</a>';
			$htmlStr .= '</li>';
			
		}
		else if ( $boxName == "projectLogo" ) {
			
			$htmlStr = '<img class="img-responsive" src="'.$translations['project_logo']['translation_text'].'" />';
			
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
		
		$translations = getTranslations("schoolcommunity");
		
		$schoolUser = self::getSchoolUser();
		
		if ( $schoolUser ) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			$roleId = $schoolUser->role_id;
			
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
			
			print '<div class="col-lg-2 col-lg-offset-1 col-lg-push-7 col-md-2 col-md-push-8 col-sm-9 col-xs-7 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h4" data-toggle="modal" data-target="#helpModal">';
				print ' <i class="fa fa-info"></i> ';
				print '</div>';
			}
			
			print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-success" >';
			print $translations['logout']['translation_text'];
			print '</a>';
			
			print '</div>'; // col-3
			
			
			//print '<div class="col-md-6 col-md-offset-1 col-md-pull-3 col-sm-12 col-xs-12 text-center" >';
			print '<div class="col-lg-6 col-lg-offset-1 col-lg-pull-3 col-md-8 col-md-pull-2 col-sm-12 col-xs-12 text-center" >';
			
			print '<table class="table statusBar" >';
			
			print '<tbody>';
			print '<tr>';
			

			print '<td class="statusBarElement statusBarAvatar" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" /></td>';
			
			
			print '<td class="text-center statusBarElement statusBarUsername" ><strong>'.$schoolUser->username.'</strong></td>';
			
			
			print '<td class="statusBarElement statusBarStars" ><i class="fa fa-lg fa-star statusIcon"></i><span class="statusBadge">' . $numStars .  '</span></td>';
	
			
			print '<td class="statusBarElement statusBarBadges" ><i class="fa fa-lg fa-circle statusIcon"></i><span class="statusBadge">' . $numBadges . '</span></td>';
			
			
			print '<td class="statusBarElement statusBarPoints">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			
			
			print '</tr>';
			print '</tbody>';
			
			print '</table>'; 
			
			print '</div>'; // col-8
			
			print '</div>'; // row
			
			if ( $backButtonLink ) {
				print '<div class="row">';
				
				print '<div class="col-md-2 col-sm-4 col-xs-4">';
			
				print '<a href="'.$translations['student_dash']['translation_text'].'" class="btn btn-primary homeBtn" >';
				print '<i class="fa fa-arrow-left"></i> ' . $translations['student_back']['translation_text'];
				print '</a>';
				
				print '</div>'; // col-1
			
				print '</div>'; // row
			}
			
			print '<div id="navEnd"></div>';
		}
			
	}
	
	
	public static function generateBackAndLogout ( $helpOptionId = 0, $slogan = null, $totalPoints = 0 ) {
		
		$translations = getTranslations("schoolcommunity");
		
		$schoolUser = self::getSchoolUser();
		
		if ( $schoolUser ) {
			
			$roleId = $schoolUser->role_id;
			print '<div class="row studentBackRow">';
			print '<div class="col-md-2 col-sm-4 col-xs-4">';
			
			print '<a href="'.$translations['student_dash']['translation_text'].'" class="btn btn-default" >';
			print '<i class="fa fa-arrow-left"></i> ' . $translations['student_back']['translation_text'];
			print '</a>';
			
			print '</div>'; // col-1
			
			print '<div class="col-md-2 col-md-offset-8 col-sm-4 col-sm-offset-4 col-xs-4 col-xs-offset-4 text-right">';
			
			if ( $helpOptionId > 0 ) {
				print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default menuHelpButton h3" data-toggle="modal" data-target="#helpModal">';
				print '<i class="fa fa-info"></i>';
				print '</div>';
			}
			
			print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			print $translations['logout']['translation_text'];
			print '</a>';
			
			print '</div>'; //col-2
			print '</div>'; // row
	
		}
	}
	
	
	public static function generateNav ( $activeItem = null, $helpOptionId = 0 ) {
		
		$translations = getTranslations("schoolcommunity");
		
		$schoolUser = self::getSchoolUser();
		
		$totalPoints = Task::getTotalUserPoints();
		
		if ( $schoolUser) {
			
			$schoolSettings = getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$logoPath = $settingsObj->logo;
			
			$roleId = $schoolUser->role_id;
			
			//print '<div class="text-center">'.$schoolUser->school.'</div>';
			
			// print '<div class="row mobileHeader">';
			// print '<div class="col-md-2 col-sm-2 col-xs-3 text-left">';
			// //print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			// //print '<img src="images/Projects/BES/BES_Black_Solid_Logo_Horizontal.jpg" alt="BES logo" height="50px">';
			// //print '</a>';
			// print '</div>'; // col-2
			// print '<div class="col-md-3 col-md-offset-5 col-sm-5 col-sm-offset-3 col-xs-12 ">';
			// print '<table class="table studentStatus" >';
			// print '<tbody>';
			// print '<tr>';
			// print '<td class="statusBarElement statusBarAvatar" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" alt="avatar image" /></td>';
			// print '<td class="text-center statusBarElement statusBarUsername" ><strong>'.$schoolUser->username.'</strong></td>';
			// print '<td class="statusBarElement statusBarPoints">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			// //print '<td style="vertical-align:middle">';
			// //print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			// //print $translations['logout']['translation_text'];
			// //print '</a>';
			// //print '</td>';
			// print '</tr>';
			// print '</tbody>';
			// print '</table>';
			// print '</div>'; // col-3+5
			// print '<div class="col-md-2 text-right">';
			// print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			// print $translations['logout']['translation_text'];
			// print '</a>';
			// print '</div>'; // col-2
			// print '</div>'; // row
			
			
			print '<nav class="navbar navbar-default">';
			print '<div class="container-fluid staffNav">';
			
			print '<div class="navbar-header">';
			
			
			print '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#besNavbarCollapse" aria-expanded="false">';
			print '<span class="sr-only">Toggle navigation</span>';
			print '<i class="fa fa-3x fa-bars "></i>';
			print '</button>';

			
			
			// print '<div class="row " >';
			// print '<div class="col-md-10 col-md-offset-1 col-sm-3 col-sm-offset-1 hidden-xs">';
			
			// print '<div class="besLogo" style="background:white; position:relative; top:-20px;padding:20px 20px 20px;">';
			// print '<img src="images/Projects/BES/BES_Black_Solid_Logo_Horizontal.jpg" class="img-responsive" />';
			
			// print '</div>';
			
			// print '</div>'; // col-10
			// print '</div>'; // row
			
		    //print '<a class="navbar-brand" href="#">BES ENCOUNTERS</a>';
			//print '<a class="navbar-brand" href="https://www.britishecologicalsociety.org"><img src="images/Projects/BES/BES_Black_Solid_Logo_Horizontal.jpg" alt="BES logo" height="42px">Encounters</a>';
			
			
        
			//print $avatarBox;
			print '</div>'; // navbar-header
			
			print '<div class="row studentMastheadRow">';
			
			print '<div class="col-md-2 col-sm-3 col-xs-3" >';
			
			print '<img src="'.$logoPath.'" class="img-responsive brandLogo" />';
			
			print '</div>'; // col-2
			
			
			
			print '<div class="col-md-4 col-md-offset-4 col-sm-5 col-sm-offset-2 col-xs-7 text-center" >';
			
			print '<table class="table statusBar" >';
			
			print '<tbody>';
			print '<tr>';
			

			print '<td class="statusBarElement statusBarAvatar" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" /></td>';
			
			
			print '<td class="text-center statusBarElement statusBarUsername" ><strong>'.$schoolUser->username.'</strong></td>';
			
			
			print '<td class="statusBarElement statusBarTeacherPoints">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			
			
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
			
			print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-success hidden-xs hidden-sm" >';
			print $translations['logout']['translation_text'];
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
				print '<a href="'.$translations['ecologist_dash']['translation_text'].'">';
				print $translations['ecologist_page']['translation_text'];
				print '</a>';
				print '</li>';
				
				// ------------------------------------------- browse tasks
				
				// $activeClass = "";
				// if ( $activeItem == "managestudents" ) {
					// $activeClass = "active";
				// }
				// print '<li class="besNavbarItem '.$activeClass.'">';
				// print '<a href="'.$translations['tasks_link']['translation_text'].'" class="manageStudents">';
				// print $translations['student_tasks']['translation_text'];
				// print '</a>';
				// print '</li>';
			
			}
			
			// ------------------------------------------ school page
			if ( $roleId != self::ECOLOGIST_ROLE ) {
				$activeClass = "";
				if ( $activeItem == "schooldashboard" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['school_link']['translation_text'].'">';
				print $translations['school_page']['translation_text'];
				print '</a>';
				print '</li>';
			}
			
			if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::ECOLOGIST_ROLE)) {
				
				$activeClass = "";
				if ( $activeItem == "managetasks" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['tasks_link']['translation_text'].'" class="manageTasks">';
				print $translations['manage_tasks']['translation_text'];
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
				print '<a href="'.$translations['students_link']['translation_text'].'" class="students">';
				print $translations['students']['translation_text'];
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
			print '<a href="'.$translations['community_link']['translation_text'].'">';
			print $translations['community']['translation_text'];
			print '</a>';
			print '</li>';
			
			// ------------------------------------------ resource hub
			if ( $roleId != self::STUDENT_ROLE ) {
				$activeClass = "";
				if ( $activeItem == "resourcehub" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['hub_link']['translation_text'].'">';
				print $translations['resource_hub']['translation_text'];
				print '</a>';
				print '</li>';
			
			
				$messageList = new MessageList();
				$numNewMessages = $messageList->newMessageCount();
	
				$activeClass = "";
				if ( $activeItem == "messages" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['messages_link']['translation_text'].'">';
				print $translations['messages']['translation_text'];
				if ( $numNewMessages > 0 ) {
					print ' <span id="messageBadge" class="badge notifyBadge">'.$numNewMessages.'</span>';
				}
				print '</a>';
				print '</li>';
			
			}
			
			print '<li class="besNavbarItem hidden-md hidden-lg">';
			print '<a href="'.$translations['logout_link']['translation_text'].'">';
			print $translations['logout']['translation_text'];
			print '</a>';
			print '</li>';
			
			print '</ul>';
			
			// print '<table class="table studentStatus" >';
			// print '<tbody>';
			// print '<tr>';
			// print '<td class="statusBarElement statusBarAvatar" ><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" alt="avatar image" /></td>';
			// print '<td class="text-center statusBarElement statusBarUsername" ><strong>'.$schoolUser->username.'</strong></td>';
			// print '<td class="statusBarElement statusBarPoints">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			// //print '<td style="vertical-align:middle">';
			// //print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			// //print $translations['logout']['translation_text'];
			// //print '</a>';
			// //print '</td>';
			// print '</tr>';
			// print '</tbody>';
			// print '</table>';
			
			//print '<div class="teacherStatus pull-right">';
		
			// print '<p class="navbar-text">';
			// print '<img src="'.$schoolUser->avatar.'" class="img-responsive avatar menuAvatar" />';
			// print '</p>';
			// // print '<p class="navbar-text">';
			// // print $schoolUser->username;
			// // print '</p>';
			// print '<p class="navbar-text">';
			// print '<strong>'.$schoolUser->username.'</strong> ' . $totalPoints . ' ' . $translations['points']['translation_text'];
			// print '</p>';

			// // ------------------------------------- status and logout
			// print '<li class="besNavbarItem">';
			// print '<table class="table table-condensed teacherStatus" style="position:relative; top:-10px;">';
			// print '<tbody>';
			// print '<tr>';
			// print '<td style="vertical-align:middle">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			// print '<td><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" style="width:60px"/></td>';
			// print '<td class="text-center" style="vertical-align:middle"><strong>'.$schoolUser->username.'</strong></td>';
			// print '</tr>';
			// print '</tbody>';
			// print '</table>';
			// print '</li>';
			
				
			// print '<button class="btn btn-default navbar-btn">';
			// print '<a href="'.$translations['logout_link']['translation_text'].'">';
			// print $translations['logout']['translation_text'];
			// print '</a>';
			// print '</button>';
			
			print '</div>';
			
			print '</div>'; // nav collapse
			
			print '</div>'; // container-fluid
			print '</nav>'; // navbar
			
			// ------------------------------------ help
			// if ( $helpOptionId > 0 ) {
				// print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default helpButton menuPageHelp h3" data-toggle="modal" data-target="#helpModal">';
				// print ' <i class="fa fa-info"></i> ';
				// print '</div>';
			// }
			
			// ----------------------------------- avatar and points
			// print '<div class="row">';
			// print '<div class="col-md-4 col-md-offset-8">';
			// print '<table class="table teacherStatus" style="position:relative; top:-20px;">';
			// print '<tbody>';
			// print '<tr>';
			// print '<td style="width:80px"><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" /></td>';
			// print '<td class="text-center" style="vertical-align:middle"><strong>'.$schoolUser->username.'</strong></td>';
			// print '<td style="vertical-align:middle">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			// print '</tr>';
			// print '</tbody>';
			// print '</table>';
			// print '</div>'; // col-4
			// print '</div>'; // row
			
			print '</div>';
			
			print '<div id="navEnd"></div>';
		
		}
	}
	
	public static function generateNavOrig ( $activeItem = null, $helpOptionId = 0 ) {
		
		$translations = getTranslations("schoolcommunity");
		
		$schoolUser = self::getSchoolUser();
		
		$totalPoints = Task::getTotalUserPoints();
		
		if ( $schoolUser) {
			
			$roleId = $schoolUser->role_id;
			
			//print '<div class="text-center">'.$schoolUser->school.'</div>';
			
			print '<div class="row mobileHeader">';
			print '<div class="col-md-2 col-sm-2 col-xs-3 text-left">';
			//print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			//print '<img src="images/Projects/BES/BES_Black_Solid_Logo_Horizontal.jpg" alt="BES logo" height="50px">';
			//print '</a>';
			print '</div>'; // col-2
			print '<div class="col-md-3 col-md-offset-7 col-sm-7 col-sm-offset-3 col-xs-12 ">';
			print '<table class="table statusTable" >';
			print '<tbody>';
			print '<tr>';
			print '<td width="20%"><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" alt="avatar image" /></td>';
			print '<td class="text-center" style="vertical-align:middle"><strong>'.$schoolUser->username.'</strong></td>';
			print '<td style="vertical-align:middle">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			print '<td style="vertical-align:middle">';
			print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			print $translations['logout']['translation_text'];
			print '</a>';
			print '</td>';
			print '</tr>';
			print '</tbody>';
			print '</table>';
			print '</div>'; // col-3+5
			// print '<div class="col-md-2 text-right">';
			// print '<a href="'.$translations['logout_link']['translation_text'].'" class="btn btn-default" >';
			// print $translations['logout']['translation_text'];
			// print '</a>';
			// print '</div>'; // col-2
			print '</div>'; // row
			
			
			print '<nav class="navbar navbar-default">';
			print '<div class="container-fluid staffNav">';
			
			print '<div class="navbar-header">';
			
			
			print '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#besNavbarCollapse" aria-expanded="false">';
			print '<span class="sr-only">Toggle navigation</span>';
			print '<i class="fa fa-3x fa-bars "></i>';
			print '</button>';

			
			
			// print '<div class="row " >';
			// print '<div class="col-md-10 col-md-offset-1 col-sm-3 col-sm-offset-1 hidden-xs">';
			
			// print '<div class="besLogo" style="background:white; position:relative; top:-20px;padding:20px 20px 20px;">';
			// print '<img src="images/Projects/BES/BES_Black_Solid_Logo_Horizontal.jpg" class="img-responsive" />';
			
			// print '</div>';
			
			// print '</div>'; // col-10
			// print '</div>'; // row
			
		    //print '<a class="navbar-brand" href="#">BES ENCOUNTERS</a>';
			//print '<a class="navbar-brand" href="https://www.britishecologicalsociety.org"><img src="images/Projects/BES/BES_Black_Solid_Logo_Horizontal.jpg" alt="BES logo" height="42px">Encounters</a>';
			
			
        
			//print $avatarBox;
			print '</div>'; // navbar-header

			print '<div class="collapse navbar-collapse" id="besNavbarCollapse">';
			print '<ul class="nav navbar-nav">';
			
			
			
			if ( $roleId == self::ECOLOGIST_ROLE ) {
				
				// ------------------------ ecologist dash
				
				$activeClass = "";
				if ( $activeItem == "ecologistdashboard" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['ecologist_dash']['translation_text'].'">';
				print $translations['ecologist_page']['translation_text'];
				print '</a>';
				print '</li>';
				
				// ------------------------------------------- browse tasks
				
				// $activeClass = "";
				// if ( $activeItem == "managestudents" ) {
					// $activeClass = "active";
				// }
				// print '<li class="besNavbarItem '.$activeClass.'">';
				// print '<a href="'.$translations['tasks_link']['translation_text'].'" class="manageStudents">';
				// print $translations['student_tasks']['translation_text'];
				// print '</a>';
				// print '</li>';
			
			}
			
			// ------------------------------------------ school page
			if ( $roleId != self::ECOLOGIST_ROLE ) {
				$activeClass = "";
				if ( $activeItem == "schooldashboard" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['school_link']['translation_text'].'">';
				print $translations['school_page']['translation_text'];
				print '</a>';
				print '</li>';
			}
			
			if ( ($roleId == self::TEACHER_ROLE) or ($roleId == self::ECOLOGIST_ROLE)) {
				
				$activeClass = "";
				if ( $activeItem == "managetasks" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['tasks_link']['translation_text'].'" class="manageTasks">';
				print $translations['manage_tasks']['translation_text'];
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
				print '<a href="'.$translations['students_link']['translation_text'].'" class="students">';
				print $translations['students']['translation_text'];
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
			print '<a href="'.$translations['community_link']['translation_text'].'">';
			print $translations['community']['translation_text'];
			print '</a>';
			print '</li>';
			
			// ------------------------------------------ resource hub
			if ( $roleId != self::STUDENT_ROLE ) {
				$activeClass = "";
				if ( $activeItem == "resourcehub" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['hub_link']['translation_text'].'">';
				print $translations['resource_hub']['translation_text'];
				print '</a>';
				print '</li>';
			
			
				$messageList = new MessageList();
				$numNewMessages = $messageList->newMessageCount();
	
				$activeClass = "";
				if ( $activeItem == "messages" ) {
					$activeClass = "active";
				}
				print '<li class="besNavbarItem '.$activeClass.'">';
				print '<a href="'.$translations['messages_link']['translation_text'].'">';
				print $translations['messages']['translation_text'];
				if ( $numNewMessages > 0 ) {
					print ' <span id="messageBadge" class="badge notifyBadge">'.$numNewMessages.'</span>';
				}
				print '</a>';
				print '</li>';
			
			}
			
			// print '<li class="besNavbarItem">';
			// print '<a href="'.$translations['logout_link']['translation_text'].'">';
			// print $translations['logout']['translation_text'];
			// print '</a>';
			// print '</li>';
			
			print '</ul>';
			
			print '<div class="teacherStatus pull-right">';
		
			print '<p class="navbar-text">';
			print '<img src="'.$schoolUser->avatar.'" class="img-responsive avatar menuAvatar" />';
			print '</p>';
			// print '<p class="navbar-text">';
			// print $schoolUser->username;
			// print '</p>';
			print '<p class="navbar-text">';
			print '<strong>'.$schoolUser->username.'</strong> ' . $totalPoints . ' ' . $translations['points']['translation_text'];
			print '</p>';

			// // ------------------------------------- status and logout
			// print '<li class="besNavbarItem">';
			// print '<table class="table table-condensed teacherStatus" style="position:relative; top:-10px;">';
			// print '<tbody>';
			// print '<tr>';
			// print '<td style="vertical-align:middle">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			// print '<td><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" style="width:60px"/></td>';
			// print '<td class="text-center" style="vertical-align:middle"><strong>'.$schoolUser->username.'</strong></td>';
			// print '</tr>';
			// print '</tbody>';
			// print '</table>';
			// print '</li>';
			
				
			print '<button class="btn btn-default navbar-btn">';
			print '<a href="'.$translations['logout_link']['translation_text'].'">';
			print $translations['logout']['translation_text'];
			print '</a>';
			print '</button>';
			
			print '</div>';
			
			print '</div>'; // nav collapse
			
			print '</div>'; // container-fluid
			print '</nav>'; // navbar
			
			// ------------------------------------ help
			// if ( $helpOptionId > 0 ) {
				// print '<div id="helpButton_'.$helpOptionId.'" class="btn btn-default helpButton menuPageHelp h3" data-toggle="modal" data-target="#helpModal">';
				// print ' <i class="fa fa-info"></i> ';
				// print '</div>';
			// }
			
			// ----------------------------------- avatar and points
			// print '<div class="row">';
			// print '<div class="col-md-4 col-md-offset-8">';
			// print '<table class="table teacherStatus" style="position:relative; top:-20px;">';
			// print '<tbody>';
			// print '<tr>';
			// print '<td style="width:80px"><img src="'.$schoolUser->avatar.'" class="img-responsive avatar" /></td>';
			// print '<td class="text-center" style="vertical-align:middle"><strong>'.$schoolUser->username.'</strong></td>';
			// print '<td style="vertical-align:middle">' . $totalPoints . ' ' . $translations['points']['translation_text'] . '</td>';
			// print '</tr>';
			// print '</tbody>';
			// print '</table>';
			// print '</div>'; // col-4
			// print '</div>'; // row
			
			print '</div>';
			
			
		
		}
	}
}



?>

