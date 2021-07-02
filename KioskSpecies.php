<?php

// No direct access to this file
defined('_JEXEC') or die;

class KioskSpecies {
	
	private $projectId;
	
	// Array of arrays of species, one for each type
	private $speciesLists;
	
	private $nothingId;
	private $humanId;
	private $dkId;
	
	private $maxSpeciesDisplayed;
	
	
	function __construct( $projectId )
	{
		error_log ( "KioskSpecies constructor called, projectId = " . $projectId );
		
		$this->projectId = $projectId;
		
		$this->maxSpeciesDisplayed = 20;
		
		$this->nothingId = codes_getCode ( "Nothing", "content");
		
		$this->humanId = codes_getCode ( "Human", "content");
		
		$this->dkId = codes_getCode ( "Don\'t Know", "content");
		
		$this->otherId = codes_getCode ( "Other", "content");
		
		$this->setKioskFilters();
		
	}
	
	public function getMaxSpeciesDisplayed() {
		return $this->maxSpeciesDisplayed;
	}
	
	public function getCommonMammals() {
		return $this->speciesLists['commonmammals'];
	}
	
	public function getAllMammals() {
		return $this->speciesLists['allmammals'];
	}
	
	public function getCommonBirds() {
		return $this->speciesLists['commonbirds'];
	}
	
	public function getAllBirds() {
		return $this->speciesLists['allbirds'];
	}
	
	public function getNothingId() {
		return $this->nothingId;
	}
	
	public function getHumanId() {
		return $this->humanId;
	}
	
	public function getDkId() {
		return $this->dkId;
	}
	
	public function getOtherId() {
		return $this->otherId;
	}
	
	// Set project specific species lists each type of list
	private function setKioskFilters () {
		
		$this->speciesLists['commonmammals'] = array();
		$this->speciesLists['allmammals'] = array();
		$this->speciesLists['commonbirds'] = array();
		$this->speciesLists['allbirds'] = array();
		
		$lang = langTag();
		
		// First get the ids of the kiosk filters
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("O.option_id")->from("Options O")
			->innerJoin("ProjectOptions PO on PO.option_id = O.option_id and PO.project_id = " . $this->projectId )
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = 'kiosklist'")
			->where("O.struc = 'kioskfilter'" );
		
		error_log("query to get list ids created: " . $query->dump() );
		
		$db->setQuery($query);
		$listIds = $db->loadColumn();
		
		$errStr = print_r ($listIds, true);
		error_log ( "KioskSpecies::setKioskFilters Species list array: " . $errStr );
			
			
		// Different version for English or not
		if ( $lang == 'en-GB' ) {
			
			$query = $db->getQuery(true);
			$query->select("OD.value as type, O.option_id as id, O.option_name as name, O.article_id as article")->from("Options O")
				->innerJoin("SpeciesList SL on SL.species_id = O.option_id and SL.list_id in (" . implode(',', $listIds) . ")")
				->innerJoin("OptionData OD on SL.list_id = OD.option_id")
				->order("type, name");
				
			error_log("query to get species for lists created: " . $query->dump() );
		
			$db->setQuery($query);
			$allSpecies = $db->loadAssocList();
		}
		else {
			$query = $db->getQuery(true);
			$query->select("OD.value as type, O.option_id as id, OD2.value as name, O.article_id as article")->from("Options O")
				->innerJoin("SpeciesList SL on SL.list_id = O.option_id and SL.list_id in (" . implode(',', $listIds) . ")")
				->innerJoin("OptionData OD on SL.species_id = OD.option_id")
				->innerJoin("OptionData OD2 on O.option_id = OD2.option_id and OD2.data_type = '" . $lang . "'")
				->order("type, name");
				
			$db->setQuery($query);
			$allSpecies = $db->loadAssocList();
		
		}
		
		foreach ( $allSpecies as $row ) {
			$type = $row['type'];
			$id = $row['id'];
			$name = $row['name'];
			$article = $row['article'];
			$this->speciesLists[$type][] = array("id"=>$id, "name"=>$name, "article"=>$article);
		}
		
		
	}



	
	
}



?>

