<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Badge {
	
	// Correspond to badge status - whether a badge is locked, unstarted, part complete or complete
	const LOCKED		= 0;
	const UNLOCKED		= 1;
	const PENDING		= 2;
	const COMPLETE		= 3;
	const COLLECTED		= 4;
	
	private $badgeId;
	private $badgeName;
	private $lockLevel;
	private $status;
	private $badgeImage;
	private $badgeGroupId;
	private $badgeGroupName;
	
	private $totalTasks;
	private $totalPoints;
	
	private $personId;
	private $winnerType;
	private $numTasks;
	private $numPoints;
	
	private $tasks;
	
	private $moduleId;
	private $moduleIcon;
	
	
	function __construct( $badgeId )
	{
		if ( $badgeId  ) {
			
			$this->badgeId = $badgeId;
			
			$details = codes_getDetails ( $badgeId, "badge" );
			$this->badgeName = $details["name"];
			$this->lockLevel = $details["lock_level"];
			$this->badgeImage = $details["badge_image"];
			$this->unlockedImage = $details["unlocked_image"];
			$this->lockedImage = $details["locked_image"];
			$this->badgeGroupId = $details["badge_group"];
			$this->badgeGroupName = codes_getName ( $this->badgeGroupId, "badgegroup" );
			
			$this->moduleId = $details["module_id"];
			$module = Module::getModule($this->moduleId);
			$this->moduleIcon = $module->icon;
			
			$this->personId = userID();
			
			$this->winnerType = 'STUDENT';
			if ( SchoolCommunity::isTeacher() ) {
				$this->winnerType = 'TEACHER';
			}
			
		}
	
	}
	
	
	private function setSummary () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$userTaskTable = "StudentTasks";
		if ( $this->winnerType == 'TEACHER' ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$completeTasksString = "select count(*) from " . $userTaskTable . " ST1 " .
							"inner join Task T1 on T1.task_id = ST1.task_id " .
							"where T1.badge_id = " . $this->badgeId . " and ST1.person_id = " . $this->personId . " AND ST1.status = " . self::COMPLETE;
				
		$unlockedQueryString = "select count(*) from " . $userTaskTable . " ST2 " .
							"inner join Task T2 on T2.task_id = ST2.task_id " .
							"where T2.badge_id = " . $this->badgeId . " and ST2.person_id = " . $this->personId . " AND ST2.status = " . self::UNLOCKED;
				
		$pendingQueryString = "select count(*) from " . $userTaskTable . " ST3 " .
							"inner join Task T3 on T3.task_id = ST3.task_id " .
							"where T3.badge_id = " . $this->badgeId . " and ST3.person_id = " . $this->personId . " AND ST3.status = " . self::PENDING;
				
		$allTasksQueryString = "select count(*) as totalTasks from Task T4 where T4.badge_id = " . $this->badgeId;
									
		$allPointsQueryString = "select SUM(T5.points) as totalPoints from Task T5 where T5.badge_id = " . $this->badgeId;		
		
	
		$query = $db->getQuery(true)
			->select("IFNULL(SUM(T.points),0) as numPoints, ( " . $completeTasksString . " ) as numComplete, ( " 
						. $unlockedQueryString . " ) as numUnlocked, ( " 
						. $pendingQueryString . " ) as numPending, ( " 
						. $allPointsQueryString . " ) as totalPoints, ( " 
						. $allTasksQueryString . " ) as totalTasks from " . $userTaskTable . " ST")
			->innerJoin("Task T on ST.task_id = T.task_id and T.badge_id = " . $this->badgeId )
			->where("ST.person_id = " . $this->personId )
			->where("ST.status = " . self::COMPLETE );
			
		
		$db->setQuery($query);
		
		//error_log("Badge select query created: " . $query->dump());
		
		$summary = $db->loadAssocList();
		
		$this->numPoints = $summary[0]["numPoints"];
		$this->numTasks = $summary[0]["numComplete"];
		$this->totalTasks = $summary[0]["totalTasks"];
		$this->totalPoints = $summary[0]["totalPoints"];
		
		if ( $summary[0]["numComplete"] == $this->totalTasks ) {
			$this->status = self::COMPLETE;
		}
		else if ( $summary[0]["numUnlocked"] == $this->totalTasks ) {
			$this->status = self::UNLOCKED;
		}
		else if ( $summary[0]["numUnlocked"] + $summary[0]["numComplete"] + $summary[0]["numPending"] == 0 ) {
			$this->status = self::LOCKED;
		}
		else {
			$this->status = self::PENDING;
		}
		
		
	}
	
	
	private function getAllStudentTasks ( $taskStatus = null ) {
		
		$statusClause = "";
		if ( $taskStatus ) {
			$statusClause = " and ST.status = " . $taskStatus;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("T.task_id, IFNULL(ST.status, ".self::LOCKED.") as status, ST.species_unlocked, B.name as badge_name, T.name as task_name, T.species as species, T.description, T.points, T.image, T.article_id, T.counted_by, T.linked_task, BG.icon, M.icon as module_icon from Task T")
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_id = " . $this->badgeId .
						" and B.winner_type = 'STUDENT'")
			->innerJoin("BadgeGroup BG on B.badge_group = BG.group_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->leftJoin("StudentTasks ST on ST.task_id = T.task_id and ST.person_id = " . $this->personId . $statusClause )
			->order("T.task_id");
			
		
		$db->setQuery($query);
		
		//error_log("getAllStudentTasks select query created: " . $query->dump());
		
		$tasks = $db->loadAssocList();
		
		return $tasks;
		
	}
	
	
	
	private function getAllTeacherTasks ( $taskStatus = null ) {
		
		return $this->getAllTasks  ( "TEACHER", $taskStatus );
		
		// $statusClause = "";
		// if ( $taskStatus ) {
			// $statusClause = " and ST.status = " . $taskStatus;
		// }
		
		// $db = \JDatabaseDriver::getInstance(dbOptions());
		
		// $query = $db->getQuery(true)
			// ->select("T.task_id, IFNULL(TT.status, ".self::LOCKED.") as status, TT.species_unlocked, B.name as badge_name, T.name as task_name, T.species as species, T.description, T.points, T.image, T.article_id, T.counted_by, T.linked_task, BG.icon from Task T")
			// ->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_id = " . $this->badgeId .
						// " and B.winner_type = 'TEACHER'")
			// ->innerJoin("BadgeGroup BG on B.badge_group = BG.group_id")
			// ->leftJoin("TeacherTasks TT on TT.task_id = T.task_id and TT.person_id = " . $this->personId . $statusClause)
			// ->order("T.task_id");
			
		
		// $db->setQuery($query);
		
		// //error_log("getAllTeacherTasks select query created: " . $query->dump());
		
		// $tasks = $db->loadAssocList();
		
		// return $tasks;
		
	}
	
	
	private function getAllEcologistTasks ( $taskStatus = null ) {
		
		return $this->getAllTasks  ( "ECOLOGIST", $taskStatus );
		
	}
	
	
	private function getAllTasks ( $winnerType, $taskStatus = null ) {
		
		$statusClause = "";
		if ( $taskStatus ) {
			$statusClause = " and ST.status = " . $taskStatus;
		}
		
		if ( $winnerType == "STUDENT" ) {
			$nullStatus = self::LOCKED;
		}
		else {
			$nullStatus = self::UNLOCKED;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("T.task_id, IFNULL(TT.status, ".$nullStatus.") as status, TT.species_unlocked, B.name as badge_name, T.name as task_name, T.species as species, T.description, T.points, T.image, T.article_id, T.counted_by, T.linked_task, BG.icon, M.icon as module_icon from Task T")
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_id = " . $this->badgeId .
						" and B.winner_type = " . $db->quote($winnerType) )
			->innerJoin("BadgeGroup BG on B.badge_group = BG.group_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->leftJoin("TeacherTasks TT on TT.task_id = T.task_id and TT.person_id = " . $this->personId . $statusClause)
			->order("T.task_id");
			
		
		$db->setQuery($query);
		
		//error_log("getAllTeacherTasks select query created: " . $query->dump());
		
		$tasks = $db->loadAssocList();
		
		return $tasks;
		
	}
	
	
	public function getBadgeGroupId() {
		return $this->badgeGroupId;
	}
	
	
	public function getSummary () {
		
		if ( $this->numTasks == null ) {
			
			$this->setSummary();
			
		}
		
		return array("numPoints" => $this->numPoints,
					"numTasks" => $this->numTasks,
					"currLevel" => $this->lockLevel,
					"status" => $this->status,
					"totalTasks" => $this->totalTasks,
					"totalPoints" => $this->totalPoints	);
	}
	
	
	public function getTasksJSON () {
		
		if ( $this->numTasks == null ) {
			
			$this->setSummary();
			
		}
		
		$resultsArray = array();
		
		$resultsArray["badge_id"] = $this->badgeId;
		$resultsArray["badge_name"] = $this->badgeName;
		$resultsArray["lock_level"] = $this->lockLevel;
		$resultsArray["status"] = $this->status;
		$resultsArray["group_id"] = $this->badgeGroupId;
		$resultsArray["group_name"] = $this->badgeGroupName;
		$resultsArray["num_tasks"] = $this->numTasks;
		$resultsArray["total_tasks"] = $this->totalTasks;
		$resultsArray["num_points"] = $this->numPoints;
		$resultsArray["total_points"] = $this->totalPoints;
		
		$tasks = null;
		if ( SchoolCommunity::isStudent() ) {
			$tasks = $this->getAllStudentTasks ();
		}
		else if ( SchoolCommunity::isTeacher() ) {
			$tasks = $this->getAllTeacherTasks ();
		}
			
		if ( count($tasks) > 0 ) {
			$resultsArray["tasks"] = $tasks;
		}

		
		return json_encode ( $resultsArray );
		
	}
	
	public function getTasks ( $taskStatus = null ) {
		
		if ( $this->numTasks == null ) {
			
			$this->setSummary();
			
		}
		
		$resultsArray = array();
		
		$resultsArray["badge_id"] = $this->badgeId;
		$resultsArray["badge_name"] = $this->badgeName;
		$resultsArray["lock_level"] = $this->lockLevel;
		$resultsArray["status"] = $this->status;
		$resultsArray["badge_image"] = $this->badgeImage;
		$resultsArray["unlocked_image"] = $this->unlockedImage;
		$resultsArray["locked_image"] = $this->lockedImage;
		$resultsArray["group_id"] = $this->badgeGroupId;
		$resultsArray["group_name"] = $this->badgeGroupName;
		$resultsArray["num_tasks"] = $this->numTasks;
		$resultsArray["total_tasks"] = $this->totalTasks;
		$resultsArray["num_points"] = $this->numPoints;
		$resultsArray["total_points"] = $this->totalPoints;
		$resultsArray["module_id"] = $this->moduleId;
		$resultsArray["module_icon"] = $this->moduleIcon;
		
		
		$tasks = null;
		if ( SchoolCommunity::isStudent( $taskStatus ) ) {
			$tasks = $this->getAllStudentTasks ();
		}
		else if ( SchoolCommunity::isTeacher() ) {
			$tasks = $this->getAllTeacherTasks ( $taskStatus );
		}
		else if ( SchoolCommunity::isEcologist() ) {
			$tasks = $this->getAllEcologistTasks ( $taskStatus );
		}
			
		if ( count($tasks) > 0 ) {
			$resultsArray["tasks"] = $tasks;
		}

		
		return $resultsArray;
		
	}
	
	public static function unlockNonStudentBadges ( $roleId ) {
		
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$completedBadgeGroups = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$newTasks = array();
				
			
		// For teachers and ecologists all tasks are unlocked, so just copy any new ones.
		$query = $db->getQuery(true)
			->select("T.* from Task T")
			->innerJoin("Badge B on B.badge_id = T.badge_id " .
				" and T.role_id = " . $roleId )
			->where("T.task_id not in (select task_id from TeacherTasks where person_id = " . $personId . ")" )
			->order("T.task_id");
			
		$db->setQuery($query);
		
		//error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$newTasks = $db->loadObjectList();
		
		
		foreach ( $newTasks as $newTask ) {
	
			$fields = new \StdClass();
			$fields->person_id = $personId;
			$fields->task_id = $newTask->task_id;
			$fields->status = self::UNLOCKED;
			
			$success = $db->insertObject("TeacherTasks", $fields);
			if(!$success){
				error_log ( "TeacherTasks insert failed" );
			}
		}
		
		
		/*
		$query = $db->getQuery(true)
			->select("B.badge_group, max(lock_level) as lock_level from Badge B")
			->innerJoin("Task T on B.badge_id = T.badge_id")
			->innerJoin("TeacherTasks ST on ST.task_id = T.task_id and ST.person_id = " . $personId)
			->where("T.role_id = " . $roleId )
			->group("B.badge_group");
		
		$db->setQuery($query);
		
		//error_log("unlockTeacherBadges lock levels query created: " . $query->dump());
		
		$lockLevels = $db->loadAssocList("badge_group");
		
		
		if ( count($lockLevels) == 0 ) {
			// new user
			$query = $db->getQuery(true)
				->select("distinct B.badge_group, 0 as lock_level from Badge B");
			
			$db->setQuery($query);
			
			//error_log("unlockTeacherBadges lock levels query created: " . $query->dump());
			
			$lockLevels = $db->loadAssocList("badge_group");
			
		}
		
		
		$query = $db->getQuery(true)
			->select("B.badge_group, count(ST.task_id) as num_incomplete from Badge B")
			->innerJoin("Task T on B.badge_id = T.badge_id")
			->leftJoin("TeacherTasks ST on ST.task_id = T.task_id and ST.person_id = " . $personId . " and ST.status < " . self::COMPLETE)
			->group("B.badge_group");	
		
		$db->setQuery($query);
		
		//error_log("unlockTeacherBadges incomplete query created: " . $query->dump());
		
		$incompleteBadges = $db->loadAssocList();
		
		foreach ( $incompleteBadges as $groupCount ) {
			
			if ( $groupCount["num_incomplete"] == 0 ) {
				
				$groupId = $groupCount["badge_group"];
				
				$newTasks = array();
				
				if ( array_key_exists ( $groupId, $lockLevels ) ) {
					$newLockLevel = $lockLevels[$groupId]["lock_level"] + 1;
				
					// All tasks complete so unlock the next level by unloading the next level into StudentTasks
					$query = $db->getQuery(true)
						->select("T.* from Task T")
						->innerJoin("Badge B on B.badge_id = T.badge_id and badge_group = " . $groupId . " and B.lock_level = " . $newLockLevel .
							" and T.role_id = " . $roleId )
						->order("T.task_id");
						
					$db->setQuery($query);
					
					//error_log("unlockBadges new tasks query created: " . $query->dump());
					
					$newTasks = $db->loadObjectList();
				}
				
				if ( count($newTasks) == 0 ) {
					$completedBadgeGroups[] = $groupCount["badge_group"];
				}
				else {
					
					foreach ( $newTasks as $newTask ) {
				
						$fields = new \StdClass();
						$fields->person_id = $personId;
						$fields->task_id = $newTask->task_id;
						$fields->status = self::UNLOCKED;
						
						$success = $db->insertObject("TeacherTasks", $fields);
						if(!$success){
							error_log ( "TeacherTasks insert failed" );
						}
					}
				}
				
			}
			
		}
		*/
		
		
		return $completedBadgeGroups;
		
				
	}
	
	public static function unlockTeacherBadges () {
		
		return self::unlockNonStudentBadges ( SchoolCommunity::TEACHER_ROLE );
		
	}
	
	public static function unlockEcologistBadges () {
		
		return self::unlockNonStudentBadges ( SchoolCommunity::ECOLOGIST_ROLE );
	}
	
	public static function unlockBadgesOrig ( $personId = null ) {
		
		if ( !$personId ) {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return array();
		}
		
		$completedBadgeGroups = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("B.badge_group, max(lock_level) as lock_level from Badge B")
			->innerJoin("Task T on B.badge_id = T.badge_id")
			->innerJoin("StudentTasks ST on ST.task_id = T.task_id and ST.person_id = " . $personId)
			->group("B.badge_group");
		
		$db->setQuery($query);
		
		//error_log("unlockBadges lock levels query created: " . $query->dump());
		
		$lockLevels = $db->loadAssocList("badge_group");
		
		
		if ( count($lockLevels) == 0 ) {
			// new user
			$query = $db->getQuery(true)
				->select("distinct B.badge_group, 0 as lock_level from Badge B");
			
			$db->setQuery($query);
			
			//error_log("unlockBadges lock levels query created: " . $query->dump());
			
			$lockLevels = $db->loadAssocList("badge_group");
			
		}
		
		
		$query = $db->getQuery(true)
			->select("B.badge_group, count(ST.task_id) as num_incomplete from Badge B")
			->innerJoin("Task T on B.badge_id = T.badge_id")
			->leftJoin("StudentTasks ST on ST.task_id = T.task_id and ST.person_id = " . $personId . " and ST.status < " . self::COMPLETE)
			->group("B.badge_group");	
		
		$db->setQuery($query);
		
		//error_log("unlockBadges incomplete query created: " . $query->dump());
		
		$incompleteBadges = $db->loadAssocList();
		
		foreach ( $incompleteBadges as $groupCount ) {
			
			if ( $groupCount["num_incomplete"] == 0 ) {
				
				$groupId = $groupCount["badge_group"];
				
				
				$newLockLevel = $lockLevels[$groupId]["lock_level"] + 1;
			
				// All tasks complete so unlock the next level by unloading the next level into StudentTasks
				$query = $db->getQuery(true)
					->select("T.* from Task T")
					->innerJoin("Badge B on B.badge_id = T.badge_id and badge_group = " . $groupId . " and B.lock_level = " . $newLockLevel .
						" and T.role_id = " . SchoolCommunity::STUDENT_ROLE )
					->order("T.task_id");
					
				$db->setQuery($query);
				
				//error_log("unlockBadges new tasks query created: " . $query->dump());
				
				$newTasks = $db->loadObjectList();
				
				if ( count($newTasks) == 0 ) {
					$completedBadgeGroups[] = $groupCount["badge_group"];
				}
				else {
					
					foreach ( $newTasks as $newTask ) {
				
						$fields = new \StdClass();
						$fields->person_id = $personId;
						$fields->task_id = $newTask->task_id;
						$fields->status = self::UNLOCKED;
						
						$success = $db->insertObject("StudentTasks", $fields);
						if(!$success){
							error_log ( "StudentTasks insert failed" );
						}
					}
				}
				
			}
			
		}
		return $completedBadgeGroups;
	}
	
	public static function unlockBadges ( $personId = null ) {
		
		if ( !$personId ) {
			$personId = userID();
		}
		
		if ( !$personId ) {
			return array();
		}
		
		//$modules = Module::getModules();
		
		$completedBadgeGroups = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("B.module_id, B.badge_group, max(lock_level) as lock_level from Badge B")
			->innerJoin("Task T on B.badge_id = T.badge_id")
			->innerJoin("StudentTasks ST on ST.task_id = T.task_id and ST.person_id = " . $personId)
			->group("B.module_id, B.badge_group");
		
		$db->setQuery($query);
		
		//error_log("unlockBadges lock levels query created: " . $query->dump());
		
		$allLockLevels = $db->loadObjectList();
		
		
		if ( count($allLockLevels) == 0 ) {
			// new user
			$query = $db->getQuery(true)
				->select("distinct B.module_id, B.badge_group, 0 as lock_level from Badge B");
			
			$db->setQuery($query);
			
			//error_log("unlockBadges lock levels query created: " . $query->dump());
			
			$allLockLevels = $db->loadObjectList();
			
		}
		
		$lockLevels = array();
		
		foreach ( $allLockLevels as $level ) {
			$moduleId = $level->module_id;
			$badgeGroup = $level->badge_group;
			if ( !array_key_exists($moduleId, $lockLevels ) ) {
				$lockLevels[$moduleId] = array();
			}
			$lockLevels[$moduleId][$badgeGroup] = $level->lock_level;
		}
		
		$query = $db->getQuery(true)
			->select("B.module_id, B.badge_group, count(ST.task_id) as num_incomplete from Badge B")
			->innerJoin("Task T on B.badge_id = T.badge_id")
			->leftJoin("StudentTasks ST on ST.task_id = T.task_id and ST.person_id = " . $personId . " and ST.status < " . self::COMPLETE)
			->group("B.module_id, B.badge_group");	
		
		$db->setQuery($query);
		
		//error_log("unlockBadges incomplete query created: " . $query->dump());
		
		$incompleteBadges = $db->loadAssocList();
		
		foreach ( $incompleteBadges as $groupCount ) {
			
			if ( $groupCount["num_incomplete"] == 0 ) {
				
				$moduleId = $groupCount["module_id"];
				$groupId = $groupCount["badge_group"];
				
				if ( array_key_exists($moduleId, $lockLevels) and array_key_exists($groupId, $lockLevels[$moduleId]) ) {
					$newLockLevel = $lockLevels[$moduleId][$groupId] + 1;
				}
				else {
					$newLockLevel = 1;
				}
			
				// All tasks complete so unlock the next level by unloading the next level into StudentTasks
				$query = $db->getQuery(true)
					->select("T.* from Task T")
					->innerJoin("Badge B on B.badge_id = T.badge_id and module_id = " .$moduleId. " and badge_group = " . $groupId . " and B.lock_level = " . $newLockLevel .
						" and T.role_id = " . SchoolCommunity::STUDENT_ROLE )
					->order("T.task_id");
					
				$db->setQuery($query);
				
				//error_log("unlockBadges new tasks query created: " . $query->dump());
				
				$newTasks = $db->loadObjectList();
				
				if ( count($newTasks) == 0 ) {
					$completedBadgeGroups[] = array($moduleId, $groupCount["badge_group"]);
				}
				else {
					
					foreach ( $newTasks as $newTask ) {
				
						$fields = new \StdClass();
						$fields->person_id = $personId;
						$fields->task_id = $newTask->task_id;
						$fields->status = self::UNLOCKED;
						
						$success = $db->insertObject("StudentTasks", $fields);
						if(!$success){
							error_log ( "StudentTasks insert failed" );
						}
					}
				}
				
			}
			
		}
		return $completedBadgeGroups;
	}
	
	
	public static function getTotalBadges ( $module=null ) {
		
		$personId = userID();
		
		$moduleStr = "";
		
		if ( $module ) {
			$moduleStr = " and B.module_id = " . $module;
		}
		
		$numBadges = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("COUNT(*) from StudentTasks ST")
				->innerJoin("Task T on T.task_id = ST.task_id and ST.person_id = ". $personId )
				->innerJoin("Badge B on B.badge_id = T.badge_id" . $moduleStr)
				->where("ST.status >= " . self::COMPLETE )
				->where("T.role_id = " . SchoolCommunity::STUDENT_ROLE );
				
			$db->setQuery($query);
			
			//error_log("Basge::getTotalBadges  select query created: " . $query->dump());
			
			$numBadges = $db->loadResult();
		}
		
		return $numBadges;
	}
	
	
}



?>

