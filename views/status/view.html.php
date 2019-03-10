<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewStatus extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
    $person_id = (int)userID();
    $person_id or die("No person_id");

    $this->root = 
    $this->status = array();
	
	// Set the photo to zero on load and the classify option back to default 0
	$app = JFactory::getApplication();
    $app->setUserState('com_biodiv.photo_id', 0);
	$app->setUserState('com_biodiv.classify_only_project', 0);
    $app->setUserState('com_biodiv.classify_project', 0);
    $app->setUserState('com_biodiv.classify_self', 0);
    $app->setUserState('com_biodiv.my_project', 0);
    

    $db = JDatabase::getInstance(dbOptions());
/*
    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT photo_id)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $db->setQuery($query);
    $this->status['Number of classifications'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(*)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $query->where("species not in (86,87,97)");
    $db->setQuery($query);
    $this->status['Number of animals identified'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT species)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $query->where("species not in (86,97)");
    $db->setQuery($query);
    $this->status['Number of species identified'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(*)");
    $query->from("Photo");
    $db->setQuery($query);
    $this->status['Total photos in system'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT person_id)");
    $query->from("Animal");
    $db->setQuery($query);
    $this->status['Total spotters in system'] = $db->loadResult();
*/
/*
	$query = $db->getQuery(true);
    $query->select("COUNT(*)")
		->from("Photo")
		->where("sequence_num = 1");
    $db->setQuery($query);
    $this->status['Total number of sequences in the system'] = $db->loadResult();

	$query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT P.sequence_id)")
		->from("Photo P")
		->innerJoin("Animal A on P.photo_id = A.photo_id")
		->where("A.species != 97");
    $db->setQuery($query);
    $this->status['Total number of sequences classified'] = $db->loadResult();


	$query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT P.sequence_id)")
		->from("Animal A")
		->innerJoin("Photo P on P.photo_id = A.photo_id")
		->where("A.species != 97")
		->where("A.person_id = " . $person_id);
    $db->setQuery($query);
    $this->status['Number of sequences classified by you'] = $db->loadResult();


	$query = $db->getQuery(true);
    $query->select("A.person_id, count(distinct P.sequence_id)")
		->from("Animal A")
		->innerJoin("Photo P on P.photo_id = A.photo_id")
		->where("A.species != 97")
		->group("A.person_id ")
		->order("count(distinct P.sequence_id) desc");
    $db->setQuery($query);
	
    $leagueTable = $db->loadAssocList();
	*/


	//print_r($leagueTable);
	
	$query = $db->getQuery(true);
    $query->select("end_date, num_uploaded as uploaded, num_classified as classified ")
		->from("Statistics")
		->where("project_id = 0")
		->order("end_date DESC");
	$db->setQuery($query, 0, 1); // LIMIT 1
	$row = $db->loadAssoc();
	$this->status['Total number of sequences in the system'] = $row['uploaded'];
	$this->status['Total number of sequences classified'] = $row['classified'];
	
	// Default to zero.
	$this->status['Number of sequences classified by you'] = 0;
	
	$query = $db->getQuery(true);
    $query->select("person_id, num_classified")
		->from("LeagueTable")
		->order("num_classified desc");
    $db->setQuery($query);
	
    $leagueTable = $db->loadAssocList();
	
	
	
	$this->leagueTable = array_column($leagueTable, 'person_id');
	$this->totalSpotters = count($this->leagueTable);
	
	//print_r($this->leagueTable);
	
	$userPos = array_search($person_id, $this->leagueTable);
	if ( $userPos === False  ) {
		//print ( "Not in league table" );
		$userPos = count($this->leagueTable);
		$this->totalSpotters += 1;
	}
	else {
		$this->status['Number of sequences classified by you'] = $leagueTable[$userPos]['num_classified'];
	}
	
	$userPos += 1;
	
	$th = 'th';
	
	$finalDigit = $userPos - 10*intval($userPos/10);
	if ( $finalDigit == 1 ) $th = 'st';
	if ( $finalDigit == 2 ) $th = 'nd';
	if ( $finalDigit == 3 ) $th = 'rd';
	
	// Set back to th for 11, 12 and 13, 111, 112, 113, etc
	if ( ($userPos - 11)%100 == 0 ) $th = 'th';
	if ( ($userPos - 12)%100 == 0 ) $th = 'th';
	if ( ($userPos - 13)%100 == 0 ) $th = 'th';
	$this->status['Total number of Spotters in the system' ] = $this->totalSpotters;
	$this->status['You are currently the ' . $userPos . $th . ' highest contributor to Spotting' ] ='';
		
	// call new biodiv.php function instead of myProjects()
	// Changed back argument to check redirect issue  
	$this->projects = mySpottingProjects( true );
	//$this->projects = mySpottingProjects();
	$this->mylikes = getLikes(1);
	
    // Display the view
    parent::display($tpl);
  }
}



?>