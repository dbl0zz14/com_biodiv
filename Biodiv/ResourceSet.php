<?php

namespace Biodiv;

// A ResourceSet is a grouping, usually a single upload.

// No direct access to this file
defined('_JEXEC') or die;

class ResourceSet {
	
	private $setId;
	private $setName;
	private $resourceType;
	private $articleId;
	private $setText;
	private $direcName;
	private $personId;
	private $userId;
	private $uploadParamsJson;
	
	
	function __construct( $setId )	{
		
		$this->userId = userID();
		
		if ( $setId  ) {
			
			$this->setId = $setId;
			
		}
		
		if ( $this->setId ) {
			
			$setDetails = codes_getDetails ( $this->setId, "resourceset" );
			
			$this->resourceType = (int)$setDetails['resource_type'];
			$this->personId = (int)$setDetails['person_id'];
			$this->setName = $setDetails['set_name'];
			$this->uploadParamsJson = $setDetails['upload_params'];
			
			$this->direcName = "biodivimages/resources/person_".$this->personId."/rtype_".$this->resourceType."/set_".$this->setId;
			
			$root = biodivRoot();
			$this->dirPath = "".$root.$this->direcName;
			
		}
	
	}
	
	
	public function getSetId () {
		return $this->setId;
	}
	
	public function getSetName () {
		return $this->setName;
	}
	
	public function getResourceType () {
		return $this->resourceType;
	}
	
	public function getDirName () {
		return $this->direcName;
	}
	
	public function getDirPath () {
		return $this->dirPath;
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
			$resourceFiles = $db->loadAssocList('resource_id');
		}
		else {
			$resourceFiles = array();
		}
		
		return $resourceFiles;
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
			
			$instance = new self( $setId );
		
			return $instance;
		}
		else {
			return null;
		}
	}
	
	
	
}



?>

