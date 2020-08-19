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
		
		error_log ( "About to get user for email " . $email );
		
		// Get config details so can get to user table
		$config = Factory::getConfig();
		
		$table ="" . $config->get("db") . "." . $config->get("dbprefix") . "users";
		
		$query = $this->db->getQuery(true);
		//$query = 'SELECT id, username, email FROM #__users WHERE email = ' . $this->db->quote($email);
		$query = 'SELECT id, username, email FROM ' . $table . ' WHERE email = ' . $this->db->quote($email);
		$this->db->setQuery($query, 0, 1);
		$user = $this->db->loadObject();
		
		$user_str = print_r ($user, true );
		error_log($user_str);

		return $user;
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


	
}

?>