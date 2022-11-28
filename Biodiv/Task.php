<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Task {
	
	private $taskId;
	private $taskDetail;
	
	private $personId;
	private $roleId;
	private $userTaskTable;
	
	
	function __construct( $taskId, $personId = null )
	{
		if ( $taskId ) {
			
			$this->taskId = $taskId;
			
			if ( $personId ) {
				$this->personId = $personId;
			}
			else {
				$this->personId = userID();
			}
			
			$this->roleId = SchoolCommunity::STUDENT_ROLE;
			$this->userTaskTable = "StudentTasks";
			
			if ( SchoolCommunity::isTeacher( $this->personId ) ) {
				$this->roleId = SchoolCommunity::TEACHER_ROLE;
				$this->userTaskTable = "TeacherTasks";
			}
			else if ( SchoolCommunity::isEcologist( $this->personId ) ) {
				$this->roleId = SchoolCommunity::ECOLOGIST_ROLE;
				$this->userTaskTable = "TeacherTasks";
			}
		
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("T.*, B.badge_group, O.option_name as group_name, B.name as badge_name, B.winner_type, " .
					"B.module_id, M.icon as module_icon, M.tag_id as module_tag_id, B.lock_level, ST.task_type, ST.related_json, ST.threshold, UT.status, UT.set_id from Task T")
				->innerJoin("Badge B on B.badge_id = T.badge_id")
				->innerJoin("Options O on O.option_id = B.badge_group")
				->innerJoin("Module M on M.module_id = B.module_id")
				->leftJoin("SystemTasks ST on T.task_id = ST.task_id")
				->leftJoin($this->userTaskTable . " UT on UT.task_id = T.task_id and UT.person_id = " . $this->personId )
				->where("T.task_id = " . $this->taskId );
				
			
			$db->setQuery($query);
			
			//error_log("Task constructor select query created: " . $query->dump());
			
			$this->taskDetail = $db->loadObject();
			
		}
	
	}
	
	public static function createFromStudentTaskId ( $studentTaskId ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
			
		$query = $db->getQuery(true)
			->select("task_id, person_id from StudentTasks")
			->where("st_id = " . $studentTaskId );
		
		$db->setQuery($query);
		
		//error_log("Task::createFromStudentTaskId select query created: " . $query->dump());
		
		$studentTask = $db->loadObject();
		
		//error_log ( "Constructing task with task id = " . $studentTask->task_id . ", person_id " . $studentTask->person_id );
		
		$task = new self ( $studentTask->task_id, $studentTask->person_id );
		
		return $task;		
		
	}
	
	public static function getLinkedTask ( $resourceFileId ) {
		
		$userId = userID();
		
		$userTaskTable = "StudentTasks";
			
		if ( SchoolCommunity::isTeacher( $userId ) ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
			
		$query = $db->getQuery(true)
			->select("UT.task_id from " . $userTaskTable . " UT ")
			->innerJoin("Resource R on R.set_id = UT.set_id and R.resource_id = " . $resourceFileId )
			->where("UT.person_id = " . $userId  )
			->where("R.deleted = 0");
		
		$db->setQuery($query);
		
		//error_log("Task getLinkedTask select query created: " . $query->dump());
		
		$taskId = $db->loadResult();
		
		if ( $taskId ) {
			return new self ( $taskId );
		}
		else {
			return null;
		}
	}
	
	public static function createFromResourceSet ( $uploadedSet ) {
		
		$userId = userID();
		
		$userTaskTable = "StudentTasks";
			
		if ( SchoolCommunity::isTeacher( $userId ) or SchoolCommunity::isEcologist( $userId ) ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
			
		$query = $db->getQuery(true)
			->select("UT.task_id from " . $userTaskTable . " UT ")
			->where("UT.set_id = " . $uploadedSet . " and UT.person_id = " . $userId  );
		
		$db->setQuery($query);
		
		//error_log("Task createFromResourceSet select query created: " . $query->dump());
		
		$taskId = $db->loadResult();
		
		if ( $taskId ) {
			return new self ( $taskId );
		}
		else {
			return null;
		}
	}
	
	
	public static function createFromSchoolResourceSet ( $uploadedSet ) {
		
		$userId = userID();
		
		$tasks = array();
		
		if ( $userId ) {
			
			$userTaskTable = "StudentTasks";
				
			$db = \JDatabaseDriver::getInstance(dbOptions());
				
			$query = $db->getQuery(true)
				->select("UT.task_id, UT.person_id from " . $userTaskTable . " UT ")
				->where("UT.set_id = " . $uploadedSet   );
			
			$db->setQuery($query);
			
			//error_log("Task createFromSchoolResourceSet select query created: " . $query->dump());
			
			$tasks = $db->loadObjectList();
		}
		
		if ( count($tasks) > 0 ) {
			return new self ( $tasks[0]->task_id, $tasks[0]->person_id );
		}
		else {
			return null;
		}
	}
	
	
	public function getArticleId () {
		if ( $this->taskId ) {
			return $this->taskDetail->article_id;
		}
		else {
			return null;
		}
	}
	
	public function getTaskName() {
		
		return $this->taskDetail->name;
	}
	
	public function getTaskPerson() {
		
		return $this->personId;
	}
	
	public function getStatus() {
		
		return $this->taskDetail->status;
	}
	
	public function getLockLevel() {
		
		return $this->taskDetail->lock_level;
	}
	
	public function getBadgeGroup() {
		
		return $this->taskDetail->badge_group;
	}
	
	public function getBadgeGroupName() {
		
		return $this->taskDetail->group_name;
	}
	
	public function getBadgeName() {
		
		return $this->taskDetail->badge_name;
	}
	
	public function getModuleTagId() {
		
		return $this->taskDetail->module_tag_id;
	}
	
	public function getSpecies() {
		
		return $this->taskDetail->species;
	}
	
	public function getSpeciesArticleId() {
		
		return $this->taskDetail->species_article_id;
	}
	
	// Unlock species
	public function unlock () {
		
		if ( $this->taskDetail->status == Badge::COLLECTED ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName('species_unlocked') . ' = 1',
				$db->quoteName('timestamp') . ' = ' . "NOW()"
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('task_id') . ' = ' . $this->taskId,
				$db->quoteName('person_id') . ' = ' . $this->personId
			);

			$query->update($this->userTaskTable)->set($fields)->where($conditions);
			
			$db->setQuery($query);
			$result = $db->execute();
			
			if ( $result ) {
				
				SchoolCommunity::addNotification ( \JText::_("COM_BIODIV_TASK_YOU_UNLOCKED") . ' ' . 
								\JText::_("COM_BIODIV_TASK_LEARN_MORE"), $this->personId );
			}
			
		}
	}
	
	public function collect () {
		
		if ( $this->taskDetail->status == Badge::COMPLETE ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName('status') . ' = ' . Badge::COLLECTED,
				$db->quoteName('timestamp') . ' = ' . "NOW()"
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('task_id') . ' = ' . $this->taskId,
				$db->quoteName('person_id') . ' = ' . $this->personId
			);

			$query->update($this->userTaskTable)->set($fields)->where($conditions);
			
			$db->setQuery($query);
			$result = $db->execute();
			
			
		}
	}
	
	public function done () {
		
		if ( $this->taskDetail->status == Badge::UNLOCKED ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
					
			$newStatus = Badge::COMPLETE;
			if ( SchoolCommunity::isStudent() ) {
				$newStatus = Badge::PENDING;
			}
			
			$fields = array(
				$db->quoteName('status') . ' = ' . $newStatus,
				$db->quoteName('timestamp') . ' = ' . "NOW()",
				$db->quoteName('complete_date') . ' = ' . "DATE(NOW())"
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('task_id') . ' = ' . $this->taskId,
				$db->quoteName('person_id') . ' = ' . $this->personId
			);

			$query->update($this->userTaskTable)->set($fields)->where($conditions);
			
			//error_log("query created: " . $query->dump());
			
			$db->setQuery($query);
			$result = $db->execute();
			
			SchoolCommunity::logEvent ( false, SchoolCommunity::SCHOOL,  'completed the ' . $this->taskDetail->badge_name . ' ' . 'badge'  );
			
		
		}
	}
	
	public function approve () {
		
		// We are approving the task of a student - the current user is different from the Task user (the student)
		if ( SchoolCommunity::isTeacher() ) {
			
			if ( SchoolCommunity::isMyStudent ( $this->personId ) ) {
				
				// Allow for teacher mistake when approving/rejecting
				if ( $this->taskDetail->status == Badge::PENDING or $this->taskDetail->status == Badge::UNLOCKED ) {
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
				
					$query = $db->getQuery(true);
							
					$fields = array(
						$db->quoteName('status') . ' = ' . Badge::COMPLETE,
						$db->quoteName('timestamp') . ' = ' . "NOW()"
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('task_id') . ' = ' . $this->taskId,
						$db->quoteName('person_id') . ' = ' . $this->personId
					);

					$query->update($this->userTaskTable)->set($fields)->where($conditions);
					
					$db->setQuery($query);
					$result = $db->execute();
					
					SchoolCommunity::addNotification ( \JText::_("COM_BIODIV_TASK_WELL_DONE") . ' ' . $this->taskDetail->name . "  " . 
								\JText::_("COM_BIODIV_TASK_ACTIVITY_APPROVED"), $this->personId );
					
				
				}
				else {
					error_log ("Approve called on non-pending task");
				}
			}
			else {
				error_log ("Approve called on student who is not mine");
			}
		}
	}
	
	public function reject () {
		
		// We are approving the task of a student - the current user is different from the Task user (the student)
		if ( SchoolCommunity::isTeacher() ) {
			
			if ( SchoolCommunity::isMyStudent ( $this->personId ) ) {
				
				// Allow for teacher mistake when approving/rejecting
				if ( $this->taskDetail->status == Badge::PENDING or $this->taskDetail->status == Badge::COMPLETE ) {
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
				
					$query = $db->getQuery(true);
							
					$fields = array(
						$db->quoteName('status') . ' = ' . Badge::UNLOCKED,
						$db->quoteName('timestamp') . ' = ' . "NOW()"
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('task_id') . ' = ' . $this->taskId,
						$db->quoteName('person_id') . ' = ' . $this->personId
					);

					$query->update($this->userTaskTable)->set($fields)->where($conditions);
					
					$db->setQuery($query);
					$result = $db->execute();
					
					SchoolCommunity::addNotification ( \JText::_("COM_BIODIV_TASK_PROBLEM") . ' ' . $this->taskDetail->name . "  " . 
								\JText::_("COM_BIODIV_TASK_YOU_ASK_TEACHER"), $this->personId, false );
					
				
				}
				else {
					error_log ("Reject called on non-pending/complete task");
				}
			}
			else {
				error_log ("Reject called on student who is not mine");
			}
		}
	}
	
	public function checkTaskComplete () {
		
		$isComplete = false;
		
		if ( $this->taskDetail->counted_by == "SYSTEM" ) {
			
			if ( $this->taskDetail->task_type == "CLASSIFY" ) {
				
				$threshold = $this->taskDetail->threshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct photo_id ) from Animal")
					->where("person_id = " . userID() );
				
				$db->setQuery($query);
				
				//error_log("Task constructor select query created: " . $query->dump());
				
				$numClassifies = $db->loadResult();
				
				if ( $numClassifies >= $threshold ) $isComplete = true;
			}
			else if ( $this->taskDetail->task_type == "UPLOAD" ) {
				
				$threshold = $this->taskDetail->threshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct upload_id ) from Upload")
					->where("person_id = " . userID() );
				
				$db->setQuery($query);
				
				//error_log("Task constructor select query created: " . $query->dump());
				
				$numUploads = $db->loadResult();
				
				if ( $numUploads >= $threshold ) $isComplete = true;
			}
			else if ( $this->taskDetail->task_type == "QUIZ" ) {
				
				$threshold = $this->taskDetail->threshold;
				$relatedJSON = $this->taskDetail->related_json;
				
				$jsonObj = json_decode ( $relatedJSON );
				
				if ( property_exists ( $jsonObj, "topicId" ) and property_exists ( $jsonObj, "minScore" ) ) {
					$topicId = $jsonObj->topicId;
					$minScore = $jsonObj->minScore;
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
				
					$query = $db->getQuery(true)
						->select("count(*) from UserTest")
						->where("person_id = " . $this->personId )
						->where("topic_id = " . $topicId )
						->where("score > " . $minScore );
					
					$db->setQuery($query);
					
					//error_log("Task checkTaskComplete select query created: " . $query->dump());
					
					$numQuizzes = $db->loadResult();
					
					if ( $numQuizzes >= $threshold ) $isComplete = true;
				}
				else {
					error_log ("Task::checkTaskComplete system task json incorrectly configured");
					$isComplete = false;
				}
			}
			
		}
		else if ( $this->taskDetail->counted_by == "USER" ) {
		}
		
		return $isComplete;
	}
	
	// Link the resource set to this task and update it to be pending or done.
	public function linkResourceSet ( $setId, $doneAsSchool = false, $approve = false ) {
		
		//$personId = userID();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		
		if ( $approve ) {
			$status = Badge::COMPLETE;
		}
		else {
			$status = Badge::PENDING;
		}
				
		$fields = array(
			$db->quoteName('set_id') . ' = ' . $setId,
			$db->quoteName('status') . ' = ' . $status,
			$db->quoteName('timestamp') . ' = ' . "NOW()"
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('task_id') . ' = ' . $this->taskId,
			$db->quoteName('person_id') . ' = ' . $this->personId,
			$db->quoteName('status') . ' < ' . Badge::PENDING
		);

		$query->update($this->userTaskTable)->set($fields)->where($conditions);
		
		$db->setQuery($query);
		$result = $db->execute();
		
		if ( $result ) {
			if ( !$doneAsSchool and $this->personId == userID() ) {
				SchoolCommunity::logEvent ( false, SchoolCommunity::SCHOOL,  'completed the ' . $this->taskDetail->badge_name . ' ' . 'badge' );
			}
		}
		
	}
	
	
	public static function checkSystemTasks () {
		
		
		$personId = userID();
		
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher() or SchoolCommunity::isEcologist() ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("UT.task_id from " . $userTaskTable . " UT")
			->innerJoin("SystemTasks ST2 on ST2.task_id = UT.task_id and UT.person_id = " . $personId)
			->where("UT.status < " . Badge::COMPLETE );
			
		
		$db->setQuery($query);
		
		$systemTasks = $db->loadColumn();
		
		foreach ( $systemTasks as $taskId ) {
			
			$systemTask = new self ( $taskId );
			
			if ( $systemTask->checkTaskComplete() ) {
				
				// Update StudentTasks table
				
				$query = $db->getQuery(true);
						
				$fields = array(
					$db->quoteName('status') . ' = ' . Badge::COMPLETE
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('task_id') . ' = ' . $taskId,
					$db->quoteName('person_id') . ' = ' . $personId,
					$db->quoteName('status') . ' != ' . Badge::COLLECTED
				);

				$query->update($userTaskTable)->set($fields)->where($conditions);
				
				$db->setQuery($query);
				$result = $db->execute();
				
				SchoolCommunity::addNotification($systemTask->getTaskName() . " " . \JText::_("COM_BIODIV_TASK_COMPLETED"));
				SchoolCommunity::logEvent ( false, SchoolCommunity::SCHOOL,  'completed the ' . $systemTask->getBadgeName() . ' ' . 'badge'  );
			}
		}
	}
	
	public static function getTotalUserPoints () {
		
		$personId = userID();
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher() or SchoolCommunity::isEcologist() ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("SUM(T.points) from Task T" )
			->innerJoin($userTaskTable . " UT on T.task_id = UT.task_id and UT.person_id = " . $personId)
			->where("UT.status >= " . Badge::COMPLETE );
			
		
		$db->setQuery($query);
		
		//error_log("Task getTotalUserPoints select query created: " . $query->dump());
		
		$totalPoints = $db->loadResult();
		
		return $totalPoints;
		
	}
	
	
	public static function getTotalUserPointsByModule () {
		
		$personId = userID();
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher() or SchoolCommunity::isEcologist() ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("B.module_id as module_id, SUM(T.points) as points from Task T" )
			->innerJoin("Badge B on B.badge_id = T.badge_id" )
			->innerJoin($userTaskTable . " UT on T.task_id = UT.task_id and UT.person_id = " . $personId)
			->where("UT.status >= " . Badge::COMPLETE )
			->group("module_id");
			
		
		$db->setQuery($query);
		
		//error_log("Task getTotalUserPoints select query created: " . $query->dump());
		
		$totalPoints = $db->loadObjectList("module_id");
		
		return $totalPoints;
		
	}
	
	
	public static function getTotalAvailablePoints () {
		
		$personId = userID();
		
		$totalPoints = null;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("T.role_id as role_id, SUM(T.points) as points from Task T" )
				->group("T.role_id" );
				
				
			$db->setQuery($query);
			
			//error_log("Task getTotalTaskPoints select query created: " . $query->dump());
			
			$totalPoints = $db->loadObjectList("role_id");
		}
		
		return $totalPoints;
		
	}
	
	
	public static function getUserPointsByGroupOrig ( $roleId ) {
		
		$personId = userID();
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher() ) {
			$userTaskTable = "TeacherTasks";
		}
		
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
			->select("B.badge_group, SUM(T.points) as points from Task T" )
			->innerJoin("Badge B on T.badge_id = B.badge_id")
			->innerJoin($userTaskTable . " UT on T.task_id = UT.task_id and UT.person_id = " . $personId)
			->where("UT.status >= " . Badge::COMPLETE )
			->group("B.badge_group");
			
			$db->setQuery($query);
			
			//error_log("Task getUserPointsByGroup select query created: " . $query->dump());
			
			$userPoints = $db->loadAssocList("badge_group", "points");
		}
		
		return $userPoints;
		
	}
	
	
	public static function getUserPointsByGroup ( $roleId ) {
		
		$personId = userID();
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher() ) {
			$userTaskTable = "TeacherTasks";
		}
		
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
			->select("B.module_id, B.badge_group, SUM(T.points) as points from Task T" )
			->innerJoin("Badge B on T.badge_id = B.badge_id")
			->innerJoin($userTaskTable . " UT on T.task_id = UT.task_id and UT.person_id = " . $personId)
			->where("UT.status >= " . Badge::COMPLETE )
			->group("B.module_id, B.badge_group");
			
			$db->setQuery($query);
			
			//error_log("Task getUserPointsByGroup select query created: " . $query->dump());
			
			$userPoints = $db->loadObjectList();
		}
		
		$userPointsArray = array();
		
		foreach ( $userPoints as $points ) {
			$moduleId = $points->module_id;
			$badgeGroup = $points->badge_group;
			if ( !array_key_exists($moduleId, $userPointsArray ) ) {
				$userPointsArray[$moduleId] = array();
			}
			$userPointsArray[$moduleId][$badgeGroup] = $points->points;
		}
		
		return $userPointsArray;
		
	}
	
	
	public static function getAvailablePointsByGroup ( $roleId ) {
		
		$personId = userID();
		
		$totalPoints = null;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("M.module_id, BG.group_id as badge_group, SUM(T.points) as points from Task T" )
				->innerJoin("Badge B on B.badge_id = T.badge_id")
				->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
				->innerJoin("Module M on M.module_id = B.module_id")
				->where("T.role_id = " . $roleId )
				->group("M.module_id, BG.group_id" );
				
				
			$db->setQuery($query);
			
			//error_log("Task getAvailablePointsByGroup select query created: " . $query->dump());
			
			//$totalPoints = $db->loadAssocList("badge_group", "points");
			$moduleGroupPoints = $db->loadObjectList();
		}
		
		$totalPoints = array();
		foreach ( $moduleGroupPoints as $row ) {
			if ( !array_key_exists($row->badge_group, $totalPoints) ) {
				$totalPoints[$row->badge_group] = array();
			}
			$totalPoints[$row->badge_group][$row->module_id] = $row->points;
		}
		
		return $totalPoints;
		
	}
	
	
	public static function getSchoolEcologistPoints ( $schoolId ) {
		
		$personId = userID();
		
		$totalPoints = 0;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SUM(T.points) FROM TeacherTasks TT" )
				->innerJoin("SchoolUsers SU on TT.person_id = SU.person_id and SU.role_id = " .SchoolCommunity::ECOLOGIST_ROLE. " and SU.school_id = " . $schoolId )
				->innerJoin("Task T on T.task_id = TT.task_id")
				->where("TT.status > " . Badge::PENDING);
								
			
			$db->setQuery($query);
			
			//error_log("Task getSchoolTeacherPoints select query created: " . $query->dump());
			
			$totalPoints = $db->loadResult();
		}
		
		return $totalPoints;
		
	}
	
	
	public static function getSchoolTeacherPoints ( $schoolId ) {
		
		$personId = userID();
		
		$totalPoints = 0;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SUM(T.points) FROM TeacherTasks TT" )
				->innerJoin("SchoolUsers SU on TT.person_id = SU.person_id and SU.role_id = " .SchoolCommunity::TEACHER_ROLE. " and SU.school_id = " . $schoolId )
				->innerJoin("Task T on T.task_id = TT.task_id")
				->where("TT.status > " . Badge::PENDING);
								
			
			$db->setQuery($query);
			
			//error_log("Task getSchoolTeacherPoints select query created: " . $query->dump());
			
			$totalPoints = $db->loadResult();
		}
		
		return $totalPoints;
		
	}
	
	
	public static function getSchoolStudentPoints ( $schoolId ) {
		
		$personId = userID();
		
		$totalPoints = 0;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("SUM(T.points) FROM StudentTasks ST" )
				->innerJoin("SchoolUsers SU on ST.person_id = SU.person_id and SU.role_id = " .SchoolCommunity::STUDENT_ROLE. " and SU.school_id = " . $schoolId )
				->innerJoin("Task T on T.task_id = ST.task_id")
				->where("ST.status > " . Badge::PENDING);
								
			
			$db->setQuery($query);
			
			//error_log("Task getSchoolStudentPoints select query created: " . $query->dump());
			
			$totalPoints = $db->loadResult();
		}
		
		return $totalPoints;
		
	}
	
	
	
	
	
	public static function getNoFileTasksForApproval () {
		
		$personId = userID();
		
		$userTaskTable = "StudentTasks";
		
		$teacherDetails = SchoolCommunity::getTeacherDetails();
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$allTasks = array();
		
		if ( $teacherDetails ) {
			
			foreach ( $teacherDetails as $teacherSchool ) {
				
				$schoolId = $teacherSchool["school_id"];
			
				$db = \JDatabaseDriver::getInstance(dbOptions());
				
				$query = $db->getQuery(true)
					->select("U.username, ST.st_id, B.name as badge_name, O.option_name as badge_group, T.* from Task T" )
					->innerJoin("StudentTasks ST on T.task_id = ST.task_id")
					->innerJoin($userDb . "." . $prefix ."users U on ST.person_id = U.id")
					->innerJoin("Badge B on B.badge_id = T.badge_id")
					->innerJoin("Options O on B.badge_group = O.option_id")
					->innerJoin("SchoolUsers SU on ST.person_id = SU.person_id and SU.school_id = " . $schoolId)
					->where("ST.set_id is NULL and ST.status = " . Badge::PENDING );
				
				$db->setQuery($query);
				
				//error_log("Task getNoFileTasksForApproval select query created: " . $query->dump());
				
				$tasks = $db->loadObjectList();
				
				$allTasks = $allTasks + $tasks;
			
			}
		
		}
		
		return $allTasks;
		
	}
	
	
	public static function getAllSchoolTasks () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "StudentTasks";
		
		$allTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.module_id as module_id, M.class_stem, M.icon, M.name as module_name, B.badge_id as badge_id, B.name as badge_name, BG.icon as badge_icon, T.* from Task T" )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Module M on B.module_id = M.module_id")
			->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("T.role_id = " . SchoolCommunity::STUDENT_ROLE )
			->where("M.active = 1")
			->order("B.module_id, O.option_id, B.lock_level");
		
		$db->setQuery($query);
		
		//error_log("Task getAllStudentTasks select query created: " . $query->dump());
		
		$allTasks = $db->loadObjectList();
		
		return $allTasks;
		
	}
	
		
	// Get all student tasks that are unlocked and user counted - no complete ones
	public static function getAllStudentTasks () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "StudentTasks";
		
		$allTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.badge_id as badge_id, B.name as badge_name, T.* from Task T" )
			->innerJoin($userTaskTable . " UT on UT.task_id = T.task_id and UT.status = " . Badge::UNLOCKED . " and UT.person_id = " . $personId )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("T.counted_by = 'USER' and T.role_id = " . SchoolCommunity::STUDENT_ROLE )
			->order("O.option_id, B.lock_level");
		
		$db->setQuery($query);
		
		//error_log("Task getAllStudentTasks select query created: " . $query->dump());
		
		$allTasks = $db->loadObjectList();
		
		return $allTasks;
		
	}
	
	// All pending, complete and collected tasks for this logged in student, used eg for celebration
	public static function getAllDoneStudentTasks () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "StudentTasks";
		
		$allTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.badge_id as badge_id, B.name as badge_name, T.*, UT.status from Task T" )
			->innerJoin($userTaskTable . " UT on UT.task_id = T.task_id and UT.status > " . Badge::UNLOCKED . " and UT.person_id = " . $personId )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("T.role_id = " . SchoolCommunity::STUDENT_ROLE )
			->order("UT.timestamp DESC");
		
		$db->setQuery($query);
		
		//error_log("Task getAllStudentTasks select query created: " . $query->dump());
		
		$allTasks = $db->loadObjectList();
		
		return $allTasks;
		
	}
	
	
	// All available student tasks - used eg for teacher or ecologist view of student tasks
	public static function getAllStudentTasksToView ( $groupId, $moduleId = 1 ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "StudentTasks";
		
		$allTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.module_id as module_id, M.class_stem, M.icon as module_icon, M.name as module_name, B.badge_id as badge_id, B.name as badge_name, ".
			" B.badge_image as badge_image, B.unlocked_image as unlocked_image, B.locked_image as locked_image, BG.icon as icon, " .
			" T.*, '.Badge::UNLOCKED.' as status from Task T" )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group and BG.group_id = " . $groupId)
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("T.role_id = " . SchoolCommunity::STUDENT_ROLE )
			->where("B.module_id = " . $moduleId )
			->order("B.lock_level");
		
		$db->setQuery($query);
		
		//error_log("Task getAllStudentTasks select query created: " . $query->dump());
		
		$allTasks = $db->loadObjectList();
		
		return $allTasks;
		
	}
	
	
	// All available student tasks - used eg for teacher or ecologist view of student tasks
	public static function getAllTeacherTasksToView ( $groupId, $moduleId = 1 ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "TeacherTasks";
		
		$allTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.module_id as module_id, M.class_stem, M.icon as module_icon, M.name as module_name, B.badge_id as badge_id, B.name as badge_name, ".
			" B.badge_image as badge_image, B.unlocked_image as unlocked_image, B.locked_image as locked_image, BG.icon as icon, " .
			" T.*, '.Badge::UNLOCKED.' as status from Task T" )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group and BG.group_id = " . $groupId)
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("T.role_id = " . SchoolCommunity::TEACHER_ROLE )
			->where("B.module_id = " . $moduleId )
			->order("B.lock_level");
		
		$db->setQuery($query);
		
		//error_log("Task getAllStudentTasks select query created: " . $query->dump());
		
		$allTasks = $db->loadObjectList();
		
		return $allTasks;
		
	}
	
	
	// Get all student tasks that are pending, complete or collected for teacher view
	public static function getAllDoneTasks () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$schoolIds = array_column ( $schoolRoles, "school_id" );
		$schoolStr = implode ( ',', $schoolIds );
		
		$userTaskTable = "StudentTasks";
		
		$allTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("UT.st_id, UT.person_id, UT.set_id, UT.status, O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.module_id as module_id, M.class_stem, M.icon as module_icon, M.name as module_name, B.badge_id as badge_id, B.name as badge_name, T.* from Task T" )
			->innerJoin($userTaskTable . " UT on UT.task_id = T.task_id and UT.status > " . Badge::UNLOCKED )
			->innerJoin("SchoolUsers SU on SU.person_id = UT.person_id and SU.school_id in (".$schoolStr.")" )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("T.role_id = " . SchoolCommunity::STUDENT_ROLE )
			->order("UT.status, UT.timestamp DESC");
		
		$db->setQuery($query);
		
		//error_log("Task getAllDoneTasks select query created: " . $query->dump());
		
		$allTasks = $db->loadObjectList();
		
		return $allTasks;
		
	}
	
	// Get all student tasks that are match the status
	public static function countStudentTasks ( $badgeStatus ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return 0;
		}
		
		$userTaskTable = "StudentTasks";
		
		$numTasks = 0;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from Task T" )
			//->innerJoin($userTaskTable . " UT on UT.task_id = T.task_id and UT.status = " . Badge::COMPLETE )
			->innerJoin($userTaskTable . " UT on UT.task_id = T.task_id and UT.status = " . $badgeStatus )
			->where("T.role_id = " . SchoolCommunity::STUDENT_ROLE )
			->where("UT.person_id = " . $personId );
		
		$db->setQuery($query);
		
		//error_log("Task getAllDoneTasks select query created: " . $query->dump());
		
		$numTasks = $db->loadResult();
		
		return $numTasks;
		
	}
	
	// Get all student tasks that are match the status
	public static function countMyStudentsTasks ( $badgeStatus ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "StudentTasks";
		
		$numTasks = 0;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("count(*) from " . $userTaskTable . " UT" )
			->innerJoin("SchoolUsers SU on SU.person_id = UT.person_id and SU.role_id = " . SchoolCommunity::STUDENT_ROLE)
			->innerJoin("SchoolUsers SU2 on SU.school_id = SU2.school_id and SU2.role_id = " .SchoolCommunity::TEACHER_ROLE. " and SU2.person_id = " . $personId )
			->where("UT.status = " . $badgeStatus );
		
		$db->setQuery($query);
		
		//error_log("Task getAllDoneTasks select query created: " . $query->dump());
		
		$numTasks = $db->loadResult();
		
		return $numTasks;
		
	}
	
	// Get all student tasks that are unlocked and user counted - no complete ones
	public static function getSuggestedTasks ( $maxTasks, $module = 1 ) {
		
		// Would be good to add teacher suggested tasks here
		
		$personId = userID();
		
		if ( !$personId ) {
			return array();
		}
		
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher() ) {
			$userTaskTable = "TeacherTasks";
		}
		
		
		$suggestedTasks = array();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("O.option_id as group_id, O.option_name as badge_group, OD.value as color_class, B.module_id as module_id, M.class_stem, M.icon as module_icon, M.name as module_name, B.badge_id as badge_id, B.name as badge_name, " .
				" B.badge_image as badge_image, B.unlocked_image as unlocked_image, B.locked_image as locked_image, BG.icon as icon, UT.status, T.name as task_name, T.* from Task T" )
			->innerJoin($userTaskTable . " UT on UT.task_id = T.task_id and UT.status = " . Badge::UNLOCKED . " and UT.person_id = " . $personId )
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin("Options O on B.badge_group = O.option_id")
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote("colorclass") )
			->where("B.module_id = " . $module )
			->order("RAND()");
		
		$db->setQuery($query, 0, $maxTasks);
		
		//error_log("Task getSuggestedTasks select query created: " . $query->dump());
		
		$suggestedTasks = $db->loadObjectList();
		
		return $suggestedTasks;
		
	}
	
	
}



?>

