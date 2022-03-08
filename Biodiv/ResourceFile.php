<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class ResourceFile {
	
	private $resourceId;
	private $resourceType;
	private $personId;
	private $accessLevel;
	private $setId;
	private $filename;
	private $description;
	private $ftype;
	private $url;
	
	
	function __construct ( $resourceId, 
							$resourceType,
							$personId,
							$accessLevel,
							$setId,
							$filename,
							$description,
							$ftype,
							$isPin,
							$isFavourite,
							$isLike,
							$numLikes,
							$s3Status,
							$url	) {
								
		$this->resourceId = (int)$resourceId;
		$this->resourceType = (int)$resourceType;
		$this->personId = (int)$personId;
		$this->accessLevel = (int)$accessLevel;
		$this->setId = (int)$setId;
		$this->filename = $filename;
		$this->description = $description;
		$this->ftype = $ftype;
		$this->isPin = $isPin;
		$this->isFavourite = $isFavourite;
		$this->isLike = $isLike;
		$this->numLikes = $numLikes;
		$this->s3Status = $s3Status;
		if ( $s3Status == 1 ) {
			$this->url = $url;
		}
		else {
			$this->url = \JURI::root().$url;
		}	
	}
		
	public function getResourceId () {
		return $this->resourceId;
	}
	
	public function getUrl () {
		return $this->url;
	}
	
	public function getFiletype () {
		return $this->ftype;
	}
	
	public function printHtml ( $idTag = null ) {
		
		$resourceId = $this->resourceId;
		
		$tagStr = "";
		if ( $idTag ) $tagStr = $idTag.'_';
		
		$translations = getTranslations("ResourceFile");
		
		$shareStatus = array(SchoolCommunity::PERSON=>"fa fa-lock fa-lg",
								SchoolCommunity::SCHOOL=>"fa fa-building-o fa-lg",
								SchoolCommunity::COMMUNITY=>"fa fa-globe fa-lg",
								SchoolCommunity::ECOLOGISTS=>"fa fa-leaf fa-lg");
		
		$shareOptions = array(SchoolCommunity::PERSON=>$translations['share_private']['translation_text'],
								SchoolCommunity::SCHOOL=>$translations['share_school']['translation_text'],
								SchoolCommunity::COMMUNITY=>$translations['share_community']['translation_text']);
					
		
		print '<div class="panel">';
			
		print '<div class="panel-heading">';
		
		print '<div class="row">';
		
		$accessLevel = $this->accessLevel;
		$shareIconClass = $shareStatus[$accessLevel];
		
		print '<div class="col-xs-1 col-sm-1 col-md-1">';
		
		$resourcePerson = $this->personId;
		
		$userId = userID();
		
		if ( $resourcePerson == $userId ) {
			print '<div class="dropdown">';
			
			print '<div id="dropdown-toggle_'.$tagStr.$resourceId.'" class="dropdown-toggle dropdown-toggle_'.$resourceId.' btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
			print '<h4><i class="'.$shareIconClass.'"></i></h4>'; 
			print '</div>';
			print '<ul class="dropdown-menu" aria-labelledby="share_resource_'.$tagStr.$resourceId.'">';
			
			$isStudent = SchoolCommunity::isStudent();
			//$studentCanShare = false;
			// if ( $isStudent ) {
				// // Student resources can only be shared if the task was approved
				// $linkedTask = Task::getLinkedTask ( $resourceId );
				// if ( $linkedTask and $linkedTask->getStatus() >= Badge::COMPLETE ) {
					// $studentCanShare = true;
				// }
			// }
			// if ( $isStudent and ($studentCanShare == false) ) {
				// print $translations['get_approved']['translation_text'] ;
			// }
			if ( !$isStudent )  {
				foreach ($shareOptions as $shareId=>$shareOpt ) {
					print '<li><div id="share_resource_'.$tagStr.$shareId.'_'.$resourceId.'" class="share_resource share_resource_'.$shareId.'_'.$resourceId.
						' h4 btn" > <i class="' . 
						$shareStatus[$shareId] . '"></i> ' . $shareOpt . '</div></li>';
				}
			}
			
			// Add pin options here for ecologists only
			if ( SchoolCommunity::isEcologist() ) {
			
				$isPinned = $this->isPin == 1;
			
				if ( $isPinned ) {
					print '<li style="display:none;"><div id="pin_resource_'.$tagStr.$resourceId.'" class="pin_resource pin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						$translations['pin']['translation_text'] . '</div></li>';
					print '<li><div id="unpin_resource_'.$tagStr.$resourceId.'" class="unpin_resource unpin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						$translations['unpin']['translation_text'] . '</div></li>';
				}
				else {
					print '<li><div id="pin_resource_'.$tagStr.$resourceId.'" class="pin_resource pin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						$translations['pin']['translation_text'] . '</div></li>';
					print '<li style="display:none;"><div id="unpin_resource_'.$tagStr.$resourceId.'" class="unpin_resource unpin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						$translations['unpin']['translation_text'] . '</div></li>';
				}
			}
			
			print '</ul>';
			
			print '</div>'; // dropdown
		}
		else {
			print '<h4><i class="'.$shareIconClass.'"></i></h4>';
		}
		
		print '</div>'; // col-1
		
		print '<div class="col-xs-10 col-sm-10 col-md-7 resource_file">';
		print '<h5>'.$this->filename.'</h5>';
		print '<p>'.$this->description.'</p>';
		
		print '</div>';
		
		print '<div id="show_resource_'.$tagStr.$resourceId.'" role="button" class="col-xs-2 col-sm-2 col-md-1 show_resource" data-toggle="tooltip" title="'.
			$translations['show']['translation_text'].'"><h4 class="text-right"><i class="fa fa-angle-down fa-lg"></i></h4></div>';
		
		print '<div id="hide_resource_'.$tagStr.$resourceId.'" role="button" class="col-xs-2 col-sm-2 col-md-1 hide_resource" data-toggle="tooltip" title="'.
			$translations['hide']['translation_text'].'" style="display:none;"><h4 class="text-right"><i class="fa fa-angle-up fa-lg"></i></h4></div>';
		
		print '<div id="download_resource_'.$tagStr.$resourceId.'" class="col-xs-2 col-sm-2 col-md-1 download_resource" role="button" data-toggle="tooltip" title="'.
			$translations['download']['translation_text'].'" ><a href="'.$this->url.
			'" download="'.$this->filename.'"><h4 class="text-right"><i class="fa fa-download fa-lg"></i></h4></a></div>';
		
		
		// Need space for likes here
		
		print '<div class="col-xs-4 col-sm-4 col-md-2">';
		print '<div class="row">';
		
		if ( $this->isFavourite ) {
			
			print '<div id="favourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 favourite_resource favourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['favourite']['translation_text'].'"  style="display:none;" ><h4 class="text-right"><i class="fa fa-bookmark-o fa-lg"></i></h4></div>';
			print '<div id="unfavourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 unfavourite_resource unfavourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['favourite']['translation_text'].'"><h4 class="text-right"><i class="fa fa-bookmark fa-lg"></i></h4></div>';
		}
		else {
			
			print '<div id="favourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 favourite_resource favourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['favourite']['translation_text'].'" ><h4 class="text-right"><i class="fa fa-bookmark-o fa-lg"></i></h4></div>';
			print '<div id="unfavourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 unfavourite_resource unfavourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['favourite']['translation_text'].'"  style="display:none;"><h4 class="text-right"><i class="fa fa-bookmark fa-lg"></i></h4></div>';
		}
		
		if ( $this->isLike ) {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 like_resource like_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['like']['translation_text'].'"  style="display:none;" ><h4 class="text-right"><i class="fa fa-heart-o fa-lg"></i></h4></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 unlike_resource unlike_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['like']['translation_text'].'"><h4 class="text-right"><i class="fa fa-heart fa-lg"></i></h4></div>';
		}
		else {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 like_resource like_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['like']['translation_text'].'" ><h4 class="text-right"><i class="fa fa-heart-o fa-lg"></i></h4></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 unlike_resource unlike_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				$translations['like']['translation_text'].'"  style="display:none;"><h4 class="text-right"><i class="fa fa-heart fa-lg"></i></h4></div>';
		}
		
		print '<div id="num_likes_'.$tagStr.$resourceId.'" class="col-xs-12 col-sm-12 col-md-12 text-right h5 num_likes_'.$resourceId.'">';
		
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' + $numLikes . ' ' . $translations['single_like']['translation_text'];
		}
		else {
			
			print '' + $numLikes . ' ' . $translations['many_likes']['translation_text'];
		}
		print '</div>'; // num_likes, col-12
		
		print '</div>'; // row
		
		print '</div>'; //col-2
		
		print '</div>'; // row
		
		print '</div>'; // panel-heading
		
		print '<div class="panel-body resource_panel" style="display:none;">';
		
		print '<div id="resource_'.$tagStr.$resourceId.'" ></div>';
			
		print '</div>'; // panel-body
		print '</div>'; //panel
		
		
	}
	
	public static function createResourceFile ( $setId, $resourceType, $clientName, $newName, $dirName, $fileSize, $fileType, $accessLevel = null ) {
		
		// Get all the text snippets for this class in the current language
		$translations = getTranslations("ResourceFile");
		
		$personId = userID();
		
		$instance = null;
			
		if ( $personId ) {
			
			$problem = true;
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
		
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("RS.school_id, RS.set_name, RS.description, S.name as school_name, U.username, O.option_name as type_name from ResourceSet RS")
					->innerJoin("School S on RS.school_id = S.school_id")
					->innerJoin($userDb . "." . $prefix ."users U on RS.person_id = U.id")
					->innerJoin("Options O on O.option_id = RS.resource_type" )
					->where("RS.set_id = " . $setId);
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$otherDetails = $db->loadAssoc();
			
			$schoolId = $otherDetails["school_id"];
			$description = $otherDetails["type_name"] . ": " . $otherDetails["set_name"] . '. ' . $otherDetails["description"]. " <small>" . 
				$translations['contributed']['translation_text'] . ' ' . $otherDetails["username"] . ', ' . $otherDetails["school_name"] . '</small>';
				
			// To start with
			$articleId = 0;
			
			if ( $accessLevel == null ) {
				$accessLevel = SchoolCommunity::PERSON;
			}
			
			$url = $dirName."/".$newName;
			
			$resourceFields = (object) [
				'access_level' => $accessLevel,
				'school_id' => $schoolId,
				'person_id' => $personId,
				'set_id' => $setId,
				'resource_type' => $resourceType,
				'upload_filename' => $clientName,
				'description' => $description,
				'url'=>$url,
				'filename' => $newName,
				'size' => $fileSize,
				'filetype' => $fileType,
				'article_id' => $articleId
			];
								
			
			$struc = 'resourcefile';

			if($resourceId = codes_insertObject($resourceFields, $struc)){
				$problem = false;
				addMsg('success', "Uploaded $clientName");
				$instance = new self( $resourceId, 
							$resourceType,
							$personId,
							$accessLevel,
							$setId,
							$newName,
							$description,
							$fileType,
							0,
							0,
							0,
							0,
							0,
							$url );
			}
			else {
				error_log ("Problem inserting resource into database" );
				$problem = true;
			}
		
		}
		
		return $instance;
		
	}
	
	public static function createResourceFileFromId( $resourceId ){
		
		if ( $resourceId  ) {
			
			$userId = userID();
			
			//$resourceDetails = codes_getDetails ( $resourceId, "resourcefile" );
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("R.*, IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fave, IFNULL(LR.lr_id, 0) as is_like, " .
						" (select count(*) from LikedResource where resource_id = " . $resourceId . ") as num_likes from Resource R")
				->innerJoin("ResourceSet RS on RS.set_id = R.set_id")
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id" )
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $userId )
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $userId)
				->where("R.resource_id = " . $resourceId  );
			
			$db->setQuery($query);
			
			//error_log("Task constructor select query created: " . $query->dump());
			
			$resourceDetails = $db->loadAssoc();
			
			$resourceType = (int)$resourceDetails['resource_type'];
			$personId = (int)$resourceDetails['person_id'];
			$accessLevel = (int)$resourceDetails['access_level'];
			$setId = (int)$resourceDetails['set_id'];
			$filename = $resourceDetails['filename'];
			$description = $resourceDetails['description'];
			$ftype = $resourceDetails['filetype'];
			$isPin = $resourceDetails['is_pin'] > 0;
			$isFavourite = $resourceDetails['is_fave'] > 0;
			$isLike = $resourceDetails['is_like'] > 0;
			$numLikes = $resourceDetails['num_likes'];
			
			$s3Status = $resourceDetails['s3_status'];
			$url = $resourceDetails['url'];
			
			
			return new self ( $resourceId, 
							$resourceType,
							$personId,
							$accessLevel,
							$setId,
							$filename,
							$description,
							$ftype,
							$isPin,
							$isFavourite,
							$isLike,
							$numLikes,
							$s3Status,
							$url );
		}
		else {
			return null;
		}
	
	}
	
	
	public static function getPinnedResources () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		
		
		$PERSON = SchoolCommunity::PERSON;
		$SCHOOL = SchoolCommunity::SCHOOL;
		$COMMUNITY = SchoolCommunity::COMMUNITY;
		$ECOLOGISTS = SchoolCommunity::ECOLOGISTS;
		
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$allPinnedResources = array();
		
		// So pinned resources are those that have been pinned for my school and role, but the resources must also have an
		// appropriate access level ie (NB no school student access level yet - may not need it.
		// if I'm a student I can only see my resources, my school student resources
		// if I'm a teacher I can see my resources, my school student resources, my school teacher resources and all community resources, but no ecologist resources.
		// if I'm a ecologist I can see my resources, my schools' student resources, my schools' resources, all schools' resources, all community resources, 
		// and all ecologist resources.  ie everything non-private.
		
		$isEcologist = false;
		
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$pinnedResources = null;
			
			if ( $roleId == SchoolCommunity::TEACHER_ROLE ) {
				
				// All my school pinned resources 
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
					" PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status from Resource R")
					->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("FR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId)
					->where("R.access_level not in (" . $PERSON . ", " . $ECOLOGISTS . ")" );
					
				$db->setQuery($query);
				
				//error_log("Set id select query created: " . $query->dump());
				
				$pinnedResources = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allPinnedResources = $allPinnedResources + $pinnedResources;
				
			}
			else if ( $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
				
				$isEcologist = true;
				
				// All my school pinned resources
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId )
					->where("R.access_level != " . $PERSON );
					
				$db->setQuery($query);
				
				//error_log("Set id select query created: " . $query->dump());
				
				$pinnedResources = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allPinnedResources = $allPinnedResources + $pinnedResources;
				
			}
			
		}
		
		// Add own pinned resources for everyone
		$query = $db->getQuery(true)
			->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
				"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
			->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
			->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
			->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.person_id = ". $personId );
			
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$pinnedResources = $db->loadAssocList("resource_id");
				
		// Key based union..
		$allPinnedResources = $allPinnedResources + $pinnedResources;
				
						
				
		// Add community pinned resources for everyone
		// All school community level pinned resources
		$query = $db->getQuery(true)
			->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
				"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
			->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
			->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
			->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.access_level = " . $COMMUNITY );
			
		$db->setQuery($query);
		
		//error_log("Set id select query created: " . $query->dump());
		
		$pinnedResources = $db->loadAssocList("resource_id");
		
		// Key based union..
		$allPinnedResources = $allPinnedResources + $pinnedResources;
		
		
		// Add ecologists pinned resources for ecologists only
		if ( $isEcologist ) {
			$query = $db->getQuery(true)
				->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
					"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status   from Resource R")
				->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.access_level = " . $ECOLOGISTS );
				
			$db->setQuery($query);
			
			//error_log("Set id select query created: " . $query->dump());
			
			$pinnedResources = $db->loadAssocList("resource_id");
			
			// Key based union..
			$allPinnedResources = $allPinnedResources + $pinnedResources;
		}
		
		return $allPinnedResources;
				
				
	}
	
	public static function getFavResources () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$allFavResources = array();
		
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$favResources = null;
			
			
			if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId );
				
				// School resources
				$query2 = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL );
				
				// Community resources
				$query3 = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::COMMUNITY );

				$query4 = $query->union($query2)->union($query3) ;
				
				$db->setQuery($query4);
				
				//error_log("ResourceFile::getResourcesByType query created: " . $query4->dump());
						
				$favResources  = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allFavResources = $allFavResources + $favResources;
		
			}
		}
		if ( SchoolCommunity::isEcologist() ) {
			// Ecologist resources
			$query = $db->getQuery(true)
				->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.access_level = " . SchoolCommunity::ECOLOGISTS );

			$db->setQuery($query);
			
			//error_log("ResourceFile::getResourcesByType query created: " . $query->dump());
					
			$favResources  = $db->loadAssocList("resource_id");
			
			// Key based union..
			$allFavResources = $allFavResources + $favResources;
		};
		
		
		return $allFavResources;
	}
	
	public static function getResourcesByType ( $resourceType ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Get resources for this resource type
		// All my files of this type plus all shared with my school files of this type plus all shared with community files of this type
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$allTypeResources = array();
		
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$typeResources = null;
			
			
			if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId )
					->where("R.resource_type = ". $resourceType );
				
				// School resources
				$query2 = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL )
					->where("R.resource_type = ". $resourceType );
				
				// Community resources
				$query3 = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::COMMUNITY )
					->where("R.resource_type = ". $resourceType );

				$query4 = $query->union($query2)->union($query3) ;
				
				$db->setQuery($query4);
				
				//error_log("ResourceFile::getResourcesByType query created: " . $query4->dump());
						
				$typeResources  = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allTypeResources = $allTypeResources + $typeResources;
		
			}
			else if ( $roleId == SchoolCommunity::STUDENT_ROLE ) {
			
				// Just my own resources for now
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId )
					->where("R.resource_type = ". $resourceType );
				
				$db->setQuery($query);
				
				//error_log("ResourceFile::getResourcesByType query created: " . $query->dump());
						
				$typeResources  = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allTypeResources = $allTypeResources + $typeResources;
			}
			
		}
		if ( SchoolCommunity::isEcologist() ) {
			// Ecologist resources
			$query = $db->getQuery(true)
				->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.access_level = " . SchoolCommunity::ECOLOGISTS )
				->where("R.resource_type = ". $resourceType );

			$db->setQuery($query);
			
			//error_log("ResourceFile::getResourcesByType query created: " . $query->dump());
					
			$typeResources  = $db->loadAssocList("resource_id");
			
			// Key based union..
			$allTypeResources = $allTypeResources + $typeResources;
		};
		
		
		return $allTypeResources;
				
	}
	
	public static function getResourcesForApproval () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Only getting tasks
		$resourceType = codes_getCode ( "Task", "resource" );
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
	
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$allResources = array();
		
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$resourcesToApprove = null;
			
			
			if ( $roleId == SchoolCommunity::TEACHER_ROLE ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.set_id, RS.set_name, U.username, ST.st_id, ST.task_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("StudentTasks ST on ST.person_id = R.person_id and ST.set_id = R.set_id and ST.status = " . Badge::PENDING)
					->innerJoin($userDb . "." . $prefix ."users U on ST.person_id = U.id")
					->innerJoin("ResourceSet RS on R.set_id = RS.set_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId )
					->where("R.resource_type = ". $resourceType )
					->order("R.set_id, R.resource_id");

				
				$db->setQuery($query);
				
				//error_log("ResourceFile::getResourcesForApproval query created: " . $query->dump());
						
				$resourcesToApprove  = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allResources = $allResources + $resourcesToApprove;
		
			}			
		}
		
		return $allResources;
				
	}
	
	public static function getStudentResources () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Only getting tasks
		$resourceType = codes_getCode ( "Task", "resource" );
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$allResources = array();
		
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$resourcesToApprove = null;
			
			
			if ( $roleId == SchoolCommunity::STUDENT_ROLE ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.set_id, RS.set_name, ST.st_id, ST.task_id, ST.status, R.resource_type, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, R.s3_status, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes  from Resource R")
					->innerJoin("StudentTasks ST on ST.set_id = R.set_id and ST.status >= " . Badge::PENDING)
					->innerJoin("ResourceSet RS on R.set_id = RS.set_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("ST.person_id = " . $personId )
					->where("R.resource_type = ". $resourceType )
					->order("R.set_id DESC, R.resource_id");

				
				$db->setQuery($query);
				
				//error_log("ResourceFile::getStudentResources query created: " . $query->dump());
						
				$studentResources  = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allResources = $allResources + $studentResources;
		
			}			
		}
		
		return $allResources;
				
	}
	
	public static function searchResources ( $searchStr ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		/*
		// Get resources with the search string in the description
		// NB need to add permissions in here...
		$query = $db->getQuery(true)
			->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
				"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes  from Resource R")
			->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
			->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
			->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.description like \"%". $searchStr . "%\"" );
			
		$db->setQuery($query);
		*/
		
		// Get resources matching search
		// All my files matching search plus all shared with my school files matching search plus all shared with community matching search
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$allSearchResources = array();
		
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$searchResources = null;
			
			
			if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId )
					->where("(R.description like \"%". $searchStr . "%\" or R.upload_filename like \"%". $searchStr . "%\")" );
				
				// School resources
				$query2 = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL )
					->where("(R.description like \"%". $searchStr . "%\" or R.upload_filename like \"%". $searchStr . "%\")" );
				
				// Community resources
				$query3 = $db->getQuery(true)
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::COMMUNITY )
					->where("(R.description like \"%". $searchStr . "%\" or R.upload_filename like \"%". $searchStr . "%\")" );

				$query4 = $query->union($query2)->union($query3) ;
				
				$db->setQuery($query4);
				
				//error_log("ResourceFile::getResourcesByType query created: " . $query4->dump());
						
				$searchResources  = $db->loadAssocList("resource_id");
				
				// Key based union..
				$allSearchResources = $allSearchResources + $searchResources;
		
			}
		}
		if ( SchoolCommunity::isEcologist() ) {
			// Ecologist resources
			$query = $db->getQuery(true)
				->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.description, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.access_level = " . SchoolCommunity::ECOLOGISTS )
				->where("(R.description like \"%". $searchStr . "%\" or R.upload_filename like \"%". $searchStr . "%\")" );

			$db->setQuery($query);
			
			//error_log("ResourceFile::getResourcesByType query created: " . $query->dump());
					
			$searchResources  = $db->loadAssocList("resource_id");
			
			// Key based union..
			$allSearchResources = $allSearchResources + $searchResources;
		};
		
		
		return $allSearchResources;
		
		
	}
	
	
}



?>

