<?php

namespace Biodiv;

// A ResourceSet is a grouping, usually a single upload.

// No direct access to this file
defined('_JEXEC') or die;

class ResourceSet {
	
	const NUM_PER_PAGE = 12;
	const NUM_DAYS_NEW = 14;
	
	private static $badgeIcon = "images/projects/BES/badges/badger-seg3.png";
	
	private $setId;
	private $setName;
	private $resourceType;
	private $articleId;
	private $setText;
	private $direcName;
	private $personId;
	private $userId;
	private $uploadParamsJson;
	private $timestamp;
	private $numInSet;
	private $likedByMe;
	private $numLikes;
	private $isMyFav;
	private $isPin;
	
	// A summary of the first few resources in this set, used when displaying the resources as cards
	private $files;
	
	private $tags;
	
	private $badgeIds;
	
	private $badgeDetails;
	
	
	function __construct( $personId, $setId, $resourceType, $setName, $setText, $schoolId, $uploadParamsJson, $timestamp = null, $numInSet = null, $numLikes = null, $likedByMe = null, $isMyFav = null, $isPin = null, $tagStr = null, $resourcesStr = null, $badgeStr = null )	{
		
		$this->userId = userID();
				
		if ( $setId  ) {
			
			$this->setId = $setId;
			$this->resourceType = $resourceType;
			$this->personId = $personId;
			$this->setName = $setName;
			$this->setText = $setText;
			$this->schoolId = $schoolId;
			$this->uploadParamsJson = $uploadParamsJson;
			$this->timestamp = $timestamp;
			$this->numInSet = $numInSet;
			$this->likedByMe = $likedByMe;
			$this->isMyFav = $isMyFav;
			$this->numLikes = $numLikes;
			$this->isPin = $isPin;
			
			$this->direcName = "biodivimages/resources/person_".$this->personId."/rtype_".$this->resourceType."/set_".$this->setId;
			
			$this->tags = array();
			if ( $tagStr ) {
				
				//error_log ( "Got tagStr: " . $tagStr );
				
				$tagBits = explode('|', $tagStr);
				foreach ( $tagBits as $nextTag ) {
					
					$tagArray = explode(',', $nextTag);
					
					$this->tags[] = (object)array("color_class"=>$tagArray[0], "name"=>$tagArray[1]);
				}
				
			}
			
			$this->files = array();
		
			if ( $resourcesStr ) {
				$resources = explode('^', $resourcesStr);
				foreach ( $resources as $resource ) {
					
					error_log ( "resource = " . $resource );
					$resourceBits = explode('|', $resource);
					
					if ( count( $resourceBits ) == 6 ) {
						error_log ( "adding new resource file" );
						$this->files[] = new ResourceFile (	$resourceBits[0], $resourceBits[1], $this->personId, $this->schoolId, $resourceBits[4],
														$this->setId, null, null, null, null, null, $resourceBits[2], null,
														null, null, null, $this->numInSet, $resourceBits[5], $resourceBits[3]);
					}
																										
				}
			}
			
			$this->badgeIds = array();
			$this->badgeDetails = array();
			if ( $badgeStr ) {
				
				//error_log ( "Got badgeStr: " . $badgeStr );
				
				$badgeBits = explode('|', $badgeStr);
				foreach ( $badgeBits as $nextBadge ) {
					
					$badgeArray = explode(',', $nextBadge);
					
					$id = $badgeArray[0];
					$this->badgeIds[] = $id;
					$this->badgeDetails[] = (object)array("id"=>$badgeArray[0], "name"=>$badgeArray[1], "image"=>$badgeArray[2]);
				}
				
			}
			
		}
	
	}
	
	
	public static function createFromId ( $setId ) {
		
		$userId = userID();
		
		if ( $userId ) {
		
			$setDetails = codes_getDetails ( $setId, "resourceset" );
			
			$personId = (int)$setDetails['person_id'];
			$resourceType = (int)$setDetails['resource_type'];
			$setName = $setDetails['set_name'];
			$setText = $setDetails['description'];
			$schoolId = $setDetails['school_id'];
			$uploadParamsJson = $setDetails['upload_params'];
			$timestamp = $setDetails['timestamp'];
			
			$db = \JDatabase::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("count(*) as num_resources, " .
						"(select count(*) from LikedResourceSet where set_id = " . $setId . ") as num_likes, " .
						"(select count(*) from PinnedResourceSet where set_id = " . $setId . ") as is_pin, " .
						"(select count(*) from LikedResourceSet where set_id = " . $setId . " and person_id = " . $userId . ") as my_like, " .
						"(select count(*) from FavouriteResourceSet where set_id = " . $setId . " and person_id = ".$personId. ") as my_fav, " .
						"(select GROUP_CONCAT( DISTINCT CONCAT_WS(',', T.color_class, T.name) SEPARATOR '|') from Tag T ".
								 " INNER JOIN ResourceTag RT on T.tag_id = RT.tag_id " .
								 " INNER JOIN Resource R2 " .
								 " WHERE RT.resource_id = R2.resource_id " .
								 " AND R2.set_id = " . $setId .
								 ") as tags, " .
						"GROUP_CONCAT( DISTINCT " .
							"CONCAT_WS( ',', CONCAT_WS('|', " .
												"R.resource_id, " .
												"R.resource_type, " .
												"R.filetype, ".
												"R.url, ".
												"R.access_level ".
											") ".
							") SEPARATOR '^') as resources, ".
						"(select GROUP_CONCAT( DISTINCT CONCAT_WS(',', BRS.badge_id, B.name, B.badge_image)  SEPARATOR '|' ) from BadgeResourceSet BRS ".
								 " INNER JOIN Badge B on B.badge_id = BRS.badge_id " .
								 " WHERE BRS.set_id = " . $setId .
								 ") as badges " .
						"from Resource R")
				->where("set_id = " . $setId );
			
			$db->setQuery($query);
			
			error_log("ResourceSet::createFromId select query created: " . $query->dump());
			
			$setCounts = $db->loadObject();
			
			return new self ( $personId, $setId, $resourceType, $setName, $setText, $schoolId, $uploadParamsJson, $timestamp, 
								$setCounts->num_resources,	$setCounts->num_likes, $setCounts->my_like, $setCounts->my_fav, 
								$setCounts->is_pin, $setCounts->tags, $setCounts->resources, $setCounts->badges );
		}
		else {
			return null;
		}
	}
	
	
	public static function getBadgeIcon () {
		
		if ( !self::$badgeIcon ) {
			
			$schoolSettings = getSetting ( "school_icons" );
			$settingsObj = json_decode ( $schoolSettings );
			
			if (property_exists($settingsObj, 'badge_icon')) {
				self::$badgeIcon = $settingsObj->badge_icon;
			}
		}
		return self::$badgeIcon;
	}
	
	
	public function getSetId () {
		return $this->setId;
	}
	
