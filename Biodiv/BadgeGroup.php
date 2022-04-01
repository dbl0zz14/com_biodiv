<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class BadgeGroup {
	
	// Correspond to badge status - whether a badge or task is unstarted, part complete or complete
	const LOCKED		= 0;
	const UNLOCKED		= 1;
	const PENDING		= 2;
	const COMPLETE		= 3;
	const COLLECTED		= 4;

	private $badgeGroupId;
	private $badgeGroupName;
	private $personId;
	private $roleId;
	private $userTaskTable;
	private $winnerType;
	private $numPoints;
	private $numTasks;
	private $numBadges;
	private $currLevel;
	private $totalTasks;
	private $totalBadges;
	
	private $imageData;
	
	
	function __construct( $badgeGroupId )
	{
		if ( $badgeGroupId  ) {
			
			$this->badgeGroupId = $badgeGroupId;
			$this->badgeGroupName = codes_getName ( $badgeGroupId, "badgegroup" );
			$this->personId = userID();
			
			
			$this->roleId = SchoolCommunity::STUDENT_ROLE;
			$this->userTaskTable = "StudentTasks";
			$this->winnerType = 'STUDENT';
			
			if ( SchoolCommunity::isTeacher() ) {
				$this->roleId = SchoolCommunity::TEACHER_ROLE;
				$this->userTaskTable = "TeacherTasks";
				$this->winnerType = 'TEACHER';
			}
			else if ( SchoolCommunity::isEcologist() ) {
				$this->roleId = SchoolCommunity::ECOLOGIST_ROLE;
				$this->userTaskTable = "TeacherTasks";
				$this->winnerType = 'ECOLOGIST';
			}
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
			->select("* from BadgeGroup")
			->where("group_id = " . $this->badgeGroupId );
			
		
			$db->setQuery($query);
		
			$this->imageData = $db->loadObject();
		
		}
	
	}
	
	private function setSummary () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		
		$tasksQueryString = "select count(*) from " . $this->userTaskTable . " ST1 " .
							"inner join Task T1 on T1.task_id = ST1.task_id " .
							"INNER JOIN Badge B1 on B1.badge_id = T1.badge_id " .
							"where B1.badge_group = " . $this->badgeGroupId . " and ST1.person_id = " . $this->personId . " AND ST1.status >= " . self::COMPLETE;
		
		$badgesQueryString = "select count(distinct B2.badge_id) from Badge B2 " .
								"inner join Task T2 on T2.badge_id = B2.badge_id " .
								"inner join " . $this->userTaskTable . " ST2 on ST2.task_id = T2.task_id " .
								"where B2.badge_group = " . $this->badgeGroupId . " and ST2.person_id = " . $this->personId . " and ST2.status >= " . self::COMPLETE  .
								" and not exists ( " .
								"select ST3.task_id from Badge B3 " .
								"inner join Task T3 on T3.badge_id = B3.badge_id " .
								"inner join " . $this->userTaskTable . " ST3 on ST3.task_id = T3.task_id " .
								"where B3.badge_group = " . $this->badgeGroupId . " and B3.badge_id = B2.badge_id and ST3.person_id = " . 
									$this->personId . " and ST3.status = " . self::UNLOCKED . ")";
								
		
		
		$allTasksQueryString = "select count(*) from Task T4 " .
							" inner join Badge B4 on B4.badge_id = T4.badge_id and B4.winner_type = '" . $this->winnerType .
							"' where B4.badge_group = " . $this->badgeGroupId;
		
		$allTaskPointsQueryString = "select SUM(T5.points) from Task T5 " .
							" inner join Badge B5 on B5.badge_id = T5.badge_id and B5.winner_type = '" . $this->winnerType .
							"' where B5.badge_group = " . $this->badgeGroupId;
		
		$allBadgesQueryString = "select count(*) from Badge B5 " .
								"where B5.winner_type = '" . $this->winnerType . "' and B5.badge_group = " . $this->badgeGroupId;
	
		$query = $db->getQuery(true)
			->select("IFNULL(SUM(T.points),0) as numPoints, ( " . $tasksQueryString . " ) as numTasks, ( " 
						. $badgesQueryString . " ) as numBadges, ( " 
						. $allTasksQueryString . " ) as totalTasks, ( "
						. $allTaskPointsQueryString . " ) as totalPoints, ( "
						. $allBadgesQueryString . " ) as totalBadges from " . $this->userTaskTable . " ST")
			->innerJoin("Task T on ST.task_id = T.task_id")
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $this->badgeGroupId )
			->where("ST.person_id = " . $this->personId )
			->where("ST.status >= " . self::COMPLETE );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$summary = $db->loadAssocList();
		
		$this->numPoints = $summary[0]["numPoints"];
		$this->numTasks = $summary[0]["numTasks"];
		$this->numBadges = $summary[0]["numBadges"];
		$this->currLevel = $this->numBadges + 1;
		$this->totalTasks = $summary[0]["totalTasks"];
		$this->totalPoints = $summary[0]["totalPoints"];
		$this->totalBadges = $summary[0]["totalBadges"];

	}
	
	
	
	private function getStudentTasks ( $lockLevel ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("ST.task_id, ST.status, T.name, T.description, T.points, T.image, T.article_id from StudentTasks ST")
			->innerJoin("Task T on ST.task_id = T.task_id")
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $this->badgeGroupId . " and B.lock_level = " . $lockLevel .
						" and B.winner_type = 'STUDENT'")
			->where("ST.person_id = " . $this->personId );
			
		
		$db->setQuery($query);
		
		//error_log("getStudentTasks select query created: " . $query->dump());
		
		$tasks = $db->loadAssocList();
		
		return $tasks;
	}
	
	
	private function getAllStudentTasks () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("B.lock_level, B.badge_id, B.name as badge_name, B.badge_image, T.task_id, IFNULL(ST.status, ".self::LOCKED.
				") as status, T.name as task_name, T.description, T.points, T.image, T.article_id from Task T")
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $this->badgeGroupId .
						" and B.winner_type = 'STUDENT'")
			->leftJoin("StudentTasks ST on ST.task_id = T.task_id and ST.person_id = " . $this->personId)
			->order("B.lock_level, T.task_id");
			
		
		$db->setQuery($query);
		
		//error_log("getAllStudentTasks select query created: " . $query->dump());
		
		$tasks = $db->loadAssocList();
		
		return $tasks;
	}
	
	
	private function getStudentBadgeDetails ( $lockLevel ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("badge_id, name from Badge B")
			->where("B.badge_group = " . $this->badgeGroupId . " and B.lock_level = " . $lockLevel  .
						" and B.winner_type = 'STUDENT'" );			
		
		$db->setQuery($query);
		
		//error_log("getTasks select query created: " . $query->dump());
		
		$badge = $db->loadAssocList();
		
		if ( count($badge) > 0 ) {
			return $badge[0];
		}
		else {
			return null;
		}
	}
	

	
	private function getTasks ( $lockLevel ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("T.task_id, T.name, T.description, T.points, T.image, T.article_id from Task T")
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $this->badgeGroupId . " and B.lock_level = " . $lockLevel );			
		
		$db->setQuery($query);
		
		//error_log("getTasks select query created: " . $query->dump());
		
		$tasks = $db->loadAssocList();
		
		return $tasks;
	}
	
	
	private function getBadgeIds ( $roleId = 0, $module = 1 ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		if ( $roleId == SchoolCommunity::STUDENT_ROLE ) {
			$winnerType = "STUDENT";
		}
		else if ( $roleId == SchoolCommunity::TEACHER_ROLE ) {
			$winnerType = "TEACHER";
		}
		else if ( $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
			$winnerType = "ECOLOGIST";
		}
		else {
			$winnerType = "STUDENT";
			if ( SchoolCommunity::isTeacher() ) {
				$winnerType = "TEACHER";
			}
			else if ( SchoolCommunity::isEcologist() ) {
				$winnerType = "ECOLOGIST";
			}
		}
		
		$query = $db->getQuery(true)
			->select("B.badge_id from Badge B where B.winner_type = '".$winnerType."' and B.badge_group = " . $this->badgeGroupId . 
				" and B.module_id = " . $module )
			->order("B.lock_level");
			
		$db->setQuery($query);
		
		//error_log("getBadgeIds select query created: " . $query->dump());
		
		$badgeIds = $db->loadColumn();
		
		return $badgeIds;
	}
	
	
	public function getSummary () {
		
		if ( $this->numTasks == null ) {
			
			$this->setSummary();
			
		}
		
		return array("numPoints" => $this->numPoints,
					"numTasks" => $this->numTasks,
					"numBadges" => $this->numBadges,
					"currLevel" => $this->currLevel,
					"totalTasks" => $this->totalTasks,
					"totalPoints" => $this->totalPoints,
					"totalBadges" => $this->totalBadges	);
	}
	
	
	public function getImageData () {
		return $this->imageData;
	}
		
	public function getResultsJSON () {
		
		if ( $this->numTasks == null ) {
			
			$this->setSummary();
			
		}
		
		$resultsArray = array();
		
		$prevLevel = $this->currLevel - 1;
		$currLevel = $this->currLevel;
		$nextLevel = $this->currLevel + 1;
		
		if ( $prevLevel > 0 ) {
			$prevArray = array("level"=>$prevLevel);
			$resultsArray["prev"] = $prevArray;
			
			$tasks = $this->getStudentTasks ( $prevLevel );
			
			if ( count($tasks) > 0 ) {
				$resultsArray["prev"]["tasks"] = $tasks;
			}
		}
		
		$badge = $this->getStudentBadgeDetails ( $currLevel );
		
		$currArray = array("level"=>$currLevel);
		if ( $badge ) {
			$currArray ["name"] = $badge["name"];
		}
		else {
			$currArray ["name"] = "";
		}
		$resultsArray["curr"] = $currArray;
			
		$tasks = $this->getStudentTasks ( $currLevel );
			
		if ( count($tasks) > 0 ) {
			$resultsArray["curr"]["tasks"] = $tasks;
		}
		
		// Need to sort max
		if ( $nextLevel <= 15 ) {
			$nextArray = array("level"=>$nextLevel);
			$resultsArray["next"] = $nextArray;
			
			$tasks = $this->getTasks ( $nextLevel );
			
			if ( count($tasks) > 0 ) {
				$resultsArray["next"]["tasks"] = $tasks;
			}
		}

		
		return json_encode ( $resultsArray );
		
	}
	
	// Sort the badges on task status (
	public static function sortBadges($a,$b)
	{
		
		if ( count($a) > 0 ) {
			//$key = array_keys($a)[0];
			$aStatus = $a['tasks'][0]['status'];
		}
		else {
			$aStatus = -1;
		}
		
		if ( count($b) > 0 ) {
			//$key = array_keys($b)[0];
			$bStatus = $b['tasks'][0]['status'];
		}
		else {
			$bStatus = -1;
		}
		
		
		if ( $aStatus == $bStatus ) {
			return 0;
		}
		if ( $aStatus == Badge::LOCKED ) {
			if ( $bStatus == Badge::COLLECTED ) {
				return 1;
			}
			else {
				return -1;
			}
		}
		else if ( $aStatus == Badge::UNLOCKED ) {
			if ( $bStatus == Badge::LOCKED or $bStatus == Badge::COLLECTED ) {
				return -1;
			}
			else {
				return 1;
			}
		}
		else if ( $aStatus == Badge::PENDING ) {
			if ( $bStatus == Badge::UNLOCKED or $bStatus == Badge::LOCKED or $bStatus == Badge::COLLECTED ) {
				return -1;
			}
			else {
				return 1;
			}
		}
		else if ( $aStatus == Badge::COMPLETE ) {
			return -1;
		}
		else if ( $aStatus == Badge::COLLECTED ) {
			return 1;
		}
		// if ($a->awardSeq == $b->awardSeq) {
			// return ( $a->schoolName > $b->schoolName );
		// }
		// return ($a->awardSeq < $b->awardSeq)?1:-1;
	}
		
	
	public function getAllBadgesJSON ( $module=1 ) {
		
		$resultsArray = $this->getAllBadges();
			
		return json_encode($resultsArray);
		
	}
	
	public function getAllBadges ( $module=1 ) {
		
		$this->setSummary();
		
		$badgeIds = $this->getBadgeIds ( $module );
		
		
		$resultsArray = array();
		
		foreach ( $badgeIds as $badgeId ) {
			
			$badge = new Badge ( $badgeId );
			
			//$resultsArray[$badgeId] = $badge->getTasks();
			$resultsArray[] = $badge->getTasks();

		}
		
		uasort($resultsArray,"self::sortBadges");
		
		// $errMsg = print_r ( $resultsArray, true );
		// error_log ( "badgeResults: " . $errMsg );
			
		return $resultsArray;
		
	}
	
	public static function getGroupIcons () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
			
		$query = $db->getQuery(true)
		->select("group_id, icon from BadgeGroup");
	
		$db->setQuery($query);
	
		$icons = $db->loadAssocList("group_id", "icon");
		
		return $icons;
	}
	
	
	public static function getSchoolSummary ( $schoolId, $badgeGroupId ) {
		
		// NB for school points we weight the points to be equivalent to 100 school users in total.
		// Actually going with no weighting for clarity
		
		// Count school users (students and teachers - and ecologists? - and supporters?
		$summaryResults = new \stdClass();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		$query = $db->getQuery(true)
			->select("role_id, count(*) as numUsers from SchoolUsers where school_id = " . $schoolId . " and include_points = 1")
			->group("role_id" );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$numSchoolUsers = $db->loadObjectList("role_id");
		
		
		// Get available points by role
		$query = $db->getQuery(true)
				->select("T.role_id, SUM(T.points) as points from Task T" )
				->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $badgeGroupId)
				->group("T.role_id" );
				
				
		$db->setQuery($query);
		
		//error_log("Task getAvailablePointsByGroup select query created: " . $query->dump());
		
		$totalAvailablePoints = $db->loadAssocList("role_id", "points");
			
			
		
		//foreach ( array("StudentTasks", "TeacherTasks") as $userTaskTable ) {
		
		// Students first
		$tasksQueryString = "select count(*) from StudentTasks ST1 " .
							" inner join Task T1 on T1.task_id = ST1.task_id " .
							" inner join Badge B1 on B1.badge_id = T1.badge_id " .
							" inner join SchoolUsers SU on SU.person_id = ST1.person_id and SU.school_id = " . $schoolId .
							" where B1.badge_group = " . $badgeGroupId . " and ST1.status >= " . self::COMPLETE . " and SU.include_points = 1";
		
				
		$allTasksQueryString = "select count(*) from Task T4 " .
							"INNER JOIN Badge B4 on B4.badge_id = T4.badge_id and B4.winner_type = 'STUDENT'" .
							"where B4.badge_group = " . $badgeGroupId;
		
		$allBadgesQueryString = "select count(*) from Badge B5 " .
								"where B5.winner_type = 'STUDENT' and B5.badge_group = " . $badgeGroupId;
	
		$query = $db->getQuery(true)
			->select("IFNULL(SUM(T.points),0) as numPoints, ( " . $tasksQueryString . " ) as numTasks, ( " 
						. $allTasksQueryString . " ) as totalTasksPerStudent, ( "
						. $allBadgesQueryString . " ) as totalBadgesPerStudent from StudentTasks ST")
			->innerJoin("Task T on ST.task_id = T.task_id")
			->innerJoin("SchoolUsers SU on SU.person_id = ST.person_id and SU.school_id = " . $schoolId . " and SU.include_points = 1" )
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $badgeGroupId )
			->where("ST.status >= " . self::COMPLETE );
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$studentSummary = $db->loadObject();
		
		if ( array_key_exists ( SchoolCommunity::STUDENT_ROLE, $numSchoolUsers ) ) {
			$numStudents = $numSchoolUsers[SchoolCommunity::STUDENT_ROLE]->numUsers;
		}
		else {
			$numStudents = 0;
		}
		if ( array_key_exists ( SchoolCommunity::STUDENT_ROLE, $numSchoolUsers ) ) {
			$studentPointsAvailable = $totalAvailablePoints[SchoolCommunity::STUDENT_ROLE];
		}
		else {
			$studentPointsAvailable = 0;
		}
		$studentSummary->numStudents = $numStudents;
		$studentSummary->totalTasks = $studentSummary->totalTasksPerStudent * $numStudents;
		$studentSummary->totalBadges = $studentSummary->totalBadgesPerStudent * $numStudents;
		$studentSummary->totalPointsAvailable = $studentPointsAvailable * $numStudents;
		
		
		// Now teachers
		$tasksQueryString = "select count(*) from TeacherTasks ST1 " .
							" inner join Task T1 on T1.task_id = ST1.task_id " .
							" inner join Badge B1 on B1.badge_id = T1.badge_id " .
							" inner join SchoolUsers SU on SU.person_id = ST1.person_id and SU.school_id = " . $schoolId . " and SU.role_id = " . SchoolCommunity::TEACHER_ROLE .
							" where B1.badge_group = " . $badgeGroupId . " and ST1.status >= " . self::COMPLETE . " and SU.include_points = 1";					
		
		
		$allTasksQueryString = "select count(*) from Task T4 " .
							"INNER JOIN Badge B4 on B4.badge_id = T4.badge_id and B4.winner_type = 'TEACHER'" .
							"where B4.badge_group = " . $badgeGroupId;
		
		$allBadgesQueryString = "select count(*) from Badge B5 " .
								"where B5.winner_type = 'TEACHER' and B5.badge_group = " . $badgeGroupId;
	
		$query = $db->getQuery(true)
			->select("IFNULL(SUM(T.points),0) as numPoints, ( " . $tasksQueryString . " ) as numTasks, ( " 
						. $allTasksQueryString . " ) as totalTasksPerTeacher, ( "
						. $allBadgesQueryString . " ) as totalBadgesPerTeacher from TeacherTasks ST")
			->innerJoin("Task T on ST.task_id = T.task_id")
			->innerJoin("SchoolUsers SU on SU.person_id = ST.person_id and SU.school_id = " . $schoolId . " and SU.include_points = 1" )
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $badgeGroupId )
			->where("ST.status >= " . self::COMPLETE )
			->where("SU.role_id = " . SchoolCommunity::TEACHER_ROLE);
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$teacherSummary = $db->loadObject();
		
		if ( array_key_exists ( SchoolCommunity::TEACHER_ROLE, $numSchoolUsers ) ) {
			$numTeachers = $numSchoolUsers[SchoolCommunity::TEACHER_ROLE]->numUsers;
		}
		else {
			$numTeachers = 0;
		}
		if ( array_key_exists ( SchoolCommunity::TEACHER_ROLE, $totalAvailablePoints ) ) {
			$teacherPointsAvailable = $totalAvailablePoints[SchoolCommunity::TEACHER_ROLE];
		}
		else {
			$teacherPointsAvailable = 0;
		}
		$teacherSummary->numTeachers = $numTeachers;
		$teacherSummary->totalTasks = $teacherSummary->totalTasksPerTeacher * $numTeachers;
		$teacherSummary->totalBadges = $teacherSummary->totalBadgesPerTeacher * $numTeachers;
		$teacherSummary->totalPointsAvailable = $teacherPointsAvailable * $numTeachers;
		
		
		
		// Now ecologists
		$tasksQueryString = "select count(*) from TeacherTasks ST1 " .
							" inner join Task T1 on T1.task_id = ST1.task_id " .
							" inner join Badge B1 on B1.badge_id = T1.badge_id " .
							" inner join SchoolUsers SU on SU.person_id = ST1.person_id and SU.school_id = " . $schoolId . " and SU.role_id = " . SchoolCommunity::ECOLOGIST_ROLE .
							" where B1.badge_group = " . $badgeGroupId . " and ST1.status >= " . self::COMPLETE . " and SU.include_points = 1";					
		
		
		$allTasksQueryString = "select count(*) from Task T4 " .
							"INNER JOIN Badge B4 on B4.badge_id = T4.badge_id and B4.winner_type = 'ECOLOGIST'" .
							"where B4.badge_group = " . $badgeGroupId;
		
		$allBadgesQueryString = "select count(*) from Badge B5 " .
								"where B5.winner_type = 'ECOLOGIST' and B5.badge_group = " . $badgeGroupId;
	
		$query = $db->getQuery(true)
			->select("IFNULL(SUM(T.points),0) as numPoints, ( " . $tasksQueryString . " ) as numTasks, ( " 
						. $allTasksQueryString . " ) as totalTasksPerTeacher, ( "
						. $allBadgesQueryString . " ) as totalBadgesPerTeacher from TeacherTasks ST")
			->innerJoin("Task T on ST.task_id = T.task_id")
			->innerJoin("SchoolUsers SU on SU.person_id = ST.person_id and SU.school_id = " . $schoolId . " and SU.include_points = 1" )
			->innerJoin("Badge B on B.badge_id = T.badge_id and B.badge_group = " . $badgeGroupId )
			->where("ST.status >= " . self::COMPLETE )
			->where("SU.role_id = " . SchoolCommunity::ECOLOGIST_ROLE);
			
		
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$ecologistSummary = $db->loadObject();
		
		if ( array_key_exists ( SchoolCommunity::ECOLOGIST_ROLE, $numSchoolUsers ) ) {
			$numEcologists = $numSchoolUsers[SchoolCommunity::ECOLOGIST_ROLE]->numUsers;
		}
		else {
			$numEcologists = 0;
		}
		if ( array_key_exists ( SchoolCommunity::ECOLOGIST_ROLE, $totalAvailablePoints ) ) {
			$ecologistPointsAvailable = $totalAvailablePoints[SchoolCommunity::ECOLOGIST_ROLE];
		}
		else {
			$ecologistPointsAvailable = 0;
		}
		$ecologistSummary->numTeachers = $numTeachers;
		$ecologistSummary->totalTasks = $ecologistSummary->totalTasksPerTeacher * $numEcologists;
		$ecologistSummary->totalBadges = $ecologistSummary->totalBadgesPerTeacher * $numEcologists;
		$ecologistSummary->totalPointsAvailable = $ecologistPointsAvailable * $numEcologists;
		
		
		
		$school = new \stdClass();
		$school->numUsers = $numStudents + $numTeachers + $numEcologists;
		
		if ( $school->numUsers > 0 ) {
			//$weighting = 60/$school->numUsers;
			// $school->weightedPoints = round(($teacherSummary->numPoints + $studentSummary->numPoints)*$weighting);
			// $teacherSummary->weightedPoints = round ( $teacherSummary->numPoints*$weighting );
			// $studentSummary->weightedPoints = round ( $studentSummary->numPoints*$weighting );
			
			$weighting = 1;
			$school->weightedPoints = $teacherSummary->numPoints + $studentSummary->numPoints + $ecologistSummary->numPoints;
			$school->pointsAvailable = $teacherSummary->totalPointsAvailable + $studentSummary->totalPointsAvailable + $ecologistSummary->totalPointsAvailable;
			$teacherSummary->weightedPoints = $teacherSummary->numPoints;
			$studentSummary->weightedPoints = $studentSummary->numPoints;
			$ecologistSummary->weightedPoints = $ecologistSummary->numPoints;
		}
		else {
			$school->weightedPoints = 0;
			$school->pointsAvailable = 0;
			$teacherSummary->weightedPoints = 0;
			$studentSummary->weightedPoints = 0;
			$ecologistSummary->weightedPoints = 0;
		}
		
				
		$summaryResults->student = $studentSummary;
		$summaryResults->teacher = $teacherSummary;
		$summaryResults->ecologist = $ecologistSummary;
		$summaryResults->school = $school;
		
		return $summaryResults;



	}
	
	
}



?>

