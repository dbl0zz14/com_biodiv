<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

include_once "local.php";

use Joomla\CMS\Factory;


class BiodivHelper {
	
	private $db;
	
	
	function __construct()
	{
		$options = dbOptions();
		$this->db = JDatabase::getInstance(dbOptions());
		
		$apiOptions = apiOptions();
		$this->scopeStem = $apiOptions['scopestem'];
		/*
		$this->db = new mysqli($options['host'],
					 $options['user'],
					 $options['password'],
					 $options['database']);
		*/
	}
	
	// Return the API details for a single user id as an object
	/* may need user details in the future but keeping to just what's needed for now
	function userDetails ( $person_id ) {
	  
		$query = $this->db->getQuery(true);
		$query->select("UE.person_id as id, O.option_name as topic, UE.score as level")->from("UserExpertise UE")
		->innerJoin("Options O on O.option_id = UE.topic_id and O.struc = 'topic'")
		->where("person_id = " . $person_id );
		$this->db->setQuery($query);
		$userDetails = $this->db->loadObjectList();

		return $userDetails;
	}
	*/
	
	// Return the stem to use when requesting api scope
	function getScopeStem () {
		return $this->scopeStem;
	}
	
	// Helper doesn't get by email, just id or username, so access the database directly
	function getUser ( $email ) {
		
		// Get config details so can get to user table
		$config = Factory::getConfig();
		
		$table ="" . $config->get("db") . "." . $config->get("dbprefix") . "users";
		
		$query = $this->db->getQuery(true);
		//$query = 'SELECT id, username, email FROM #__users WHERE email = ' . $this->db->quote($email);
		$query = 'SELECT id, username, email FROM ' . $table . ' WHERE email = ' . $this->db->quote($email);
		$this->db->setQuery($query, 0, 1);
		$user = $this->db->loadObject();
		
		// $user_str = print_r ($user, true );
		// error_log($user_str);

		return $user;
	}

	// Find the group id, given the name
	function getUserGroupId ( $groupName ) {
		
		// Get config details so can get to user table
		$config = Factory::getConfig();
		
		$groupId = null;
		
		$table ="" . $config->get("db") . "." . $config->get("dbprefix") . "usergroups";
		
		$query = $this->db->getQuery(true);
		$query = 'SELECT id FROM ' . $table . ' WHERE title = ' . $this->db->quote($groupName);
		$this->db->setQuery($query, 0, 1);
		$groupId = $this->db->loadResult();
		
		return $groupId;
	}

	// Return the expertise details for a single user id as a list of objects
	function userExpertise( $person_id ) {
		
		// Use language directly to get translated option names for speed.
		$query = $this->db->getQuery(true);
		$query->select("O.option_id as topic_id, O.option_name as topic, UE.score as level")->from("UserExpertise UE")
			->innerJoin("Options O on O.option_id = UE.topic_id")
			->where("person_id = " . $this->db->quote($person_id) );
		$this->db->setQuery($query);
		$userDetails = $this->db->loadAssocList();
		
		if ( $userDetails == null ) return array();

		return $userDetails;
	}


	// Look up the MammalWeb species_id using the code given
	function imageExists ( $imageId, $filename ) {
				  
		$query = $this->db->getQuery(true);
		$query->select("count(*)")->from("Photo P")
					 ->where("photo_id = " . $imageId );
					 
		if ( $filename ) {
			$query->where("filename = " . $this->db->quote($filename) );
		}
		
		$this->db->setQuery($query);
		
		$numMatching = $this->db->loadResult();

		return $numMatching == 1;
				  
	}


	// Look up the MammalWeb species_id using the code given
	function getPhotoDetails ( $siteId, $filename ) {
		
		$photoDetails = null;
		
		if ( is_numeric ( $siteId ) ) {
				  
			$query = $this->db->getQuery(true);
			$query->select("photo_id, sequence_id")->from("Photo P")
						 ->where("site_id = " . $siteId )
						 ->where("filename = " . $this->db->quote($filename) );
						 
			$this->db->setQuery($query);
			
			$photoDetails = $this->db->loadObject();
		}

		return $photoDetails;
				  
	}