	public function getSetName () {
		return $this->setName;
	}
	
	public function getSetText () {
		return $this->setText;
	}
	
	public function getResourceType () {
		return $this->resourceType;
	}
	
	public function getDirName () {
		return $this->direcName;
	}
	
	public function getDirPath () {
		return $this->direcName;
	}
	
	private function setBadgeIds () {
		
		if ( !$this->badgeIds ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("BRS.badge_id from BadgeResourceSet BRS")
					->where("BRS.set_id = " . $this->setId)
					->order("BRS.badge_id");
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$this->badgeIds = $db->loadColumn("badge_id");
		}
	}
	
	public function getBadgeIds() {
		if ( !$this->badgeIds ) {
			
			$this->setbadgeIds();
		}
		return $this->badgeIds;
	}
	
	
	public function getUploadParamsJson () {
		return $this->uploadParamsJson;
	}
	
	public function getFiles () {
		
		if ( $this->setId ) {
			$db = \JDatabase::getInstance(dbOptions());
					
			$query = $db->getQuery(true)
				->select("R.resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id and R2.deleted = 0) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $this->personId)
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $this->personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->where("set_id = " . $this->setId )
				->where("R.deleted = 0")
				->order("upload_filename");
				
			$db->setQuery($query);
			
			error_log("ResourceFile::getFiles query created: " . $query->dump());
			
			$resourceFiles = $db->loadAssocList('resource_id');
		}
		else {
			$resourceFiles = array();
		}
		
		return $resourceFiles;
	}
	
	
	public function postSet () {
		
		error_log ("About to post set");
		
		$schoolUser = SchoolCommunity::getSchoolUser();
		
		if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
				
			$fields = new \StdClass();
			$fields->set_id = $this->setId;
			$fields->person_id = $schoolUser->person_id;
			
			$success = $db->insertObject("PostedResourceSet", $fields);
			if(!$success){
				error_log ( "PostedResourceSet insert failed" );
			}			
		}
	}
	
	public function printCard ( $schoolUser = null ) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		print '<div id="resourceSet_'.$this->setId.'"class="panel panel-default actionPanel resourceSet" role="button" >';
		
		// print '<div class="panel-header setHeader">';
		
		// print '<div class="row small-gutter">';
		// //print '<div class="col-md-2 col-sm-2 col-xs-2"><img class="img-responsive postSchoolImage" src="'.$this->schoolImage.'"></div>';
		// print '<div class="col-md-12 col-sm-12 col-xs-12 h5 ">'.$this->setName.'</div>';
		// print '</div>'; // row
		
		// print '</div>'; // panel-header
		
		//print '<div id="setBody_'.$this->setId.'" class="panel-body setBody openSet">';
		print '<div id="setBody_'.$this->setId.'" class="panel-body setBody ">';
		
		print '<div id="setCarousel_'.$this->setId.'" class="carousel slide setCarousel">';
		
		$numFiles = count($this->files);
		error_log ( "num files = " . $numFiles );
		if ( $numFiles > 1 ) {
			print '<ol class="carousel-indicators">';
			$i = 0;
			foreach ( $this->files as $file ) {
				print '<li';
				print '     data-target="#setCarousel_'.$this->setId.'"';
				print '      data-slide-to="'.$i.'"';
				if ( $i == 0 ) {
					print '      class="active"';
				}
				print '      aria-current="true"';
				print '      aria-label="Slide '.$i.'"';
				print '   ></li>';
				$i++;
			}
			print '</ol>'; // carousel-indicators
		}
		
		print '<div class="carousel-inner setCarouselInner">';
		$i = 0;
		foreach ( $this->files as $file ) {
			
			if ( $i == 0 ) {
				print '<div class="item active">';
			}
			else {
				print '<div class="item">';
			}
			print '<div id="filesetInfo_'.$i.'_'.$this->setId.'" class="openSet">';
			$file->printSummary();
			print '</div>';
			
			print '</div>'; // carousel-item
			$i++;
		}
		print '</div>'; // carousel-inner
		
		if ( $numFiles > 1 ) {
			print '<a class="left carousel-control setCarouselControl" href="#setCarousel_'.$this->setId.'" data-slide="prev">';
			print '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
			print '<span class="sr-only">Previous</span>';
			print '</a>';
			print '<a class="right carousel-control setCarouselControl" href="#setCarousel_'.$this->setId.'" data-slide="next">';
			print '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
			print '<span class="sr-only">Next</span>';
			print '</a>';
		}
		
		print '</div>'; // setCarousel
		
		print '</div>'; // panel-body
		
		print '<div class="panel-footer setFooter">';
		
		
		
		print '<div class="row setLikes h5">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 text-left">';
		if ( $this->likedByMe ) {
			print '<div id="unlikeSet_'.$this->setId.'" class="unlikeSet h4"><i class="fa fa-lg fa-heart setMyLike" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_UNLIKE").'"></i></div>';
			print '<div id="likeSet_'.$this->setId.'" class="likeSet h4" style="display:none" ><i class="fa fa-lg fa-heart-o" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_LIKE").'"></i></div>';
		}
		else {
			print '<div id="unlikeSet_'.$this->setId.'" class="unlikeSet h4" style="display:none"><i class="fa fa-lg fa-heart setMyLike" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_UNLIKE").'"></i></div>';
			print '<div id="likeSet_'.$this->setId.'" class="likeSet h4"><i class="fa fa-lg fa-heart-o" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_LIKE").'"></i></div>';
		}		
		print '</div>'; // col
		
