<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Award {
	
	private $schoolUser;
	private $personId;
	private $awardId;
	private $type;
	private $name;
	private $level;
	private $image;
	private $uncollectedImage;
	private $certificate;
	private $collected;
	private $userAwardTable;
	private $classId;
	private $awardDate;
	
	
	function __construct( $schoolUser, $classId, $awardId, $type, $name, $level, $image, $uncollectedImage, $certificate, $collected, $awardDate )
	{
		
		if ( !$schoolUser ) {
			$this->schoolUser = SchoolCommunity::getSchoolUser();
		}
		else {
			$this->schoolUser = $schoolUser;
		}
		$this->personId = $this->schoolUser->person_id;
		$this->userAwardTable = "StudentAwards";
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$this->userAwardTable = "TeacherAwards";
		}
		
		$this->classId = $classId;
		$this->awardId = $awardId;
		$this->type = $type;
		$this->name = $name;
		$this->level = $level;
		$this->image = $image;
		$this->uncollectedImage = $uncollectedImage;
		$this->certificate = $certificate;
		$this->collected = $collected;
		$this->awardDate = $awardDate;
		
	}
	
	
	public function getAwardId() {
		return $this->awardId;
	}
	
	
	public function getName() {
		return $this->name;
	}
	
	
	public function getWhoFor() {
		
		if ( $this->classId ) {
			$classDetails = SchoolCommunity::getClassDetails($this->schoolUser, $this->classId);
			return $classDetails->name;
		}
		else {
			return $this->schoolUser->username;
		}
	}
	
	
	public function getCertificate () {
		return $this->certificate;
	}
	
	
	public function getAwardDate() {
		return $this->awardDate;
	}
	
	
	public function getDisplayImage() {
		if ( $this->collected ) {
			return $this->image;
		}
		else {
			return $this->uncollectedImage;
		}
	}
	
	
	public function isNew() {
		return !$this->collected;
	}
	
	
	public function isCollected() {
		return $this->collected;
	}
	
	
	public function collect () {
		
		$personId = userID();
		
		if ( $personId == $this->personId ) {
			
			$this->collected = 1;
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true);

			// Fields to update.
			$fields = array(
				$db->quoteName('collected') . ' = 1',

			);

			// Conditions for which records should be updated.
			if ( $this->classId ) {
				$conditions = array(
					$db->quoteName('class_id') . ' = ' . $this->classId,
					$db->quoteName('award_id') . ' = ' . $this->awardId
				);
			}
			else {
				$conditions = array(
					$db->quoteName('person_id') . ' = ' . $personId,
					$db->quoteName('award_id') . ' = ' . $this->awardId
				);
			}
			
			$query->update($db->quoteName($this->userAwardTable))->set($fields)->where($conditions);

			$db->setQuery($query);
			
			error_log("Award::collect update query created: " . $query->dump());

			$success = $db->execute();
			
			if(!$success){
				error_log ( $userAwardTable . " insert failed" );
			}
		}	
	}
	
	
	public function printAward ( $includeName = true ) {
		
		print '<a id="awardBtn_'.$this->awardId.'" class="awardBtn" >';
		print '<div class="awarditem">';
		if ( $this->collected ) {
			$animateClass = 'expandImage';
			if ( $this->classId ) {
				$animateClass = 'wobbleImage';
			}
			print '<img src="'.$this->image.'" class="img-responsive '.$animateClass.'">';
		}
		else {
			print '<img src="'.$this->uncollectedImage.'" class="img-responsive ">';
		}
		if ( $includeName ) {
			print '<div class="awardName h4 text-center">'.$this->name.'</div>';
		}
		print '</div>';
		print '</a>';
	}
	
	
	public static function createFromId ( $schoolUser, $classId, $awardId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userAwardTable = "StudentAwards";
		$isTeacher = false;
		$userAwardWhere = "UA.person_id = " . $personId;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$isTeacher = true;
			$userAwardTable = "TeacherAwards";
			$userAwardWhere = "UA.class_id = " . $classId;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.award_id, A.type, A.name, A.level, A.image, A.uncollected_image, A.certificate, UA.collected, ".
				"DATE_FORMAT(UA.timestamp, '%D %M %Y') as award_date from " . $userAwardTable . " UA")
			->innerJoin("Award A on A.award_id = UA.award_id")
			->where("A.role_id = " . $schoolUser->role_id )
			->where($userAwardWhere)
			->where("A.award_id = " . $awardId );
			
		$db->setQuery($query);
		
		//error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$award = $db->loadObject();
		
		$newAward = new self ( $schoolUser, $classId, $award->award_id, $award->type, $award->name, $award->level, 
								$award->image, $award->uncollected_image, $award->certificate, $award->collected, $award->award_date );
		
		
		return $newAward;
	}
	
	
	
	public static function getNewAwardId ( $schoolUser ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
			
		if ( $schoolUser ) {
			$personId = $schoolUser->person_id;
			
			$userAwardTable = "StudentAwards";
			$isTeacher = false;
			$userAwardWhere = "UA.person_id = " . $personId;
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				$isTeacher = true;
				$userAwardTable = "TeacherAwards";
				$userAwardWhere = "UA.class_id = " . $classId;
			}
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("UA.award_id from " . $userAwardTable . " UA")
				->where($userAwardWhere)
				->where("UA.collected = 0")
				->order("A.level");
				
			$db->setQuery($query);
			
			//error_log("unlockBadges new tasks query created: " . $query->dump());
			
			$awardId = $db->loadResult();
			
			return $awardId;
		}
		else {
			return null;
		}
	}
	
	
	

	
	public static function addAward ( $schoolUser, $level, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userAwardTable = "StudentAwards";
		$isTeacher = false;
		$userAwardWhere = "UA.person_id = " . $personId;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$isTeacher = true;
			$userAwardTable = "TeacherAwards";
			$userAwardWhere = "UA.class_id = " . $classId;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Create a new query object.
		$query = $db->getQuery(true);
		
		$query
			->select("award_id")
			->from("Award where role_id = " . $schoolUser->role_id . " and level = " . $level);

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		
		$awardId = $db->loadResult();


		$award = new \StdClass();
		$award->person_id = $personId;
		$award->level = $level;
		$award->award_id = $awardId;
		
		if ( $isTeacher ) {
			if ( $classId ) {
				$award->class_id = $classId;
			}
		}
		
		$success = $db->insertObject($userAwardTable, $award);
		
		if(!$success){
			error_log ( $userAwardTable . " insert failed" );
		}
		
	}
	
	
	public static function getAwardsPlusBlanks ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userAwardTable = "StudentAwards";
		$isTeacher = false;
		$userAwardWhere = "UA.person_id = " . $personId;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$isTeacher = true;
			$userAwardTable = "TeacherAwards";
			$userAwardWhere = "UA.class_id = " . $classId;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.award_id, A.type, A.name, A.level, A.image, A.uncollected_image, A.certificate, " .
				"IFNULL(UA.collected,0) as collected, IFNULL(DATE_FORMAT(UA.timestamp, '%D %M %Y'),0) as award_date from Award A")
			->leftJoin($userAwardTable . " UA on A.award_id = UA.award_id and " . $userAwardWhere)
			->where("A.role_id = " . $schoolUser->role_id )
			->order("A.level");
			
		$db->setQuery($query);
		
		//error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$awards = $db->loadObjectList("level");
		
		$awardObjects = array();
		foreach ( $awards as $level=>$award ) {
			$newAward = new self ( $schoolUser, $classId, $award->award_id, $award->type, $award->name, $award->level, $award->image, $award->uncollected_image, $award->certificate, $award->collected, $award->award_date );
			$awardObjects[$level] = $newAward;
		}
		
		return $awardObjects;
	}
	
	
	public static function getAwards ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userAwardTable = "StudentAwards";
		$isTeacher = false;
		$userAwardWhere = "UA.person_id = " . $personId;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$isTeacher = true;
			$userAwardTable = "TeacherAwards";
			$userAwardWhere = "UA.class_id = " . $classId;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.award_id, A.type, A.name, A.level, A.image, A.uncollected_image, A.certificate, UA.collected, " .
				"DATE_FORMAT(UA.timestamp, '%D %M %Y') as award_date from " . $userAwardTable . " UA")
			->innerJoin("Award A on A.level = UA.level")
			->where("A.role_id = " . $schoolUser->role_id )
			->where($userAwardWhere)
			->order("A.level");
			
		$db->setQuery($query);
		
		//error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$awards = $db->loadObjectList("level");
		
		$awardObjects = array();
		foreach ( $awards as $level=>$award ) {
			$newAward = new self ( $schoolUser, $classId, $award->award_id, $award->type, $award->name, $award->level, $award->image, $award->uncollected_image, $award->certificate, $award->collected, $award->award_date );
			$awardObjects[$level] = $newAward;
		}
		
		return $awardObjects;
	}
	
	
	public static function getBlankAwards ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.award_id, A.type, A.name, A.level, A.image, A.uncollected_image, A.certificate from Award A")
			->where("A.role_id = " . $schoolUser->role_id )
			->order("A.level");
			
		$db->setQuery($query);
		
		//error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$awards = $db->loadObjectList("level");
		
		
		$awardObjects = array();
		foreach ( $awards as $level=>$award ) {
			$newAward = new self ( $schoolUser, null, $award->award_id, $award->type, $award->name, $award->level, $award->image, 
									$award->uncollected_image, $award->certificate, 0, 0 );
			$awardObjects[$level] = $newAward;
		}
		
		return $awardObjects;
	}
	
	
	public static function getAllBlankAwards () {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.role_id, A.award_id, A.type, A.name, A.level, A.image, A.uncollected_image from Award A")
			->order("A.role_id, A.level");
			
		$db->setQuery($query);
		
		error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$awards = $db->loadObjectList();
		
		
		$awardObjects = array();
		foreach ( $awards as $award ) {
			if ( !array_key_exists($award->role_id, $awardObjects) ) {
				$awardObjects[$award->role_id] = array();
			}
			$awardObjects[$award->role_id][$award->level] = $award;
		}
		
		//$errMsg = print_r ( $awardObjects, true );
		//error_log ( "awardObjects: " . $errMsg );
		
		return $awardObjects;
	}
	
	
	public static function getCollectedAwards ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("* from Award A")
			->order("A.level, A.role_id");
			
		$db->setQuery($query);
		
		error_log("unlockBadges new tasks query created: " . $query->dump());
		
		//Used directly so don't create objects
		$awards = $db->loadObjectList();
		
		$teacherAwards = array();
		$studentAwards = array();
		$awardObjects = array( SchoolCommunity::TEACHER_ROLE=>$teacherAwards,
								SchoolCommunity::STUDENT_ROLE=>$studentAwards );
								
		foreach ( $awards as $award ) {
			
			$awardObjects[$award->role_id][$award->level] = $award;
			
		}
		
		return $awardObjects;
	}
	
	
	public static function getSchoolAwards ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("A.* from Award A")
			->innerJoin("StudentAwards SA on SA.award_id = A.award_id")
			->innerJoin("SchoolUsers SU on SU.person_id = SA.person_id")
			->where("SA.collected = 1")
			->where("SU.include_points = 1")
			->where("SU.school_id = " . $schoolUser->school_id);
			
		$db->setQuery($query);
		
		//error_log("getSchoolAwards award students query created: " . $query->dump());
		
		//Used directly so don't create objects
		$studentAwards = $db->loadObjectList();
		
		
		$query = $db->getQuery(true)
			->select("A.* from Award A")
			->innerJoin("TeacherAwards TA on TA.award_id = A.award_id")
			->innerJoin("SchoolClass SC on SC.class_id = TA.class_id")
			->where("TA.collected = 1")
			->where("SC.is_active = 1")
			->where("SC.school_id = " . $schoolUser->school_id);
			
		$db->setQuery($query);
		
		//error_log("getSchoolAwards award students query created: " . $query->dump());
		
		//Used directly so don't create objects
		$teacherAwards = $db->loadObjectList();
		
		
		
		$awardObjects = (object)array( "classAwards"=>$teacherAwards,
								"studentAwards"=>$studentAwards );
								
		return $awardObjects;
	}
}



?>

