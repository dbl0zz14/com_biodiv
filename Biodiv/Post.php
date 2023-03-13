<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Post {
	
	const NUM_PER_PAGE = 24;
	
	private $schoolUser;
	private $personId;
	private $setId;
	private $text;
	private $schoolId;
	private $schoolName;
	private $schoolImage;
	private $numLikes;
	private $likedByMe;
	private $files;
	private $timestamp;
	
	
	function __construct( $schoolUser, $postPersonId, $setId, $text, $schoolId, $schoolName, $schoolImage, $numLikes, $likedByMe, $resourcesStr, $timestamp )
	{
		
		if ( !$schoolUser ) {
			$this->schoolUser = SchoolCommunity::getSchoolUser();
		}
		else {
			$this->schoolUser = $schoolUser;
		}
		//$this->personId = $this->schoolUser->person_id;
		$this->personId = $postPersonId;
		
		$this->setId = $setId;
		$this->text = $text;
		$this->schoolId = $schoolId;
		$this->schoolName = $schoolName;
		$this->schoolImage = $schoolImage;
		$this->numLikes = $numLikes;
		$this->likedByMe = $likedByMe;
		$this->timestamp = $timestamp;
		
		$this->files = array();
		
		$resources = explode(',', $resourcesStr);
		
		
		foreach ( $resources as $resource ) {
			
			$resourceBits = explode('|', $resource);
			$this->files[] = $resourceBits;
			
		}
		
	}
	
	
	public function printPost () {
		
		print '<div id="resourceSet_'.$this->setId.'"class="panel panel-default actionPanel communityPost" role="button" data-toggle="modal" data-target="#postModal">';
		
		print '<div class="panel-header postHeader">';
		
		print '<div class="row small-gutter">';
		print '<div class="col-md-2 col-sm-2 col-xs-2"><img class="img-responsive postSchoolImage" src="'.$this->schoolImage.'"></div>';
		print '<div class="col-md-10 col-sm-10 col-xs-10 h4 postSchoolName">'.$this->schoolName.'</div>';
		print '</div>'; // row
		
		print '</div>'; // panel-header
		
		print '<div class="panel-body postBody">';
		
		print '<div id="postCarousel_'.$this->setId.'" class="carousel slide postCarousel">';
		
		$numFiles = count($this->files);
		if ( $numFiles > 1 ) {
			print '<ol class="carousel-indicators">';
			$i = 0;
			foreach ( $this->files as $file ) {
				print '<li';
				print '     data-target="#postCarousel_'.$this->setId.'"';
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
		
		print '<div class="carousel-inner">';
		$i = 0;
		foreach ( $this->files as $file ) {
			
			if ( $i == 0 ) {
				print '<div class="item active">';
			}
			else {
				print '<div class="item">';
			}
			
			$filetype = $file[1];
			$fileTypeBits = explode('/', $filetype );
				
			$mainType = $fileTypeBits[0];
			
			if ( $mainType == "image" ) {
				print '<img src="'.$file[2].'" type="'.$filetype.'" class="postMedia" alt="post image">';
			}
			else if ( $mainType == "video" ) {
				print '<video src="'.$file[2].'" type="'.$filetype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" class="postMedia"  ></video>';
			}
			else if ( $mainType == "audio" ) {
				print '<audio src="'.$file[2].'" type="'.$filetype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" class="postMedia"  ></audio>';
			}
			
			print '</div>'; // carousel-item
			$i++;
		}
		print '</div>'; // carousel-inner
		
		if ( $numFiles > 1 ) {
			print '<a class="left carousel-control" href="#postCarousel_'.$this->setId.'" data-slide="prev">';
			print '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
			print '<span class="sr-only">Previous</span>';
			print '</a>';
			print '<a class="right carousel-control" href="#postCarousel_'.$this->setId.'" data-slide="next">';
			print '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
			print '<span class="sr-only">Next</span>';
			print '</a>';
		}
		
		print '</div>'; // postCarousel
		
		print '</div>'; // panel-body
		
		print '<div class="panel-footer postFooter">';
		
		
		
		// foreach ( $this->files as $file ) {
			
			// print '<div>Resource id = ' . $file[0] . '</div>';
			// print '<div>File type = ' . $file[1] . '</div>';
			// //print 'Url = ' . $file[2];
			
		// }
		
		print '<div class="row setLikes h4">';
		print '<div class="col-md-2 col-sm-3 col-xs-3 text-left">';
		
		if ( $this->likedByMe ) {
			print '<div id="unlikeSet_'.$this->setId.'" class="unlikeSet"><i class="fa fa-lg fa-heart setMyLike"></i></div>';
			print '<div id="likeSet_'.$this->setId.'" class="likeSet" style="display:none"><i class="fa fa-lg fa-heart-o"></i></div>';
		}
		else {
			print '<div id="unlikeSet_'.$this->setId.'" class="unlikeSet" style="display:none"><i class="fa fa-lg fa-heart setMyLike"></i></div>';
			print '<div id="likeSet_'.$this->setId.'" class="likeSet"><i class="fa fa-lg fa-heart-o"></i></div>';
		}
				
		print '</div>';
		
		print '<div id="numSetLikes_'.$this->setId.'" class="col-md-10 col-sm-9 col-xs-9 text-right">';
		$numLikes = $this->numLikes;
		if ( $numLikes == 1 ) {
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_POST_SINGLE_LIKE");
		}
		else {
			
			print '' . $numLikes . ' ' . \JText::_("COM_BIODIV_POST_MANY_LIKES");
		}
		print '</div>';
		print '</div>'; // row
		print '<div class="postText">'.$this->text.'</div>';
		print '<div class="row postDateRow">';
		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '<div class="postDate">'.date( "d/m/Y", strtotime($this->timestamp)).'</div>';
		print '</div>'; // col-6
		print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
		if ( ($this->schoolUser->role_id == SchoolCommunity::ADMIN_ROLE) or ($this->schoolUser->person_id == $this->personId) ) {
			print '<div class="postDelete"><button id="deletePost_'.$this->setId.'" type="button" class="btn btn-info deletePost" data-toggle="modal" data-target="#deletePostModal">'.\JText::_("COM_BIODIV_POST_DELETE").'</button></div>';
		}
		print '</div>'; // col-6
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		
	}
	
	
	public static function createFromId ( $schoolUser = null, $setId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$currUserId = userID();
		
		$post = null;
		
		if ( $currUserId == $schoolUser->person_id ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			
			$query = $db->getQuery(true)
				->select("RS.school_id, PRS.person_id, S.name, S.image, RS.set_id, PRS.timestamp as tstamp, RS.description as text, GROUP_CONCAT(DISTINCT CONCAT_WS('|',R.resource_id,R.filetype,R.url)) as files, count(LRS.like_id) as num_likes, count(LRS2.like_id) as my_likes from ResourceSet RS")
				->innerJoin("PostedResourceSet PRS on PRS.set_id = RS.set_id")
				->innerJoin("Resource R on R.set_id = RS.set_id")
				->innerJoin("School S on S.school_id = RS.school_id")
				->leftJoin("LikedResourceSet LRS on LRS.set_id = RS.set_id")
				->leftJoin("LikedResourceSet LRS2 on LRS2.set_id = RS.set_id and LRS2.person_id = " . $currUserId)
				->where("PRS.set_id = " . $setId)
				->where("R.deleted = 0");
													
			$db->setQuery($query);
			
			$postDetails = $db->loadObject();
		
			$post = new self ( $schoolUser, $postDetails->person_id, $postDetails->set_id, $postDetails->text, 
								$postDetails->school_id, $postDetails->name, $postDetails->image, 
								$postDetails->num_likes, $postDetails->my_likes, $postDetails->files, null );
		}
		
		return $post;
	}
	
	
	public static function deletePost ( $schoolUser, $setId ) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		$currUserId = userID();
		
		if ( $currUserId == $schoolUser->person_id ) {
			
			$post = self::createFromId ( $schoolUser, $setId );
			
			if ( ($schoolUser->role_id == SchoolCommunity::ADMIN_ROLE) or ($currUserId == $post->personId) ) {
				
				$options = dbOptions();
				$db = \JDatabaseDriver::getInstance($options);
			
				$conditions = array(
					$db->quoteName('set_id') . ' = ' . $setId );
				$query = $db->getQuery(true)
					->delete($db->quoteName('PostedResourceSet'))
					->where($conditions);
				$db->setQuery($query);
				$result = $db->execute();
			
			}
		}
	}
	
	public static function getPosts ($schoolUser = null, $schoolId = null, $page = 1, $pageLength = self::NUM_PER_PAGE) {
		
		if ( !$schoolUser ) {
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		if ( !$schoolUser ) {
			return null;
		}
		$personId = $schoolUser->person_id;
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$allPosts = array();
		
		$query = $db->getQuery(true)
			->select("RS.school_id, PRS.person_id, S.name, S.image, RS.set_id, PRS.timestamp as tstamp, RS.description as text, GROUP_CONCAT(DISTINCT CONCAT_WS('|',R.resource_id,R.filetype,R.url)) as files, count(LRS.like_id) as num_likes, count(LRS2.like_id) as my_likes from ResourceSet RS")
			->innerJoin("PostedResourceSet PRS on PRS.set_id = RS.set_id")
			->innerJoin("Resource R on R.set_id = RS.set_id")
			->innerJoin("School S on S.school_id = RS.school_id")
			->leftJoin("LikedResourceSet LRS on LRS.set_id = RS.set_id")
			->leftJoin("LikedResourceSet LRS2 on LRS2.set_id = RS.set_id and LRS2.person_id = " . $personId)
			->where("R.deleted = 0");
							
		if ( $schoolId ) {
			$query->where("RS.school_id = " . $schoolId);
		}
				
		$query->group("RS.set_id");
		$query->order("tstamp DESC");
		
		$db->setQuery($query);
		
		error_log("Set id select query created: " . $query->dump());
		
		$db->execute();
		$totalRows = $db->getNumRows();
		
		$start = ($page-1)*$pageLength;
		
		$db->setQuery($query, $start, $pageLength);
		
		error_log("Set id select query created: " . $query->dump());
		
		$allPosts  = $db->loadObjectList("set_id");
		
		return (object)array("total"=>$totalRows, "posts"=>$allPosts);
	
	}
	
}



?>