	// Look up the MammalWeb species_id using the code given
	function getSpecies ( $type, $code ) {
				  
		$query = $this->db->getQuery(true);
		$query->select("OD.option_id as species_id")->from("OptionData OD")
					 ->where("data_type = " . $this->db->quote($type) )
					 ->where("value = " . $this->db->quote($code) );
		$this->db->setQuery($query);
		$speciesId = $this->db->loadResult();

		return $speciesId;
				  
	}


	// Return the expertise details for a single user id as a list of objects
	function classify( $sequenceId, $photoId, $origin, $model, $originRef, $siteId, $filename, $speciesType, $species, $speciesId, $prob = null, $xmin = null, $ymin = null, $xmax = null, $ymax = null ) {
		
		try
		{
			
			// Use transaction...
			$this->db->transactionStart();
			
			$fields = new StdClass;
			$fields->sequence_id = $sequenceId;
			$fields->photo_id = $photoId;
			$fields->origin = $origin;
			$fields->species_id = $speciesId;
			$fields->prob = $prob;
			
			if ( $model ) {
				$fields->model = $model;
			}

			if ( $xmin !== null && $ymin !== null && $xmax !== null && $ymax !== null ) {
				$fields->xmin = $xmin;
				$fields->ymin = $ymin;
				$fields->xmax = $xmax;
				$fields->ymax = $ymax;
			}
			$success = $this->db->insertObject("Classify", $fields);
			$classifyId = $this->db->insertid();
			
			if(!$success){
				
				return null;
				
			}
			else {
				
				// Keep copy of raw data
				$rawFields = new StdClass;
				$rawFields->classify_id = $classifyId;
				if ( $originRef ) {
					$rawFields->origin_ref = $originRef;
				}
				if ( $siteId ) {
					$rawFields->site_id = $siteId;
				}
				if ( $filename ) {
					$rawFields->filename = $filename;
				}
				
				$rawFields->species_type = $speciesType;
				$rawFields->species = $species;
				
				$success = $this->db->insertObject("ClassifyRaw", $rawFields);
				
				if ( !$success ) {
					
					error_log ( "Failed to write to ClassifyRaw table with classify_id " . $classifyId . ", photo_id " . $photoId );
					
					$this->db->transactionRollback();
					
					return null;
				}
				else {
					
					$this->db->transactionCommit();
				
					return $classifyId;
				}
			}
		}
		catch ( Exception $e ) {
			
			error_log("Classify insert failed due to " . $e);
			
			// catch any database errors.
			$this->db->transactionRollback();
			
			return null;
			
		}
				  
	}
	
	
	// Return the expertise details for a single user id as a list of objects
	function ruleOfThumb( $origin, $aiType,	$aiVersion, $analysisVersion, $sequenceId, $species ) {
		
		try
		{
			
			$fields = new StdClass;
			$fields->origin = $origin;
			$fields->ai_type = $aiType;
			$fields->ai_version = $aiVersion;
			$fields->analysis_version = $analysisVersion;
			$fields->sequence_id = $sequenceId;
			
			$analysisIds = array();
			$problem = false;
			
			foreach ( $species as $speciesId ) {
				
				$fields->species_id = $speciesId;
				$success = $this->db->insertObject("AnalysisRThumb", $fields);
				$analysisIds[] = $this->db->insertid();
				
				if(!$success){
				
					$problem = true;
					error_log ( "Failed to write to AnalysisRThumb table for sequence " . $sequenceId );
				}
			
			}
			
			if ( $problem ){
				
				return null;
			}
			else {
				
				return $analysisIds;
			}
			
		}
		catch ( Exception $e ) {
			
			error_log("AnalysisRThumb insert failed due to " . $e);
			
			return null;
			
		}
				  
	}

}

?>
