<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

class Sequence {
	
	protected $id;
	protected $media;
	protected $type; 
	protected $siteId;
	protected $siteName;
	protected $location;
	protected $mediaFiles;
	protected $primarySpecies;
	protected $secondarySpecies;
	
	function __construct($id)
	{
		$this->id = $id;
		$this->mediaFiles = array();
		$this->primarySpecies = array();
		$this->secondarySpecies = array();
		
		$db = JDatabase::getInstance(dbOptions());
	
		$query = $db->getQuery(true);
		$query->select("P.sequence_id, P.photo_id, P.site_id, S.site_name, S.latitude, S.longitude")->from("Photo P")
			->innerJoin("Site S on P.site_id = S.site_id")
			->where("P.sequence_id = ".$id )
			->order("sequence_num");
		$db->setQuery($query);
		$photos = $db->loadObjectList();
		
		$this->siteId = $photos[0]->site_id ;
		$this->siteName = $photos[0]->site_name;
		$this->location = new Location($photos[0]->latitude, $photos[0]->longitude);
				
		foreach ( $photos as $photo ) {
			$photo_id = $photo->photo_id;
			$photo_url = photoURL($photo_id);
			
			//error_log("photo url: " . $photo_url );
			
			if ( isVideo($photo_id) ) {
				$ext = strtolower(JFile::getExt($photo_url));
				//error_log ( "Found video file, ext = " . $ext );
				$this->setMedia("video", $ext);
			}
			else if ( isAudio ($photo_id) ){
				$ext = strtolower(JFile::getExt($photo_url));
				//error_log ( "Found audio file, ext = " . $ext );
				$this->setMedia("audio", $ext);
			}
			else {
				$ext = strtolower(JFile::getExt($photo_url));
				$this->setMedia("photo", $ext);
			}
					
			$this->addMediaFile ( $photo_id, $photo_url );
		}
	
	}
	
	function getId() {
		return $this->id;
	}
	
	function getMedia() {
		return $this->media;
	}
	
	function getMediaType() {
		return $this->type;
	}
	
	function setMedia ( $media, $type = null ) {
		$this->media = $media;
		$this->type = $type;
	}
	
	function getSiteName () {
		return $this->siteName;
	}
	
	function getSiteId () {
		return $this->siteId;
	}
	
	function getLocation () {
		return $this->location;
	}
	
	function setLocation ( $location ) {
		$this->location = $location;
	}
	
	function addMediaFile ( $id, $url ) {
		
		$this->mediaFiles[''.$id] = $url;
	}
	
	function getMediaFiles () {
		
		return $this->mediaFiles;
	}
	
	function addPrimarySpecies ( $species ) {
		$this->primarySpecies[] = $species;
	}
	
	function addSecondarySpecies ( $species ) {
		$this->secondarySpecies[] = $species;
	}
	
	function getPrimarySpecies () {
		return $this->primarySpecies;
	}
	
	function getSecondarySpecies () {
		return $this->secondarySpecies;
	}


	
	
}

?>