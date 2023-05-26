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
	
	const FINAL_LEVEL	= 3;
	
	private $schoolUser;
	private $classId;
	private $badgeId;
	private $badgeName;
	private $lockLevel;
	private $status;
	private $badgeImage;
	private $badgeGroupId;
	private $badgeGroupName;
	private $badgeGroupIcon;
	private $unlockedImage;
	private $lockedImage;
	private $personId;
	private $taskName;
	private $speciesName;
	private $description;
	private $points;
	private $speciesImage;
	private $articleId;
	private $speciesArticleId;
	private $countedBy;
	private $moduleId;
	private $moduleName;
	private $moduleIcon;
	private $systemType;
	private $systemJson;
	private $systemThreshold;
	private $systemLink;
	private $buttonText;
	private $userBadgeTable;
	private $numResources;
	
	
	function __construct( $schoolUser,
						$classId,
						$badgeId, 
						$badgeName, 
						$lockLevel, 
						$badgeImage, 
						$unlockedImage, 
						$lockedImage, 
						$badgeGroupId, 
						$badgeGroupName, 
						$badgeGroupIcon, 
						$taskName,
						$speciesName,
						$description,
						$points,
						$linkedBadge,
						$speciesImage,
						$articleId,
						$speciesArticleId,
						$countedBy,
						$moduleId,
						$moduleName,
						$moduleIcon,
						$status,
						$numResources,
						$systemType = null,
						$systemJson = null,
						$systemThreshold = null,
						$systemLink = null,
						$buttonText = null)
	{
		if ( !$schoolUser ) {
			$this->schoolUser = SchoolCommunity::getSchoolUser();
		}
		else {
			$this->schoolUser = $schoolUser;
		}
		$this->personId = $this->schoolUser->person_id;
		$this->userBadgeTable = "StudentBadges";
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$this->userBadgeTable = "TeacherBadges";
		}
		
		$this->classId = $classId;
		$this->badgeId = $badgeId;
		$this->badgeName = $badgeName;
		$this->lockLevel = $lockLevel;
		$this->status = $status;
		$this->badgeImage = $badgeImage;
		$this->unlockedImage = $unlockedImage;
		$this->lockedImage = $lockedImage;
		$this->badgeGroupId = $badgeGroupId;
		$this->badgeGroupName = $badgeGroupName;
		$this->badgeGroupIcon = $badgeGroupIcon;
		$this->taskName = $taskName;
		$this->speciesName = $speciesName;
		$this->description = $description;
		$this->points = $points;
		$this->linkedBadge = $linkedBadge;
		$this->speciesImage = $speciesImage;
		$this->articleId = $articleId;
		$this->speciesArticleId = $speciesArticleId;
		$this->countedBy = $countedBy;
		$this->moduleId = $moduleId;
		$this->moduleName = $moduleName;
		$this->moduleIcon = $moduleIcon;
		$this->numResources = $numResources;
		$this->systemType = $systemType;
		$this->systemJson = $systemJson;
		$this->systemThreshold = $systemThreshold;
		$this->systemLink = $systemLink;
		$this->buttonText = $buttonText;
		
	}
	
	
	public function getBadgeId () {
		return $this->badgeId;
	}
	
	
	public function getBadgeName () {
		return $this->badgeName;
	}
	
	
	public function getModuleId () {
		return $this->moduleId;
	}
	
	
	public function getModuleName () {
		return $this->moduleName;
	}
	
	
	public function getModuleIcon () {
		return $this->moduleIcon;
	}
	
	
	public function getBadgeGroupId () {
		return $this->badgeGroupId;
	}
	
	
	public function getBadgeGroupName () {
		return $this->badgeGroupName;
	}
	
	
	public function getBadgeGroupIcon () {
		return $this->badgeGroupIcon;
	}
	
	
	public function getBadgeImage () {
		return $this->badgeImage;
	}
	
	
	public function getArticleId () {
		return $this->articleId;
	}
	
	
	public function getName () { 
		return $this->badgeName;
	}
	
	
	public function getTaskName () { 
		return $this->taskName;
	}
	
	
	public function getDescription () { 
		return $this->description;
	}
	
	
	public function getLinkedBadge () { 
		return $this->linkedBadge;
	}
	
	
	public function isSystemCounted () {
		
		return ( $this->countedBy == "SYSTEM" );
		
	}
	
	
	private function setNumResources () {
		
		if ( !$this->numResources ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("count(*) from BadgeResourceSet BRS")
					->where("BRS.badge_id = " . $this->badgeId);
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$this->numResources = $db->loadResult();
		}
	}
	
	public function getNumResources() {
		if ( !$this->numResources ) {
			
			$this->setNumResources();
		}
		return $this->numResources;
	}
	
	
	public function printBadge () {
		
		$lockedStr = '';
		$articleId = $this->articleId;
		$btnType = 'badgeBtn';
		$modal = '#badgeModal';
		$unlocked = true;
		
		if ( $this->status >= self::COLLECTED ) {
			$badgeImg = $this->badgeImage;
			$articleId = $this->speciesArticleId;
			$btnType = 'speciesBtn';
			$modal = '#speciesModal';
		}
		else if ( ($this->status == self::UNLOCKED) or ($this->status == self::PENDING) or ($this->status == self::COMPLETE) ) {
			$badgeImg = $this->unlockedImage;
		}
		else {
			$unlocked = false;
			$badgeImg = $this->lockedImage;
			$lockedStr = 'locked';
		}
		
		if ( $unlocked ) {
			print '<a id="'.$btnType.'_'.$this->badgeId.'_'.$articleId.'" class="'.$btnType.'" data-toggle="modal" data-target="'.$modal.'">';
		}
		print '<div class="badgesitem">';
		print '<img src="'.$badgeImg.'" class="img-responsive '.$lockedStr.'">';
		print '<div class="badgeName h4 text-center">'.$this->badgeName.'</div>';
		print '</div>';
		if ( $unlocked ) {
			print '</a>';
		}
	}
	
	
	public function printBadgeOnly () {
		
		print '<div class="badgesitem">';
		print '<img src="'.$this->badgeImage.'" class="img-responsive spinImage">';
		print '</div>';
		
	}
	
	
	public function printBadgeHeader ( $readonly = false ) {
		
		print '<div class="row">';
		
		print '<div class="col-md-2 col-sm-3 col-xs-3">';
		print '<img src="'.$this->unlockedImage.'" class="img-responsive">';
		print '</div>'; // col-2
		
		print '<div class="col-md-2 col-md-offset-6 col-sm-3 col-sm-offset-3 col-xs-3 col-xs-offset-3">';
		print '<img src="'.$this->moduleIcon.'" class="img-responsive">';
		print '</div>'; // col-6
		
		print '<div class="col-md-2 col-sm-3 col-xs-3">';
		print '<img src="'.$this->badgeGroupIcon.'" class="img-responsive">';		
		print '</div>'; // col-2
		
		print '</div>'; // row
		
		
		print '<div class="row">';
		
		print '<div class="col-md-12">';
		print '<h2>'.$this->badgeName.'</h2>';
		print '</div>'; // col-12		
		
		print '<div class="col-md-12">';
		print '<h3 class="vSpaced">'.\JText::_("COM_BIODIV_BADGE_LEVEL_".$this->lockLevel).'</h3>';
		print '</div>'; // col-12		


		if ( !$readonly && $this->isSystemCounted() ) {
			$badgeLink = $this->systemLink . "?badge=".$this->badgeId;
			if ( $this->classId ) {
				$badgeLink .= "&class_id=" . $this->classId;
			}
			print '<div class="col-md-12">';
			
			print '<a class="btn btn-primary btn-lg vSpaced" href="'.$badgeLink.'">'.$this->buttonText.'</a>';
			
			print '</div>'; // col-12		
		}

		print '</div>'; // row

	}
	
	
	public function printBadgeComplete () {
		
		print '<div class="row">';
		
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<img src="'.$this->moduleIcon.'" class="img-responsive">';
		print '</div>'; // col-2
		
		print '<div class="col-md-8 col-sm-8 col-xs-8 text-center h2">';
		print $this->badgeName . ' ' . \JText::_("COM_BIODIV_BADGE_COMPLETE");
		print '</div>'; // col-8
		
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<img src="'.$this->badgeGroupIcon.'" class="img-responsive">';		
		print '</div>'; // col-2
		
		print '</div>'; // row
		
		
		print '<div class="row">';
		
		print '<div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-5">';
		print '<img src="'.$this->unlockedImage.'" class="img-responsive">';
		print '</div>'; // col-3
		
		print '</div>'; // row
		
		

		// print '<div class="row">';
		
		// print '<div class="col-md-12">';
		// print '<h2>'.\JText::_("COM_BIODIV_BADGECOMPLETE_WELL_DONE"). ' ' . $this->badgeName.'</div>';
		// print '</div>'; // col-12		

		// print '</div>'; // row

	}
	
	
	public function checkSystemBadgeComplete () {
		
		$returnObj = new \stdClass();
		$returnObj->isComplete = false;
		$returnObj->numAchieved = 0;
		$returnObj->numRequired = 0;
		
		if ( $this->countedBy == "SYSTEM" ) {
			
			if ( $this->systemType == "CLASSIFY" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( animal_id ) from Animal")
					->where("person_id = " . userID() );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSCLASSIFY" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( animal_id ) from Animal A")
					->innerJoin("ClassBadgeProgress CBP on A.animal_id = CBP.related_id")
					->where("CBP.class_id = " . $this->classId )
					->where("CBP.badge_id = " . $this->badgeId );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSIFYOTHER" ) {
				
				$schoolProject = $this->schoolUser->project_id;
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct animal_id ) from Animal A")
					->innerJoin("Photo P on P.photo_id = A.photo_id")
					->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
					->where("A.person_id = " . userID() )
					->where("PSM.project_id != " . $schoolProject )
					->where("((P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL) or (P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id))"  );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSCLASSIFYOTHER" ) {
				
				$schoolProject = $this->schoolUser->project_id;
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct animal_id ) from Animal A")
					->innerJoin("Photo P on P.photo_id = A.photo_id")
					->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
					->innerJoin("ClassBadgeProgress CBP on A.animal_id = CBP.related_id")
					->where("CBP.class_id = " . $this->classId )
					->where("CBP.badge_id = " . $this->badgeId )
					->where("PSM.project_id != " . $schoolProject )
					->where("((P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL) or (P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id))"  );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSIFYSPECIES" ) {
				
				$schoolProject = $this->schoolUser->project_id;
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct species ) from Animal A")
					->where("A.person_id = " . userID() );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSCLASSIFYSPECIES" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct species ) from Animal A")
					->innerJoin("ClassBadgeProgress CBP on A.animal_id = CBP.related_id")
					->where("CBP.class_id = " . $this->classId )
					->where("CBP.badge_id = " . $this->badgeId );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSSHARE" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( RS.set_id ) from ResourceSet RS")
					->innerJoin("ClassBadgeProgress CBP on RS.set_id = CBP.related_id")
					->where("CBP.class_id = " . $this->classId )
					->where("CBP.badge_id = " . $this->badgeId );
				
				$db->setQuery($query);
				
				error_log("Badge::checkSystemBadgeComplete  CLASSSHARE select query created: " . $query->dump());
					
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSUPLOAD" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct upload_id ) from Upload")
					->where("person_id = " . userID() )
					->where("upload_id in (select upload_id from Photo)" );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "CLASSUPLOADOTHER" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
			
				$query = $db->getQuery(true)
					->select("count( distinct site_id ) from Upload")
					->where("person_id = " . userID() )
					->where("upload_id in (select upload_id from Photo)" );
				
				$db->setQuery($query);
				
				$returnObj->numAchieved = $db->loadResult();
				
				if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
			}
			else if ( $this->systemType == "QUIZ" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				$relatedJSON = $this->systemJson;
				
				$jsonObj = json_decode ( $relatedJSON );
				
				if ( property_exists ( $jsonObj, "topicId" ) and property_exists ( $jsonObj, "minScore" ) ) {
					$topicId = $jsonObj->topicId;
					$minScore = $jsonObj->minScore;
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
				
					$query = $db->getQuery(true)
						->select("count(*) from UserTest")
						->where("person_id = " . $this->personId )
						->where("topic_id = " . $topicId )
						->where("score >= " . $minScore );
					
					$db->setQuery($query);
					
					//error_log("Badge::checkSystemBadgeComplete  select query created: " . $query->dump());
					
					$returnObj->numAchieved = $db->loadResult();
					
					if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
				}
				else {
					error_log ("Badge::checkSystemBadgeComplete system task json incorrectly configured");
					$returnObj->isComplete = false;
				}
			}
			else if ( $this->systemType == "CLASSQUIZ" ) {
				
				$returnObj->numRequired = $this->systemThreshold;
				$relatedJSON = $this->systemJson;
				
				$jsonObj = json_decode ( $relatedJSON );
				
				if ( property_exists ( $jsonObj, "topicId" ) and property_exists ( $jsonObj, "minScore" ) ) {
					$topicId = $jsonObj->topicId;
					$minScore = $jsonObj->minScore;
					
					$db = \JDatabaseDriver::getInstance(dbOptions());
				
					$query = $db->getQuery(true)
						->select("count(*) from ClassBadgeProgress CBP")
						->innerJoin("UserTest UT on UT.ut_id = CBP.related_id")
						->where("CBP.class_id = " . $this->classId )
						->where("CBP.badge_id = " . $this->badgeId )
						->where("UT.score >= " . $minScore );
					
					$db->setQuery($query);
					
					$returnObj->numAchieved = $db->loadResult();
					
					if ( $returnObj->numAchieved >= $returnObj->numRequired ) $returnObj->isComplete = true;
				}
				else {
					error_log ("Badge::checkSystemBadgeComplete system task json incorrectly configured");
					$returnObj->isComplete = false;
				}
			}
			
		}
		
		return $returnObj;
	}
	
	// Link the resource set to this badge
	public function linkResourceSet ( $setId, $completeText ) {
		
		error_log ("linkResourceSet called, setId = " . $setId . ", completeText = " . $completeText );
		//$personId = userID();
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		
		$status = Badge::COMPLETE;
				
		$fields = array(
			$db->quoteName('set_id') . ' = ' . $setId,
			$db->quoteName('complete_text') . ' = ' . $db->quote($completeText),
			$db->quoteName('complete_date') . ' = ' . "NOW()",
			$db->quoteName('timestamp') . ' = ' . "NOW()"
		);

		// Conditions for which records should be updated.
		if ( $this->classId ) {
			$conditions = array(
				$db->quoteName('badge_id') . ' = ' . $this->badgeId,
				$db->quoteName('class_id') . ' = ' . $this->classId
			);

		}
		else {
			$conditions = array(
				$db->quoteName('badge_id') . ' = ' . $this->badgeId,
				$db->quoteName('person_id') . ' = ' . $this->personId
			);
		}

		$query->update($this->userBadgeTable)->set($fields)->where($conditions);
		
		$db->setQuery($query);
		
		//error_log("Badge::linkResourceSet  select query created: " . $query->dump());
		
		$result = $db->execute();
		
		if ( $result ) {
			SchoolCommunity::logEvent ( false, SchoolCommunity::SCHOOL,  'uploaded evidence for the ' . $this->badgeName . ' ' . 'badge' );
		}
		
	}
	
	
	public  function collect () {
				
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		
		// Fields to update.
		$fields = array(
			$db->quoteName('status') . ' = ' . SELF::COLLECTED
		);
		
		// Conditions for which records should be updated.
		if ( $this->classId ) {
			$conditions = array(
				$db->quoteName('class_id') . ' = ' . $this->classId, 
				$db->quoteName('badge_id') . ' = ' . $this->badgeId,
				$db->quoteName('status') . ' = ' . SELF::COMPLETE
			);
		}
		else {
			$conditions = array(
				$db->quoteName('person_id') . ' = ' . $this->personId, 
				$db->quoteName('badge_id') . ' = ' . $this->badgeId,
				$db->quoteName('status') . ' = ' . SELF::COMPLETE
			);
		}

		$query->update($db->quoteName($this->userBadgeTable))->set($fields)->where($conditions);

		$db->setQuery($query);
		
		//error_log("Badge::collect  update query created: " . $query->dump());

		$result = $db->execute();
		
		if ( $result ) {
			$this->status = self::COLLECTED;
		}
		
		return $result;
			
		
	}
	
	
	public function getKioskParams () {
		
		$kioskParams = new \StdClass();
		$kioskParams->projectId = $this->schoolUser->project_id;
		
		if ( $this->countedBy == "SYSTEM" ) {
			
			$kioskParams->systemType = $this->systemType;
			
			if ( $this->systemType == "QUIZ" ) {
				$systemObj = json_decode ( $this->systemJson );
				$kioskParams->topic = $systemObj->topicId;
			}
			else if ( $this->systemType == "CLASSQUIZ" ) {
				$systemObj = json_decode ( $this->systemJson );
				$kioskParams->topic = $systemObj->topicId;
			}
			else if ( $this->systemType == "CLASSIFY" ) {
				$kioskParams->threshold = $this->systemThreshold;
			}
			else if ( $this->systemType == "CLASSCLASSIFY" ) {
				$kioskParams->threshold = $this->systemThreshold;
			}
		}
		
		return json_encode($kioskParams);
	}
	
	
	public static function createFromId ( $schoolUser, $classId, $badgeId )
	{
		if ( $badgeId  ) {
			
			if ( !$schoolUser ) {
				$schoolUser = SchoolCommunity::getSchoolUser();
			}
			
			$personId = $schoolUser->person_id;
			
			$userBadgeTable = "StudentBadges";
			$badgeTableWhere = "UB.person_id = " . $personId;
			$isTeacher = false;
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				$userBadgeTable = "TeacherBadges";
				$badgeTableWhere = "UB.class_id = " . $classId;
				$isTeacher = true;
			}
		
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			if ( $schoolUser->role_id == SchoolCommunity::ADMIN_ROLE ) {
			
				$query = $db->getQuery(true)
					->select("B.*, ".self::LOCKED." as status, M.name as module_name, M.icon, BG.name as gp_name, BG.icon as bg_icon, SB.badge_type, SB.related_json, SB.threshold, SB.system_link, SB.button_text from Badge B")
					->innerJoin("Module M on M.module_id = B.module_id")
					->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
					->leftJoin("SystemBadges SB on B.badge_id = SB.badge_id")
					->where("B.badge_id = " . $badgeId);
			
			}
			else {
			
				$query = $db->getQuery(true)
					->select("B.*, IFNULL(UB.status, ".self::LOCKED.") as status, M.name as module_name, M.icon, BG.name as gp_name, BG.icon as bg_icon, SB.badge_type, SB.related_json, SB.threshold, SB.system_link, SB.button_text from Badge B")
					->innerJoin("Module M on M.module_id = B.module_id")
					->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
					->leftJoin("SystemBadges SB on B.badge_id = SB.badge_id")
					->leftJoin($userBadgeTable . " UB on UB.badge_id = B.badge_id and " . $badgeTableWhere)
					->where("B.role_id = " . $schoolUser->role_id )
					->where("B.badge_id = " . $badgeId);
			
			}
			
			$db->setQuery($query);
			
			//error_log("Badge::createFromId  select query created: " . $query->dump());
			
			$badge = $db->loadObject();
			
			return new self ( 
						$schoolUser,
						$classId,
						$badgeId,
						$badge->name,
						$badge->lock_level,
						$badge->badge_image,
						$badge->unlocked_image,
						$badge->locked_image,
						$badge->badge_group,
						$badge->gp_name,
						$badge->bg_icon,
						$badge->task_name,
						$badge->species,
						$badge->description,
						$badge->points,
						$badge->linked_badge,
						$badge->species_image,
						$badge->article_id,
						$badge->species_article_id,
						$badge->counted_by,
						$badge->module_id,
						$badge->module_name,
						$badge->icon,
						$badge->status,
						null,
						$badge->badge_type,
						$badge->related_json,
						$badge->threshold,
						$badge->system_link,
						$badge->button_text
						);
			
		}
		else {
			return null;
		}
	
	}
	
	
	public static function writeBadgeProgress ( $schoolUser, $classId, $badgeId, $relatedId ) {
		
		if ( !$classId ) {
			error_log ( "Badge::writeBadgeProgress no class id given" );
			return false;
		}
		
		$success = false;
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		if ( $schoolUser ) {
			
			$personId = $schoolUser->person_id;
				
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			if ( is_array($relatedId) ) {
				
				foreach ( $relatedId as $nextId ) {
					
					if ( self::checkProgressSuccess ( $schoolUser, $classId, $badgeId, $relatedId ) ) {
						$fields = new \StdClass();
						$fields->class_id = $classId;
						$fields->badge_id = $badgeId;
						$fields->related_id = $nextId;
						$fields->person_id = $personId;
						
						$result = $db->insertObject("ClassBadgeProgress", $fields);
						$success = $success && $result;
					}
				}
				
			}
			else {
				
				if ( self::checkProgressSuccess ( $schoolUser, $classId, $badgeId, $relatedId ) ) {
					$fields = new \StdClass();
					$fields->class_id = $classId;
					$fields->badge_id = $badgeId;
					$fields->related_id = $relatedId;
					$fields->person_id = $personId;
					
					$success = $db->insertObject("ClassBadgeProgress", $fields);
				}
			
			}
			if(!$success){
				error_log ( "Badge::writeBadgeProgress ClassBadgeProgress insert failed" );
			}
		}
		else {
			error_log ( "Badge::writeBadgeProgress no school user" );
			
		}
		return $success;
	}
	
	
	private static function checkProgressSuccess ( $schoolUser, $classId, $badgeId, $relatedId ) {
		
		$progressIsValid = true;
		if ( $schoolUser && $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$badgeObj = self::createFromId ( $schoolUser, $classId, $badgeId );
			if ( $badgeObj->systemType == "CLASSSHARE" ) {
				
				// Check there are resources loaded for the given set
				$db = \JDatabaseDriver::getInstance(dbOptions());
		
				$query = $db->getQuery(true)
					->select("count(*) from Resource")
					->where("set_id = " . $relatedId);
					
				
				$db->setQuery($query);
				
				$numResources = $db->loadResult();
				
				if ( $numResources > 0 ) {
					$progressIsValid = true;
				}
				else {
					$progressIsValid = false;
				}
			}
			else {
				$progressIsValid = true;
			}
		}
		else {
			$progressIsValid = false;
		}
		return $progressIsValid;
	}
	
	
	public static function checkJustCompleted ( $schoolUser, $classId, $badgeId ) {
		
		$badgeStatus = null;
		
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userBadgeTable = "StudentBadges";
		$badgeTableWhere = "UB.person_id = " . $personId;
		$isTeacher = false;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$userBadgeTable = "TeacherBadges";
			$badgeTableWhere = "UB.class_id = " . $classId;
			$isTeacher = true;
		}
			
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("UB.badge_id from " . $userBadgeTable . " UB")
			->innerJoin("SystemBadges SB2 on SB2.badge_id = UB.badge_id and " . $badgeTableWhere)
			->where("UB.badge_id = " . $badgeId)
			->where("UB.status > " . Badge::LOCKED )
			->where("UB.status < " . Badge::COMPLETE );
			
		
		$db->setQuery($query);
		
		error_log("checkJustCompleted select system badges query created: " . $query->dump());
		
		$systemBadges = $db->loadColumn();
		
		if ( count($systemBadges) == 0 ) {
			error_log ( "Badge::checkJustCompleted count system badges = 0" );
			return null;
		}
		
		$systemBadge = self::createFromId ( $schoolUser, $classId, $badgeId );
		
		$badgeStatus = $systemBadge->checkSystemBadgeComplete();
		
		
		if ( $badgeStatus->isComplete ) {
			
			error_log ( "Badge::checkJustCompleted badge is complete, updating table" );
		
			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName('status') . ' = ' . Badge::COMPLETE
			);

			// Conditions for which records should be updated.
			if ( $isTeacher ) {
				$conditions = array(
					$db->quoteName('badge_id') . ' = ' . $badgeId,
					$db->quoteName('class_id') . ' = ' . $classId,
					$db->quoteName('status') . ' != ' . Badge::COLLECTED
				);
			}
			else {
				$conditions = array(
					$db->quoteName('badge_id') . ' = ' . $badgeId,
					$db->quoteName('person_id') . ' = ' . $personId,
					$db->quoteName('status') . ' != ' . Badge::COLLECTED
				);
			}

			$query->update($userBadgeTable)->set($fields)->where($conditions);
			
			$db->setQuery($query);
			$result = $db->execute();
			
			SchoolCommunity::addNotification($systemBadge->getBadgeName() . " " . \JText::_("COM_BIODIV_BADGE_COMPLETED"));
			
			SchoolCommunity::logEvent ( false, SchoolCommunity::SCHOOL,  'class ' .$classId. ' completed the ' . $systemBadge->getBadgeName() . ' ' . 'badge'  );
			
		}
		
		return $badgeStatus;
		
	}
	
		
	public static function unlockBadges ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$newUser = false;
		
		$userBadgeTable = "StudentBadges";
		$badgeTableWhere = "UB.person_id = " . $personId;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$userBadgeTable = "TeacherBadges";
			$badgeTableWhere = "UB.class_id = " . $classId;
		}
			
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("UB.status, count(*) as num from " . $userBadgeTable . " UB")
			->innerJoin("Badge B on B.badge_id = UB.badge_id")
			->where($badgeTableWhere)
			->group("UB.status")
			->order("UB.status");
			
		$db->setQuery($query);
		
		//error_log("unlockBadges get incomplete badges created: " . $query->dump());
		
		$statusCounts = $db->loadObjectList("status");
		
		// If no badges in UserTasks table, copy all and unlock bronze.
		if ( count($statusCounts) == 0 ) {
			self::initBadges( $schoolUser, $classId );
			$newUser = true;
		}
		else {
			
			$gotUnlocked = false;
			$gotLocked = false;
			if ( array_key_exists(self::UNLOCKED, $statusCounts) ) {
				$gotUnlocked = true;
			}
			if ( array_key_exists(self::LOCKED, $statusCounts) ) {
				$gotLocked = true;
			}
		
			if ( !$gotUnlocked && !$gotLocked ) {
				
				// All done, award Gold if not already awarded
				Award::addAward ( $schoolUser, self::FINAL_LEVEL, $classId );
				
			}
			else if ( !$gotUnlocked && $gotLocked ) {
				
				$query = $db->getQuery(true)
					->select("MIN(B.lock_level) from Badge B")
					->innerJoin($userBadgeTable . " UB on B.badge_id = UB.badge_id")
					->where($badgeTableWhere)
					->where("UB.status = " . self::LOCKED);
					
				$db->setQuery($query);
				
				$newLevel = $db->loadResult();
				
				Award::addAward ( $schoolUser, $newLevel - 1, $classId );
				self::unlockLevel ( $schoolUser, $classId, $newLevel );
				
			}
		}
		
		return $newUser;
	}
	
	
	public static function initBadges ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userBadgeTable = "StudentBadges";
		$isTeacher = false;
		$badgeTableWhere = "person_id = " . $personId;
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$isTeacher = true;
			$userBadgeTable = "TeacherBadges";
			$badgeTableWhere = "class_id = " . $classId;
		}
		
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("B.*, B.lock_level from Badge B")
			->where("B.role_id = " . $schoolUser->role_id )
			->where("B.badge_id not in (select badge_id from ".$userBadgeTable." where ".$badgeTableWhere . ")" )
			->order("B.badge_id");
			
		$db->setQuery($query);
		
		//error_log("unlockBadges new tasks query created: " . $query->dump());
		
		$newBadges = $db->loadObjectList();
		
		
		foreach ( $newBadges as $newBadge ) {
	
			$fields = new \StdClass();
			$fields->person_id = $personId;
			$fields->badge_id = $newBadge->badge_id;
			
			if ( $newBadge->lock_level == 1 ) {
				$fields->status = self::UNLOCKED;
			}
			else {
				$fields->status = self::LOCKED;
			}
			
			if ( $isTeacher ) {
				if ( $classId ) {
					$fields->class_id = $classId;
				}
			}
			
			$success = $db->insertObject($userBadgeTable, $fields);
			if(!$success){
				error_log ( $userBadgeTable . " insert failed" );
			}
		}
		
	}

	
	private static function unlockLevel ( $schoolUser = null, $classId = null, $newLevel ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		if ( $personId ) {
			$userBadgeTable = "StudentBadges";
			$isTeacher = false;
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				$isTeacher = true;
				$userBadgeTable = "TeacherBadges";
			}
			
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
			
			$fields = array(
				$db->quoteName('status') . ' = ' . self::UNLOCKED
			);

			// Conditions for which records should be updated.
			if ( $isTeacher ) {
				$conditions = array(
					$db->quoteName('class_id') . ' = ' . $classId, 
					$db->quoteName('badge_id') . ' in (select badge_id from Badge where role_id = ' . $schoolUser->role_id . ' and lock_level = ' . $newLevel .')'
				);
			}
			else {
				$conditions = array(
					$db->quoteName('person_id') . ' = ' . $personId, 
					$db->quoteName('badge_id') . ' in (select badge_id from Badge where role_id = ' . $schoolUser->role_id . ' and lock_level = ' . $newLevel .')'
				);
			}
				
			$query->update($db->quoteName($userBadgeTable))->set($fields)->where($conditions);

			$db->setQuery($query);
			
			error_log("Badge::unlockLevel  select query created: " . $query->dump());

			$result = $db->execute();
		}
	}

	
	public static function updateStatus ( $schoolUser = null, $classId = null, $badgeId, $status, $completeText ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		if ( $personId ) {
			
			$userBadgeTable = "StudentBadges";
			$isTeacher = false;
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				$isTeacher = true;
				$userBadgeTable = "TeacherBadges";
			}
				
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
			
			// Fields to update.
			$fields = array(
				$db->quoteName('status') . ' = ' . $status
			);
			
			if ( ($status == self::COMPLETE) && $completeText ) {
				error_log ("Adding complete_text");
				$fields[] = $db->quoteName('complete_text') . ' = ' . $db->quote($completeText);
				$fields[] = $db->quoteName('complete_date') . ' = ' . "NOW()";  
			}

			// Conditions for which records should be updated.
			if ( $isTeacher ) {
				$conditions = array(
					$db->quoteName('class_id') . ' = ' . $classId, 
					$db->quoteName('badge_id') . ' = ' . $badgeId
				);
			}
			else {
				$conditions = array(
					$db->quoteName('person_id') . ' = ' . $personId, 
					$db->quoteName('badge_id') . ' = ' . $badgeId
				);
			}

			$query->update($db->quoteName($userBadgeTable))->set($fields)->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();
			
			return $result;
			
		}
	}
	
	
	public static function countComplete ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$personId = $schoolUser->person_id;
		
		$userBadgeTable = "StudentBadges";
		$userBadgeWhere = "UB.person_id = " . $personId;
		
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$userBadgeTable = "TeacherBadges";
			$userBadgeWhere = "UB.class_id = " . $classId;
		}
		
		
		$numBadges = 0;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("COUNT(*) from ".$userBadgeTable." UB")
				->where("UB.status = " . self::COLLECTED )
				->where( $userBadgeWhere );
				
			$db->setQuery($query);
			
			//error_log("Basge::getTotalBadges  select query created: " . $query->dump());
			
			$numBadges = $db->loadResult();
		}
		
		return $numBadges;
	}
	
	
	public static function getBadges ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$personId = $schoolUser->person_id;
		
		$userBadgeTable = "StudentBadges";
		$userBadgeWhere = "UB.person_id = " . $personId;
		
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$userBadgeTable = "TeacherBadges";
			$userBadgeWhere = "UB.class_id = " . $classId;
		}
		
		
		$allBadges = array(1=>array(), 2=>array(), 3=>array());
		
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("B.*, IFNULL(UB.status, ".self::LOCKED.") as status, M.name as module_name, M.icon, BG.name as gp_name, BG.icon as gp_icon, SB.badge_type, SB.related_json, SB.threshold, SB.system_link, SB.button_text from Badge B")
				->innerJoin("Module M on M.module_id = B.module_id")
				->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
				->leftJoin("SystemBadges SB on B.badge_id = SB.badge_id")
				->leftJoin($userBadgeTable . " UB on UB.badge_id = B.badge_id and " . $userBadgeWhere)
				->where("B.role_id = " . $schoolUser->role_id )
				->group("B.badge_id")
				->order("B.lock_level, badge_id");
				
			$db->setQuery($query);
			
			//error_log("Badge::getBadges  select query created: " . $query->dump());
			
			$badges = $db->loadObjectList( "badge_id" );
			
			foreach ( $badges as $badge ) {
				$allBadges[$badge->lock_level][] = new self ( 
													$schoolUser,
													$classId,
													$badge->badge_id,
													$badge->name,
													$badge->lock_level,
													$badge->badge_image,
													$badge->unlocked_image,
													$badge->locked_image,
													$badge->badge_group,
													$badge->gp_name,
													$badge->gp_icon,
													$badge->task_name,
													$badge->species,
													$badge->description,
													$badge->points,
													$badge->linked_badge,
													$badge->species_image,
													$badge->article_id,
													$badge->species_article_id,
													$badge->counted_by,
													$badge->module_id,
													$badge->module_name,
													$badge->icon,
													$badge->status,
													null,
													$badge->badge_type,
													$badge->related_json,
													$badge->threshold,
													$badge->system_link,
													$badge->button_text);
				
			}
		}
		
		return $allBadges;
	}
	
	
	public static function getBadgeScheme ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$personId = $schoolUser->person_id;
		
		
		$allBadges = array(1=>array(), 2=>array(), 3=>array());
		
		
		if ( $personId and $schoolUser->role_id != SchoolCommunity::STUDENT_ROLE ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("B.*, ".self::LOCKED." as status, M.name as module_name, M.icon, BG.name as gp_name, BG.icon as gp_icon, SB.badge_type, SB.related_json, SB.threshold, SB.system_link, SB.button_text, count(BRS.set_id) as num_resources from Badge B")
				->innerJoin("Module M on M.module_id = B.module_id")
				->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
				->leftJoin("SystemBadges SB on B.badge_id = SB.badge_id")
				->leftJoin("BadgeResourceSet BRS on B.badge_id = BRS.badge_id")
				->where("B.role_id = " . SchoolCommunity::TEACHER_ROLE)
				->group("B.badge_id")
				->order("B.lock_level, B.badge_group, B.module_id, badge_id");
				
			$db->setQuery($query);
			
			error_log("Badge::getBadges  select query created: " . $query->dump());
			
			$badges = $db->loadObjectList();
			
			foreach ( $badges as $badge ) {
				$allBadges[$badge->lock_level][] = new self ( 
													$schoolUser,
													null,
													$badge->badge_id,
													$badge->name,
													$badge->lock_level,
													$badge->badge_image,
													$badge->unlocked_image,
													$badge->locked_image,
													$badge->badge_group,
													$badge->gp_name,
													$badge->gp_icon,
													$badge->task_name,
													$badge->species,
													$badge->description,
													$badge->points,
													$badge->linked_badge,
													$badge->species_image,
													$badge->article_id,
													$badge->species_article_id,
													$badge->counted_by,
													$badge->module_id,
													$badge->module_name,
													$badge->icon,
													$badge->status,
													$badge->num_resources,
													$badge->badge_type,
													$badge->related_json,
													$badge->threshold,
													$badge->system_link,
													$badge->button_text);
				
			}
		}
		
		return $allBadges;
	}
	
	
	public static function getNewBadgeId ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$personId = $schoolUser->person_id;
		
		$userBadgeTable = "StudentBadges";
		$userBadgeWhere = "UB.person_id = " . $personId;
		
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			$userBadgeTable = "TeacherBadges";
			$userBadgeWhere = "UB.class_id = " . $classId;
		}
		
		$newBadgeId = null; 
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("MIN(B.badge_id) from Badge B")
				->innerJoin($userBadgeTable . " UB on UB.badge_id = B.badge_id and " . $userBadgeWhere)
				->where("UB.status = " . self::COMPLETE );
				
			$db->setQuery($query);
			
			//error_log("Badge::getBadges  select query created: " . $query->dump());
			
			$newBadgeId = $db->loadResult();
		}
		
		return $newBadgeId;
	}
	
	public static function getLockLevels ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$personId = $schoolUser->person_id;
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("distinct A.level, A.image as t_award, A.uncollected_image as t_uncollected, A.white_image as t_white, A2.image as s_award, A2.uncollected_image as s_uncollected, A2.white_image as s_white, A.report_id from Award A")
				->innerJoin("Award A2 on A2.level = A.level")
				->where("A.role_id = " . SchoolCommunity::TEACHER_ROLE)
				->where("A2.role_id = " . SchoolCommunity::STUDENT_ROLE);
				
			$db->setQuery($query);
			
			error_log("Badge::getLockLevels  select query created: " . $query->dump());
			
			$lockLevels = $db->loadObjectList("level");
			
		}
		
		return $lockLevels;
	}
}


?>

