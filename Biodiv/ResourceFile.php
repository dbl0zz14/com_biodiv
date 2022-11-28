<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class ResourceFile {
	
	// Default page length
	const NUM_PER_PAGE = 8;
	const NUM_DAYS_NEW = 14;
	
	private $resourceId;
	private $resourceType;
	private $personId;
	private $schoolId;
	private $accessLevel;
	private $setId;
	private $filename;
	private $title;
	private $description;
	private $source;
	private $externalText;
	private $ftype;
	private $isPin;
	private $isFavourite;
	private $isLike;
	private $numLikes;
	private $url;
	private $tags;
	private $numInSet;
	private $extension;
	
	private static $types = null;
	private static $tagGroups = null;
	
	private static $maxTitleChars = 50;
	private static $maxDescChars = 200;
		
	
	
	function __construct ( $resourceId, 
							$resourceType,
							$personId,
							$schoolId,
							$accessLevel,
							$setId,
							$filename,
							$title,
							$description,
							$source,
							$externalText,
							$ftype,
							$isPin,
							$isFavourite,
							$isLike,
							$numLikes,
							$numInSet,
							$s3Status,
							$url	) {
								
		$this->resourceId = (int)$resourceId;
		$this->resourceType = (int)$resourceType;
		$this->personId = (int)$personId;
		$this->schoolId = (int)$schoolId;
		$this->accessLevel = (int)$accessLevel;
		$this->setId = (int)$setId;
		$this->filename = $filename;
		$this->extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$this->title = $title;
		$this->description = $description;
		$this->source = $source;
		$this->externalText = $externalText;
		$this->ftype = $ftype;
		$this->isPin = $isPin;
		$this->isFavourite = $isFavourite;
		$this->isLike = $isLike;
		$this->numLikes = $numLikes;
		$this->numInSet = $numInSet;
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
	
	public function getTitle () {
		return $this->title;
	}
	
	public function getDescription () {
		return $this->description;
	}
	
	public function getSchoolId () {
		return $this->schoolId;
	}
	
	public function getResourceType () {
		return $this->resourceType;
	}
	
	public function getSource () {
		return $this->source;
	}
	
	public function getSourceText () {
		
		$sourceText = "";
		if ( $this->source && $this->source == "user" ) {
			
			$sourceText = SchoolCommunity::getUserName ($this->personId);
			
		}
		else if ( $this->source && $this->source == "external" ) {
			$sourceText = $this->externalText;
		}
		else {
			
			$sourceText = SchoolCommunity::getRoleText ( $this->personId );
		}
		return $sourceText;
	}
	
	public function getExternalText () {
		return $this->externalText;
	}
	
	public function getAccessLevel () {
		return $this->accessLevel;
	}
	
	public function getUrl () {
		return $this->url;
	}
	
	public function getFiletype () {
		return $this->ftype;
	}
	
	public static function getResourceTypes () {
		
		if ( self::$types == null ) {
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("RT.* from ResourceType RT")
					->order("RT.seq");
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			self::$types = $db->loadObjectList("type_id");
		}
		
		return self::$types;
	
	}
	
	public static function getTypeName ( $typeId ) {
		
		$types = self::getResourceTypes ();
		$type = $types[$typeId];
		$name = null;
		if ( $type ) $name = $type->name;
		return $name;
	}
	
	public static function getClassStem ( $typeId ) {
		
		$types = self::getResourceTypes ();
		$type = $types[$typeId];
		$stem = null;
		if ( $type ) $stem = $type->class_stem;
		return $stem;
	}
	
	public static function getResourceTags () {
		
		if ( self::$tagGroups == null ) {
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("TG.tag_group_id, TG.name as group_name, TG.upload_text, T.* from TagGroup TG")
					->innerJoin("Tag T on TG.tag_group_id = T.tag_group_id")
					->order("TG.seq, T.seq");
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$results = $db->loadObjectList();
			
			self::$tagGroups = array();
			
			foreach ( $results as $row ) {
				if ( !array_key_exists($row->tag_group_id, self::$tagGroups) ) {
					
					$tagGroup = new \StdClass();
					$tagGroup->name = $row->group_name;
					$tagGroup->upload_text = $row->upload_text;
					$tagGroup->tags = array();
					
					self::$tagGroups[$row->tag_group_id] = $tagGroup;
					
				}
				self::$tagGroups[$row->tag_group_id]->tags[$row->tag_id] = $row;
			}
			
		}
		
		return self::$tagGroups;
	
	}
	
	private function setTags () {
		
		if ( !$this->tags ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("T.* from Tag T")
					->innerJoin("ResourceTag RT on RT.tag_id = T.tag_id")
					->where("RT.resource_id = " . $this->resourceId)
					->order("T.seq");
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$this->tags = $db->loadObjectList("tag_id");
		}
	}
	
	public function getTags() {
		if ( !$this->tags ) {
			
			$this->setTags();
		}
		return $this->tags;
	}
	
	public function getTagIds() {
		if ( !$this->tags ) {
			
			$this->setTags();
		}
		return array_keys ( $this->tags );
	}
	
	public function printHtml ( $idTag = null ) {
		
		$resourceId = $this->resourceId;
		
		$tagStr = "";
		if ( $idTag ) $tagStr = $idTag.'_';
		
		$shareStatus = array(SchoolCommunity::PERSON=>"fa fa-lock fa-lg",
								SchoolCommunity::SCHOOL=>"fa fa-building-o fa-lg",
								SchoolCommunity::COMMUNITY=>"fa fa-globe fa-lg",
								SchoolCommunity::ECOLOGISTS=>"fa fa-leaf fa-lg");
		
		$shareOptions = array(SchoolCommunity::PERSON=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_PRIVATE"),
								SchoolCommunity::SCHOOL=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_SCHOOL"),
								SchoolCommunity::COMMUNITY=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_COMMUNITY"));
					
		
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
						\JText::_("COM_BIODIV_RESOURCEFILE_PIN") . '</div></li>';
					print '<li><div id="unpin_resource_'.$tagStr.$resourceId.'" class="unpin_resource unpin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						\JText::_("COM_BIODIV_RESOURCEFILE_UNPIN") . '</div></li>';
				}
				else {
					print '<li><div id="pin_resource_'.$tagStr.$resourceId.'" class="pin_resource pin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						\JText::_("COM_BIODIV_RESOURCEFILE_PIN") . '</div></li>';
					print '<li style="display:none;"><div id="unpin_resource_'.$tagStr.$resourceId.'" class="unpin_resource unpin_resource_'.$resourceId.' h5"> <i class="fa fa-thumb-tack fa-lg"></i> ' . 
						\JText::_("COM_BIODIV_RESOURCEFILE_UNPIN") . '</div></li>';
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
			\JText::_("COM_BIODIV_RESOURCEFILE_SHOW").'"><h4 class="text-right"><i class="fa fa-angle-down fa-lg"></i></h4></div>';
		
		print '<div id="hide_resource_'.$tagStr.$resourceId.'" role="button" class="col-xs-2 col-sm-2 col-md-1 hide_resource" data-toggle="tooltip" title="'.
			\JText::_("COM_BIODIV_RESOURCEFILE_HIDE").'" style="display:none;"><h4 class="text-right"><i class="fa fa-angle-up fa-lg"></i></h4></div>';
		
		print '<div id="download_resource_'.$tagStr.$resourceId.'" class="col-xs-2 col-sm-2 col-md-1 download_resource" role="button" data-toggle="tooltip" title="'.
			\JText::_("COM_BIODIV_RESOURCEFILE_DOWNLOAD").'" ><a href="'.$this->url.
			'" download="'.$this->filename.'"><h4 class="text-right"><i class="fa fa-download fa-lg"></i></h4></a></div>';
		
		
		// Need space for likes here
		
		print '<div class="col-xs-4 col-sm-4 col-md-2">';
		print '<div class="row">';
		
		if ( $this->isFavourite ) {
			
			print '<div id="favourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 favourite_resource favourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'"  style="display:none;" ><h4 class="text-right"><i class="fa fa-bookmark-o fa-lg"></i></h4></div>';
			print '<div id="unfavourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 unfavourite_resource unfavourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'"><h4 class="text-right"><i class="fa fa-bookmark fa-lg"></i></h4></div>';
		}
		else {
			
			print '<div id="favourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 favourite_resource favourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'" ><h4 class="text-right"><i class="fa fa-bookmark-o fa-lg"></i></h4></div>';
			print '<div id="unfavourite_resource_'.$tagStr.$resourceId.
				'" class="col-xs-6 col-sm-6 col-md-6 unfavourite_resource unfavourite_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'"  style="display:none;"><h4 class="text-right"><i class="fa fa-bookmark fa-lg"></i></h4></div>';
		}
		
		if ( $this->isLike ) {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 like_resource like_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"  style="display:none;" ><h4 class="text-right"><i class="fa fa-heart-o fa-lg"></i></h4></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 unlike_resource unlike_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"><h4 class="text-right"><i class="fa fa-heart fa-lg"></i></h4></div>';
		}
		else {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 like_resource like_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'" ><h4 class="text-right"><i class="fa fa-heart-o fa-lg"></i></h4></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="col-xs-6 col-sm-6 col-md-6 unlike_resource unlike_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"  style="display:none;"><h4 class="text-right"><i class="fa fa-heart fa-lg"></i></h4></div>';
		}
		
		print '<div id="num_likes_'.$tagStr.$resourceId.'" class="col-xs-12 col-sm-12 col-md-12 text-right h5 num_likes_'.$resourceId.'">';
		
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_MANY_LIKES");
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
	
	public function printStudentHtml ( $idTag = null ) {
		
		$resourceId = $this->resourceId;
		
		$tagStr = "";
		if ( $idTag ) $tagStr = $idTag.'_';
		
		print '<div class="panel">';
			
		print '<div class="panel-heading">';
		
		print '<div class="row">';
		
		
		print '<div class="col-xs-8 col-sm-8 col-md-8 resource_file">';
		print '<h5>'.$this->title.'</h5>';
		print '<p>'.$this->description.'</p>';
		
		print '</div>';
		
		print '<div class="col-xs-4 col-sm-4 col-md-4 ">';
		print '<div class="row">';
		
		print '<div id="show_resource_'.$tagStr.$resourceId.'" role="button" class="col-xs-4 col-sm-4 col-md-4 show_resource" data-toggle="tooltip" title="'.
			\JText::_("COM_BIODIV_RESOURCEFILE_SHOW").'"><h4 class="text-right"><i class="fa fa-angle-down fa-lg"></i></h4></div>';
		
		print '<div id="hide_resource_'.$tagStr.$resourceId.'" role="button" class="col-xs-4 col-sm-4 col-md-4 hide_resource" data-toggle="tooltip" title="'.
			\JText::_("COM_BIODIV_RESOURCEFILE_HIDE").'" style="display:none;"><h4 class="text-right"><i class="fa fa-angle-up fa-lg"></i></h4></div>';
		
		print '<div id="download_resource_'.$tagStr.$resourceId.'" class="col-xs-4 col-sm-4 col-md-4 download_resource" role="button" data-toggle="tooltip" title="'.
			\JText::_("COM_BIODIV_RESOURCEFILE_DOWNLOAD").'" ><a href="'.$this->url.
			'" download="'.$this->filename.'"><h4 class="text-right"><i class="fa fa-download fa-lg"></i></h4></a></div>';
		
		
		if ( $this->isLike ) {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="col-xs-4 col-sm-4 col-md-4 like_resource like_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"  style="display:none;" ><h4 class="text-right"><i class="fa fa-heart-o fa-lg"></i></h4></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="col-xs-4 col-sm-4 col-md-4 unlike_resource unlike_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"><h4 class="text-right"><i class="fa fa-heart fa-lg"></i></h4></div>';
		}
		else {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="col-xs-4 col-sm-4 col-md-4 like_resource like_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'" ><h4 class="text-right"><i class="fa fa-heart-o fa-lg"></i></h4></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="col-xs-4 col-sm-4 col-md-4 unlike_resource unlike_resource_'.$resourceId.'" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"  style="display:none;"><h4 class="text-right"><i class="fa fa-heart fa-lg"></i></h4></div>';
		}
		
		print '<div id="num_likes_'.$tagStr.$resourceId.'" class="col-xs-12 col-sm-12 col-md-12 text-right h5 num_likes_'.$resourceId.'">';
		
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_MANY_LIKES");
		}
		print '</div>'; // num_likes, col-12
		
		print '</div>'; // row
		
		print '</div>'; //col-4
		
		print '</div>'; // row
		
		print '</div>'; // panel-heading
		
		print '<div class="panel-body resource_panel" style="display:none;">';
		
		print '<div id="resource_'.$tagStr.$resourceId.'" ></div>';
			
		print '</div>'; // panel-body
		print '</div>'; //panel
		
		
	}
	
	public function printResource( $displayClass ) {
		
		$fileTypeBits = explode('/', $this->ftype );
				
		$mainType = $fileTypeBits[0];
		
		if ( $mainType == "image" ) {
			print '<img src="'.$this->url.'" type="'.$this->ftype.'" class="'.$displayClass.'" />';
		}
		else if ( $mainType == "video" ) {
			print '<video src="'.$this->url.'" type="'.$this->ftype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" class="'.$displayClass.'"  ></video>';
		}
		else if ( $mainType == "audio" ) {
			print '<audio src="'.$this->url.'" type="'.$this->ftype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" class="'.$displayClass.'"  ></audio>';
		}
		else {
			
			if ( strpos($this->ftype, "word") !== false ) {
				print '<h4 class="text-center">'.\JText::_("COM_BIODIV_RESOURCEFILE_WORD_DOWNLOAD").'</h4>';
			}
			else if ( strpos($this->ftype, "pdf") !== false ) {
				print '<iframe src="'.$this->url.'#toolbar=0" type="'.$this->ftype.'" class="'.$displayClass.'" ></iframe>';
			}
			else if ( strpos($this->extension, "ppt") !== false ) {
				print '<h4 class="text-center">'.\JText::_("COM_BIODIV_RESOURCEFILE_PPT_DOWNLOAD").'</h4>';
			}
			else if ( strpos($this->extension, "odp") !== false ) {
				print '<h4 class="text-center">'.\JText::_("COM_BIODIV_RESOURCEFILE_ODP_DOWNLOAD").'</h4>';
			}
			else {
				print '<h4 class="text-center">'.\JText::_("COM_BIODIV_RESOURCEFILE_UNKNOWN_FTYPE").'</h4>';
			}
		}
	}
	
	public function printThumbnail( $displayClass ) {
		
		$fileTypeBits = explode('/', $this->ftype );
				
		$mainType = $fileTypeBits[0];
		
		if ( $mainType == "image" ) {
			print '<img src="'.$this->url.'" type="'.$this->ftype.'" class="'.$displayClass.'" />';
		}
		else if ( $mainType == "video" ) {
			print '<video src="'.$this->url.'" type="'.$this->ftype.'" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload noplaybackrate" class="'.$displayClass.'"  ></video>';
		}
		// else if ( $mainType == "audio" ) {
			// print '<audio src="'.$this->url.'" type="'.$this->ftype.'" oncontextmenu="return false;" disablePictureInPicture controlsList="nodownload noplaybackrate" class="'.$displayClass.'"  ></audio>';
		// }
		else if ( strpos($this->ftype, "pdf") !== false ) {
			print '<div id="pdfThumb_'.$this->resourceId.'" class="text-center pdfThumb" data-pdfurl="'.$this->url.'">';
			print '<canvas id="pdfCanvas_'.$this->resourceId.'"  class="pdfCanvas"></canvas>';
			print '</div>';
			
		}
		else {
			
			print '<div class="defaultThumbnail text-center">';
			
			print $this->printFileTypeIcon();
			
			print '</div>';
			
			if ( $mainType == "audio" ) {
				print '<div class="text-center h5">'.\JText::_("COM_BIODIV_RESOURCEFILE_AUDIO_DOC").'</div>';
			}
			else if ( strpos($this->ftype, "word") !== false ) {
				print '<div class="text-center h5">'.\JText::_("COM_BIODIV_RESOURCEFILE_WORD_DOC").'</div>';
			}
			else if ( strpos($this->extension, "ppt") !== false ) {
				print '<div class="text-center h5">'.\JText::_("COM_BIODIV_RESOURCEFILE_PPT_DOC").'</div>';
			}
			else if ( strpos($this->extension, "odp") !== false ) {
				print '<div class="text-center h5">'.\JText::_("COM_BIODIV_RESOURCEFILE_ODP_DOC").'</div>';
			}
			else {
				print '<div class="text-center h5">'.\JText::_("COM_BIODIV_RESOURCEFILE_OTHER_DOC").'</div>';
			}
		}
	}
	
	public function printFiletypeIcon() {
		
		$fileTypeBits = explode('/', $this->ftype );
				
		$mainType = $fileTypeBits[0];
		
		if ( $mainType == "image" ) {
			print '<i class="fa fa-image"></i>';
		}
		else if ( $mainType == "video" ) {
			print '<i class="fa fa-video-camera"></i>';
		}
		else if ( $mainType == "audio" ) {
			print '<i class="fa fa-headphones"></i>';
		}
		else if ( strpos($this->ftype, "word") !== false ) {
			print '<i class="fa fa-file-word-o"></i>';
		}
		else if ( strpos($this->ftype, "pdf") !== false ) {
			print '<i class="fa fa-file-pdf-o"></i>';
		}
		else if ( strpos($this->extension, "ppt") !== false ) {
			print '<i class="fa fa-file-powerpoint-o"></i>';
		}
		else if ( strpos($this->extension, "odp") !== false ) {
			print '<i class="fa fa-file-text-o"></i>';
		}
		else {
			print '<i class="fa fa-file-text-o"></i>';
		}
		
		/*
		if ( $mainType == "image" ) {
			print '<i class="fa fa-image fa-lg"></i>';
		}
		else if ( $mainType == "video" ) {
			print '<i class="fa fa-video-camera fa-lg"></i>';
		}
		else if ( $mainType == "audio" ) {
			print '<i class="fa fa-headphones fa-lg"></i>';
		}
		else {
			print '<i class="fa fa-file-text-o fa-lg"></i>';
		}
		*/
	}
	
	public function printTags() {
		
		$this->setTags();
		
		foreach ( $this->tags as $tag ) {
			
			print '<div class="tag '.$tag->color_class.'">' . $tag->name . '</div>';
		}

	}
	
	public function printCard ( $idTag = null ) {
		
		$resourceId = $this->resourceId;
		
		$tagStr = "";
		if ( $idTag ) $tagStr = $idTag.'_';
		
		$shareStatus = array(SchoolCommunity::PERSON=>"fa fa-lock",
								SchoolCommunity::SCHOOL=>"fa fa-building-o",
								SchoolCommunity::COMMUNITY=>"fa fa-globe",
								SchoolCommunity::ECOLOGISTS=>"fa fa-leaf");
		
		$shareOptions = array(SchoolCommunity::PERSON=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_PRIVATE"),
								SchoolCommunity::SCHOOL=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_SCHOOL"),
								SchoolCommunity::COMMUNITY=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_COMMUNITY"));
					
		
		print '<div id="resource_'.$tagStr.$resourceId.'" class="panel resource_panel ">';
		
		$filterClasses = ' isType_'.$this->resourceType;
		if ( $this->isPin ) $filterClasses .= ' isPin';
		if ( $this->isFavourite ) $filterClasses .= ' isFav';
		if ( userID() == $this->personId ) $filterClasses .= ' isMine';
		
		foreach ($this->getTagIds() as $tagId ) {
			$filterClasses .= ' isTag_' . $tagId;
		}
		
		
		print '<div class="panel-body resource_panel_body '.$filterClasses.'">';
		
		$colorClass = self::getClassStem ( $this->resourceType );
		$colorClass .= "Color";
		
		$accessLevel = $this->accessLevel;
		$shareIconClass = $shareStatus[$accessLevel];
		
		// The card grid
		print '<div class="resourceCardGrid">';
		
		print '<div class="resourceCardFileType '.$colorClass.' text-left">';
		$this->printFiletypeIcon();
		
		if ( $this->numInSet > 1 ) {
			print ' +' . ($this->numInSet - 1);
		}
		print '</div>';
		
		print '<div class="resourceCardType '.$colorClass.' text-center">';
		print self::getTypeName ( $this->resourceType );
		print '</div>';
		
		print '<div class="resourceCardShare '.$colorClass.' text-right">';
		print '<i class="'.$shareIconClass.'"></i>';
		print '</div>';
		
		print '<div class="resourceCardThumbnail">';
		$this->printThumbnail( "cardResource" );
		print '</div>';
		
		print '<div class="resourceCardTitle">';
		print '<h4>'.$this->title.'</h4>';
		print '</div>';
		
		print '<div class="resourceCardDescription">';
		print '<p>'.$this->description.'</p>';
		print '</div>';
		
		print '<div class="resourceCardSource">';
		print '<p>'.\JText::_("COM_BIODIV_RESOURCEFILE_SOURCE").' '.$this->getSourceText().'</p>';
		print '</div>';
		
		print '<div class="resourceCardLikes text-right">';
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_MANY_LIKES");
		}
		print '</div>';
		
		print '<div class="resourceCardTags">';
		$this->printTags();
		print '</div>';
		
		print '</div>'; // resourceCardGrid
		
		
		print '</div>'; // panel-body
		print '</div>'; //panel
		
		
	}
	
	public function printFull ( $idTag = null ) {
		
		$resourceId = $this->resourceId;
		$resourcePerson = $this->personId;
		$userId = userID();
		
		
		$tagStr = "";
		if ( $idTag ) $tagStr = $idTag.'_';
		
		$shareStatus = array(SchoolCommunity::PERSON=>"fa fa-lock fa-lg",
								SchoolCommunity::SCHOOL=>"fa fa-building-o fa-lg",
								SchoolCommunity::COMMUNITY=>"fa fa-globe fa-lg",
								SchoolCommunity::ECOLOGISTS=>"fa fa-leaf fa-lg");
		
		$shareOptions = array(SchoolCommunity::PERSON=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_PRIVATE"),
								SchoolCommunity::SCHOOL=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_SCHOOL"),
								SchoolCommunity::COMMUNITY=>\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_COMMUNITY"));
					
		$accessLevel = $this->accessLevel;
		$shareIconClass = $shareStatus[$accessLevel];
		
		$colorClass = self::getClassStem ( $this->resourceType );
		$colorClass .= "Color";
		
		
		print '<div class="panel">';
			
		print '<div class="panel-body">';
		
		print '<div class="fullResourceGrid">';
		
		print '<div class="fullResourceFiletype '.$colorClass.' h4">';
		$this->printFiletypeIcon();
		if ( $this->numInSet > 1 ) {
			$numExtras = $this->numInSet - 1;
			print ' +'.$numExtras;
		}
		print '</div>';
		
		print '<div class="fullResourceType '.$colorClass.' h4">';
		print self::getTypeName ( $this->resourceType );
		print '</div>';
		
		print '<div class="fullResourceShareLevel '.$colorClass.' text-right h4">';
		print '<i class="'.$shareIconClass.'"></i>';
		print '</div>';
		
		print '<div class="fullResourceShare">';
		if ( $resourcePerson == $userId ) {
			print '<div id="share_resource_'.$tagStr.$resourceId.'" class="share_resource share_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCEFILE_SHARE").'"   ><h4 ><i class="fa fa-share-alt fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_SHARE").'</div></div>';
					
			print '<div id="shareMenu" class="miniMenu" style="display:none;">';
				
			$isStudent = SchoolCommunity::isStudent();
			if ( !$isStudent )  {
				foreach ($shareOptions as $shareId=>$shareOpt ) {
					print '<div id="share_resource_'.$tagStr.$shareId.'_'.$resourceId.'" class="share_resource_btn share_resource_'.$shareId.'_'.$resourceId.
						' h4 btn miniMenuBtn text-left" ><div class="menuIcon"><i class="' . 
						$shareStatus[$shareId] . '"></i></div><div class="menuText">' . $shareOpt . '</div></div>';
				}
			}
			
			print '        <button id="hide_shareMenu" type="button" class="btn btn-default hideMiniMenu" >'.\JText::_("COM_BIODIV_RESOURCEFILE_CANCEL").'</button>';
		
			print '</div>'; //shareMenu
				
		}
		print '</div>'; // fullResourceShare
		
		print '<div class="fullResourceLike">';
		if ( $this->isLike ) {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="like_resource like_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'"  style="display:none;" ><h4><i class="fa fa-heart-o fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'</div></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="unlike_resource unlike_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_UNLIKE").'"><h4 ><i class="fa fa-heart fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_UNLIKE").'</div></div>';
		}
		else {
			
			print '<div id="like_resource_'.$tagStr.$resourceId.'" class="like_resource like_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'" ><h4 ><i class="fa fa-heart-o fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_LIKE").'</div></div>';
			print '<div id="unlike_resource_'.$tagStr.$resourceId.'" class="unlike_resource unlike_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_UNLIKE").'"  style="display:none;"><h4><i class="fa fa-heart fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_UNLIKE").'</div></div>';
		}
		print '</div>';
		
		print '<div class="fullResourceDownload">';
		print '<div id="download_resource_'.$tagStr.$resourceId.'" class="download_resource text-center" role="button" data-toggle="tooltip" title="'.
			\JText::_("COM_BIODIV_RESOURCEFILE_DOWNLOAD").'" ><a href="'.$this->url.
			'" download="'.$this->filename.'"><h4><i class="fa fa-download fa-lg"></i></h4></a><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_DOWNLOAD").'</div></div>';
		print '</div>';
		
		print '<div class="fullResourceBookmark">';
		if ( $this->isFavourite ) {
			
			print '<div id="favourite_resource_'.$tagStr.$resourceId.
				'" class="favourite_resource favourite_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'"  style="display:none;" ><h4><i class="fa fa-bookmark-o fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'</div></div>';
			print '<div id="unfavourite_resource_'.$tagStr.$resourceId.
				'" class="unfavourite_resource unfavourite_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_UNFAVOURITE").'"><h4 ><i class="fa fa-bookmark fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_UNFAVOURITE").'</div></div>';
		}
		else {
			
			print '<div id="favourite_resource_'.$tagStr.$resourceId.
				'" class="favourite_resource favourite_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'" ><h4 ><i class="fa fa-bookmark-o fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_FAVOURITE").'</div></div>';
			print '<div id="unfavourite_resource_'.$tagStr.$resourceId.
				'" class="unfavourite_resource unfavourite_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
				\JText::_("COM_BIODIV_RESOURCEFILE_UNFAVOURITE").'"  style="display:none;"><h4><i class="fa fa-bookmark fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_UNFAVOURITE").'</div></div>';
		}
		print '</div>';
		
		//if ( $resourcePerson == $userId ) {
		if ( self::canEdit($resourceId) ) {
			
			print '<div class="fullResourceMoreOptions">';
		
			print '<div id="more_resource_'.$tagStr.$resourceId.'" class="more_resource more_resource_'.$resourceId.' text-center" role="button" data-toggle="tooltip" title="'.
					\JText::_("COM_BIODIV_RESOURCEFILE_MORE_OPTIONS").'"   ><h4 ><i class="fa fa-ellipsis-h fa-lg"></i></h4><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_MORE_OPTIONS").'</div></div>';
			
			print '<div id="moreMenu" class="miniMenu" style="display:none;">';
			
			print '<div id="resourceEdit_'.$resourceId.'" class="edit_resource miniMenuBtn h4" role="button" data-toggle="modal" data-target="#editModal">';
			print '<div class="menuIcon"><i class="fa fa-pencil-square-o fa-lg"></i></div>';
			print '<div class="menuText">'.\JText::_("COM_BIODIV_RESOURCEFILE_EDIT").'</div>';
			print '</div>';
			
			print '<div id="deleteResource_'.$resourceId.'" class="deleteResource miniMenuBtn h4" role="button" >';
			print '<div class="menuIcon"><i class="fa fa-trash-o fa-lg"></i></div>';
			print '<div class="menuText">'.\JText::_("COM_BIODIV_RESOURCEFILE_DELETE").'</div>';
			print '</div>';
			
			print '<div id="viewSet_'.$this->setId.'" class="viewSet miniMenuBtn h4">';
			print '<a href="'.\JText::_("COM_BIODIV_RESOURCEFILE_RESOURCE_SET_PAGE").'?set_id='.$this->setId.'" ><div class="menuIcon"><i class="fa fa-files-o fa-lg"></i></div>';
			print '<div class="menuText">'.\JText::_("COM_BIODIV_RESOURCEFILE_VIEW_SET").'</div></a>';
			print '</div>';
			
			print '        <button id="hide_moreMenu" type="button" class="btn btn-default hideMiniMenu" >'.\JText::_("COM_BIODIV_RESOURCEFILE_CANCEL").'</button>';
		
			print '</div>'; //moreMenu
			
			print '</div>'; // moreOptions
			
		}
		else {
			print '<div class="fullResourceMoreOptions">';
			print '<div id="viewSet_'.$this->setId.'" class="viewSet text-center">';
			print '<a href="'.\JText::_("COM_BIODIV_RESOURCEFILE_RESOURCE_SET_PAGE").'?set_id='.$this->setId.'" ><h4><i class="fa fa-files-o fa-lg"></i></h4></a><div class="hidden-xs">'.\JText::_("COM_BIODIV_RESOURCEFILE_VIEW_SET");
			print '</div></div>';
			print '</div>'; // moreOptions
		}
		
		
		print '<div class="fullResourceTitle">';
		print '<h3>'.$this->title.'</h3>';
		print '</div>';
		
		print '<div class="fullResourceDescription">';
		print '<p>'.$this->description.'</p>';
		print '</div>';
		
		print '<div class="fullResourceSource">';
		print '<p>'.\JText::_("COM_BIODIV_RESOURCEFILE_SOURCE").' '.$this->getSourceText().'</p>';
		print '</div>';
		
		print '<div class="fullResourceTags">';
		$this->printTags();
		print '</div>';
		
		print '<div class="fullResourceNumLikes text-right">';
		print '<div id="num_likes_'.$tagStr.$resourceId.'" class="h5 num_likes_'.$resourceId.'">';
		
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_RESOURCEFILE_MANY_LIKES");
		}
		print '</div>'; // num_likes
		print '</div>';
		
		print '<div class="fullResourceDoc">';
		$this->printResource( "fullResource" );
		print '</div>';
		
		
		
		print '</div>'; // fullResourceGrid
		
		
		print '<div id="editModal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog"  >';

		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		print '      <div class="modal-header">';
		print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '      </div>';
		print '     <div class="modal-body">';
		print '	    <div id="editArea" ></div>';
		print '      </div>';
		print '	  <div class="modal-footer">';
		print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.\JText::_("COM_BIODIV_RESOURCEFILE_CANCEL").'</button>';
		print '      </div>';
				  
		print '    </div>'; // modal-content

		print '  </div>'; // modal dialog
		print '</div>'; // uploadModal
		
		
		print '</div>'; // panel-body
		print '</div>'; //panel
		
		
	}
	
	public static function printMetaCapture ( $resourceId = null ) {
		
		$resource = null;
		if ( $resourceId ) {
			$resource = self::createResourceFileFromId ( $resourceId );
			
			// $errMsg = print_r ( $resource, true );
			// error_log ( "printMetaCapture: Resource = " . $errMsg );
		}
		$resourceTypes = self::getResourceTypes();
		$tagGroups = self::getResourceTags();
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		$isEcologist = SchoolCommunity::isEcologist();
		
		$pageNum = 1;
	
		// -------------------------School, title and description
		print '<div id="resourceMeta_'.$pageNum.'" class="metaPage">';

		
		// Create dropdown of schools
		if ( count ($schoolRoles) > 1 ) {
			
			$currSchoolId = null;
			if ( $resource ) {
				$currSchoolId = $resource->getSchoolId();
			}
			print '<label for="school"><h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_CHOOSE_SCHOOL").'</h4></label>';
			print "<select id = 'school' name = 'school'>"; // class = 'form-control'>";

			$isFirst = true;
			$firstId = null;
			foreach($schoolRoles as $schoolRole){
				
				if ( $isFirst ) {
					if ( !$currSchoolId || ($currSchoolId && ($currSchoolId == $schoolRole['school_id'])) ) {
						// Default to first project
						print "<option value='".$schoolRole['school_id']."' selected>".$schoolRole['name']."</option>";
					}
					else {
						print "<option value='".$schoolRole['school_id']."'>".$schoolRole['name']."</option>";
					}
					$isFirst = false;
					$firstId = $schoolRole['school_id'];
				}
				else {
					if ( $currSchoolId && ($currSchoolId == $schoolRole['school_id']) ) {
						print "<option value='".$schoolRole['school_id']."' selected>".$schoolRole['name']."</option>";
					}
					else {
						print "<option value='".$schoolRole['school_id']."'>".$schoolRole['name']."</option>";
					}
				}
			}

			print "</select>";
		}
		else {
			print '<input type="hidden" name="school" value="'.$schoolRoles[0]['school_id'].'"/>';
		}
		
		// Name the upload
		$title = "";
		if ( $resource ) {
			$title = $resource->getTitle();
		}
		print '<label for="uploadName"><h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_NAME_UPLOAD").'</h4></label>';
		print '<input type="text" id="uploadName" name="uploadName" value = "'.$title.'">';
		print '<div id="uploadNameCount" class="text-right" data-maxchars="'.self::$maxTitleChars.'">0/'.self::$maxTitleChars.'</div>';
		
		
		// Describe the upload
		$desc = "";
		if ( $resource ) {
			$desc = $resource->getDescription();
		}

		print '<label for="uploadDescription"><h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_UPLOAD_DESC").'</h4></label>';
		print '<textarea id="uploadDescription" name="uploadDescription" rows="2" cols="100" >'.$desc.'</textarea>';
		print '<div id="uploadDescriptionCount" class="text-right" data-maxchars="'.self::$maxDescChars.'">0/'.self::$maxDescChars.'</div>';
		
		print '<div id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceNextBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_NEXT");
		print '</div>'; // uploadNext
		
		print '</div>'; // newUpload
		
		$pageNum++;
		
		// ------------------------- Resource type
		$type = 0;
		if ( $resource ) {
			$type = $resource->getResourceType();
		}

		print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
		
		print '<label for="resourceType"><h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_CHOOSE_TYPE").'</h4></label>';
		
		print '<div class="row">';
		foreach($resourceTypes as $resType){
			
			print '<div class="col-md-6">';
			$checked = "";
			if ( $resType->type_id == $type ) {
				$checked = "checked";
			}
			print '<div class="type_'.$resType->type_id.'">';
			print '<input type="radio" id="'.$resType->type_id.'" name="resourceType" value="'.$resType->type_id.'" '.$checked.'>';
			print '<label for="'.$resType->type_id.'" class="uploadLabel '.$resType->class_stem.'Color">'.$resType->name.'</label>';
			print '</div>';
			print '</div>'; // col-6
		}
		print '</div>'; // row
		
		print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_BACK");
		print '</div>'; // resourceBack
		
		print '<div id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceNextBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_NEXT");
		print '</div>'; // resourceNext
		
		print '</div>'; // resourceMeta
		
		$pageNum++;

		// ------------------------- The various tags
		$existingTags = null;
		if ( $resource ) {
			$existingTags = $resource->getTagIds();
		}
		foreach ( $tagGroups as $tagGroup ) {
			
			print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
			
			print '<h4>'.$tagGroup->upload_text.'</h4>';
			
			print '<h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_AS_MANY").'</h4>';
		
			foreach($tagGroup->tags as $tag){
				
				$checked = "";
				if ( $existingTags && in_array($tag->tag_id, $existingTags) ) {
					$checked = "checked";
				}
				
				print '<div class="uploadTag">';
				print '<input type="checkbox" id="tag_'.$tag->tag_id.'" name="tag[]" value="'.$tag->tag_id.'" '.$checked.'>';
				print '<label for="tag_'.$tag->tag_id.'" class="uploadLabel '.$tag->color_class.'">'.$tag->name.'</label>';
				print '</div>';
			}
			
			print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn"  >';
			print \JText::_("COM_BIODIV_RESOURCEFILE_BACK");
			print '</div>'; // resourceBack
			
			print '<div id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceNextBtn"  >';
			print \JText::_("COM_BIODIV_RESOURCEFILE_NEXT");
			print '</div>'; // resourceNext
			
			print '</div>'; // resourceMeta
			
			$pageNum++;
		}
		
		

		// ------------------------- Resource source
		$existingSource = null;
		if ( $resource ) {
			$existingSource = $resource->getSource();
		}
		
		print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
		
		print '<h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_SET_SOURCE").'</h4>';
		
		$checked = "checked";
		if ( $existingSource && $existingSource != "user" ) {
			$checked = "";
		}
		print '<div class="uploadRow">';
		print '<input type="radio" id="displayUsername" name="source" value="user" '.$checked.'>';
		print '<label for="displayUsername" class="uploadLabel">'.\JText::_("COM_BIODIV_RESOURCEFILE_MY_USERNAME").'</label>';
		print '</div>'; // uploadRow
		
		
		$checked = "";
		if ( $existingSource && $existingSource == "role" ) {
			$checked = "checked";
		}
		print '<div class="uploadRow">';
		print '<input type="radio" id="displayRole" name="source" value="role" '.$checked.'>';
		print '<label for="displayRole" class="uploadLabel" >'.\JText::_("COM_BIODIV_RESOURCEFILE_NOT_USERNAME").'</label>';
		print '</div>'; // uploadRow
		
		$checked = "";
		$externalText = \JText::_("COM_BIODIV_RESOURCEFILE_EXTERNAL_TEXT");
		$displayExternalStyle = 'style="display:none"';
		if ( $existingSource && $existingSource == "external" ) {
			$checked = "checked";
			$externalText = $resource->getExternalText();
			$displayExternalStyle = '';
		}
		print '<div class="uploadRow ">';
		print '<input type="radio" id="displayExternal" name="source" value="external" '.$checked.'>';
		print '<label for="displayExternal" class="uploadLabel">'.\JText::_("COM_BIODIV_RESOURCEFILE_EXTERNAL_SOURCE").'</label>';
		print '</div>'; // uploadRow
		
		
		print '<div class="uploadRow externalExtras" '.$displayExternalStyle.'>';
		print '<input type="checkbox" id="externalPermission" name="externalPermission" value="1" '.$checked.'>';
		print '<label for="externalPermission" class="uploadLabel">'.\JText::_("COM_BIODIV_RESOURCEFILE_EXTERNAL_PERMISSION").'</label>';
		print '</div>'; // uploadRow
		
		print '<div class="uploadRow externalExtras" '.$displayExternalStyle.'>';
		print '<label for="externalText">'.\JText::_("COM_BIODIV_RESOURCEFILE_EXTERNAL_TEXT").'</label>';
		print '<input type="text" id="externalText" name="externalText" value="'.$externalText.'">';
		print '</div>'; // uploadRow
				
			
		print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_BACK");
		print '</div>'; // resourceBack
		
		print '<div id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceNextBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_NEXT");
		print '</div>'; // resourceNext
		
		print '</div>'; // resourceMeta
		
		$pageNum++;

		
		// ------------------------- Share level
		$existingShareLevel = null;
		if ( $resource ) {
			$existingShareLevel = $resource->getAccessLevel();
		}
		print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
		
		print '<h4>'.\JText::_("COM_BIODIV_RESOURCEFILE_SET_SHARE").'</h4>';
		
		$checked = "checked";
		if ( $existingShareLevel && $existingShareLevel != SchoolCommunity::PERSON ) {
			$checked = "";
		}
		print '<div class="uploadRow">';
		print '<input type="radio" id="sharePrivate" name="shareLevel" value="'.SchoolCommunity::PERSON.'" '.$checked.'>';
		print '<label for="sharePrivate" class="uploadLabel">'.\JText::_("COM_BIODIV_RESOURCEFILE_KEEP_PRIVATE").'</label>';
		print '</div>'; // uploadRow
		
		$checked = "";
		if ( $existingShareLevel && $existingShareLevel == SchoolCommunity::SCHOOL ) {
			$checked = "checked";
		}
		print '<div class="uploadRow">';
		print '<input type="radio" id="shareSchool" name="shareLevel" value="'.SchoolCommunity::SCHOOL.'" '.$checked.'>';
		print '<label for="shareSchool" class="uploadLabel" >'.\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_SCHOOL").'</label>';
		print '</div>'; // uploadRow
		
		$checked = "";
		if ( $existingShareLevel && $existingShareLevel == SchoolCommunity::COMMUNITY ) {
			$checked = "checked";
		}
		print '<div class="uploadRow">';
		print '<input type="radio" id="shareCommunity" name="shareLevel" value="'.SchoolCommunity::COMMUNITY.'" '.$checked.'>';
		print '<label for="shareCommunity" class="uploadLabel">'.\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_COMMUNITY").'</label>';
		print '</div>'; // uploadRow
		
		$checked = "";
		if ( $existingShareLevel && $existingShareLevel == SchoolCommunity::ECOLOGISTS ) {
			$checked = "checked";
		}
		if ( $isEcologist ) {
			print '<div class="uploadRow">';
		print '<input type="radio" id="shareEcologists" name="shareLevel" value="'.SchoolCommunity::ECOLOGISTS.'" '.$checked.'>';
			print '<label for="shareEcologists" class="uploadLabel">'.\JText::_("COM_BIODIV_RESOURCEFILE_SHARE_ECOLOGISTS").'</label>';
			print '</div>'; // uploadRow
		}	
		
		print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_BACK");
		print '</div>'; // resourceBack
		
		print '<div id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceNextBtn"  >';
		print \JText::_("COM_BIODIV_RESOURCEFILE_NEXT");
		print '</div>'; // resourceNext
		
		print '</div>'; // resourceMeta
		
		return $pageNum;
		
	}
	
	public static function createResourceFile ( $setId, $resourceType, $clientName, $newName, $dirName, $fileSize, $fileType, $title = null, $description = null, $accessLevel = null, $source = null, $externalText = null ) {
		
		$personId = userID();
		
		$instance = null;
			
		if ( $personId ) {
			
			$problem = true;
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
		
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("RS.school_id, RS.set_name, RS.description, RS.upload_params, S.name as school_name, U.username, O.option_name as type_name from ResourceSet RS")
					->innerJoin("School S on RS.school_id = S.school_id")
					->innerJoin($userDb . "." . $prefix ."users U on RS.person_id = U.id")
					->innerJoin("Options O on O.option_id = RS.resource_type" )
					->where("RS.set_id = " . $setId);
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$otherDetails = $db->loadAssoc();
			
			$schoolId = $otherDetails["school_id"];
			
			$typeName = $otherDetails["type_name"];
			
			$uploadParams = json_decode ( $otherDetails["upload_params"] );
			
			if ( !$title ) {
				$title = $otherDetails["set_name"];
			}
			if ( !$description ) {
				$description = $otherDetails["description"];
			}
			if ( !$accessLevel ) {
				if ( property_exists ( $uploadParams, 'shareLevel' ) ) {
					$accessLevel = $uploadParams->shareLevel;
				}
				else {
					$accessLevel = SchoolCommunity::PERSON;
				}
			}
			if ( !$source ) {
				if ( property_exists ( $uploadParams, 'source' ) ) {
					$source = $uploadParams->source;
				}
				else {
					$source = 'user';
				}
			}
			if ( !$externalText ) {
				if ( property_exists ( $uploadParams, 'externalText' ) ) {
					$externalText = $uploadParams->externalText;
				}
			}
			
			$tags = null;
			if ( property_exists ( $uploadParams, 'tags' ) ) {
					$tags = $uploadParams->tags;
			}
			
			$tagNames = '';
			if ( count($tags) > 0 ) {
				// Create readable text for easy searching
				$query = $db->getQuery(true)
						->select("T.name from Tag T")
						->where("T.tag_id in (" . implode(',', $tags) . ')');
						
				$db->setQuery($query);
					
				//error_log("Set id select query created: " . $query->dump());
					
				$tagNameArray = $db->loadColumn();
				
				$tagNames = implode( ' ', $tagNameArray );
			}
			
			$readable = $typeName . ' ' . $tagNames;
			
			// To start with
			$articleId = 0;
			
			$url = $dirName."/".$newName;
			
			$resourceFields = (object) [
				'access_level' => $accessLevel,
				'school_id' => $schoolId,
				'person_id' => $personId,
				'set_id' => $setId,
				'resource_type' => $resourceType,
				'upload_filename' => $clientName,
				'title' => $title,
				'description' => $description,
				'readable' => $readable,
				'source' => $source,
				'external_text' => $externalText,
				'url'=>$url,
				'filename' => $newName,
				'size' => $fileSize,
				'filetype' => $fileType,
				'article_id' => $articleId
			];
								
			
			$struc = 'resourcefile';
			
			if($resourceId = codes_insertObject($resourceFields, $struc)){
				
				$problem = false;
				addUploadMessage('success', "Uploaded $clientName");
				$instance = new self( $resourceId, 
							$resourceType,
							$personId,
							$schoolId,
							$accessLevel,
							$setId,
							$newName,
							$title,
							$description,
							$source,
							$externalText,
							$fileType,
							0,
							0,
							0,
							0,
							1,
							0,
							$url );
							
				
				if ( $tags ) {
					
					foreach ( $tags as $tagId ) {
						$tagFields = (object) [
							'resource_id' => $resourceId,
							'tag_id' => $tagId ];
							
						$success = $db->insertObject("ResourceTag", $tagFields);
						if(!$success){
							error_log ( "ResourceTag insert failed" );
						}
						
					}
				}
			}
			else {
				error_log ("Problem inserting resource into database" );
				$problem = true;
			}
		
		}
		
		return $instance;
		
	}
	
	public static function updateReadable ( $resourceId ) {
		
		$options = dbOptions();
		
		$db = \JDatabaseDriver::getInstance($options);
		
		// Create readable text for easy searching
		$query = $db->getQuery(true)
				->select("CONCAT(' ', O.option_name, ' ', GROUP_CONCAT(DISTINCT T.name SEPARATOR' ')) from Resource R")
				->innerJoin("ResourceTag RT on RT.resource_id = R.resource_id" )
				->innerJoin("Tag T on T.tag_id = RT.tag_id")
				->innerJoin("Options O on O.option_id = R.resource_type")
				->where("R.resource_id = " . $resourceId)
				->group("R.resource_id");
				
		$db->setQuery($query);
			
		//error_log("Set id select query created: " . $query->dump());
			
		$readable = $db->loadResult();
		
		$fields = new \stdClass();
		$fields->resource_id = $resourceId;
		$fields->readable = $readable;
			
		$success = $db->updateObject('Resource', $fields, 'resource_id');
		if(!$success){
			error_log ( "Resource update failed" );
		}
		
		return $success;
	}
	
	public static function createResourceFileFromId( $resourceId ){
		
		if ( $resourceId  ) {
			
			$userId = userID();
			
			//$resourceDetails = codes_getDetails ( $resourceId, "resourcefile" );
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("R.*, IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fave, IFNULL(LR.lr_id, 0) as is_like, " .
						" (select count(*) from LikedResource where resource_id = " . $resourceId . ") as num_likes, ".
						" (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set from Resource R")
				->innerJoin("ResourceSet RS on RS.set_id = R.set_id")
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id" )
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $userId )
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $userId)
				->where("R.resource_id = " . $resourceId  )
				->where("R.deleted = 0");
			
			$db->setQuery($query);
			
			//error_log("Task constructor select query created: " . $query->dump());
			
			$resourceDetails = $db->loadAssoc();
			
			if ( $resourceDetails ) {
				$resourceType = (int)$resourceDetails['resource_type'];
				$personId = (int)$resourceDetails['person_id'];
				$schoolId = (int)$resourceDetails['school_id'];
				$accessLevel = (int)$resourceDetails['access_level'];
				$setId = (int)$resourceDetails['set_id'];
				$filename = $resourceDetails['filename'];
				$title = $resourceDetails['title'];
				$description = $resourceDetails['description'];
				$source = $resourceDetails['source'];
				$externalText = $resourceDetails['external_text'];
				$ftype = $resourceDetails['filetype'];
				$isPin = $resourceDetails['is_pin'] > 0;
				$isFavourite = $resourceDetails['is_fave'] > 0;
				$isLike = $resourceDetails['is_like'] > 0;
				$numLikes = $resourceDetails['num_likes'];
				$numInSet = $resourceDetails['num_in_set'];
				
				$s3Status = $resourceDetails['s3_status'];
				$url = $resourceDetails['url'];
				
				
				return new self ( $resourceId, 
								$resourceType,
								$personId,
								$schoolId,
								$accessLevel,
								$setId,
								$filename,
								$title,
								$description,
								$source,
								$externalText,
								$ftype,
								$isPin,
								$isFavourite,
								$isLike,
								$numLikes,
								$numInSet,
								$s3Status,
								$url );
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	
	}
	
	/*
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
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					" PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set,  R.resource_type, R.set_id, R.s3_status from Resource R")
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
					->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
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
			->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
				"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes,  (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
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
			->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
				"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes,  (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
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
				->select("R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes,  (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status   from Resource R")
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
	
	*/
	
	//public static function getPinnedResources ( $startRow = 0, $numRows = null ) {
	public static function getPinnedResources ($filters, $page = 1, $pageLength = self::NUM_PER_PAGE) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = self::getFilterWhereStr($filters);
		
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
		
		// Add own pinned resources for everyone
		$query1 = $db->getQuery(true)
			->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
				"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes,  (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
			->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
			->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
			->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.person_id = ". $personId )
			->where("R.deleted = 0");
							
		if ( $filters ) {
			$query1->where($whereStr);
		}
				
						
		// Add community pinned resources for everyone
		// All school community level pinned resources
		$query2 = $db->getQuery(true)
			->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
				"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
				"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes,  (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
			->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
			->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
			->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.access_level = " . $COMMUNITY )
			->where("R.deleted = 0");
		
		if ( $filters ) {
			$query2->where($whereStr);
		}
				
		$query3 = array();
		foreach ( $schoolRoles as $schoolRole ) {
			
			$schoolId = $schoolRole['school_id'];
			$roleId = $schoolRole['role_id'];
			$pinnedResources = null;
			
			if ( $roleId == SchoolCommunity::TEACHER_ROLE ) {
				
				// All my school pinned resources 
				$q3 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					" PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set,  R.resource_type, R.set_id, R.s3_status from Resource R")
					->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("FR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId)
					->where("R.access_level not in (" . $PERSON . ", " . $ECOLOGISTS . ")" )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$q3->where($whereStr);
				}
				
				$query3[] = $q3;
		
			}
			else if ( $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
				
				$isEcologist = true;
				
				// All my school pinned resources
				$q3 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id = " . $schoolId )
					->where("R.access_level != " . $PERSON )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$q3->where($whereStr);
				}
				
				$query3[] = $q3;
			}
		}
		
		// Add ecologists pinned resources for ecologists only
		$query4 = null;
		if ( $isEcologist ) {
			$query4 = $db->getQuery(true)
				->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"PR.pr_id as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes,  (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status   from Resource R")
				->innerJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
			->where("R.access_level = " . $ECOLOGISTS )
			->where("R.deleted = 0");
			
			if ( $filters ) {
				$query4->where($whereStr);
			}
			
		}
		
		$finalQuery = $query1->union($query2);
		
		foreach ( $query3 as $q3 ) {
			$finalQuery = $finalQuery->union($q3);
		}
		if ( $query4 ) $finalQuery = $finalQuery->union($query4);
		
		$finalQuery->order("tstamp DESC");
		
		//$finalQuery->setLimit($numRows, $startRow);
		
		$db->setQuery($finalQuery);
		
		//error_log("Set id select query created: " . $finalQuery->dump());
		
		$db->execute();
		$totalRows = $db->getNumRows();
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
		$allPinnedResources  = $db->loadAssocList("resource_id");
		
		return (object)array("total"=>$totalRows, "resources"=>$allPinnedResources);
				
				
	}
	
	public static function getFavResources ($filters, $page = 1, $pageLength = self::NUM_PER_PAGE) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = self::getFilterWhereStr($filters);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$isAdmin = false;
		$ecolSchools = array();
		$teacherSchools = array();
		$allSchools = array();
		foreach ( $schoolRoles as $schoolRole ) {
			if ( $schoolRole['role_id'] == SchoolCommunity::ADMIN_ROLE ) {
				$isAdmin = true;
			}
			else if ( $schoolRole['role_id'] == SchoolCommunity::TEACHER_ROLE ) {
				$teacherSchools[] = $schoolRole['school_id'];
			}
			else if ( $schoolRole['role_id'] == SchoolCommunity::ECOLOGIST_ROLE ) {
				$ecolSchools[] = $schoolRole['school_id'];
			}
			$allSchools[] = $schoolRole['school_id'];
		}
		
		$ecolSchoolsStr = "";
		$teacherSchoolsStr = "";
		$allSchoolsStr = "";
		if ( count($ecolSchools) > 0 ) {
			$ecolSchoolsStr = implode (',', $ecolSchools);
		}
		if ( count($teacherSchools) > 0 ) {
			$teacherSchoolsStr = implode (',', $teacherSchools);
		}
		if ( count($allSchools) > 0 ) {
			$allSchoolsStr = implode (',', $allSchools);
		}
		
		$allFavResources = array();
		$finalQuery = null;
		
		// foreach ( $schoolRoles as $schoolRole ) {
			
			// $schoolId = $schoolRole['school_id'];
			// $roleId = $schoolRole['role_id'];
			$favResources = null;
			
		if ( $isAdmin ) {
			$finalQuery = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.deleted = 0");

				
				if ( $filters ) {
					$finalQuery->where($whereStr);
				}
		}
		else {
			
			if ( count($teacherSchools) > 0 or count($ecolSchools) > 0 ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$query->where($whereStr);
				}
				
				// School resources
				$query2 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id in ( " . $allSchoolsStr . ") and R.access_level = " . SchoolCommunity::SCHOOL )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$query2->where($whereStr);
				}
				
				
				// Community resources
				$query3 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::COMMUNITY )
					->where("R.deleted = 0");
					
				if ( $filters ) {
					$query3->where($whereStr);
				}
				
				

				$query4 = $query->union($query2)->union($query3) ;
		
			}
			if ( $finalQuery ) {
				$finalQuery = $finalQuery->union($query4);
			}
			else {
				$finalQuery = $query4;
			}
		
			if ( count($ecolSchools) > 0 ) {
				// Ecologist resources
				$query = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->innerJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::ECOLOGISTS )
					->where("R.deleted = 0");

				
				if ( $filters ) {
					$query->where($whereStr);
				}
				
				$finalQuery = $finalQuery->union($query);
			};
		
		}
		
		$finalQuery->order("tstamp DESC");
		
		$db->setQuery($finalQuery);
		
		//error_log("ResourceFile::getResourcesByType query created: " . $finalQuery->dump());
		
		$db->execute();
		$totalRows = $db->getNumRows();
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
		$allFavResources  = $db->loadAssocList("resource_id");
		
		return (object)array("total"=>$totalRows, "resources"=>$allFavResources);
		
	}
	
	public static function getResourcesByType ( $resourceType, $filters, $page = 1, $pageLength = self::NUM_PER_PAGE ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = self::getFilterWhereStr($filters);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Get resources for this resource type
		// All my files of this type plus all shared with my school files of this type plus all shared with community files of this type
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$ecolSchools = array();
		$teacherSchools = array();
		$allSchools = array();
		$isStudent = false;
		$isAdmin = false;
		foreach ( $schoolRoles as $schoolRole ) {
			if ( $schoolRole['role_id'] == SchoolCommunity::ADMIN_ROLE ) {
				$isAdmin = true;
			}
			else if ( $schoolRole['role_id'] == SchoolCommunity::TEACHER_ROLE ) {
				$teacherSchools[] = $schoolRole['school_id'];
			}
			else if ( $schoolRole['role_id'] == SchoolCommunity::ECOLOGIST_ROLE ) {
				$ecolSchools[] = $schoolRole['school_id'];
			}
			else if ( $schoolRole['role_id'] == SchoolCommunity::STUDENT_ROLE ) {
				$isStudent = true;
			}
			$allSchools[] = $schoolRole['school_id'];
		}
		
		$ecolSchoolsStr = "";
		$teacherSchoolsStr = "";
		$allSchoolsStr = "";
		$isTeacher = false;
		$isEcologist = false;
		if ( count($ecolSchools) > 0 ) {
			$isEcologist = true;
			$ecolSchoolsStr = implode (',', $ecolSchools);
		}
		if ( count($teacherSchools) > 0 ) {
			$isTeacher = true;
			$teacherSchoolsStr = implode (',', $teacherSchools);
		}
		if ( count($allSchools) > 0 ) {
			$allSchoolsStr = implode (',', $allSchools);
		}
		
		$allTypeResources = array();
		$finalQuery = null;
		
		$typeResources = null;
		if ( $isAdmin ) {
			$finalQuery = $db->getQuery(true)
				->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.resource_type = ". $resourceType )
				->where("R.deleted = 0");
					
			if ( $filters ) {
				$finalQuery->where($whereStr);
			}
		}
		else {
			
			if ( $isTeacher or $isEcologist ) {
			
				// My own resources
				$query = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId )
					->where("R.resource_type = ". $resourceType )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$query->where($whereStr);
				}
			
			
				// School resources
				$query2 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.school_id in (" . $allSchoolsStr . ") and R.access_level = " . SchoolCommunity::SCHOOL )
					->where("R.resource_type = ". $resourceType )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$query2->where($whereStr);
				}
			
			
				// Community resources
				$query3 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::COMMUNITY )
					->where("R.resource_type = ". $resourceType )
					->where("R.deleted = 0");
					
				if ( $filters ) {
					$query3->where($whereStr);
				}
			
			

				$query4 = $query->union($query2)->union($query3) ;
				
				
			}
			else if ( $isStudent ) {
			
				// Just my own resources for now
				$query4 = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.person_id = " . $personId )
					->where("R.resource_type = ". $resourceType )
					->where("R.deleted = 0");
				
				if ( $filters ) {
					$query4->where($whereStr);
				}
			
			
			}
			if ( $finalQuery ) {
				$finalQuery = $finalQuery->union($query4);
			}
			else {
				$finalQuery = $query4;
			}
		
			if ( $isEcologist ) {
				// Ecologist resources
				$query = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::ECOLOGISTS )
					->where("R.resource_type = ". $resourceType )
					->where("R.deleted = 0");

				if ( $filters ) {
					$query->where($whereStr);
				}
				
				
				$finalQuery = $finalQuery->union($query);
			};
		}
		
		$finalQuery->order("tstamp DESC");
		
		//error_log("ResourceFile::getResourcesByType query created: " . $finalQuery->dump());
		
		
		$db->setQuery($finalQuery);
		$db->execute();
		
		$totalRows = $db->getNumRows();
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
					
		$allTypeResources  = $db->loadAssocList("resource_id");
		
		return (object)array("total"=>$totalRows, "resources"=>$allTypeResources);
				
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
					->order("R.set_id, R.resource_id")
					->where("R.deleted = 0");

				
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
					->select("R.resource_id as resource_id, R.set_id, RS.set_name, ST.st_id, ST.task_id, ST.status, R.resource_type, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, R.s3_status, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set from Resource R")
					->innerJoin("StudentTasks ST on ST.set_id = R.set_id and ST.status >= " . Badge::PENDING)
					->innerJoin("ResourceSet RS on R.set_id = RS.set_id")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("ST.person_id = " . $personId )
					->where("R.deleted = 0")
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
	
	private static function getFilterWhereStr($filters) {
	
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
					//$isFav = true;
					$whereArray[] = " (ISNULL(FR.fr_id) = 0) ";
				}
				else if ( $keyVal[0] == 'mine' ) {
					//$isMine = true;
					$whereArray[] = " (R.person_id = " . $personId . ") ";
				}
				else if ( $keyVal[0] == 'pin' ) {
					//$isPin = true;
					$whereArray[] = " (ISNULL(PR.pr_id) = 0) ";
				}
				else if ( $keyVal[0] == 'new' ) {
					//$isNew = true;
					$whereArray[] = " (TIMESTAMPDIFF(DAY, R.timestamp, NOW())<".self::NUM_DAYS_NEW . ") " ;
				}
			}
			if ( count($typeArray) > 0 ) {
				$whereArray[] = " R.resource_type in (" . implode(',', $typeArray) . ") ";
			}
			if ( count($tagArray) > 0 ) {
				$whereArray[] = " (R.resource_id in ( select resource_id from `ResourceTag` RT where tag_id in (".implode(',', $tagArray)."))) ";
			
			}
			
			$whereStr = " ( " . implode(' or ', $whereArray) . " )";
			
		}
		
		return $whereStr;
	}
	
	
	public static function searchResources ( $searchStr , $filters, $page = 1, $pageLength = self::NUM_PER_PAGE) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = self::getFilterWhereStr($filters);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Get resources matching search
		// All my files matching search plus all shared with my school files matching search plus all shared with community matching search
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$allSearchResources = array();
		
		$finalQuery = null;
		
		if ( SchoolCommunity::isAdmin() ) {
			
			$finalQuery = $db->getQuery(true)
				->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.deleted = 0")
				->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
				
			if ( $filters ) {
				$query->where($whereStr);
			}
		}
		else {	
			foreach ( $schoolRoles as $schoolRole ) {
				
				$schoolId = $schoolRole['school_id'];
				$roleId = $schoolRole['role_id'];
				
				if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
				
					// My own resources
					$query = $db->getQuery(true)
						->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
							"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
							"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
						->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
						->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
						->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
						->where("R.person_id = " . $personId )
						->where("R.deleted = 0")
						->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
						
					if ( $filters ) {
						$query->where($whereStr);
					}
						
					
					// School resources
					$query2 = $db->getQuery(true)
						->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
							"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
							"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
						->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
						->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
						->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
						->where("R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL )
						->where("R.deleted = 0")
						->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
						
					if ( $filters ) {
						$query2->where($whereStr);
					}
					
					// Community resources
					$query3 = $db->getQuery(true)
						->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
							"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
							"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
						->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
						->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
						->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
						->where("R.access_level = " . SchoolCommunity::COMMUNITY )
						->where("R.deleted = 0")
						->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
						
					if ( $filters ) {
						$query3->where($whereStr);
					}
					

					$query4 = $query->union($query2)->union($query3) ;
					
				}
			}
			if ( $finalQuery ) {
				$finalQuery = $finalQuery->union($query4);
			}
			else {
				$finalQuery = $query4;
			}
			
			if ( SchoolCommunity::isEcologist() ) {
				// Ecologist resources
				$query = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::ECOLOGISTS )
					->where("R.deleted = 0")
					->where("MATCH(R.upload_filename, R.title, R.description, R.readable, R.external_text) AGAINST ('".$searchStr."' IN NATURAL LANGUAGE MODE)");
					
				if ( $filters ) {
					$query->where($whereStr);
				}
					

				//error_log("ResourceFile::getResourcesByType query created: " . $query->dump());
				
				$finalQuery = $finalQuery->union($query);
			};
		}
		
		$finalQuery->order("tstamp DESC");
		
		//error_log("ResourceFile::searchResurces query created: " . $finalQuery->dump());
		
		$db->setQuery($finalQuery);
		$db->execute();
		
		$totalRows = $db->getNumRows();
		
		//error_log ( "ResourceFile search total rows = " . $totalRows );
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
		//error_log("ResourceFile::searchResurces query created: " . $finalQuery->dump());
		
		//$db->execute();
		
		$allSearchResources  = $db->loadAssocList("resource_id");
		
		return (object)array("total"=>$totalRows, "resources"=>$allSearchResources);
		
		
	}
	
	public static function getNewResources ( $filters, $page = 1, $pageLength = self::NUM_PER_PAGE ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = self::getFilterWhereStr($filters);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// All my files matching search plus all shared with my school files matching search plus all shared with community matching search
		
		// Loop through my school roles and add files accordingly
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$newResources = array();
		$finalQuery = null;
		
		if ( SchoolCommunity::isAdmin() ) {
			
			$finalQuery = $db->getQuery(true)
				->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.deleted = 0")
				->where("(TIMESTAMPDIFF(DAY, R.timestamp, NOW())<".self::NUM_DAYS_NEW.")");
			
			if ( $filters ) {
				$finalQuery->where($whereStr);
			}
		}
		else {
		
			foreach ( $schoolRoles as $schoolRole ) {
				
				$schoolId = $schoolRole['school_id'];
				$roleId = $schoolRole['role_id'];
				$searchResources = null;
				
				$finalQuery = null;
				
				if ( $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
				
					// My own resources
					$query = $db->getQuery(true)
						->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
							"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
							"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
						->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
						->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
						->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
						->where("R.person_id = " . $personId )
						->where("R.deleted = 0")
						->where("(TIMESTAMPDIFF(DAY, R.timestamp, NOW())<".self::NUM_DAYS_NEW.")");
					
					if ( $filters ) {
						$query->where($whereStr);
					}
					
					
					// School resources
					$query2 = $db->getQuery(true)
						->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
							"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
							"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
						->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
						->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
						->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
						->where("R.school_id = " . $schoolId . " and R.access_level = " . SchoolCommunity::SCHOOL )
						->where("R.deleted = 0")
						->where("(TIMESTAMPDIFF(DAY, R.timestamp, NOW())<".self::NUM_DAYS_NEW.")");
						
					if ( $filters ) {
						$query2->where($whereStr);
					}
					
					
					// Community resources
					$query3 = $db->getQuery(true)
						->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
							"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
							"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
						->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
						->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
						->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
						->where("R.access_level = " . SchoolCommunity::COMMUNITY )
						->where("R.deleted = 0")
						->where("(TIMESTAMPDIFF(DAY, R.timestamp, NOW())<".self::NUM_DAYS_NEW.")");
						
					if ( $filters ) {
						$query3->where($whereStr);
					}
					
					
					$query4 = $query->union($query2)->union($query3) ;
					
				}
			}
			if ( $finalQuery ) {
				$finalQuery = $finalQuery->union($query4);
			}
			else {
				$finalQuery = $query4;
			}
			
			if ( SchoolCommunity::isEcologist() ) {
				// Ecologist resources
				$query = $db->getQuery(true)
					->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
						"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
						"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
					->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
					->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
					->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
					->where("R.access_level = " . SchoolCommunity::ECOLOGISTS )
					->where("R.deleted = 0")
					->where("(TIMESTAMPDIFF(DAY, R.timestamp, NOW())<".self::NUM_DAYS_NEW.")");
					
				if ( $filters ) {
					$query->where($whereStr);
				}
					
					
				//error_log("ResourceFile::getResourcesByType query created: " . $query->dump());
				
				$finalQuery = $finalQuery->union($query);
			};
		
		}
		
		$finalQuery->order("tstamp DESC");
		
		//error_log("ResourceFile::getResourcesByType query created: " . $finalQuery->dump());
		
		//$db->setQuery($finalQuery);
		
		//$newResources  = $db->loadAssocList("resource_id");
		
		//return $newResources;
		
		$db->setQuery($finalQuery);
		$db->execute();
		
		$totalRows = $db->getNumRows();
		
		//error_log ( "ResourceFile search total rows = " . $totalRows );
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($finalQuery, $start, $pageLength);
		
		//error_log("ResourceFile::searchResurces query created: " . $finalQuery->dump());
		
		//$db->execute();
		
		$newResources  = $db->loadAssocList("resource_id");
		
		return (object)array("total"=>$totalRows, "resources"=>$newResources);
	}
	
	
	public static function getMyResources ( $filters, $page = 1, $pageLength = self::NUM_PER_PAGE ) {
		
		$personId = userID();
		
		if ( !$personId ) {
			return null;
		}
		
		$whereStr = self::getFilterWhereStr($filters);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Loop through my school roles and add files accordingly
		$schoolUser = SchoolCommunity::getSchoolUser();
		$roleId = $schoolUser->role_id;
		
		$myResources = array();
		$finalQuery = null;
		
		$query = null;
		
		if ( $roleId == SchoolCommunity::ADMIN_ROLE or $roleId == SchoolCommunity::TEACHER_ROLE or $roleId == SchoolCommunity::ECOLOGIST_ROLE ) {
		
			// My own resources
			$query = $db->getQuery(true)
				->select("R.timestamp as tstamp, R.resource_id as resource_id, R.filetype, R.upload_filename, R.title, R.description, R.source, R.external_text, R.url, R.access_level, R.person_id, R.school_id, " .
					"IFNULL(PR.pr_id, 0) as is_pin, IFNULL(FR.fr_id, 0) as is_fav, IFNULL(LR.lr_id, 0) as is_like, " .
					"(select count(*) from LikedResource LRALL where LRALL.resource_id = R.resource_id) as num_likes, (select count(*) from Resource R2 where R2.set_id = R.set_id) as num_in_set, R.resource_type, R.set_id, R.s3_status  from Resource R")
				->leftJoin("FavouriteResource FR on FR.resource_id = R.resource_id and FR.person_id = " . $personId)
				->leftJoin("PinnedResource PR on PR.resource_id = R.resource_id")
				->leftJoin("LikedResource LR on LR.resource_id = R.resource_id and LR.person_id = " . $personId)
				->where("R.person_id = " . $personId )
				->where("R.deleted = 0")
				->order("tstamp DESC");
			
			if ( $filters ) {
				$query->where($whereStr);
			}
				
			//$db->setQuery($query);
		
			//$myResources  = $db->loadAssocList("resource_id");
			
			$db->setQuery($query);
			$db->execute();
			
			$totalRows = $db->getNumRows();
			
			//error_log ( "ResourceFile search total rows = " . $totalRows );
			
			$start = ($page-1)*$pageLength;
			
			$db->setQuery($query, $start, $pageLength);
			
			//error_log("ResourceFile::searchResurces query created: " . $query->dump());
			
			//$db->execute();
			
			$newResources  = $db->loadAssocList("resource_id");
			
			return (object)array("total"=>$totalRows, "resources"=>$newResources);
			}
		
		return $myResources;
		
	}
	
	public static function deleteResource ( $resourceId ) {
		
		if ( self::canEdit($resourceId) ) {
			
			$options = dbOptions();
		
			$db = \JDatabaseDriver::getInstance($options);
			
			$fields = new \stdClass();
			$fields->resource_id = $resourceId;
			$fields->deleted = 1;
				
			$success = $db->updateObject('Resource', $fields, 'resource_id');
			if(!$success){
				error_log ( "Resource update failed" );
			}
			
			return $success;
		}
		else {
			return false;
		}
	}
	
	public static function canEdit ( $resourceId ) {
		
		$returnValue = false;
		
		if ( SchoolCommunity::isAdmin() ) {
			$returnValue = true;
		}
		else {
			$user = userID();
		
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("person_id from Resource")
					->where("resource_id = " . $resourceId);
					
			$db->setQuery($query);
				
			//error_log("Set id select query created: " . $query->dump());
				
			$resourcePerson = $db->loadResult();
			
			$returnValue = false;
			if ( $user == $resourcePerson ) {
				$returnValue = true;
			}
		}
		
		return $returnValue;
			
	}

/*
	public static function getSourceText ( $type, $text ) {
		
		$sourceText = "";
		if ( $type && $type == "user" ) {
			
			$sourceText = SchoolCommunity::getUserName ();
			
		}
		else if ( $type && $type == "role" ) {
			
			$sourceText = SchoolCommunity::getRoleText ();
		}
		else if ( $type && $type == "external" ) {
			$sourceText = $text;
		}
		return $sourceText;
	}
*/



}


?>