		print '<div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-3 col-xs-offset-3 text-right">';
		if ( $schoolUser->role_id == SchoolCommunity::ADMIN_ROLE ) {
			
			if ( $this->isPin ) {
				print '<div id="unpinSet_'.$this->setId.'" class="unpinSet h4"><i class="fa fa-lg fa-star setPin" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_UNFEATURE").'"></i></div>';
				print '<div id="pinSet_'.$this->setId.'" class="pinSet h4" style="display:none"><i class="fa fa-lg fa-star-o" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_FEATURE").'"></i></div>';
			}
			else {
				print '<div id="unpinSet_'.$this->setId.'" class="unpinSet h4" style="display:none"><i class="fa fa-lg fa-star setPin" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_UNFEATURE").'"></i></div>';
				print '<div id="pinSet_'.$this->setId.'" class="pinSet h4"><i class="fa fa-lg fa-star-o" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_FEATURE").'"></i></div>';
			}
		}
		print '</div>'; // col-3
		
		print '<div class="col-md-3 col-sm-3 col-xs-3 text-right">';
		if ( $this->isMyFav ) {
			print '<div id="unfaveSet_'.$this->setId.'" class="unfaveSet h4"><i class="fa fa-lg fa-bookmark setMyFave" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_UNFAVE").'"></i></div>';
			print '<div id="faveSet_'.$this->setId.'" class="faveSet h4" style="display:none"><i class="fa fa-lg fa-bookmark-o" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_FAVE").'"></i></div>';
		}
		else {
			print '<div id="unfaveSet_'.$this->setId.'" class="unfaveSet h4" style="display:none"><i class="fa fa-lg fa-bookmark setMyFave" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_UNFAVE").'"></i></div>';
			print '<div id="faveSet_'.$this->setId.'" class="faveSet h4"><i class="fa fa-lg fa-bookmark-o" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCESET_FAVE").'"></i></div>';
		}		
		print '</div>';
				
		print '</div>'; // row
		
		
		print '<div class="row">';
		
