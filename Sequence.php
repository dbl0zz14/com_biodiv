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
	protected $location;
	protected $mediaFiles;
	protected $primarySpecies;
	protected $secondarySpecies;
	
	function __construct($id)
	{
		$this->id = $id;
		$this->media = "photo"; // Default to photo
		$this->type = null;
		$this->mediaFiles = array();
		$this->primarySpecies = array();
		$this->secondarySpecies = array();
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