		print '<div id="numSetLikes_'.$this->setId.'" class="col-md-10 col-sm-9 col-xs-9">';
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCESET_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCESET_MANY_LIKES");
		}
		print '</div>';
		print '</div>'; // row
		
		print '<div id="setInfo_'.$this->setId.'" class="openSet">';
		print '<div class="setText h4">'.$this->setName.'</div>';
		
		print '<div class="setTags">';
		foreach ( $this->tags as $tag ) {
			
			print '<div class="tag '.$tag->color_class.'">' . $tag->name . '</div>';
		}
		print '</div>';
		
		print '</div>'; // openSet
		
		print '<div class="setBadges">';
		if ( $this->badgeDetails ) {
			foreach ( $this->badgeDetails as $badge ) {
				
				print '<div id="badge_'.$badge->id.'" class="setBadge" role="button" data-toggle="tooltip" title="'.
					$badge->name.'"><img src="' . $badge->image . '" class="img-responsive" alt="badge icon"></div>';
			}
		}
		print '</div>';
		
		//print '<div class="postDate small">'.date( "d/m/Y", strtotime($this->timestamp)).'</div>';
		
		
		print '</div>'; // panel-footer
		print '</div>'; // panel
		
		//print '</a>';
		
	}
	
	
	public function printFullHeader ( $schoolUser = null ) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		print '<div class="fullResourceSetGrid">';
		
		print '<div class="fullResourceSetName">';
		print '<h3>'.$this->getSetName().'</h3>';
		print '</div>'; //fullResourceSetName
		
		print '<div class="fullResourceSetText">';
		print '<h4>'.$this->getSetText().'</h4>';
		print '</div>'; // fullResourceSetText
		
		print '<div class="fullResourceSetBadges">';
		print '<div id="setBadges">';
	
		if ( $this->badgeDetails ) {
			foreach ( $this->badgeDetails as $badge ) {
				
				//print '<div id="badge_'.$badge->id.'" class="setBadge" role="button" data-toggle="tooltip" title="'.
				//	$badge->name.'"><img src="' . $badge->image . '" class="img-responsive" alt="badge icon"></div>';
				
				print '<div id="badge_'.$badge->id.'" class="setBadge" role="button" data-toggle="modal" target="#badgeModal"><img src="' . $badge->image . '" class="img-responsive" alt="badge icon"></div>';
			}
		}
		print '</div>';
		print '</div>';
		
		print '<div class="fullResourceSetAdd">';
		
		$canEdit = false;
		$canAddBadge = false;
		
		if ( $schoolUser->role_id == SchoolCommunity::ADMIN_ROLE ) {
			$canEdit = true;
			$canAddBadge = true;
		}
		else if ( $schoolUser->person_id == $this->personId ) {
			$canEdit = true;
		}
		
		if ( $canEdit ) {
			
			print '<div id="addFilesToSet_'.$this->setId.'" class="addFilesToSet text-center" role="button" '.
					'data-toggle="modal" data-target="#addFilesModal">'.
					'<h4><i class="fa fa-lg fa-plus-square-o"></i></h4>'.
					'<div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCESET_ADD_FILES").'</div></div>';
			
			// data-toggle="tooltip" title="'.	\JText::_("COM_BIODIV_RESOURCESET_ADD_FILES").'"
			//print '<h4><div id="addFilesToSet_'.$this->setId.'" class="btn btn-primary addFilesToSet" data-toggle="modal" data-target="#addFilesModal">'.\JText::_("COM_BIODIV_RESOURCESET_ADD_FILES").'</div></h4>';
		}
		
		print '</div>'; // fullResourceSetAdd
		
		$shareOptions = array(SchoolCommunity::PERSON=>\JText::_("COM_BIODIV_RESOURCESET_SHARE_PRIVATE"),
								SchoolCommunity::SCHOOL=>\JText::_("COM_BIODIV_RESOURCESET_SHARE_SCHOOL"),
								SchoolCommunity::COMMUNITY=>\JText::_("COM_BIODIV_RESOURCESET_SHARE_COMMUNITY"));
								
		$shareStatus = array(SchoolCommunity::PERSON=>"fa fa-lock fa-lg",
								SchoolCommunity::SCHOOL=>"fa fa-building-o fa-lg",
								SchoolCommunity::COMMUNITY=>"fa fa-globe fa-lg",
								SchoolCommunity::ECOLOGISTS=>"fa fa-leaf fa-lg");
		
		print '<div class="fullResourceSetShare">';
		if ( $canEdit ) {
			
			print '<div id="shareSet"  href="#shareMenu" class="openShareMenu text-center" role="button" data-toggle="collapse" >'.
					'<h4><i class="fa fa-lg fa-share-alt"></i></h4>'.
					'<div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCESET_SHARE").'</div></div>';
			
			// print '<div id="share_resource_'.$tagStr.$resourceId.'" class="share_resource share_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
					// \JText::_("COM_BIODIV_RESOURCEFILE_SHARE").'"   ><h4 ><i class="fa fa-share-alt fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_SHARE").'</div></div>';
					
			print '<div id="shareMenu" class="miniMenu collapse" >';
				
			$isStudent = $schoolUser->role_id == SchoolCommunity::STUDENT_ROLE;
			if ( !$isStudent )  {
				foreach ($shareOptions as $shareId=>$shareOpt ) {
					print '<div id="shareSet_'.$shareId.'_'.$this->setId.'" '.
						'class="shareSet h4 btn miniMenuBtn text-left" ><div class="menuIcon"><i class="' . 
						$shareStatus[$shareId] . '"></i></div><div class="menuText">' . $shareOpt . '</div></div>';
				}
			}
			
			print '        <button id="hide_shareMenu" href="#shareMenu" type="button" class="btn btn-default" data-toggle="collapse" >'.\JText::_("COM_BIODIV_RESOURCESET_CANCEL").'</button>';
		
			print '</div>'; //shareMenu
				
		}
		print '</div>'; // fullResourceSetShare
		
		
		
		print '<div class="fullResourceSetLike">';
		if ( $this->likedByMe ) {
			
			print '<div id="unlikeSet_'.$this->setId.'" class="unlikeSet text-center"  role="button"  data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_UNLIKE").'">';
			print '<h4><i class="fa fa-lg fa-heart setMyLike"></i></h4>';
			//print  '<div class="hidden-xs text-center">'. \JText::_("COM_BIODIV_RESOURCESET_UNLIKE"). '</div>'.
			print '</div>';
			
			print '<div id="likeSet_'.$this->setId.'" class="likeSet text-center"  role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_LIKE").'" style="display:none">';
			print '<h4><i class="fa fa-lg fa-heart-o"></i></h4>';
			//print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_LIKE").'</div>';
			print '</div>';
		}
		else {
			
			print '<div id="unlikeSet_'.$this->setId.'" class="unlikeSet text-center"  role="button"  data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_UNLIKE").'" style="display:none">';
			print '<h4><i class="fa fa-lg fa-heart setMyLike"></i></h4>';
			//print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_UNLIKE").'</div>';
			print '</div>';
			
			print '<div id="likeSet_'.$this->setId.'" class="likeSet text-center"  role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_LIKE").'">';
			print '<h4><i class="fa fa-lg fa-heart-o"></i></h4>';
			//print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_LIKE").'</div>';
			print '</div>';
		}
		
		print '<div id="numSetLikes_'.$this->setId.'" class="text-center" >';
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCESET_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCESET_MANY_LIKES");
		}
		print '</div>';
		
		print '</div>'; // fullResourceSetLike
		
		// print '<div class="fullResourceSetNumLikes">';
		
		// print '<div id="numSetLikes_'.$this->setId.'" class="text-center" >';
		// $numLikes = $this->numLikes;
		// if ( $numLikes == 1 ) {
			// print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCESET_SINGLE_LIKE");
		// }
		// else {
			
			// print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCESET_MANY_LIKES");
		// }
		// // '<div class="hidden-xs">'. \JText::_("COM_BIODIV_RESOURCESET_UNLIKE"). '</div>'.	
		
		// //print '<div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCESET_LIKE").'</div>';
		// print '</div>';
		
		// print '</div>'; // fullResourceSetNumLikes
			
		
		print '<div class="fullResourceSetFave">';
		if ( $this->isMyFav ) {
			print '<div id="unfaveSet_'.$this->setId.'" class="unfaveSet text-center" role="button"  data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_UNFAVE").'">';
			print '<h4><i class="fa fa-lg fa-bookmark setMyFave"></i></h4>';
			print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_UNFAVE").'</div>';
			print '</div>';
			print '<div id="faveSet_'.$this->setId.'" class="faveSet text-center" role="button"  data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_FAVE").'" style="display:none">';
			print '<h4><i class="fa fa-lg fa-bookmark-o"></i></h4>';
			print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_FAVE").'</div>';
			print '</div>';
		}
		else {
			print '<div id="unfaveSet_'.$this->setId.'" class="unfaveSet text-center" role="button"  data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_UNFAVE").'" style="display:none">';
			print '<h4><i class="fa fa-lg fa-bookmark setMyFave"></i></h4>';
			print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_UNFAVE").'</div>';
			print '</div>';
			print '<div id="faveSet_'.$this->setId.'" class="faveSet text-center" role="button"  data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCESET_FAVE").'">';
			print '<h4><i class="fa fa-lg fa-bookmark-o"></i></h4>';
			print '<div class="hidden-xs text-center">'.\JText::_("COM_BIODIV_RESOURCESET_FAVE").'</div>';
			print '</div>';
		}		
		print '</div>'; // fullResourceSetFave
		
		
		print '<div class="fullResourceSetBadge">';
		
		if ( $canAddBadge ) {
			
			print '<div id="addBadgeToSet_'.$this->setId.'" class="addBadgeToSet text-center" role="button" '.
					'data-toggle="modal" data-target="#addBadgeModal">'.
					'<h4><i class="fa fa-lg fa-plus-circle"></i></h4>'.
					'<div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCESET_ADD_BADGE").'</div></div>';
			
		}
		
		print '</div>'; // fullResourceSetBadge
		
		print '</div>'; // fullResourceSetGrid
	}
	
	
	public function printWork ( $schoolUser, $image, $name ) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		print '<div id="resourceSet_'.$this->setId.'"class="panel panel-default actionPanel workSet" role="button" >';
		
		print '<div class="panel-header workHeader">';
		
		print '<div class="row small-gutter">';
		print '<div class="col-md-2 col-sm-2 col-xs-2"><img class="img-responsive workSchoolImage" src="'.$image.'"></div>';
		print '<div class="col-md-10 col-sm-10 col-xs-10 h4 workSchoolName">'.$name.'</div>';
		print '</div>'; // row
		
		print '</div>'; // panel-header
		
		print '<div class="panel-body workBody">';
		
		print '<div id="setCarousel_'.$this->setId.'" class="carousel slide setCarousel">';
		
		$numFiles = count($this->files);
		error_log ( "num files = " . $numFiles );
		if ( $numFiles > 1 ) {
			print '<ol class="carousel-indicators">';
			$i = 0;
			foreach ( $this->files as $file ) {
				print '<li';
				print '     data-target="#setCarousel_'.$this->setId.'"';
				print '      data-slide-to="'.$i.'"';
				if ( $i == 0 ) {
					print '      class="active"';
				}
				print '      aria-current="true"';
				print '      aria-label="Slide '.$i.'"';
				print '   ></li>';
				$i++;
			}
			print '</ol>'; // carousel-indicators
		}
		
		print '<div class="carousel-inner setCarouselInner">';
		$i = 0;
		foreach ( $this->files as $file ) {
			
			if ( $i == 0 ) {
				print '<div class="item active">';
			}
			else {
				print '<div class="item">';
			}
			$file->printThumbnail("workMedia");
			
			print '</div>'; // carousel-item
			$i++;
		}
		print '</div>'; // carousel-inner
		
		if ( $numFiles > 1 ) {
			print '<a class="left carousel-control setCarouselControl" href="#setCarousel_'.$this->setId.'" data-slide="prev">';
			print '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
			print '<span class="sr-only">Previous</span>';
			print '</a>';
			print '<a class="right carousel-control setCarouselControl" href="#setCarousel_'.$this->setId.'" data-slide="next">';
			print '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
			print '<span class="sr-only">Next</span>';
			print '</a>';
		}
		
		print '</div>'; // setCarousel
		
		
		
		// print '<div id="postCarousel_'.$this->setId.'" class="carousel slide postCarousel">';
		
		// $numFiles = count($this->files);
		// if ( $numFiles > 1 ) {
			// print '<ol class="carousel-indicators">';
			// $i = 0;
			// foreach ( $this->files as $file ) {
				// print '<li';
				// print '     data-target="#postCarousel_'.$this->setId.'"';
				// print '      data-slide-to="'.$i.'"';
				// if ( $i == 0 ) {
					// print '      class="active"';
				// }
				// print '      aria-current="true"';
				// print '      aria-label="Slide '.$i.'"';
				// print '   ></li>';
				// $i++;
			// }
			// print '</ol>'; // carousel-indicators
		// }
		
		// print '<div class="carousel-inner">';
		// $i = 0;
		// foreach ( $this->files as $file ) {
			
			// if ( $i == 0 ) {
				// print '<div class="item active">';
			// }
			// else {
				// print '<div class="item">';
			// }
			
			// $filetype = $file[1];
			// $fileTypeBits = explode('/', $filetype );
				
			// $mainType = $fileTypeBits[0];
			
			// if ( $mainType == "image" ) {
				// print '<img src="'.$file[2].'" type="'.$filetype.'" class="postMedia" alt="post image">';
			// }
			// else if ( $mainType == "video" ) {
				// print '<video src="'.$file[2].'" type="'.$filetype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" class="postMedia"  ></video>';
			// }
			// else if ( $mainType == "audio" ) {
				// print '<audio src="'.$file[2].'" type="'.$filetype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" class="postMedia"  ></audio>';
			// }
			
			// print '</div>'; // carousel-item
			// $i++;
		// }
		// print '</div>'; // carousel-inner
		
		// if ( $numFiles > 1 ) {
			// print '<a class="left carousel-control" href="#postCarousel_'.$this->setId.'" data-slide="prev">';
			// print '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
			// print '<span class="sr-only">Previous</span>';
			// print '</a>';
			// print '<a class="right carousel-control" href="#postCarousel_'.$this->setId.'" data-slide="next">';
			// print '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
			// print '<span class="sr-only">Next</span>';
			// print '</a>';
		// }
		
		// print '</div>'; // postCarousel
		
		print '</div>'; // panel-body
		
		print '<div class="panel-footer workFooter">';
		
		print '<div class="workText">'.$this->setName.'. '.$this->setText.'</div>';
		print '<div class="workDate">'.date( "d/m/Y", strtotime($this->timestamp)).'</div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		
	}
	
	
	public static function getLastSetId () {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$db = \JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("max(set_id) from ResourceSet")
			->where("person_id = " . $personId );
		
		$db->setQuery($query);
		
		$setId = $db->loadResult();
		
		return $setId;
	}
	
	
	//public static function createResourceSet ( $schoolId, $resourceType, $setName, $setText ) {
	public static function createResourceSet ( $schoolId, $resourceType, $setName, $setText, $uploadParams ) {
		
		$personId = userID();
			
		if($personId){
				
			// Create a Joomla article with the text.
			// For now set to 0
			$articleId = 0;
			
			$setFields = (object) [
				'person_id' => $personId,
				'school_id' => $schoolId,
				'resource_type' => $resourceType,
				'set_name' => $setName,
				'description' => $setText,
				'article_id' => $articleId,
				'upload_params' => $uploadParams
			];

			$setId = codes_insertObject($setFields, 'resourceset');
			
			$instance = new self( $personId, $setId,  $resourceType, $setName, $setText, $schoolId, $uploadParams);
		
			return $instance;
		}
		else {
			return null;
		}
	}
	
	public static function canEdit ( $setId ) {
		
		$returnValue = false;
		
		if ( SchoolCommunity::isAdmin() ) {
			$returnValue = true;
		}
		else {
		
			$user = userID();
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("person_id from ResourceSet")
					->where("set_id = " . $setId);
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$resourceSetPerson = $db->loadResult();
			
			
			if ( $user == $resourceSetPerson ) {
				$returnValue = true;
			}
		}
		
		return $returnValue;
			
	}
	
	
	public static function getFilterWhereStr($filters) {
	
		$typeArray = array();
		$tagArray = array();
		
		$whereStr = "";
		$whereArray = array();
		
		$personId = userID();
		
		if ( $personId and $filters ) {
	
			foreach ( $filters as $filter ) {
				$keyVal = explode('_', $filter);
				if ( $keyVal[0] == 'type' ) {
					$typeArray[] = $keyVal[1];
				}
				else if ( $keyVal[0] == 'tag' ) {
					$tagArray[] = $keyVal[1];
				}
				else if ( $keyVal[0] == 'fav' ) {
					$whereArray[] = " RS.set_id in ( SELECT set_id from FavouriteResourceSet where person_id = ".$personId.") ";
				}
				else if ( $keyVal[0] == 'badges' ) {
					$whereArray[] = " RS.set_id in ( SELECT set_id from BadgeResourceSet  ) ";
				}
				else if ( $keyVal[0] == 'badge' ) {
					$whereArray[] = " RS.set_id in ( SELECT set_id from BadgeResourceSet where badge_id = ".$keyVal[1]."  ) ";
				}
				else if ( $keyVal[0] == 'mine' ) {
					$whereArray[] = " (RS.person_id = " . $personId . ") ";
				}
				else if ( $keyVal[0] == 'pin' ) {
					$whereArray[] = " RS.set_id in ( SELECT set_id from PinnedResourceSet ) ";
				}
				else if ( $keyVal[0] == 'like' ) {
					$whereArray[] = " RS.set_id in ( SELECT set_id from LikedResourceSet where person_id = ".$personId.") ";
				}
				else if ( $keyVal[0] == 'new' ) {
					$whereArray[] = " (TIMESTAMPDIFF(DAY, RS.timestamp, NOW())<".self::NUM_DAYS_NEW . ") " ;
				}
			}
			if ( count($typeArray) > 0 ) {
				$whereArray[] = " R.resource_type in (" . implode(',', $typeArray) . ") ";
			}
			if ( count($tagArray) > 0 ) {
				$whereArray[] = " (R.resource_id in ( select resource_id from `ResourceTag` RT where tag_id in (".implode(',', $tagArray)."))) ";
			
			}
			
			$whereStr = " ( " . implode(' AND ', $whereArray) . " )";
			
		}
		
		return $whereStr;
	}
	
	
	
	public static function getBadgeResourceSets ( $schoolUser, $badgeId, $page = 1, $pageLength = self::NUM_PER_PAGE ) {
		
		$filters = ["badge_" . $badgeId];
		
		return self::searchResourceSets ( $schoolUser, null , $filters, $page, $pageLength );
		
	}
	
	
	public static function getResourceSetBadges ( $schoolUser, $setId ) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$badges = array();
		
		if ( $schoolUser ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("BRS.badge_id, B.name, B.badge_image from BadgeResourceSet BRS")
					->innerJoin("Badge B on B.badge_id = BRS.badge_id")
					->where("set_id = " . $setId);
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$badges = $db->loadObjectList("badge_id");
		}
		
		return $badges;
		
	}
	
	
	public static function setResourceSetBadges ( $schoolUser, $setId, $badgeIds ) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		if ( $schoolUser ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$conditions = array(
				$db->quoteName('set_id') . ' = ' . $setId );
			$query = $db->getQuery(true)
				->delete($db->quoteName('BadgeResourceSet'))
				->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();
				
				
			foreach ( $badgeIds as $badgeId ) {
				$badgeFields = (object) [
					'set_id' => $setId,
					'badge_id' => $badgeId,
					'timestamp' => "CURRENT_TIMESTAMP"	];
					
				$success = $db->insertObject("BadgeResourceSet", $badgeFields);
				if(!$success){
					error_log ( "BadgeResourceSet insert failed" );
				}
				
			}
		}
		
		return self::getResourceSetBadges ( $schoolUser, $setId );
		
	}
	
	
	public static function searchResourceSets ( $schoolUser, $searchStr , $filters, $page = 1, $pageLength = self::NUM_PER_PAGE) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$personId = $schoolUser->person_id;
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = ResourceSet::getFilterWhereStr($filters);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Get resources matching search
		
		
		$roleId = $schoolUser->role_id;
		$schoolId = $schoolUser->school_id;
		
		$allSearchResources = array();
		
		$finalQuery = null;
		
		if ( $roleId == SchoolCommunity::ADMIN_ROLE ) {
			
			$countQuery = $db->getQuery(true)
				->select("RS.person_id, RS.set_id, RS.resource_type, RS.set_name, RS.description, RS.school_id, RS.timestamp as tstamp " .
					"  from ResourceSet RS")
				->innerJoin("Resource R on R.set_id = RS.set_id and R.deleted = 0")
				->innerJoin("ResourceType RT on RT.type_id = R.resource_type and RT.seq > 0");
			
			
			
			if ( $searchStr ) {
				$countQuery->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
				
			if ( $filters ) {
				$countQuery->where($whereStr);
			}
			
			$countQuery->group("R.set_id");
		}
		else  if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
				
			// All my files matching search plus all shared with my school files matching search plus all shared with community matching search
			$countQuery = $db->getQuery(true)
				->select("RS.person_id, RS.set_id, RS.resource_type, RS.set_name, RS.description, RS.school_id, RS.timestamp as tstamp " .
					"  from ResourceSet RS")
				->innerJoin("Resource R on R.set_id = RS.set_id and R.deleted = 0")
				->innerJoin("ResourceType RT on RT.type_id = R.resource_type and RT.seq > 0")
				->where("((R.access_level = " . SchoolCommunity::COMMUNITY . ")" . 
						" or (R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL . ")" . 
						" or (RS.person_id = " . $personId . " and R.access_level = " . SchoolCommunity::PERSON . "))" );
						
			if ( $searchStr ) {
				$countQuery->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
			if ( $filters ) {
				$countQuery->where($whereStr);
			}
			
			$countQuery->group("RS.set_id");
			
			error_log("ResourceFile::searchResources query created: " . $countQuery->dump());
		}
		else {
			return null;
		}
		
		$db->setQuery($countQuery);
		$db->execute();
		
		$totalRows = $db->getNumRows();
		
		
		if ( $roleId == SchoolCommunity::ADMIN_ROLE ) {

			$finalQuery = $db->getQuery(true)
				->select("RS.person_id, RS.set_id, RS.resource_type, RS.set_name, RS.description, RS.school_id, RS.timestamp as tstamp, " .
					"count(R.resource_id) as num_in_set, " .
					"(SELECT COUNT(*) FROM LikedResourceSet LRS where LRS.set_id = RS.set_id) as num_likes, " .
					"(SELECT COUNT(*) FROM LikedResourceSet LRS2 where LRS2.set_id = RS.set_id and LRS2.person_id = ".$personId.") as my_like, " .
					"(SELECT COUNT(*) FROM FavouriteResourceSet FRS where FRS.set_id = RS.set_id and FRS.person_id = ".$personId.") as my_fav, " .
					"(SELECT COUNT(*) FROM PinnedResourceSet PRS where PRS.set_id = RS.set_id) as is_pin, " .
					"(select GROUP_CONCAT( DISTINCT CONCAT_WS(',', T.color_class, T.name) SEPARATOR '|') from Tag T ".
								 " INNER JOIN ResourceTag RT on T.tag_id = RT.tag_id " .
 								 " INNER JOIN Resource R2 " .
								 " WHERE RT.resource_id = R2.resource_id " .
 								 " AND R2.set_id = RS.set_id " .
								 ") as tags, " .
					"GROUP_CONCAT( DISTINCT " .
						"CONCAT_WS( ',', CONCAT_WS('|', " .
											"R.resource_id, " .
											"R.resource_type, " .
											"R.filetype, ".
											"R.url, ".
											"R.access_level, ".
											"R.s3_status".
										") ".
						") SEPARATOR '^') as resources, ".
					"(select GROUP_CONCAT( DISTINCT CONCAT_WS(',', BRS.badge_id, B.name, B.badge_image)  SEPARATOR '|' ) from BadgeResourceSet BRS ".
								 " INNER JOIN Badge B on B.badge_id = BRS.badge_id " .
								 " WHERE BRS.set_id = RS.set_id " .
								 ") as badges ".
					
					"  from ResourceSet RS")
				->innerJoin("Resource R on R.set_id = RS.set_id and R.deleted = 0")
				->innerJoin("ResourceType RT on RT.type_id = R.resource_type and RT.seq > 0");
			
			if ( $searchStr ) {
				$finalQuery->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
				
			if ( $filters ) {
				$finalQuery->where($whereStr);
			}
			
			$finalQuery->group("R.set_id");
		}
		else if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
			
			$finalQuery = $db->getQuery(true)
				->select("RS.person_id, RS.set_id, RS.resource_type, RS.set_name, RS.description, RS.school_id, RS.timestamp as tstamp, " .
					"count(R.resource_id) as num_in_set, " .
					"(SELECT COUNT(*) FROM LikedResourceSet LRS where LRS.set_id = RS.set_id) as num_likes, " .
					"(SELECT COUNT(*) FROM LikedResourceSet LRS2 where LRS2.set_id = RS.set_id and LRS2.person_id = ".$personId.") as my_like, " .
					"(SELECT COUNT(*) FROM FavouriteResourceSet FRS where FRS.set_id = RS.set_id and FRS.person_id = ".$personId.") as my_fav, " .
					"(SELECT COUNT(*) FROM PinnedResourceSet PRS where PRS.set_id = RS.set_id) as is_pin, " .
					"(select GROUP_CONCAT( DISTINCT CONCAT_WS(',', T.color_class, T.name) SEPARATOR '|') from Tag T ".
								 " INNER JOIN ResourceTag RT on T.tag_id = RT.tag_id " .
								 " INNER JOIN Resource R2 " .
								 " WHERE RT.resource_id = R2.resource_id " .
								 " AND R2.set_id = RS.set_id " .
								 ") as tags, " .
					"GROUP_CONCAT( DISTINCT " .
						"CONCAT_WS( ',', CONCAT_WS('|', " .
											"R.resource_id, " .
											"R.resource_type, " .
											"R.filetype, ".
											"R.url, ".
											"R.access_level, ".
											"R.s3_status".
										") ".
						") SEPARATOR '^') as resources, ".
					"(select GROUP_CONCAT( DISTINCT CONCAT_WS(',', BRS.badge_id, B.name, B.badge_image)  SEPARATOR '|') from BadgeResourceSet BRS ".
								 " INNER JOIN Badge B on B.badge_id = BRS.badge_id " .
								 " WHERE BRS.set_id = RS.set_id " .
								 ") as badges ".
					"  from ResourceSet RS")
				->innerJoin("Resource R on R.set_id = RS.set_id and R.deleted = 0")
				->innerJoin("ResourceType RT on RT.type_id = R.resource_type and RT.seq > 0")
				->where("((R.access_level = " . SchoolCommunity::COMMUNITY . ")" . 
						" or (R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL . ")" . 
						" or (RS.person_id = " . $personId . " and R.access_level = " . SchoolCommunity::PERSON . "))" );
									
			if ( $searchStr ) {
				$finalQuery->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
			if ( $filters ) {
				$finalQuery->where($whereStr);
			}
			
			$finalQuery->group("RS.set_id");

			//$finalQuery = $query->union($query2)->union($query3)->group("R.set_id") ;
			
		}
		
		$finalQuery->order("tstamp DESC");
		
		//error_log("ResourceSet::searchResurces query created: " . $finalQuery->dump());
		
		// $db->setQuery($finalQuery);
		// $db->execute();
		
		// $totalRows = $db->getNumRows();
		
		//error_log ( "ResourceFile search total rows = " . $totalRows );
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
		error_log("ResourceFile::searchResurces query created: " . $finalQuery->dump());
		
		//$db->execute();
		
		$allSearchSets  = $db->loadObjectList("set_id");
		
		return (object)array("total"=>$totalRows, "sets"=>$allSearchSets);
		
		
	}
	
	public static function getSchoolWork ( $schoolUser, $searchStr, $page = 1, $pageLength = self::NUM_PER_PAGE) {
		
		if ( $schoolUser == null ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		$personId = $schoolUser->person_id;
		
		if ( !$personId ) {
			return null;
		}
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
			
		$db = \JDatabaseDriver::getInstance($options);
		
		$roleId = $schoolUser->role_id;
		$schoolId = $schoolUser->school_id;
		
		$allSearchResources = array();
		
		$finalQuery = null;
		
		if ( $roleId == SchoolCommunity::ADMIN_ROLE ) {
			
			$queryTB = $db->getQuery(true)
				->select("TB.badge_id, TB.class_id as account_id, SC.name, A.image, TB.set_id, " .
					"RS.person_id, RS.resource_type, RS.set_name, RS.description, TB.complete_text, SC.school_id, " .
					"count(R.resource_id) as num_in_set, " .
					"GROUP_CONCAT( DISTINCT CONCAT_WS( ',', CONCAT_WS('|', " .
						"R.resource_id, R.resource_type, R.filetype, R.url, R.access_level, R.s3_status ) ) SEPARATOR '^') as resources, " .
					"RS.timestamp as tstamp	" .
					"from TeacherBadges TB")
				->innerJoin("SchoolClass SC on SC.class_id = TB.class_id")
				->innerJoin("Avatar A on A.avatar_id = SC.avatar")
				->innerJoin("ResourceSet RS on RS.set_id = TB.set_id")
				->innerJoin("Resource R on R.set_id = RS.set_id");
				
			if ( $searchStr ) {
				$queryTB->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
			
			$queryTB->group("TB.badge_id, account_id, TB.set_id");
				
			$querySB = $db->getQuery(true)
				->select("SB.badge_id, SB.person_id as account_id, U.username as name, A.image, SB.set_id, " .
					"RS.person_id, RS.resource_type, RS.set_name, RS.description, SB.complete_text, SU.school_id, " .
					"count(R.resource_id) as num_in_set, " .
					"GROUP_CONCAT( DISTINCT CONCAT_WS( ',', CONCAT_WS('|', " .
						"R.resource_id, R.resource_type, R.filetype, R.url, R.access_level, R.s3_status ) ) SEPARATOR '^') as resources, " .
					"RS.timestamp as tstamp	" .
					"from StudentBadges SB")
				->innerJoin("SchoolUsers SU on SU.person_id = SB.person_id")
				->innerJoin($userDb . "." . $prefix ."users U on SB.person_id = U.id")
				->innerJoin("Avatar A on A.avatar_id = SU.avatar")
				->innerJoin("ResourceSet RS on RS.set_id = SB.set_id")
				->innerJoin("Resource R on R.set_id = RS.set_id");
			
			if ( $searchStr ) {
				$querySB->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
						
			$querySB->group("SB.badge_id, account_id, SB.set_id");	
				
			$finalQuery = $queryTB->union($querySB);
			
		}
		else if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
			
			$queryTB = $db->getQuery(true)
				->select("TB.badge_id, TB.class_id as account_id, SC.name, A.image, TB.set_id, " .
					"RS.person_id, RS.resource_type, RS.set_name, RS.description, TB.complete_text, SC.school_id, " .
					"count(R.resource_id) as num_in_set, " .
					"GROUP_CONCAT( DISTINCT CONCAT_WS( ',', CONCAT_WS('|', " .
						"R.resource_id, R.resource_type, R.filetype, R.url, R.access_level, R.s3_status ) ) SEPARATOR '^') as resources, " .
					"RS.timestamp as tstamp	" .
					"from TeacherBadges TB")
				->innerJoin("SchoolClass SC on SC.class_id = TB.class_id")
				->innerJoin("Avatar A on A.avatar_id = SC.avatar")
				->innerJoin("ResourceSet RS on RS.set_id = TB.set_id")
				->innerJoin("Resource R on R.set_id = RS.set_id")
				->where("SC.school_id = " . $schoolId);
				
			if ( $searchStr ) {
				$queryTB->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
			
			$queryTB->group("TB.badge_id, account_id, TB.set_id");
				
			$querySB = $db->getQuery(true)
				->select("SB.badge_id, SB.person_id as account_id, U.username as name, A.image, SB.set_id, " .
					"RS.person_id, RS.resource_type, RS.set_name, RS.description, SB.complete_text, SU.school_id, " .
					"count(R.resource_id) as num_in_set, " .
					"GROUP_CONCAT( DISTINCT CONCAT_WS( ',', CONCAT_WS('|', " .
						"R.resource_id, R.resource_type, R.filetype, R.url, R.access_level, R.s3_status ) ) SEPARATOR '^') as resources, " .
					"RS.timestamp as tstamp	" .
					"from StudentBadges SB")
				->innerJoin("SchoolUsers SU on SU.person_id = SB.person_id")
				->innerJoin($userDb . "." . $prefix ."users U on SB.person_id = U.id")
				->innerJoin("Avatar A on A.avatar_id = SU.avatar")
				->innerJoin("ResourceSet RS on RS.set_id = SB.set_id")
				->innerJoin("Resource R on R.set_id = RS.set_id")
				->where("SU.school_id = " . $schoolId);
			
			if ( $searchStr ) {
				$querySB->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
						
			$querySB->group("SB.badge_id, account_id, SB.set_id");	
				
			$finalQuery = $queryTB->union($querySB);
						
		}
		else if ( $roleId == SchoolCommunity::STUDENT_ROLE ) {
			
			$finalQuery = $db->getQuery(true)
				->select("SB.badge_id, SB.person_id as account_id, U.username as name, A.image, SB.set_id, " .
					"RS.person_id, RS.resource_type, RS.set_name, RS.description, SB.complete_text, SU.school_id, " .
					"count(R.resource_id) as num_in_set, " .
					"GROUP_CONCAT( DISTINCT CONCAT_WS( ',', CONCAT_WS('|', " .
						"R.resource_id, R.resource_type, R.filetype, R.url, R.access_level, R.s3_status ) ) SEPARATOR '^') as resources, " .
					"RS.timestamp as tstamp	" .
					"from StudentBadges SB")
				->innerJoin("SchoolUsers SU on SU.person_id = SB.person_id")
				->innerJoin($userDb . "." . $prefix ."users U on SB.person_id = U.id")
				->innerJoin("Avatar A on A.avatar_id = SU.avatar")
				->innerJoin("ResourceSet RS on RS.set_id = SB.set_id")
				->innerJoin("Resource R on R.set_id = RS.set_id")
				->where("SU.person_id = " . $personId);
			
			if ( $searchStr ) {
				$finalQuery->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
			}
						
			$finalQuery->group("SB.badge_id, account_id, SB.set_id");	
			
		}
		
		$finalQuery->order("tstamp DESC");
		
		error_log("ResourceSet::searchResurces query created: " . $finalQuery->dump());
		
		$db->setQuery($finalQuery);
		$db->execute();
		
		$totalRows = $db->getNumRows();
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
		error_log("ResourceFile::searchResurces query created: " . $finalQuery->dump());
		
		//$db->execute();
		
		$allSets  = $db->loadObjectList("set_id");
		
		return (object)array("total"=>$totalRows, "sets"=>$allSets);
		
		
	}
}



?>

