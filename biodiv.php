<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

include "local.php";
include "Project.php";
include "Location.php";

define('BIODIV_MAX_FILE_SIZE', 35000000);

// link to javascript stuff
$document = JFactory::getDocument();
$document->addScriptDeclaration('
var BioDiv = {};
BioDiv.root = "'. BIODIV_ROOT . '";');

JHtml::_('bootstrap.framework');
JHTML::stylesheet("bootstrap3-editable/bootstrap-editable.css", array(), true);
//JHTML::stylesheet("bootstrap3-editable/bootstrap-editable.css", true, true);
JHTML::script("bootstrap3-editable/bootstrap-editable.js", true, true);
JHTML::script("com_biodiv/biodiv.js", true, true);

// set up external database links
include "codes.php";
require_once('libraries/getid3/getid3/getid3.php');

include "aws.php";



function db(){
  static $db;
  $options = dbOptions();
  if(!$db){
    $db = new mysqli($options['host'],
		     $options['user'],
		     $options['password'],
		     $options['database']);
  }
  return $db;
}

function userID(){
  $user = JFactory::getUser();
  if($user->guest){
    return null;
  }
  else{
    return $user->id;
  }
}


$dbOptions = dbOptions();
codes_parameters_addTable("StructureMeta", $dbOptions['database']);



function codes_insertObject($fields, $struc){
  if(!canCreate($struc, $fields)){
  print "Cannot create $struc";
    return false;
  }
  $table = codes_getTable($struc);

  $db = JDatabase::getInstance(dbOptions());
  $success = $db->insertObject($table, $fields);
  if($success){
    $id = $db->insertid();
    return $id;
  }
else{
  print "Insert failed";
}
  return $success;
}

function codes_updateObject($fields, $struc){
  $codeField = codes_getCodeField($struc);
  $code = $fields->$codeField;
  if(!canEdit($code, $struc)){
    print "Cannot update $code $struc";
    return false;
  }
  $table = codes_getTable($struc);
  $db = JDatabase::getInstance(dbOptions());
  $success = $db->updateObject($table, $fields, $codeField);
  return $success;
}

function codes_deleteObject($code, $struc){
  if(!canEdit($code, $struc)){
    return false;
  }
  $codeField = codes_getCodeField($struc);
  $table = codes_getTable($struc);
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->delete($db->quoteName($table));
  $query->where($db->quoteName($codeField) . " = '$code'");
  $db->setQuery($query);
  $success = $db->execute();
  return $success;
}


// NB this is all done in this function for now.  May want to move to biodiv.php and codes.php
// but for now treating as a special case.
function codes_updateSiteProjects($fields, $struc) {
	
	//print "codes_updateSiteProjects called ";
	if ( $struc != 'site' ) {
		return false;
	}
	$codeField = codes_getCodeField($struc); // "site_id"
	$code = $fields->$codeField; // the actual site_id
	//print "codes_updateSiteProjects site_id = " . $code;
	
	if(!canEdit($code, $struc)){
		return false;
	}
	
	$name = 'projects';
	
	// NB $fields->projects is a comma separated list of project ids.  If it's empty just create an empty array.
	$projects = $fields->$name;
	$valuesArray = null;
	if ( $projects == "" ) $valuesArray = array();
	else $valuesArray = explode(',',$fields->$name);
	
	$success = false;
	
	$db = JDatabase::getInstance(dbOptions());
	
	// So, 
	// for each project_id in valuesArray:
	//   if there is no mapping in the psm table with a null end_date, end_photo_id
	//     add one
	//   (if there's one already then nothing to do)
	// for each mapping with no end_date or end_photo_id where the project_id is not on the list:
	//   update it to have end_ fields as this site has now left this project.
	
	// Make a list of all the existing mappings (projects) with no end_ fields that already exist for this site
	$query = $db->getQuery(true);
	$query->select("project_id ")->from("ProjectSiteMap");
	$query->where("site_id = " . $code );
	$query->where("end_time is NULL" );
	$db->setQuery($query); 
    $currentProjects = $db->loadColumn();
	
	// Get the project_ids that are on the new list but there is not already a mapping in place for.
	// ie any new projects for this site.
	$newProjects = array_diff ( $valuesArray, $currentProjects );
	
	//error_log ( "newProjects count: " . count($newProjects)  );
	
	// And get all the ones which there is a current mapping for but are not on the new list.  ie projects this
	// site is leaving.
	$oldProjects = array_diff ( $currentProjects, $valuesArray );
	//error_log ( "oldProjects count: " . count($oldProjects)  );
	
	// Use a transaction as we want to do various updates, and need 
	// all these operations to succeed or fail.
	try
	{
		$db->transactionStart();
	
		$table = "ProjectSiteMap";

		// Prepare the current time (start_time) and start_photo_id
		$query = $db->getQuery(true);
		$query->select("now(), max(photo_id), max(photo_id) + 1")->from("Photo");
		$db->setQuery($query); 
		$currDetails = $db->loadRow();
		
		
		//error_log ( "currDetails: " . $currDetails[0] . ", " . $currDetails[1] );
	
		
		foreach ( $newProjects as $thisValue ) {
			//error_log ( "adding project: " . $thisValue );
			$query2 = $db->getQuery(true);
			$query2->insert($db->quoteName($table));
			$query2->columns($db->quoteName(array('project_id', 'site_id', 'start_time', 'start_photo_id')));
			// Plus 1 because the current max photo id is not included in the time this site is a member of this project
			// but the next photo_id will be.
			$query2->values("" . $thisValue . ", " . $code . ", '" . $currDetails[0] . "', " . $currDetails[2]);
			$db->setQuery($query2);
			$result = $db->execute();
		}
		
		foreach ( $oldProjects as $thisValue ) {
			//error_log ( "setting end values for project: " . $thisValue );
			$query3 = $db->getQuery(true);
			
			$fields = array(
				$db->quoteName('end_time') . ' = ' . $db->quote($currDetails[0]),
				$db->quoteName('end_photo_id') . ' = ' . $currDetails[1]
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('site_id') . ' = ' . $code, 
				$db->quoteName('project_id') . ' = ' . $thisValue
			);

			$query3->update($db->quoteName($table))->set($fields)->where($conditions);
			
			$db->setQuery($query3);
			$result = $db->execute();

		}
		
		$db->transactionCommit();
		
		$success = true;
	}
	catch (Exception $e)
	{
		error_log("ProjectSiteMap update failed due to " . $e);
		
		// catch any database errors.
		$db->transactionRollback();
	}
	
    return $success;
}

function codes_getOptionName ( $option_id ) {
	return codes_getName ( $option_id, "option" );
}
  
function codes_getOptionTranslation ( $option_id ) {
	
	$langObject = JFactory::getLanguage();
	$lang = $langObject->getTag();
		
	if (!isLanguageSupported($lang)) $lang = "en-GB";
	
	if ( $lang == "en-GB" ) {
		return codes_getName ( $option_id, "option" );
	}
	else {
		// Remove spaces and use first 12 letters
		$structure = "opt-" . $lang;
		//error_log ( "codes_getOptionTranslation, structure = " . $structure );
		
		// Get the translated name
		//error_log ( "codes_getOptionTranslation, calling getName with id " . $option_id );
		$name_tl = codes_getName ( $option_id, $structure );
		
		// Default to the english name is there's no translation
		return $name_tl ? $name_tl : codes_getName ( $option_id, "option" );
	}
}
  
function update_siteLatLong ( $site_id, $lat, $lon ) {
	if ( !$lat or !$lon ) {
		return;
	}
	// check user id one of admin team
	// could do this better but short of time
	$person_id = userID();
	if ($person_id and ($person_id == 972 or $person_id == 900 or $person_id == 659) ) {
	
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
				
		$fields = array(
			$db->quoteName('latitude') . ' = ' . $lat,
			$db->quoteName('longitude') . ' = ' . $lon
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('site_id') . ' = ' . $site_id
		);

		$query->update("Site")->set($fields)->where($conditions);
		
		$db->setQuery($query);
		$result = $db->execute();
	
	}
	else {
		error_log ("No permission to update lat long" );
	}
			
}

function biodiv_label($type, $what=""){
  switch($type){
  case "add":
    return "<i class='fa fa-plus'></i> Add $what";
    break;

  case "edit":
    return "<i class='fa fa-edit'></i> Edit $what";
    break;

  case "delete":
    return "<i class='fa fa-remove'></i> Delete $what";
    break;

  case "upload":
    return "<i class='fa fa-upload'></i> Upload $what";
    break;

  }
}

// For multilingual
function biodiv_label_icons($type, $str, $what=""){
  switch($type){
  case "add":
    return "<i class='fa fa-plus'></i> $str $what";
    break;

  case "edit":
    return "<i class='fa fa-edit'></i> $str $what";
    break;

  case "delete":
    return "<i class='fa fa-remove'></i> $str $what";
    break;

  case "upload":
    return "<i class='fa fa-upload'></i> $str $what";
    break;
  case "nothing":
	return "$str <span class='fa fa-ban'/>";
	break;
  case "human":
    return "$str <span class='fa fa-male'/>";
	break;
  default:
    return $str;
	break;
  }
}

function canEdit($id, $struc, $allow = 0){
  static $allows;
  
  if($struc == "sequence"){
  	    return true;
  }
  
  if($allow){
   $allows[$struc][$id] = 1;
  }
  if($allows[$struc][$id]){
    return true;
  }
  
  // Moving from here to top - allows not used for sequencing...
  //if($struc == "sequence"){
  //	    return true;
  //}
  // End move
  
  $details = codes_getDetails($id, $struc);
  if(!userID()){
    return false;
  }
  switch($struc){
  case 'upload':
  case 'photo':
  case 'site':
  case 'animal':
    return $details['person_id'] == userID();
  break;
    
  case 'sequence':
    return canEdit($details['upload_id'], 'upload');
    break;

  default:
    return false;
  }
}


function canCreate($struc, $fields){
	 if($struc=="sequence"){
	 return true;
}
  if(!userID()){
    return false;
  }
  switch($struc){

  case 'photo':
  case 'site':
  case 'sitedata':
  case 'upload':
  case 'photo':
  case 'classification':
  case 'animal':
    return $fields->person_id == userID();   
    break;

  case 'sequence':
    //    return canEdit($fields->upload_id, 'upload');
    return true;
    break;

  default:
    return false;
  }
}

function canView ($struc, $fields) {
	switch($struc){
		case 'project':
			// If it's a public or protected project can view it.  If not must have access.  Only private projects cannot be viewed.
			$db = JDatabase::getInstance(dbOptions());
			$query = $db->getQuery(true);
			$query->select("project_id, access_level from Project");
			$query->where("access_level < 3" );
			$db->setQuery($query);
			$projects = $db->loadAssocList('project_id');
			if ( in_array($fields->project_id, array_keys($projects)) ) return true;
			else return false;
			break;
		default:
			return false;
	}
}

function canClassify ($struc, $fields) {
	switch($struc){
		case 'project':
			$person_id = userID();
			
			if (!$person_id ) {
				return false;
			}
			
			// If it's a public or protected project can view it.  If not must have access.
			// Check this project against "myProjects".
			$projects = mySpottingProjects();
			
			if ( in_array($fields->project_id, array_keys($projects)) ) return true;
			else return false;
			break;
		default:
			return false;
	}
}

function canRunScripts(){
  
  /* For now return true - need IP login to work for this...
  $user = JFactory::getUser();
  $groups = $user->getAuthorisedGroups();
  //print_r($groups);
  $groupnames = array();
  foreach ( $groups as $id ) {
	  array_push ( $groupnames, JAccess::getGroupTitle($id) );
  }
  //print_r ( $groupnames );
  if(in_array("Backend", $groupnames)){
    return true;
  }
  else{
    return false;
  }
  */
  return true;
}

function uploadRoot(){
//  return JPATH_COMPONENT . "/uploads";
    return "/var/www/html/biodivimages";
}

// get dir of where images from a given site are stored
function siteDir($site_id){
  $site_id = (int)$site_id;
  $site_id or die("Cannot find siteDir");

  $details = codes_getDetails($site_id, "site");
  $person_id = (int)$details['person_id'];
  $person_id or die("Cannot find person for siteDir");
  
  return uploadRoot()."/person_${person_id}/site_${site_id}";
}

function siteURL($site_id){
  $site_id = (int)$site_id;
  $site_id or die("Cannot find siteDir");

  $details = codes_getDetails($site_id, "site");
  $person_id = (int)$details['person_id'];
  $person_id or die("Cannot find person for siteDir");
  
  return JURI::root()."/biodivimages/person_${person_id}/site_${site_id}";
}

function isVideo($photo_id) {
	$details = codes_getDetails($photo_id, 'photo');
	$filename = $details['filename'];
	if ( strpos(strtolower($filename), '.mp4') !== false ) {
		return true;
	}
	return false;
	
}

function helpVideoURL() {
	return JURI::root()."/images/video/HowTo.mp4";
}

function photoURL($photo_id){
  $details = codes_getDetails($photo_id, 'photo');
  if ( $details['s3_status'] == 1 ) {
	  // File has been transferred to s3 so get AWS S3 url
	  return s3URL($details);
  }
  else {
	  // debug
	  // echo siteURL($details['site_id']) . "/". $details['filename'];
	  // debug end
	  return siteURL($details['site_id']) . "/". $details['filename'];
  }
}

function projectImageURL($proj_id) {
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("dirname, image_file")->from("Project");
  $query->where("project_id = " . (int)$proj_id); 
  $db->setQuery($query);
  $row = $db->loadAssoc();

  return JURI::root().$row['dirname']."/".$row['image_file'];
}

function classifications($restrictions){
  if(!is_array($restrictions)){
    return array();
  }
  $allowed = array('photo_id', 'person_id', 'species', 'gender', 'age', 'number');
  $restrictions = array_intersect_key($restrictions, array_flip($allowed));
  if(count($restrictions)<1){
    return array();
  }

  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("animal_id, species, gender, age, number")->from("Animal");
  foreach($restrictions as $field => $val){
    $query->where($db->quoteName($field) . " = " . (int)$val);
  }
  // Add not to include liked images - these are stored as species = 97!
  $query->where("species != 97");
  
  $db->setQuery($query);
  return $db->loadObjectList('animal_id');

}

function myClassifications($photo_id){
  $person_id = (int)userID();
  return classifications(array('photo_id' => $photo_id,
			       'person_id' => $person_id));
}

function haveClassified($photo_id){
  return count(myClassifications($photo_id));
}

function likes($restrictions){
  if(!is_array($restrictions)){
    return array();
  }
  $allowed = array('photo_id', 'person_id', 'species', 'gender', 'age', 'number');
  $restrictions = array_intersect_key($restrictions, array_flip($allowed));
  if(count($restrictions)<1){
    return array();
  }

  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("animal_id, species, gender, age, number")->from("Animal");
  foreach($restrictions as $field => $val){
    $query->where($db->quoteName($field) . " = " . (int)$val);
  }
  // Add to include only liked images - these are stored as species = 97!
  $query->where("species = 97");
  
  $db->setQuery($query);
  return $db->loadObjectList('animal_id');

}

function myLikes($photo_id){
  $person_id = (int)userID();
  return likes(array('photo_id' => $photo_id,
			       'person_id' => $person_id));
}

function getLikes($max_num){
	
	$person_id = (int)userID();
	
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("DISTINCT photo_id ")->from("Animal");
	$query->where("person_id = " . $person_id . " and species = 97");
	$query->order("rand()");
    $db->setQuery($query, 0, $max_num); // LIMIT $max_num
    $mylikes = $db->loadColumn();

	return $mylikes;
	
}

function deleteNothingClassification ( $photo_id, $animal_ids = 0 ) {
	
	// Default to 0 means no matches if $animals_id set or meaningless if general delete done.
	$delete_id = 0;
	// If animal_ids, remove any existing classification of 'Nothing' which match the ids on the list
	if ( $animal_ids ) {		
		$animals_csv = implode (',', explode ( "_", $animal_ids ) );
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Do any Nothings match?
		$query = $db->getQuery(true);
		$query->select($db->quoteName("animal_id"))
			->from("Animal")
			->where($db->quoteName("photo_id") . " = '$photo_id'" )
			->where($db->quoteName("person_id") . " = " . (int)userID())
			->where($db->quoteName("animal_id") . "in (".$animals_csv.")")
			->where($db->quoteName("species") . " = 86");
		$db->setQuery($query);
		$delete_id = $db->loadResult();
		
		if ( $delete_id ) {
			$query = $db->getQuery(true);
			$query->delete("Animal")
				->where($db->quoteName("photo_id") . " = '$photo_id'" )
				->where($db->quoteName("person_id") . " = " . (int)userID())
				->where($db->quoteName("animal_id") . " = '$delete_id'" )
				->where($db->quoteName("species") . " = 86");
			$db->setQuery($query);
			$success = $db->execute();
		}
	}		
	// If no animal_ids, remove any existing classification of 'Nothing'
	else {
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->delete("Animal")
			->where($db->quoteName("photo_id") . " = '$photo_id'" )
			->where($db->quoteName("person_id") . " = " . (int)userID())
			->where($db->quoteName("species") . " = 86");
		$db->setQuery($query);
		$success = $db->execute();
	}
	
	return $delete_id;
}

function removeAnimalId ( $animal_id ) {
	$app = JFactory::getApplication();
	$animal_ids = $app->getUserState('com_biodiv.animal_ids', 0);
	$all_animal_ids = $app->getUserState('com_biodiv.all_animal_ids', 0);
	
	if ( $animal_ids ) {
		$animals = explode("_", $animal_ids);
		
		if (($key = array_search($animal_id , $animals)) !== false) {
			unset($animals[$key]);
			$animals = array_values ( $animals );
		}
    
		$app->setUserState('com_biodiv.animal_ids', implode ("_" , $animals));
	}
	if ( $all_animal_ids ) {
		$all_animals = explode("_", $all_animal_ids);
		
		if (($key = array_search($animal_id , $all_animals)) !== false) {
			unset($all_animals[$key]);
			$all_animals = array_values ( $all_animals );
		}
    
		$app->setUserState('com_biodiv.all_animal_ids', implode ("_" , $all_animals));
	}
}

// Helper function to add child projects.
/*
function findSubProjects ($projects, $pairs) {

  //echo "findSubProjects called<br>";
  //print "<br/>Got " . count($projects) . " projects.<br/>";
  
  // Start by adding the projects we know about to a new array
  $allProjects = array();
  foreach ($projects as $proj_id=>$proj_prettyname){
	  $allProjects += [ $proj_id=>$proj_prettyname ];
  }
  
  foreach ($allProjects as $proj_id=>$proj_prettyname){	
	//print "proj_id, proj_prettyname = " . $proj_id . ", " . $proj_prettyname . "<br>";
	$project_id = null;
	$parent_project_id = null;
	$project_prettyname = null;
	$childAdded = false;
	foreach ($pairs as $allpairsline){
	  extract($allpairsline);
	  //echo "all pairs: parent_project_id, project_id = " . $parent_project_id . ", " . $project_id . "<br>";
      if ( $parent_project_id == $proj_id ) {
	    if ( !array_key_exists($project_id, $allProjects) ) {
		  //print "<br/>Not there yet so adding " . $project_id . "=>" . $project_prettyname . " to allProjects<br/>\n";
		  // need to watch this - new project must be added at the end...
		  $allProjects += [ $project_id => $project_prettyname ];
		  $childAdded = true;
	    }
	  }
	}
  }
  return $allProjects;
}*/

function addSubProjects (&$projects, &$pairs) {

  //echo "addSubProjects called<br>";
  //print "<br/>Got " . count($projects) . " projects.<br/>";
  foreach ($projects as $proj_id=>$proj_prettyname){
	
	//echo "proj_id, proj_prettyname = " . $proj_id . ", " . $proj_prettyname . "<br>";
	$project_id = null;
	$parent_project_id = null;
	$project_prettyname = null;
	$addedNew = False;
	foreach ($pairs as $allpairsline){
	  extract($allpairsline);
	  //echo "all pairs: parent_project_id, project_id = " . $parent_project_id . ", " . $project_id . "<br>";
      if ( $parent_project_id == $proj_id and $project_id != null ) {
	    if ( !array_key_exists($project_id, $projects) ) {
		  //print "<br/>Not there yet so adding " . $project_id . "=>" . $project_prettyname . " to projects<br/>\n";
		  // need to watch this - new project must be added at the end...
		  $projects += [ $project_id => $project_prettyname ];
			
		  // Added new subproject so flag we will need to call recursively
		  $addedNew = True;
	    }
	  }
	} // foreach $pairs
	if ($addedNew) {
	  //echo "Calling addSubProjects recursively as added more.<br>";
	  addSubProjects ( $projects, $pairs );
    }
  }
}

/*
function myProjects(){
  //print "<br/>myProjects called<br/>";
  // what user am I?
  $person_id = (int)userID();
  
  // first select all project/parent pairs into memory
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project");
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("PUM.person_id = " . $person_id . " or P.access_level = 0");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $myprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br/>Got " . count($myprojects) . " top level projects user is registered for<br/>\n";

  // add to project list by working through $allpairs to find the children, repeatedly
  //print "<br/>Calling addSubProjects<br/>";
  addSubProjects( $myprojects, $allpairs );
  
  //print "<br/>Got " . count($myprojects) . " all projects user has access to<br/>They are:<br>";
  //print implode(",", $myprojects);
  
  return $myprojects;
}
*/

function myTrappingProjects () {
  //print "<br/>myTrappingProjects called<br/>";
  // what user am I?
  $person_id = (int)userID();
  
  // first select all project/parent pairs into memory but don't include the private ones
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
	->where("access_level < 3")
	->where("parent_project_id is not NULL");
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
	->where("parent_project_id is not NULL");
  $db->setQuery($query);
  $allpairsincpriv = $db->loadAssocList();
  
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  
  /*
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("(P.access_level = 0) or (PUM.person_id = " . $person_id . ") ");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $myprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br/>Got " . count($myprojects) . " top level projects user is registered for<br/>\n";

  // add to project list by working through $allpairs to find the children, repeatedly
  //print "<br/>Calling addSubProjects<br/>";
  addSubProjects( $myprojects, $allpairs );
  */
  
  // First, public projects
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level = 0");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $publicprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br>Public and hybrid projects<br>";
  //print_r ( $publicandhybridprojects );
  
  // Next all private projects I am a member of
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level = 3 and PUM.person_id = " . $person_id);
  $query->order("P.project_id" );
  $db->setQuery($query);
  $privateprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  // add to private project list by working through $allpairsincpriv to find the children, repeatedly
  //print "<br/>Calling addSubProjects<br/>";
  addSubProjects( $privateprojects, $allpairsincpriv );
  // Include all the child projects
  //$privateprojects = findSubProjects ( $privateprojects, $allpairsincpriv );

  
  // Next get any hybrid, restricted or private projects I am a member of
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level in (1,2) and PUM.person_id = " . $person_id );
  $query->order("P.project_id" );
  $db->setQuery($query);
  $myprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br/>Got " . count($myprojects) . " top level projects user is registered for<br/>\n";

  // add to restricted/hybrid project list by working through $allpairs to find the children, repeatedly
  //print "<br/>Calling addSubProjects<br/>";
  addSubProjects( $myprojects, $allpairs );
  // Include all the child projects
  //$myprojects = findSubProjects ( $myprojects, $allpairs );

  
  //print "<br>Pre mergerd restricted and hybrid projects: <br>";
  //print_r ( $myprojects );
  
  // Add in the public and hybrid projects
  $myprojects = $myprojects + $publicprojects + $privateprojects;
  
  //print "<br>Mergerd projects: <br>";
  //print_r ( $myprojects );
  
  //print "<br/>Got " . count($myprojects) . " all projects user has access to<br/>They are:<br>";
  //print implode(",", $myprojects);
  
  return $myprojects;
}

function mySpottingProjects ($reduce = false) {
	$person_id = (int)userID();
  
  // first select all project/parent pairs into memory, exclude private ones.
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
	->where("access_level < 3")
	->where("parent_project_id is not NULL");
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
	->where("parent_project_id is not NULL");
  $db->setQuery($query);
  $allpairsincpriv = $db->loadAssocList();
  
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  
  // First, public and hybrid projects
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level in (0,1)");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $publicandhybridprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br>Public and hybrid projects<br>";
  //print_r ( $publicandhybridprojects );
  
  // Next all private projects I am a member of
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level = 3 and PUM.person_id = " . $person_id);
  $query->order("P.project_id" );
  $db->setQuery($query);
  $privateprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  // Include all the child projects
  //$privateprojects = findSubProjects ( $privateprojects, $allpairsincpriv );
  addSubProjects($privateprojects, $allpairsincpriv );
	
  
  // Next get any restricted projects I am a member of, and get subprojects of those
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level = 2 and PUM.person_id = " . $person_id );
  $query->order("P.project_id" );
  $db->setQuery($query);
  $restrictedprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br/>Got " . count($myprojects) . " top level projects user is registered for<br/>\n";

  // Get all the child projects
  //$restrictedprojects = findSubProjects ( $restrictedprojects, $allpairs );
  addSubProjects($restrictedprojects, $allpairs );

  
  //print "<br>Pre mergerd restricted and hybrid projects: <br>";
  //print_r ( $myprojects );
  
  // Add in the public, hybrid and private projects
  $myprojects = $restrictedprojects + $publicandhybridprojects + $privateprojects;
  
  //print "<br>Mergerd projects: <br>";
  //print_r ( $myprojects );
  
  
  if ( $reduce ) {
	  // Only want the private projects plus the projects that don't have a parent already in the list.
	
	$query = $db->getQuery(true);
	$query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
	$query->where("P.access_level = 3 or (P.access_level < 3 and P.parent_project_id is null) or (P.access_level < 3 and P.parent_project_id in (select project_id from Project where parent_project_id is null and access_level > 2))");
	$db->setQuery($query);
	$allreduced = $db->loadAssocList('proj_id', 'proj_prettyname');
	$myprojects = array_intersect_key ( $myprojects, $allreduced );
  }
  
  //print "<br/>Got " . count($myprojects) . " all projects user has access to<br/>They are:<br>";
  //print implode(",", $myprojects);
  
  asort($myprojects);
  return $myprojects;
  
  
}
/*
// If $reduce is True, return only the private projects and the top level listed ones.
function oldSpottingProjects ( $reduce = false ) {
  //print "<br/>myTrappingProjects called<br/>";
  // what user am I?
  $person_id = (int)userID();
  
  // first select all project/parent pairs into memory, exclude private ones.
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
	->where("access_level < 3");
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project");
  $db->setQuery($query);
  $allpairsincpriv = $db->loadAssocList();
  
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  
  // First, public and hybrid projects
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level in (0,1)");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $publicandhybridprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br>Public and hybrid projects<br>";
  //print_r ( $publicandhybridprojects );
  
  // Next all private projects I am a member of
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level = 3 and PUM.person_id = " . $person_id);
  $query->order("P.project_id" );
  $db->setQuery($query);
  $privateprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  // add to private project list by working through $allpairs to find the children, repeatedly
  //print "<br/>Calling addSubProjects<br/>";
  addSubProjects( $privateprojects, $allpairsincpriv );
  
  // Next get any restricted projects I am a member of, and get subprojects of those
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
  $query->leftJoin("ProjectUserMap PUM ON P.project_id = PUM.project_id");
  $query->where("P.access_level = 2 and PUM.person_id = " . $person_id );
  $query->order("P.project_id" );
  $db->setQuery($query);
  $myprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  //print "<br/>Got " . count($myprojects) . " top level projects user is registered for<br/>\n";

  // add to restricted project list by working through $allpairs to find the children, repeatedly
  //print "<br/>Calling addSubProjects<br/>";
  addSubProjects( $myprojects, $allpairs );
  
  //print "<br>Pre mergerd restricted and hybrid projects: <br>";
  //print_r ( $myprojects );
  
  // Add in the public, hybrid and private projects
  $myprojects = $myprojects + $publicandhybridprojects + $privateprojects;
  
  //print "<br>Mergerd projects: <br>";
  //print_r ( $myprojects );
  
  
  if ( $reduce ) {
	  // Only want the private projects plus the projects that don't have a parent already in the list.
	
	$query = $db->getQuery(true);
	$query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname")->from("Project P");
	//$query->where("P.access_level = 3 or (P.access_level < 2 and P.parent_project_id is null) or (P.access_level < 2 and P.parent_project_id in (select project_id from Project where parent_project_id is null and access_level > 2))");
	$query->where("P.access_level = 3 or (P.access_level < 3 and P.parent_project_id is null) or (P.access_level < 3 and P.parent_project_id in (select project_id from Project where parent_project_id is null and access_level > 2))");
	$db->setQuery($query);
	$allreduced = $db->loadAssocList('proj_id', 'proj_prettyname');
	$myprojects = array_intersect_key ( $myprojects, $allreduced );
  }
  
  //print "<br/>Got " . count($myprojects) . " all projects user has access to<br/>They are:<br>";
  //print implode(",", $myprojects);
  
  asort($myprojects);
  return $myprojects;
}
*/

// Get all options for a single project by project id or just a particular option type if set
function getSingleProjectOptions ( $project_id, $option_type ) {
	
	$projectoptions = array();
  
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("DISTINCT P.project_id, P.project_prettyname, O.struc, O.option_name")->from("Project P");
	$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
	$query->innerJoin("Options O on PO.option_id = O.option_id" );
	$where_str = "P.project_id = ".$project_id;
	if ( $option_type ) $where_str .= " AND O.struc = '" . $option_type . "'";
	$query->where($where_str);
	$query->order("P.project_id" );
	$db->setQuery($query);
	$projectoptions = $db->loadAssocList();
  
  
	//print "<br/>Got " . count($projectoptions) . " project options for project " . $project_id . "<br/>They are:<br>";
	//print_r($projectoptions);
	
	return $projectoptions;
}

// If project_id is null return for all mySpottingProjects.  Option_type is the struc in the Options table.  If null return all.
function getProjectOptions( $project_id, $option_type, $use_exclusions ){
  // Call myprojects to get the project list, then get details for each.
  $myprojects = null;
  if ( $project_id ) {
	  $myprojects = getSubProjectsById( $project_id );
  }
  else {
	  $myprojects = mySpottingProjects();
  }
  
  //print "<br>getProjectOptions, before exclusions got projects: <br>" ;
  //print_r ( $myprojects );
  
  // if we need to exclude some projects then remove them from the project list here
  if ( $use_exclusions ) {
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("DISTINCT P.project_id, P.project_prettyname")->from("Project P");
	$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
	$query->innerJoin("Options O on PO.option_id = O.option_id" );
	$query->where("O.struc = 'exclude'");
	$query->order("P.project_id" );
	$db->setQuery($query);
	$excludeprojects = $db->loadAssocList("project_id");
	
	$myprojects = array_diff_key ( $myprojects, $excludeprojects );
	
	//print "<br>getProjectOptions, after exclusions got projects: <br>" ;
    //print_r ( $myprojects );
  
  }
  
  $projectdetails = array();
  
  if ( count($myprojects) > 0 ) {
  
	$id_string = implode(",", array_keys($myprojects));
  
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("DISTINCT P.project_id, P.project_prettyname, O.struc, O.option_name")->from("Project P");
	$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
	$query->innerJoin("Options O on PO.option_id = O.option_id" );
	$where_str = "P.project_id in (".$id_string.")";
	if ( $option_type ) $where_str .= " AND O.struc = '" . $option_type . "'";
	$query->where($where_str);
	$query->order("P.project_id" );
	$db->setQuery($query);
	$projectdetails = $db->loadAssocList("project_id");
	
	//NOTE this will only return a single row per project_id!! Possible bug.
  
  
	//print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
	//print_r($projectdetails);
  }
  
  return $projectdetails;
}

function myProjectDetails( $project_id ){
  // Call myprojects to get the project list, then get details for each.
  $myprojects = null;
  if ( $project_id ) {
	  $myprojects = getSubProjectsById( $project_id );
  }
  else {
	  $myprojects = myProjects();
  }
  
  $id_string = implode(",", array_keys($myprojects));
  
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.dirname, P.image_file, P.project_text")->from("Project P");
  $query->where("P.project_id in (".$id_string.")");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $projectdetails = $db->loadAssocList("project_id");
  
  
  //print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
  //print implode(",", $projectdetails);
  
  return $projectdetails;
}

// Return the details for a single project id as an object
function projectDetails ( $project_id ) {
  
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.dirname, P.image_file, P.project_text")->from("Project P");
  $query->where("P.project_id = " . $project_id );
  $query->where("P.access_level < 3" );
  $db->setQuery($query);
  $projectdetails = $db->loadObject();
  
  //print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
  //print implode(",", $projectdetails);
  
  return $projectdetails;
}

// Calculate the number of sequences uploaded and the number of sequences with at least one classification for the project given (and children)
// or for all projects.  Store the results in the Statistics table.  Overwrite a row if it already exists.
function calculateStats ( $project_id = null, $end_date = null ) {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$projects = null;
	
	if ($project_id ) {
		$projects = getSubProjectsById( $project_id );	
	}
	else {
		$query = $db->getQuery(true);
		$query->select("DISTINCT P.project_id, P.project_prettyname from Project P");
		$db->setQuery($query);
		$projects = $db->loadAssocList('project_id');
	}
	$project_ids = array_keys($projects);
	
	$endDatePlus1 = strtotime("first day of next month" );
	$endDate = date('Ymd', strtotime("last day of this month"));
	if ( $end_date ) {
		$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
		$endDate = date('Ymd', strtotime($end_date));
	}
	
	$dateToUse = date('Ymd', $endDatePlus1);
	
	// Work through project by project as we don't want to include the children here.
	foreach ( $project_ids as $proj_id ) {
		
		print "project_id = " . $proj_id . ", dateToUse = " . $dateToUse . "<br>";
		
		$query = $db->getQuery(true);
		$query->select("distinct sequence_id from Photo P");
		$query->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query->where("P.sequence_id > 0");
		$query->where("P.uploaded < " . $dateToUse );
		$query->where("PSM.project_id = " . $proj_id );
		//$query->where("(P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL) or (P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id)"  );
		$query->where("P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL"  );
		
		$query2 = $db->getQuery(true);
		$query2->select("distinct sequence_id from Photo P");
		$query2->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query2->where("P.sequence_id > 0");
		$query2->where("P.uploaded < " . $dateToUse );
		$query2->where("PSM.project_id = " . $proj_id );
		$query2->where("P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id"  );
		
		$db->setQuery($query2);
		
		$q3 = $db->getQuery(true)
             ->select('count(distinct a.sequence_id)')
             ->from('(' . $query->union($query2) . ') a');
			 
		$db->setQuery($q3);

		$numLoaded = $db->loadResult();
	
		$query = $db->getQuery(true);
		$query->select("distinct sequence_id from Photo P");
		$query->innerJoin("Animal A on P.photo_id = A.photo_id");
		$query->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query->where("A.species != 97");
		$query->where("A.timestamp < " . $dateToUse );
		$query->where("PSM.project_id = " . $proj_id );
		$query->where("P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id"  );
		$db->setQuery($query);
		
		$query2 = $db->getQuery(true);
		$query2->select("distinct sequence_id from Photo P");
		$query2->innerJoin("Animal A on P.photo_id = A.photo_id");
		$query2->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query2->where("A.species != 97");
		$query2->where("A.timestamp < " . $dateToUse );
		$query2->where("PSM.project_id = " . $proj_id );
		$query2->where("P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL"  );
		$db->setQuery($query2);
		
		$q3 = $db->getQuery(true)
             ->select('count(distinct a.sequence_id)')
             ->from('(' . $query->union($query2) . ') a');
		
		$db->setQuery($q3);

		$numClassified = $db->loadResult();
		
		print ("For project $proj_id, numLoaded = " . $numLoaded . ", numClassified = " . $numClassified );
	
	/*
		$query = $db->getQuery(true);
		$query->select("count(distinct photo_id) from ProjectSequences");
		$query->where("project_id = " . $proj_id );
		$query->where("uploaded < " . $dateToUse );
		$db->setQuery($query);
		$numLoaded = $db->loadResult();
	
		$query = $db->getQuery(true);
		$query->select("count(distinct start_photo_id) from ProjectAnimals");
		$query->where("project_id = " . $proj_id );
		$query->where("classify_time < " . $dateToUse );
		$db->setQuery($query);
		$numClassified = $db->loadResult();
	*/
		// Now update or insert - this would all be better with a stored procedure
		$query = $db->getQuery(true);
		$query->select("project_id, end_date from Statistics")
			->where("project_id = " . $proj_id )
			->where("end_date = '" . $endDate . "'" );
		$db->setQuery($query);
		$resultRow = $db->loadRowList();
		
		if ( count($resultRow) > 0 ) {
			print "Existing entry for project $proj_id, end_date $endDate - updating<br/>";
			
			$query = $db->getQuery(true);
			$query->update("Statistics");
			$query->set("num_uploaded = " . $numLoaded . ", num_classified = " . $numClassified );
			$query->where("project_id = " . $proj_id );
			$query->where("end_date = '" . $endDate . "'" );
			$db->setQuery($query);
			$result = $db->execute();
		}
		else {
			print "New entry in statistics for project $proj_id, end_date $endDate<br/>";
			
			$query = $db->getQuery(true);
			$query->insert("Statistics");
			$query->columns($db->quoteName(array('project_id', 'end_date', 'num_uploaded', 'num_classified')));
			$query->values("" . $proj_id . ", '" . $endDate . "', " . $numLoaded . ", " . $numClassified );
			$db->setQuery($query);
			$result = $db->execute();
		}
		
	}
}

// Recalculate statistics for all projects
// Use with care this will overwrite existing figures.
function calculateStatsHistory ( $project_id = null, $num_months = null) {
	
	$endDate = strtotime("last day of this month");
	$endDatePlus1 = strtotime("first day of next month" );

	$datesArray = array();
	
	// Default to 3 years of data.
	$numDisplayedMonths = 39;
	if ( $num_months ) {
		$numDisplayedMonths = $num_months + 3;
	}
  
	for ( $i=$numDisplayedMonths; $i>=0; $i-- ) {
		$minusMonths = "-" . $i . " months";
		$firstOfMonth = strtotime($minusMonths, $endDatePlus1);
		calculateStats($project_id, date('Ymd', strtotime("-1 day", $firstOfMonth)) );
	}
}

function calculateStatsTotals ( $end_date = null ) {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$endDatePlus1 = strtotime("first day of next month" );
	$endDate = date('Ymd', strtotime("last day of this month"));
	if ( $end_date ) {
		$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
		$endDate = date('Ymd', strtotime($end_date));
	}
	
	$dateToUse = date('Ymd', $endDatePlus1);
	
	$query = $db->getQuery(true);
	$query->select("count(distinct sequence_id) from Photo");
	$query->where("sequence_id > 0");
	$query->where("uploaded < " . $dateToUse );
	$db->setQuery($query);
	$numLoaded = $db->loadResult();
	
	$query = $db->getQuery(true);
	$query->select("count(distinct sequence_id) from Photo P");
	$query->innerJoin("Animal A on P.photo_id = A.photo_id");
	$query->where("A.species != 97");
	$query->where("P.sequence_id > 0");
	$query->where("A.timestamp < " . $dateToUse );
	$db->setQuery($query);
	$numClassified = $db->loadResult();
	
	// Now update or insert - this would all be better with a stored procedure
	$query = $db->getQuery(true);
	$query->select("project_id, end_date from Statistics")
		->where("project_id = 0" )
		->where("end_date = '" . $endDate . "'" );
	$db->setQuery($query);
	$resultRow = $db->loadRowList();
		
	if ( count($resultRow) > 0 ) {
		print "Existing entry for project 0 (ie total) , end_date $endDate - updating<br/>";
			
		$query = $db->getQuery(true);
		$query->update("Statistics");
		$query->set("num_uploaded = " . $numLoaded . ", num_classified = " . $numClassified );
		$query->where("project_id = 0" );
		$query->where("end_date = '" . $endDate . "'" );
		$db->setQuery($query);
		$result = $db->execute();
	}
	else {
		print "New entry in statistics for project 0 (ie total) , end_date $endDate<br/>";
			
		$query = $db->getQuery(true);
		$query->insert("Statistics");
		$query->columns($db->quoteName(array('project_id', 'end_date', 'num_uploaded', 'num_classified')));
		$query->values("0, '" . $endDate . "', " . $numLoaded . ", " . $numClassified );
		$db->setQuery($query);
		$result = $db->execute();
	}

}

function calculateLeagueTable () {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true);
    $query->select("A.person_id as person_id, count(distinct P.sequence_id) as num_sequences")
		->from("Animal A use index (classification_id)")
		->innerJoin("Photo P on P.photo_id = A.photo_id")
		->where("A.species != 97")
		->group("A.person_id ")
		->order("count(distinct P.sequence_id) desc");
    $db->setQuery($query);
	
    $leagueTable = $db->loadAssocList('person_id','num_sequences');
	
	// Work through person by person.
	foreach ( $leagueTable as $person_id=>$num_sequences ) {
		
		print "person_id = " . $person_id . ", num_sequences = " . $num_sequences . "<br>";
		
		// Now update or insert - this would all be better with a stored procedure
		$query = $db->getQuery(true);
		$query->select("person_id, num_classified from LeagueTable")
			->where("person_id = " . $person_id );
		$db->setQuery($query);
		$resultRow = $db->loadRowList();
		
		if ( count($resultRow) > 0 ) {
			print "Existing entry for person_id $person_id - updating<br/>";
			
			$query = $db->getQuery(true);
			$query->update("LeagueTable");
			$query->set("num_classified = " . $num_sequences . ", timestamp = CURRENT_TIMESTAMP" );
			$query->where("person_id = " . $person_id );
			$db->setQuery($query);
			$result = $db->execute();
		}
		else {
			print "New entry in LeagueTable for person_id $person_id<br/>";
			
			$query = $db->getQuery(true);
			$query->insert("LeagueTable");
			$query->columns($db->quoteName(array('person_id', 'num_classified')));
			$query->values("" . $person_id . ", " . $num_sequences );
			$db->setQuery($query);
			$result = $db->execute();
		}
	}
}

// Update the AnimalStatistics table with count of classifications by project and species
function calculateAnimalStatistics () {

  $db = JDatabase::getInstance(dbOptions());
  
  print "<br>Truncating table";
  // Truncate the table
  
  $truncate = $db->getQuery(true)
                   ->truncateTable('AnimalStatistics');

  $db->truncateTable('AnimalStatistics');
  
  print "<br>Inserting new data";
  
  $query = $db->getQuery(true);
  $query2 = $db->getQuery(true);
  // count every classification but exclude Likes
  $query->select("PSM.project_id as project_id, A.species as option_id, O.option_name as species, count(A.animal_id) as num_animals from Animal A")
        ->innerJoin("Photo P on P.photo_id = A.photo_id and P.sequence_num = 1")
		->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
        ->innerJoin("Options O on A.species = O.option_id")
		->where("A.species!= 97")
		->group("PSM.project_id, A.species");
		
  $query2->select("PSM.project_id as project_id, A.species as option_id, O.option_name as species, count(A.animal_id) as num_animals from Animal A")
        ->innerJoin("Photo P on P.photo_id = A.photo_id and P.sequence_num = 1")
		->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL")
        ->innerJoin("Options O on A.species = O.option_id")
		->where("A.species!= 97")
		->group("PSM.project_id, A.species");
		
  
 $q3 = $db->getQuery(true)
             ->select('a.project_id,  a.option_id, a.species, sum(a.num_animals)')
			 ->from('(' . $query->union($query2) . ') a')
			 ->group("a.project_id, a.option_id");
  
    
  $queryInsert = $db->getQuery(true)
  ->insert('AnimalStatistics')
  ->columns($db->qn(array('project_id','option_id','species','num_animals')))
  ->values($q3);
  
  $db->setQuery($queryInsert);
  $result = $db->execute();
  
  print "<br>Complete";
  
  
}


// Return some upload and classification data for the project (used in displaying charts)
function projectData ( $project_id, $num_months, $interval_in_months = 1, $end_date = null ) {
  
  // check project id is public or protected, or that this user can acess this project??
  $fields = new StdClass();
  $fields->project_id = $project_id;
	      
  if ( !canView('project',$fields) ) {
	  return array();
  }
  
  // Get all the text snippets for this view in the current language
  $translations = getTranslations("project");
  
  $title = $translations["seq_dflt"]["translation_text"];
  if ( $num_months == 6 ) $title = $translations["seq_sixm"]["translation_text"];
  else if ( $num_months == 12 ) $title = $translations["seq_oneyr"]["translation_text"];
  else if ( $num_months == 36 ) $title = $translations["seq_thryr"]["translation_text"];
  
  //$numDisplayedMonths = 6;
  $numDisplayedMonths = $num_months + $interval_in_months;
  if ( $end_date == null ) {
	  $endDatePlus1 = strtotime("first day of next month" );
	  $endDate = strtotime("last day of this month");
  }
  else {
	  $endDatePlus1 = strtotime("+1 day", strtotime($end_date));
	  $endDate = strtotime($end_date);
  }
  
  $datesArray = array();
  $labelsArray = array();
  for ( $i=$numDisplayedMonths; $i>0; $i-=$interval_in_months ) {
	  //$dateStr = "first day of next month -" . $i . " months";
	  $minusMonths = "-" . $i . " months";
	  $months = $i-$interval_in_months+1;
	  $labelStr = "- " . $months . " months";
	  $dateMinusMonths = strtotime($minusMonths, $endDatePlus1);
	  //array_push ( $datesArray, date('Y-m-d H:i:s', strtotime($dateStr)) );
	  array_push ( $datesArray, date('Ymd', strtotime("-1 day", $dateMinusMonths)) );
	  array_push ( $labelsArray, date('M Y', strtotime($labelStr, $endDatePlus1)) );
	  //array_push ( $labelsArray, $i-$interval_in_months+1 );
  }
  //array_push ( $datesArray, date('Ymd', strtotime("first day of next month")) );
  array_push ( $datesArray, date('Ymd', strtotime("-1 day", $endDatePlus1)) );
  
  // Select number of sequences uploaded and number classified up to each of our dates.
  $numIntervals = count($datesArray)-1;
  //print "num intervals = " . $numIntervals;
  
  $projects = getSubProjectsById( $project_id );
  $id_string = implode(",", array_keys($projects));
  
  $uploadedArray = array();
  $classifiedArray = array();
  $db = JDatabase::getInstance(dbOptions());
  
  for ( $j=0; $j<$numIntervals; $j++ ) {
	$query = $db->getQuery(true);
	$query->select("sum(num_uploaded), sum(num_classified) from Statistics");
	$query->where("project_id in (" . $id_string . ")" );
	$query->where("end_date = " . $datesArray[$j+1] );
	$db->setQuery($query);
	
	$row = $db->loadRow();
	
	array_push ( $uploadedArray, $row['0'] );
	array_push ( $classifiedArray, $row['1'] );
  }

  
  $projectData = array ( 
		"labels" => $labelsArray,
		"uploaded" => $uploadedArray,
		"classified" => $classifiedArray,
		"cla_label" => $translations["classified"]["translation_text"],
		"upl_label" => $translations["uploaded"]["translation_text"],
		"title" => $title
		
		);
  
  
  //print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
  //print implode(",", $projectdetails);
  
  return $projectData;
}

function akrem($array,$key){
    $holding=array();
    foreach($array as $k => $v){
        if($key!=$k){
            $holding[$k]=$v;
        }
    }    
    return $holding;
}

// Return some animal data for the project (used in displaying charts)
// num_species is the max number of different species to return prioritised by number, if null use all, if not all animals are included the last entry is 
// a combined number for everything else named "Other"
function projectAnimals ( $project_id, $num_species = null, $include_dontknow = false, $include_human = false, $include_nothing = false ) {
  
  // check project id is public or protected, or that this user can acess this project??
  $fields = new StdClass();
  $fields->project_id = $project_id;
	      
  if ( !canView('project',$fields) ) {
	  return array();
  }
  
  // Get all the text snippets for this view in the current language
  $translations = getTranslations("project");
  
  $title = $translations["by_sp"]["translation_text"];
  	
  $projects = getSubProjectsById( $project_id );
  $id_string = implode(",", array_keys($projects));
  
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  // count every classification
  //$query->select("species, count(distinct start_photo_id) as num_animals FROM ProjectAnimals");
  $query->select("option_id, species, sum(num_animals) as num_animals FROM AnimalStatistics");
  $query->where("project_id in (" . $id_string . ")" );
  $query->group("species");
  $query->order("num_animals  DESC");
  $db->setQuery($query);
  
  
  // Include id...
  $animals_w_id = $db->loadAssocList('species');
  
  $animals = $db->loadAssocList('species', 'num_animals');
  //$animals = array_column($animals_w_id, 'num_animals', 'species');
  
  
  // Little fix to remove the additional text on Nothing and Human.
  $array_changed = false;
  if (isset($animals["Human <span class='fa fa-male'/>"])) {
    $animals['Human'] = $animals["Human <span class='fa fa-male'/>"];
    unset($animals["Human <span class='fa fa-male'/>"]);
	$array_changed = true;
  }
  if (isset($animals["Nothing <span class='fa fa-ban'/>"])) {
    $animals['Nothing'] = $animals["Nothing <span class='fa fa-ban'/>"];
    unset($animals["Nothing <span class='fa fa-ban'/>"]);
	$array_changed = true;
  }
  
  // Remove human and nothing if not to be included
  if ( !$include_human ) {
	  unset($animals['Human']);
	  $array_changed = true;
  }
  if ( !$include_nothing ) {
	  unset($animals['Nothing']);
	  $array_changed = true;
  }
   if ( !$include_dontknow ) {
	  unset($animals["Don't Know"]);
	  $array_changed = true;
  }
  
  // Remove Likes
  unset($animals["Like"]);
  $array_changed = true;
  
  // If we had to change the key names we need to re-sort the array
  if ( $array_changed ) {
	  arsort($animals);
  }
   
  $animals_to_return_en = array();
  // Finally handle the number of species, if we have more than required
  // NB Other is a possible option so combine this with Other Species if we have both
  $num_other = 0;
  
  if ( key_exists("Other", $animals) ) {
	$num_other = $animals["Other"];
	$animals = akrem($animals, "Other");
  }
  
  if ( $num_species && (count($animals) > $num_species) ) {
	  $animals_to_return_en = array_slice($animals, 0, $num_species-1);
	  $total_other = array_sum(array_slice($animals, $num_species-1)) + $num_other;
	  $animals_to_return_en['Other Species'] = "" + $total_other;
  }
  else {
	  $animals_to_return_en = $animals;
  }
  
  $animals_to_return = array();
  foreach ( $animals_to_return_en as $sp=>$num ) {
	  error_log( "animal row: " . $sp . ", " . $num );
	  if ( $sp == "Other Species" ) {
		  //error_log ( "key = " . $translations["other_sp"]["translation_text"] );
		  $animals_to_return[$translations["other_sp"]["translation_text"]] = $num;
	  }
	  else {
		  //error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
		  $animals_to_return[codes_getName($animals_w_id[$sp]["option_id"], "speciestran")] = $num;
	  }
  }
  
  
  /*
  $animals = $db->loadAssocList('species', 'num_animals');
  
  // Little fix to remove the additional text on Nothing and Human.
  $array_changed = false;
  if (isset($animals["Human <span class='fa fa-male'/>"])) {
    $animals['Human'] = $animals["Human <span class='fa fa-male'/>"];
    unset($animals["Human <span class='fa fa-male'/>"]);
	$array_changed = true;
  }
  if (isset($animals["Nothing <span class='fa fa-ban'/>"])) {
    $animals['Nothing'] = $animals["Nothing <span class='fa fa-ban'/>"];
    unset($animals["Nothing <span class='fa fa-ban'/>"]);
	$array_changed = true;
  }
  
  // Remove human and nothing if not to be included
  if ( !$include_human ) {
	  unset($animals['Human']);
	  $array_changed = true;
  }
  if ( !$include_nothing ) {
	  unset($animals['Nothing']);
	  $array_changed = true;
  }
   if ( !$include_dontknow ) {
	  unset($animals["Don't Know"]);
	  $array_changed = true;
  }
  
  // Remove Likes
  unset($animals["Like"]);
  $array_changed = true;
  
  // If we had to change the key names we need to re-sort the array
  if ( $array_changed ) {
	  arsort($animals);
  }
   
  $animals_to_return = array();
  // Finally handle the number of species, if we have more than required
  // NB Other is a possible option so combine this with Other Species if we have both
  $num_other = 0;
  
  if ( key_exists("Other", $animals) ) {
	$num_other = $animals["Other"];
	$animals = akrem($animals, "Other");
  }
  
  if ( $num_species && (count($animals) > $num_species) ) {
	  $animals_to_return = array_slice($animals, 0, $num_species-1);
	  $total_other = array_sum(array_slice($animals, $num_species-1)) + $num_other;
	  $animals_to_return['Other Species'] = "" + $total_other;
  }
  else {
	  $animals_to_return = $animals;
  }
  */
  
  /*
  $labelsArray = array('Badger','Rabbit','Mouse','Blackbird','Other');
  $animalsArray = array(120,103,76,56,30);
  $projectData = array ( 
		"labels" => $labelsArray,
		"animals" => $animalsArray
		);
  */
  
  //print "<br/>Got " . count($animals_to_return) . " species to return from projectanimals <br/>They are:<br>";
  //print_r ($animals_to_return);
  
  //$final_array = array ();
  /*
  $final_array["labels"] = array_keys($animals_to_return);
  $final_array["animals"] = array_values($animals_to_return);
  $final_array["title"] = $title;
  */
  //return $final_array;


  return array (
		"labels" => array_keys($animals_to_return),
		"animals" => array_values($animals_to_return),
		"title" => $title
		);
		
}


function getProjectTree ( $project_id ) {
	
	//print ("getProjectTree called, project_id = " . $project_id );
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.listing_level as level, P.access_level, O.option_name as priority")->from("Project P");
	$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
	$query->innerJoin("Options O on PO.option_id = O.option_id");
	$query->where("O.struc = 'priority'");
	$query->where("P.project_id = ".$project_id);
	
	$db->setQuery($query);

	$result = $db->loadObject();
	
	//print_r($result);
	
	return new Project ( $result->project_id, $result->project_prettyname, $result->project_description, $result->level, $result->access_level, $result->priority );
}


// Return a list of this project and all its children.
// Called with proj prettyname for now.  Refactor later if necessary.
/*  Refactoring now - see below, use project_id
function getSubProjects($project_prettyname, $exclude_private = false){
  //print "<br/>getSubProjects called<br/>";
  
  // first select all project/parent pairs into memory
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
		->where("parent_project_id is not NULL");
  // exclude private projects
  if ( $exclude_private ) {
	  $query->where("access_level < 3");
  }
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  //print_r($allpairs);
  
  // Need to get the proj id and name of the top level project
  $query = $db->getQuery(true);
  $query->select("project_id AS proj_id, project_prettyname AS proj_prettyname")->from("Project");
  $query->where("project_prettyname = '" . $project_prettyname . "'");
  $db->setQuery($query);
  $subprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  
  // add to project list by working through $allpairs to find the children, repeatedly
  addSubProjects( $subprojects, $allpairs );
  
  //print "<br/>Got " . count($subprojects) . " sub projects.<br/>They are:<br>";
  //print implode(",", $subprojects);
  
  return $subprojects;
  
}
*/


function getSubProjectsById($project_id, $exclude_private = false){
	/*
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_prettyname")->from("Project");
  $query->where("project_id = '" . $project_id . "'");
  $db->setQuery($query);
  $prettyname = $db->loadResult();
  
  return getSubProjects($prettyname);
  */
  
  // first select all project/parent pairs into memory
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project")
		->where("parent_project_id is not NULL");
  // exclude private projects
  if ( $exclude_private ) {
	  $query->where("access_level < 3");
  }
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  //print_r($allpairs);
  
  // Need to get the proj id and name of the top level project
  $query = $db->getQuery(true);
  $query->select("project_id AS proj_id, project_prettyname AS proj_prettyname")->from("Project");
  $query->where("project_id = '" . $project_id . "'");
  $db->setQuery($query);
  $subprojects = $db->loadAssocList('proj_id', 'proj_prettyname');
  
  
  // add to project list by working through $allpairs to find the children, repeatedly
  addSubProjects( $subprojects, $allpairs );
  
  //print "<br/>Got " . count($subprojects) . " sub projects.<br/>They are:<br>";
  //print implode(",", $subprojects);
  
  return $subprojects;
  
}

function cmpListedProjects($a, $b)
{
    if ($a->level == $b->level) {
		
        return ($a->project_prettyname < $b->project_prettyname) ? -1 : 1;
    }
    return ($a->level < $b->level) ? -1 : 1;
}
// return all projects that are/should be listed on the website Projects page - all non-private top level projects
// plus all second level projects which are non private but have a private parent (eg Highland Squirrel)
function listedProjects(){
  
  /*
  SELECT * FROM `project` WHERE access_level < 2 
and parent_project_id in (
    select project_id from project where parent_project_id is null and access_level > 1)
*/
	
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.article_id, P.listing_level as level, O.option_name as priority")->from("Project P");
  $query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
  $query->innerJoin("Options O on PO.option_id = O.option_id");
  $query->where("O.struc = 'priority'");
  $query->where("P.access_level < 3");
  $query->where("P.parent_project_id is null" );
  //$query->order("P.listing_level, P.project_prettyname" );
  //$db->setQuery($query);
  
  // Include second level non private...
  $query2 = $db->getQuery(true);
  $query2->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.article_id, P.listing_level as level, O.option_name as priority")->from("Project P");
  $query2->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
  $query2->innerJoin("Options O on PO.option_id = O.option_id");
  $query2->where("O.struc = 'priority'");
  $query2->where("P.access_level < 3");
  $query2->where("P.parent_project_id in (select project_id from Project where parent_project_id is null and access_level > 2)" );
  $db->setQuery($query);
  
  $q3 = $db->getQuery(true)
             ->select('a.*')
             ->from('(' . $query->union($query2) . ') a')
             ->order("a.level");
			 
  $listedprojects = $db->loadObjectList();		
  
  usort($listedprojects, "cmpListedProjects");
		
  //print "<br/>Got " . count($listedprojects) . " non-private top level projects<br/>They are:<br>";
  //print_r($listedprojects);
  
  return $listedprojects;
}

// return two values: number of classifications and total number of sequences uploaded for this project
// and all subprojects...
function projectProgress ( $project_id ) {
	
	$thisAndSubs = getSubProjectsById ( $project_id );
			
	$db = JDatabase::getInstance(dbOptions());
	
	$id_string = implode(",", array_keys($thisAndSubs));
	
	$endDate = date('Ymd', strtotime("last day of this month"));
	
	$query = $db->getQuery(true);
	$query->select("sum(num_uploaded), sum(num_classified) from Statistics");
	$query->where("project_id in (" . $id_string . ")" );
	$query->where("end_date = " . $endDate );
	$db->setQuery($query);
	
	$row = $db->loadRow();
	
	$sequences = $row['0'];
	$classifications = $row['1'];
 
 /*
	$query = $db->getQuery(true);
	$query->select("A.photo_id")->from("Animal A");
	$query->innerJoin("Photo P on A.photo_id = P.photo_id");
	$query->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
	$query->where("P.sequence_num = 1");
	$query->where("PSM.project_id in (" . $id_string . ")" );
	$query->where("P.photo_id >= PSM.start_photo_id" );
	$query->where("PSM.end_photo_id is NULL" );
	$query->where("A.species != 97" );
	
	// Next the number of classifications for sites that have left the project, when they were part of the project!
	$query2 = $db->getQuery(true);
	$query2->select("A.photo_id")->from("Animal A");
	$query2->innerJoin("Photo P on A.photo_id = P.photo_id");
	$query2->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
	$query2->where("P.sequence_num = 1");
	$query2->where("PSM.project_id in (" . $id_string . ")" );
	$query2->where("P.photo_id >= PSM.start_photo_id" );
	$query2->where("P.photo_id <= PSM.end_photo_id" );
	$query2->where("A.species != 97" );
	
	$q3 = $db->getQuery(true)
             ->select('count( distinct a.photo_id)')
             ->from('(' . $query->union($query2) . ') a');
	$db->setQuery($q3);
	
	$classifications = $db->loadResult();
*/             
/*
SELECT count(*) from photo P 
inner Join ProjectSiteMap PSM on P.site_id = PSM.site_id
where P.photo_id >= PSM.start_photo_id
and P.photo_id <= PSM.end_photo_id
and P.sequence_num = 1
and PSM.project_id = 1
UNION
SELECT count(*) from photo P 
inner Join ProjectSiteMap PSM2 on P.site_id = PSM2.site_id
and P.photo_id >= PSM2.start_photo_id 
and PSM2.end_photo_id is null
and P.sequence_num = 1
where PSM2.project_id = 1
*/
/*
	$query3 = $db->getQuery(true);
	$query3->select("P.photo_id")->from("Photo P");
	$query3->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
	$query3->where("P.sequence_num = 1");
	$query3->where("PSM.project_id in (" . $id_string . ")" );
	$query3->where("P.photo_id >= PSM.start_photo_id" );
	$query3->where("PSM.end_photo_id is NULL" );
	
	// And the total number of sequences uploaded, for sites that are have left the project
	$query4 = $db->getQuery(true);
	$query4->select("P.photo_id")->from("Photo P");
	$query4->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
	$query4->where("P.sequence_num = 1");
	$query4->where("PSM.project_id in (" . $id_string . ")" );
	$query4->where("P.photo_id >= PSM.start_photo_id" );
	$query4->where("P.photo_id <= PSM.end_photo_id" );
	
	$q4 = $db->getQuery(true)
             ->select('count(distinct a.photo_id)')
             ->from('(' . $query3->union($query4) . ') a');
	$db->setQuery($q4);
	$sequences = $db->loadResult();
*/    
		
	$percentComplete = 0;
	if ( $sequences > 0 ) $percentComplete = (int)(($classifications*100.0)/$sequences);
  
	return array("numClassifications"=>$classifications, "numSequences"=>$sequences, "percentComplete"=>$percentComplete);
}

// return all sites for this project if user is project admin
function getSites($project_id){
  // what user am I?
  $person_id = (int)userID();
  
  // For now we'll load all projects
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT S.site_id, S.site_name, S.grid_ref, S.person_id, PSM.start_time, PSM.end_time, PSM.start_photo_id, PSM.end_photo_id")->from("Site S");
  $query->innerJoin("ProjectSiteMap PSM on PSM.site_id = S.site_id");
  $query->innerJoin("ProjectUserMap PUM on PUM.project_id = PSM.project_id");
  $query->where("PUM.project_id = ".$project_id);
  $query->where("PUM.person_id = ".$person_id);
  $query->where("PUM.role_id = 1");
  $query->order("S.site_id" );
  $db->setQuery($query);
  $sites = $db->loadObjectList();
  
  //print "<br/>Got " . count($listedprojects) . " non-private projects<br/>They are:<br>";
  //print_r($listedprojects);
  
  return $sites;
}

// return all users for this project if user is project admin
function getProjectUsers($project_id){
  // what user am I?
  $person_id = (int)userID();
  
  // For now we'll load all projects
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT PUM.person_id, U.username, R.role_name")->from("ProjectUserMap PUM");
  $query->innerJoin("rhombus.cgf6k_users U on PUM.person_id = U.id");
  $query->innerJoin("ProjectUserMap PUM2 on PUM2.project_id = PUM.project_id");
  $query->innerJoin("Role R on R.role_id = PUM.role_id");
  $query->where("PUM.project_id = ".$project_id);
  $query->where("PUM2.person_id = ".$person_id);
  $query->where("PUM2.role_id = 1");
  $db->setQuery($query);
  $users = $db->loadObjectList();
  
  //print "<br/>Got " . count($listedprojects) . " non-private projects<br/>They are:<br>";
  //print_r($listedprojects);
  
  return $users;
}
function isFavourite($photo_id){
  if ( count(myLikes($photo_id)) > 0 ) {
	  return true;
  }
  else {
	  return false;
  }
}


// New version of nextSequence which considers the classify priority mode of each project.
// At the time of writing this could be:
// Multiple (allow multiple classifications per photo, done by different users), Single (classify all photos once), Time ordered (classify the oldest sequence first)
/*
function nextSequenceSlow(){
  
  //print "<br/>nextSequence called<br/>";
	
  $db = JDatabase::getInstance(dbOptions());
  $app = JFactory::getApplication();
  
  // Initialise photo_id and sequence_id to null
  $photo_id = null;
  $sequence_id = null;
  
  // First find out which classify button was pressed.
  $classify_project = $app->getUserState("com_biodiv.classify_only_project");
  $classify_own = $app->getUserState("com_biodiv.classify_self");
  $last_photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);
  
  $my_project = null;
  
  if ( $classify_project ) $my_project = $app->getUserState("com_biodiv.my_project");
  
  //print "<br>nextSequence, my_project: " . $my_project . "<br>" ;
  
  // Get a list of projects over which we are working and their priority mode (an assoc list keyed on project_id)
  // If we are doing a "classify all" then exclude some projects
  $exclude_flag = true;
  if ( $classify_project or $classify_own ) $exclude_flag = false;
  $project_options = getProjectOptions ( $my_project, "priority", $exclude_flag );
  
  //print "<br>nextSequence, got project options: <br>" ;
  //print_r ( $project_options );
  
  // Get all the priority option names indexed on project_id
  $priority_array = array_column ( $project_options, "option_name", "project_id" );
  
  //print "<br>nextSequence, got priority_array: <br>" ;
  //print_r ( $priority_array );
  
  $all_priorities = array("Single", "Multiple", "Single to multiple", "Site time ordered");
  
  // which priorities do we have projects for?
  $distinct_priorities = array_intersect ( $all_priorities, $priority_array );
  
  //print "<br>nextSequence, got distinct_priorities: <br>" ;
  //print_r ( $distinct_priorities );
  
  //error_log ( "distinct priorities: " . implode ( ',', $distinct_priorities ) );
  
  $photo_id_candidates = array();
  
  // THere may be no photos left to classify.
  $num_candidates = 0;
  
  if ( count($distinct_priorities) > 0 ) {
	//print "distinct priorities are:<br>";
	//print_r($distinct_priorities);
	if ( in_array("Single", $distinct_priorities ) ) {
	  $project_ids = array_keys ( $priority_array, "Single" );
	  $ph_id = chooseSingle ( $project_ids, $classify_own );
	  if ( $ph_id ) $photo_id_candidates["Single"] = $ph_id;
	}
	if ( in_array("Multiple", $distinct_priorities ) ) {
	  //print "<br>nextSequence, about to ChooseMultiple <br>" ;
	  $project_ids = array_keys ( $priority_array, "Multiple" );
	  $ph_id = chooseMultiple ( $project_ids, $classify_own );
	  //print "<br>nextSequence, chooseMultiple chose ph_id " . $ph_id . " <br>" ;
	  if ( $ph_id ) $photo_id_candidates["Multiple"] = $ph_id;
	}
	if ( in_array("Single to multiple", $distinct_priorities ) ) {
	  $project_ids = array_keys ( $priority_array, "Single to multiple" );
	  $ph_id = chooseSingle ( $project_ids, $classify_own );
	  if ( $ph_id ) $photo_id_candidates["Single to multiple"] = $ph_id;
	  else {
		  $ph_id = chooseMultiple ( $project_ids, $classify_own );
		  if ( $ph_id ) $photo_id_candidates["Single to multiple"] = $ph_id;
	  }
	}
	if ( in_array("Site time ordered", $distinct_priorities ) ) {
	  $project_ids = array_keys ( $priority_array, "Site time ordered" );
	  $ph_id = chooseSiteTimeOrdered ( $project_ids, $last_photo_id, $classify_own );
	  if ( $ph_id ) $photo_id_candidates["Site time ordered"] = $ph_id;
	}
  
	$num_candidates = count($photo_id_candidates);
  
	//print "<br>nextSequence, num_candidates = " . $num_candidates . " <br>" ;
  
	//$chosen_priority = reset(array_keys($photo_id_candidates)); // the first one
	if ( $num_candidates > 0 ) {
		$chosen_priority = array_keys($photo_id_candidates)[0];
	}
  
	// If only one priority type we are done, but if more than one we have to pick.
	if ( $num_candidates > 1 ) {
	
		// Determine which priority type we're using.  Take a weighted choice from each priority type that this user has 
		// access to.  For now use these hardcoded values but this needs to be taken out and put in the database, then updated daily based 
		// on what photos/projects there are...
		$all_weightings = getPriorityWeightings ();
	
		$reqd_weightings = array_intersect_key ( $all_weightings, $photo_id_candidates );
		//print "<br>nextSequence, got reqd_weightings: <br>" ;
		//print_r ( $reqd_weightings );
  
		$total_weighting = array_sum($reqd_weightings);
  
		// Choose a random integer between 0 and $total_weightings.
		$choice = rand ( 1, $total_weighting );
	
		//print "<br>nextSequence, choice:" . $choice . " <br>" ;
	
		// check through the accumulated weightings to see where the choice lies..
		$count = 0;
		foreach ( $reqd_weightings as $priority=>$weighting ) {
			$count += $weighting;
			if ( $choice <= $count ) {
				$chosen_priority = $priority;
				break;
			}
		}
	}
  
	//print "<br>nextSequence, chosen priority is:" . $chosen_priority . " <br>" ;
	//error_log ( "nextSequence, chosen priority is:" . $chosen_priority );
	
	if ( $num_candidates > 0 ) {
		$photo_id = $photo_id_candidates[$chosen_priority];
	}
  }
  //print "<br/> returning at end of nextSequence, photo_id = " . $photo_id;
  //return getSequence($photo_id);
  return getSequenceDetails($photo_id);
}
*/

// New version of nextSequence which considers the classify priority mode of each project.
// At the time of writing this could be:
// Multiple (allow multiple classifications per photo, done by different users), Single (classify all photos once), Time ordered (classify the oldest sequence first)
function nextSequence(){
  
  //print "<br/>nextSequence called<br/>";
	
  $db = JDatabase::getInstance(dbOptions());
  $app = JFactory::getApplication();
  
  // Initialise photo_id and sequence_id to null
  $photo_id = null;
  $sequence_id = null;
  
  // First find out which classify button was pressed.
  $classify_project = $app->getUserState("com_biodiv.classify_only_project");
  $classify_own = $app->getUserState("com_biodiv.classify_self");
  //$last_photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);
  $last_photo_id = (int)$app->getUserState('com_biodiv.prev_photo_id', 0);
  
  $project_id = null;
  
  if ( $classify_project ) $project_id = $app->getUserState("com_biodiv.project_id");
  
  //print "<br>nextSequence, project_id: " . $project_id . "<br>" ;
  
  // Get a list of projects over which we are working and their priority mode (an assoc list keyed on project_id)
  // If we are doing a "classify all" then exclude some projects
  $exclude_flag = true;
  if ( $classify_project or $classify_own ) $exclude_flag = false;
  $project_options = getProjectOptions ( $project_id, "priority", $exclude_flag );
  
  //print "<br>nextSequence, got project options: <br>" ;
  //print_r ( $project_options );
  
  // Get all the priority option names indexed on project_id
  $priority_array = array_column ( $project_options, "option_name", "project_id" );
  
  //print "<br>nextSequence, got priority_array: <br>" ;
  //print_r ( $priority_array );
  
  $all_priorities = array("Single", "Multiple", "Single to multiple", "Site time ordered", "Repeat");
  
  // which priorities do we have projects for?
  $distinct_priorities = array_intersect ( $all_priorities, $priority_array );
  
  //print "<br>nextSequence, got distinct_priorities: <br>" ;
  //print_r ( $distinct_priorities );
  
  //error_log ( "distinct priorities: " . implode ( ',', $distinct_priorities ) );
  
  // Determine the order of priority types to try.  Take a weighted choice from each priority type that this user has 
  // access to.  
  $all_weightings = getPriorityWeightings ();
  //print "<br>nextSequence, got all_weightings: <br>" ;
  //print_r ( $all_weightings );


  $reqd_weightings = array_intersect_key ( $all_weightings, array_flip($priority_array ));
  //print "<br>nextSequence, got reqd_weightings: <br>" ;
  //print_r ( $reqd_weightings );

  $total_weighting = array_sum($reqd_weightings);
  
  $ordered_priorities = array();
  
  $num_iterations = count($reqd_weightings);

  for ( $i=0; $i< $num_iterations; $i++ ) {
	//print "<br>nextSequence, i = " . $i . ", now got reqd_weightings: <br>" ;
    //print_r ( $reqd_weightings );
	//print "<br>nextSequence, i = " . $i . ", total_weighting =  " . $total_weighting . "<br>" ;
    

	if ( count($reqd_weightings) == 1 ) {
		//print "<br>just one reqd weighting left <br>" ;

		$ordered_priorities[] = array_keys($reqd_weightings)[0];
		break;
	}
	// Choose a random integer between 0 and $total_weightings.
    $choice = rand ( 1, $total_weighting );

    //print "<br>nextSequence, choice:" . $choice . " <br>" ;

    // check through the accumulated weightings to see where the choice lies..
    $count = 0;
    foreach ( $reqd_weightings as $priority=>$weighting ) {
	  $count += $weighting;
	  if ( $choice <= $count ) {
	    $ordered_priorities[] = $priority;
		$total_weighting -= $weighting;
		//print "<br>About to unset " . $priority . " <br>" ;
        unset($reqd_weightings[$priority]);
		//print "<br>nextSequence, after unset reqd_weightings: <br>" ;
        //print_r ( $reqd_weightings );
	
	    break;
	  }
    }
  }
  //print "<br>nextSequence, got ordered_priorities: <br>" ;
  //print_r ( $ordered_priorities );

  foreach ( $ordered_priorities as $current_priority ) {
	  if ( !$photo_id ) {
		  switch ($current_priority) {
			  case "Multiple":
				$project_ids = array_keys ( $priority_array, "Multiple" );
	            $photo_id = chooseMultiple ( $project_ids, $classify_own );
				break;
			  case "Single":
				$project_ids = array_keys ( $priority_array, "Single" );
	            $photo_id = chooseSingle ( $project_ids, $classify_own );
				break;
			  case "Single to multiple":
				$project_ids = array_keys ( $priority_array, "Single to multiple" );
	            $photo_id = chooseSingle ( $project_ids, $classify_own );
				if ( !$photo_id ) {
					$photo_id = chooseMultiple ( $project_ids, $classify_own );
				}
				break;
			  case "Site time ordered":
				$project_ids = array_keys ( $priority_array, "Site time ordered" );
	            $photo_id = chooseSiteTimeOrdered ( $project_ids, $last_photo_id, $classify_own );
				break;
			  case "Repeat":
			    $project_ids = array_keys ( $priority_array, "Repeat" );
	            $photo_id = chooseRepeat ( $project_ids, $classify_own );
				break;
			  case "Single to repeat":
			    $project_ids = array_keys ( $priority_array, "Single to repeat" );
	            $photo_id = chooseSingle ( $project_ids, $classify_own );
				if ( !$photo_id ) {
					$photo_id = chooseRepeat ( $project_ids, $classify_own );
				}
				break;
			  default:
			    break;
	  
		  }
	  }
  }
  
  /*
  $photo_id_candidates = array();
  
  // THere may be no photos left to classify.
  $num_candidates = 0;
  
  if ( count($distinct_priorities) > 0 ) {
	//print "distinct priorities are:<br>";
	//print_r($distinct_priorities);
	if ( in_array("Single", $distinct_priorities ) ) {
	  $project_ids = array_keys ( $priority_array, "Single" );
	  $ph_id = chooseSingle ( $project_ids, $classify_own );
	  if ( $ph_id ) $photo_id_candidates["Single"] = $ph_id;
	}
	if ( in_array("Multiple", $distinct_priorities ) ) {
	  //print "<br>nextSequence, about to ChooseMultiple <br>" ;
	  $project_ids = array_keys ( $priority_array, "Multiple" );
	  $ph_id = chooseMultiple ( $project_ids, $classify_own );
	  //print "<br>nextSequence, chooseMultiple chose ph_id " . $ph_id . " <br>" ;
	  if ( $ph_id ) $photo_id_candidates["Multiple"] = $ph_id;
	}
	if ( in_array("Single to multiple", $distinct_priorities ) ) {
	  $project_ids = array_keys ( $priority_array, "Single to multiple" );
	  $ph_id = chooseSingle ( $project_ids, $classify_own );
	  if ( $ph_id ) $photo_id_candidates["Single to multiple"] = $ph_id;
	  else {
		  $ph_id = chooseMultiple ( $project_ids, $classify_own );
		  if ( $ph_id ) $photo_id_candidates["Single to multiple"] = $ph_id;
	  }
	}
	if ( in_array("Site time ordered", $distinct_priorities ) ) {
	  $project_ids = array_keys ( $priority_array, "Site time ordered" );
	  $ph_id = chooseSiteTimeOrdered ( $project_ids, $last_photo_id, $classify_own );
	  if ( $ph_id ) $photo_id_candidates["Site time ordered"] = $ph_id;
	}
  
	$num_candidates = count($photo_id_candidates);
  
	//print "<br>nextSequence, num_candidates = " . $num_candidates . " <br>" ;
  
	//$chosen_priority = reset(array_keys($photo_id_candidates)); // the first one
	
	if ( $num_candidates > 0 ) {
		$chosen_priority = array_keys($photo_id_candidates)[0];
	}
  
	// If only one priority type we are done, but if more than one we have to pick.
	if ( $num_candidates > 1 ) {
	
		// Determine which priority type we're using.  Take a weighted choice from each priority type that this user has 
		// access to.  For now use these hardcoded values but this needs to be taken out and put in the database, then updated daily based 
		// on what photos/projects there are...
		$all_weightings = getPriorityWeightings ();
	
		$reqd_weightings = array_intersect_key ( $all_weightings, $photo_id_candidates );
		//print "<br>nextSequence, got reqd_weightings: <br>" ;
		//print_r ( $reqd_weightings );
  
		$total_weighting = array_sum($reqd_weightings);
  
		// Choose a random integer between 0 and $total_weightings.
		$choice = rand ( 1, $total_weighting );
	
		//print "<br>nextSequence, choice:" . $choice . " <br>" ;
	
		// check through the accumulated weightings to see where the choice lies..
		$count = 0;
		foreach ( $reqd_weightings as $priority=>$weighting ) {
			$count += $weighting;
			if ( $choice <= $count ) {
				$chosen_priority = $priority;
				break;
			}
		}
	}
    
	//print "<br>nextSequence, chosen priority is:" . $chosen_priority . " <br>" ;
	//error_log ( "nextSequence, chosen priority is:" . $chosen_priority );
	
	if ( $num_candidates > 0 ) {
		$photo_id = $photo_id_candidates[$chosen_priority];
	}
  }
  */
  //print "<br/> returning at end of nextSequence, photo_id = " . $photo_id;
  //return getSequence($photo_id);
  return getSequenceDetails($photo_id);
}

function chooseMultiple ( $project_ids, $classify_own ) {
	
	//print "<br>chooseMultiple called, classify_own = " . $classify_own . " <br>";
	$photo_id = null;
	
	// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
	$own_string = "";
	if ( $classify_own ) {
		$own_string = " AND P.person_id = " . (int)userID();
	}
		
	if ( count($project_ids) > 0 ) {
	
		$project_id_str = implode(',', $project_ids);
	
		//error_log ( "chooseMultiple, project_id_str = " . $project_id_str );
		//print "<br>chooseMultiple, project_id_str = " . $project_id_str . " <br>";
	
  
		$db = JDatabase::getInstance(dbOptions());
		$q1 = $db->getQuery(true);
		    
		$q1->select("P.photo_id, P.sequence_id")
			->from("Photo P")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
			->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
			->where("P.status = 1")
			->where("A.photo_id IS NULL")
			->where("P.contains_human =0" . $own_string )
			->where("P.sequence_id > 0")
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
			->order("rand()");
		
		$db->setQuery($q1, 0, 1); // LIMIT 1
		$photo = $db->loadObject();
		if ( $photo ) {
			$photo_id = $photo->photo_id;
			//print "<br>chooseMultiple, photo found with id " . $photo_id . " <br>";
		}	
	}
	
	return $photo_id;
}


function chooseSingle ( $project_ids, $classify_own ) {
	
	$photo_id = null;
	
	// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
	$own_string = "";
	if ( $classify_own ) {
		$own_string = " AND P.person_id = " . (int)userID();
	}
	
	$project_id_str = implode(',', $project_ids);
  
	$db = JDatabase::getInstance(dbOptions());
	$q1 = $db->getQuery(true);
	        
	$q1->select("P.photo_id, P.sequence_id")
        ->from("Photo P")
        ->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
        ->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
        ->leftJoin("Animal A ON P.photo_id = A.photo_id" )
        ->where("P.status = 1")
		->where("A.photo_id IS NULL")
        ->where("P.contains_human =0" . $own_string )
	    ->where("P.sequence_id > 0")
		->where("P.sequence_num = 1" )
		->where("P.photo_id >= PSM.start_photo_id")
		->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
		->order("rand()");
	
	
	$db->setQuery($q1, 0, 1); // LIMIT 1
	$photo = $db->loadObject();
	if ( $photo ) {
		$photo_id = $photo->photo_id;
	}	
	
	return $photo_id;
}

function chooseSiteTimeOrdered ( $project_ids, $last_photo_id, $classify_own ) {
	
	//error_log( "chooseSiteTimeOrdered, last_photo_id is:" . $last_photo_id  );
	
	$photo_id = null;
	
	// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
	$own_string = "";
	if ( $classify_own ) {
		$own_string = " AND P.person_id = " . (int)userID();
	}
	
	$project_id_str = implode(',', $project_ids);
  
	$db = JDatabase::getInstance(dbOptions());
	
	// If given a photo id, check for the next sequence in time order on that site.  If site is finished, start a new site.
	if ( $last_photo_id ) {
		$q1 = $db->getQuery(true);
		    
		$q1->select("P.photo_id, P.sequence_id, P.taken")
			->from("Photo P")
			->innerJoin("Site S ON P.site_id = S.site_id")
			->innerJoin("Photo P2 ON P2.site_id = P.site_id" )
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = S.site_id AND PSM.project_id = PROJ.project_id")
			->leftJoin("Animal A ON P.photo_id = A.photo_id" )
			->where("P.status = 1")
			->where("A.photo_id IS NULL")
			->where("P.contains_human =0" . $own_string )
			->where("P2.photo_id = ".$last_photo_id )
			->where("P.sequence_id > 0")
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
			->order("P.taken");
			
		$db->setQuery($q1, 0, 1); // LIMIT 1
		$photo = $db->loadObject();
		if ( $photo ) {
			$photo_id = $photo->photo_id;
		}	
	}
	if ( !$photo_id ) {
		$q1 = $db->getQuery(true);
		    
		$q1->select("P.photo_id, P.sequence_id, P.taken")
			->from("Photo P")
			->innerJoin("Site S ON P.site_id = S.site_id")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = S.site_id AND PSM.project_id = PROJ.project_id")
			->leftJoin("Animal A ON P.photo_id = A.photo_id" )
			->where("P.status = 1")
			->where("A.photo_id IS NULL")
			->where("P.contains_human =0" . $own_string )
			->where("P.sequence_id > 0")
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
			->order("P.taken");
		
		$db->setQuery($q1, 0, 1); // LIMIT 1
		$photo = $db->loadObject();
		if ( $photo ) {
			$photo_id = $photo->photo_id;
		}	
	}
	
	return $photo_id;
}

function chooseRepeat ( $project_ids, $classify_own ) {
	
	//error_log ( "chooseRepeat called, classify_own = " . $classify_own );
	$photo_id = null;
	
	// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
	$own_string = "";
	if ( $classify_own ) {
		$own_string = " AND P.person_id = " . (int)userID();
	}
		
	if ( count($project_ids) > 0 ) {
	
		$project_id_str = implode(',', $project_ids);
	
		//error_log ( "chooseRepeat, project_id_str = " . $project_id_str );
		//print "<br>chooseRepeat, project_id_str = " . $project_id_str . " <br>";
	
  
		$db = JDatabase::getInstance(dbOptions());
		$q1 = $db->getQuery(true);
		$q2 = $db->getQuery(true);
            
		$q1->select("P.photo_id, P.sequence_id")
			->from("Photo P")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
			->where("P.status = 1")
			->where("P.contains_human =0" . $own_string )
			->where("P.sequence_id > 0")
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
			->order("rand()");
		
		$db->setQuery($q1, 0, 1); // LIMIT 1
		$photo = $db->loadObject();
		if ( $photo ) {
			$photo_id = $photo->photo_id;
			//print "<br>chooseRepeat, photo found with id " . $photo_id . " <br>";
		}	
	}
	
	return $photo_id;
}


function getPriorityWeightings () {
	$db = JDatabase::getInstance(dbOptions());
	$app = JFactory::getApplication();
	$query = $db->getQuery(true);
	
	$query->select("O.option_name, OD.value")
        ->from("Options O")
        ->innerJoin("OptionData OD ON O.option_id = OD.option_id")
        ->where("OD.data_type = 'weighting'");
        
	$db->setQuery($query); 
	$weightings = $db->loadAssocList("option_name", "value");
	
	//print "<br>getPriorityWeightings, got weightings: <br>" ;
	//print_r ( $weightings );
  
	return $weightings;
}


// Return sequence this photo belongs to from this photo onwards...
// might need to change this to get whole sequence
function getSequence($photo_id) {
  
  $sequence = array();
  
  $next_photo_id = $photo_id;
  while ($next_photo_id>0) {
	$sequence[] = $next_photo_id;
	$next_photo_details = codes_getDetails($next_photo_id, 'photo');
	$next_photo_id = $next_photo_details['next_photo'];      
  }

  return $sequence;
}

// Return all the photo details for this sequence, in order.  Do one db call (well two) rather than one photo at a time.
function getSequenceDetails($photo_id) {
  
  $sequenceDetails = array();
  
  if ( $photo_id > 0 ) {
    // First get the sequence id
    $photoDetails = codes_getDetails($photo_id, 'photo');
    $sequenceId = $photoDetails['sequence_id'];
  
    $db = JDatabase::getInstance(dbOptions());
	
    $query = $db->getQuery(true);
    $query->select("photo_id, site_id, dirname, filename, sequence_id, sequence_num, prev_photo, next_photo, person_id from Photo P")
		->where("P.sequence_id = " . $sequenceId )
		->order("P.sequence_num");
    $db->setQuery($query);
	
    $sequenceDetails = $db->loadAssocList();
  }
  return $sequenceDetails;
}

function updateSequence($photo_id){
	// Check this photo is part of the current sequence
	
	// Check for existing classifications and copy over if so.
	$pdetails = codes_getDetails($photo_id, 'photo');
    $prev_photo_id = $pdetails['prev_photo'];
    if($prev_photo_id){
	  //print "<br/>Have a prev_photo_id<br/>";
	
      $prevDetails = codes_getDetails($prev_photo_id, 'photo');
      if(!$pdetails['contains_human'] and !haveClassified($photo_id)){
        // copy across classification
        $db = JDatabase::getInstance(dbOptions());
        $query = $db->getQuery(true);
        $query->select("person_id, species, gender, age, number")
	    ->from("Animal")
	    ->where("photo_id = ".(int)$prev_photo_id)
	    ->where("person_id = " . (int)userID())
	    ->order("animal_id");
        $db->setQuery($query);
        foreach($db->loadObjectList() as $animal){
	      $speciesDetails = codes_getDetails($animal->species, 'content');
		  $speciesGroup = $speciesDetails['struc'];
		  if(!in_array($speciesGroup, array('noanimal','like'))){
	        $animal->photo_id = $photo_id;
	        codes_insertObject($animal, "animal");
	      }
        }
      }
    }
	return true;
}

/* NO LONGER USED
function nextPhoto($prev_photo_id){
	
  //print "<br/>nextPhoto called<br/>";
	
  $db = JDatabase::getInstance(dbOptions());
  $app = JFactory::getApplication();
  
  // Initialise photo_id to null
  $photo_id = null;

  $pdetails = codes_getDetails($prev_photo_id, 'photo');
  $next_photo = $pdetails['next_photo'];
  if($next_photo){
	//print "<br/>Have a next_photo<br/>";
	
    $nextDetails = codes_getDetails($next_photo, 'photo');
    $photo_id = $next_photo;
    if(!$nextDetails['contains_human'] and !haveClassified($next_photo)){
      // copy across classification
      $query = $db->getQuery(true);
      $query->select("person_id, species, gender, age, number")
	->from("Animal")
	->where("photo_id = ".(int)$prev_photo_id)
	->where("person_id = " . (int)userID())
	->order("animal_id");
      $db->setQuery($query);
      foreach($db->loadObjectList() as $animal){
	$speciesDetails = codes_getDetails($animal->species, 'content');
	$speciesGroup = $speciesDetails['struc'];
	if(!in_array($speciesGroup, array('noanimal','like'))){
	  $animal->photo_id = $photo_id;
	  codes_insertObject($animal, "animal");
	}
      }
    }
  }
  if($prev_photo_id && !$photo_id){
    // find next photo sequence on same trap usually
	// continue causes error when used in included file so avoid this..
    //if(rand(0,10)>7){
    //  continue;
    //}
	
	if(rand(0,10)<=7){
	  //print "<br/>rand < 7<br/>";
	
      $site_id = $pdetails['site_id'];
      $taken = $pdetails['taken'];
    
      $query = $db->getQuery(true);
      $query->select("P.photo_id")
        ->from("Photo P")
        ->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
        ->where("A.photo_id IS NULL")
        ->where("P.contains_human = 0")
        ->where("P.site_id = " . (int)$site_id)
        ->where("taken > '$taken'")
        ->order("taken");
      $db->setQuery($query, 0, 1); // LIMIT 1
      $photo_id = $db->loadResult();
	}
  }
  if (!$photo_id) {
	//print "<br/>no photo_id, testing for classify own project<br/>";
  
    if($app->getUserState("com_biodiv.classify_only_project")){
      //print "<br/>classifying current project only<br/>";
	  // NB need to include all sub projects too
      $my_project = $app->getUserState("com_biodiv.my_project");
	  $allsubs = getSubProjects($my_project);
	  //print "<br/>Got " . count($allsubs) . " sub projects.<br/>They are:<br>";
	  //print implode(",", $allsubs);
	  $id_string = implode(',', array_keys($allsubs));
	  
	  //print "id_string = ".$id_string;
	  $query = $db->getQuery(true);
      $query->select("P.photo_id, P.sequence_id")
        ->from("Photo P")
        ->innerJoin("Site S ON P.site_id = S.site_id")
        ->innerJoin("Project PROJ ON PROJ.project_id in (".$id_string.")")
        ->innerJoin("ProjectSiteMap PSM ON PSM.site_id = S.site_id AND PSM.project_id = PROJ.project_id")
        ->leftJoin("Animal A ON P.photo_id = A.photo_id")
        ->where("A.photo_id IS NULL")
        ->where("P.contains_human =0")
		->order("rand()");
      $db->setQuery($query, 0, 1); // LIMIT 1
      $photo = $db->loadObject();
	  if ( $photo ) {
		  $photo_id = $photo->photo_id;
	  }
	  // Classifying my project only so return here even if no photo_id.
	  // find first unclassified picture in this sequence
	  // Copied from end of function as need to return for my project ONLY
	  // Really need to do this more neatly
	  if ( $photo_id ) {
        //print "photo found = ".$photo_id;
	    $sequence_id = $photo->sequence_id;
        //print "sequence id = ".$sequence_id;
	    $sequence = codes_getDetails($sequence_id, 'sequence');
        $photo_id = $sequence['start_photo_id'];
        //print "start photo = ".$photo_id;
	    while($photo_id && haveClassified($photo_id)){
          $photoDetails = codes_getDetails($photo_id, 'photo');
          $photo_id = $photoDetails['next_photo'];
        }
		//print "after checking classified, photo_id = ".$photo_id;
	  }
	  return $photo_id;
    }
  }
  if(!$photo_id){
	//print "<br/>no photo_id, testing for classify self first<br/>";
  
    // choose random picture
    //$app = JFactory::getApplication();
    if($app->getUserState("com_biodiv.classify_self")){
	  //print "<br/>classifying self<br/>";
	
      $query = $db->getQuery(true);
      $query->select("P.photo_id, P.sequence_id")
	->from("Photo P")
	->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
	->where("A.photo_id IS NULL")
	->where("P.contains_human =0")
	->where("P.person_id = " . (int)userID())
	->order("rand()");
      $db->setQuery($query, 0, 1); // LIMIT 1
      $photo = $db->loadObject();
      $photo_id = $photo->photo_id;
    }
    if ( !$photo_id ) {
		//print "<br/>no photo_id, testing for classify project first<br/>";
  
	if($app->getUserState("com_biodiv.classify_project")){
      //print "<br/>classifying project first<br/>";
	  $my_project = $app->getUserState("com_biodiv.my_project");
	  $query = $db->getQuery(true);
      $query->select("P.photo_id, P.sequence_id")
        ->from("Photo P")
        ->innerJoin("Site S ON P.site_id = S.site_id")
        ->innerJoin("Project PROJ ON PROJ.project_prettyname = '".$my_project."'")
        ->innerJoin("ProjectSiteMap PSM ON PSM.site_id = S.site_id AND PSM.project_id = PROJ.project_id")
        ->leftJoin("Animal A ON P.photo_id = A.photo_id")
        ->where("A.photo_id IS NULL")
        ->where("P.contains_human =0")
		->order("rand()");
      $db->setQuery($query, 0, 1); // LIMIT 1
      $photo = $db->loadObject();
	  if ( $photo ) {
		  $photo_id = $photo->photo_id;
	  }
    }
	//print "<br/>photo_id = " . $photo_id . " after classify_project check<br/>";
	}
		
	if (!$photo_id) {
		//print "<br/>No photo so looking at all my projects<br/>";
		// only display photos from projects the user has access to.
		$projects = myProjects();
		//print "<br/>Got " . count($projects) . " all projects user has access to<br/>They are:<br>";
		//print implode(",", $projects);
  
		$id_string = implode(',', array_keys($projects));
		
		$query = $db->getQuery(true);
		$query->select("P.photo_id, P.sequence_id")
			->from("Photo P")
			->innerJoin("Site S ON P.site_id = S.site_id")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$id_string.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = S.site_id AND PSM.project_id = PROJ.project_id")
			->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
			->where("A.photo_id IS NULL")
			->where("P.contains_human =0")
			->order("rand()");
		$db->setQuery($query, 0, 1); // LIMIT 1
		$photo = $db->loadObject();
		if ( $photo ) {
		  $photo_id = $photo->photo_id;
		}
	}
	  
    //if(!$photo_id){
    //  $query = $db->getQuery(true);
    //  $query->select("P.photo_id, P.sequence_id")
	//->from("Photo P")
	//->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
	//->where("A.photo_id IS NULL")
	//->where("P.contains_human =0")
    //  ->order("rand()");
    //  $db->setQuery($query, 0, 1); // LIMIT 1
    //  $photo = $db->loadObject();
    //  $photo_id = $photo->photo_id;
    //}
	

    // find first unclassified picture in this sequence
	$sequence_id = $photo->sequence_id;
	$sequence = codes_getDetails($sequence_id, 'sequence');
	$photo_id = $sequence['start_photo_id'];
	
	//echo "sequence_id = ".$sequence_id;
	//echo "photo_id = ".$photo_id;
	//echo "photo->photo_id = ".$photo->photo_id;
	//if ( $sequence_id != -1 ) {
	  //$sequence = codes_getDetails($sequence_id, 'sequence');
	  //echo "sequence = ".$sequence;
    
      //$photo_id = $sequence['start_photo_id'];
	  //echo "photo_id = ".$photo_id;
	//}
	
	//echo "photo_id = ".$photo_id;
	
    while($photo_id && haveClassified($photo_id)){
      $photoDetails = codes_getDetails($photo_id, 'photo');
      $photo_id = $photoDetails['next_photo'];
    }
  }
  
  //print "<br/> returning at end of nextPhoto, photo_id = " . $photo_id;

  return $photo_id;
}

*/

function prevPhoto($last_photo_id){
  $photoDetails = codes_getDetails($last_photo_id, 'photo');
  $photo_id = $photoDetails['prev_photo'];
  return $photo_id;
}

function photoSequenceStart($last_photo_id){
  $photoDetails = codes_getDetails($last_photo_id, 'photo');
  $sequence_id = $photoDetails['sequence_id'];
  $sequenceDetails = codes_getDetails($sequence_id, 'sequence');
  $photo_id = $sequenceDetails['start_photo_id'];
  return $photo_id;
}


function sequencePhotos($upload_id){
  if(!$upload_id = (int)$upload_id){
    return false;
  }
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("photo_id, taken")
    ->from("Photo")
    ->where("upload_id = " . (int)$upload_id)
    ->order("taken");
  
  $db->setQuery($query);
  $res = $db->loadAssocList();

  print "<br/>Got " . count($res) . " results<br/>\n";

  $prev_photo_id = 0;
  $prev_dateTime = null;
  $prev_seq_num = 0;
  $sequence_id = 0;
  $seq_num = 0;
  $this_row = 0;
  $num_results = count($res);

  foreach($res as $line){
    extract($line);
	$this_row++;
    print "<br/>sequencing photoid_id $photo_id<br/>";
	//print "<br/>Just read next row: seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
    $dateTime = new DateTime($taken);
    //print "photo_id $photo_id ";
	
	if($prev_dateTime !== null){
      $diff = $dateTime->diff($prev_dateTime);
      print "photo_id $photo_id prev_photo_id $prev_photo_id diff ". $diff->s;
      if((abs($diff->s) <10) && ($diff->i==0) && ($diff->h==0) && ($diff->d==0) & ($diff->m==0) & ($diff->y ==0)){ // less than 10 seconds between photos
	    print "<br/> photos are close<br/>\n";
		if($sequence_id>0){
	      print "Existing sequence $sequence_id<br/>";	
	      $seq_num++;
	      $fields = new StdClass();
	      $fields->end_photo_id = $photo_id;
	      $fields->sequence_length = $seq_num;
	      $fields->sequence_id = $sequence_id;
	      codes_updateObject($fields, 'sequence');
		  //print "<br/>end of existing sequence seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
	    }
	    else{ // start new sequence
	      $fields = new StdClass();
	      $fields->start_photo_id = $prev_photo_id;
	      $fields->upload_id = $upload_id;
		  // Currently know the sequence is of length at least 2.  Set the end and length, these will be updated
		  // if the sequence has a longer length.
	      $fields->end_photo_id = $photo_id;
	      $fields->sequence_length = 2;
	      $sequence_id = codes_insertObject($fields, 'sequence');
	      print "New sequence $sequence_id<br/>";
	      $prev_seq_num = 1;
	      $seq_num = 2;
		  //print "<br/>end of start new sequence seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
	    }

        canEdit( $prev_photo_id, 'photo', 1);
        canEdit( $photo_id, 'photo' , 1);
//	print "canEdit photo $prev_photo_id ".canEdit('photo' , $prev_photo_id); 
//	print "canEdit photo $photo_id ".canEdit('photo' , $photo_id); 
	    $fields = new StdClass();
	    $fields->sequence_id = $sequence_id;
	    $fields->prev_photo = $prev_photo_id;
	    $fields->sequence_num = $seq_num;
	    $fields->photo_id = $photo_id;
	    print "updating ";
	    print_r($fields);
	    codes_updateObject($fields, 'photo');
		//print "<br/>after updating photo_id $photo_id seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";

	    $fields = new StdClass();
	    $fields->sequence_id = $sequence_id;
	    $fields->next_photo = $photo_id;
	    $fields->sequence_num = $prev_seq_num;
	    $fields->photo_id = $prev_photo_id;
	    print "updating ";
	    print_r($fields);
	    codes_updateObject($fields, 'photo');
		//print "<br/>after updating prev photo id $prev_photo_id seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
	
      }
      else {  // end current sequence
	    // at this point we want to check whether the previous photo was sequenced.  If no, then it is a single photo, not 
		// part of a burst.  So we want to give it a sequence_id but no prev or next photo.
		print "<br/><br/>Photos are not close so now on next sequence<br/>";
		$seq_num = 1; // as we're just starting a new sequence??
	    // Previous one was a loner if it was first in a sequence and this is not close enough
		// to be in same sequence OR if it was the first one and this is not close enough..
		if ( $prev_seq_num == 1 or $this_row == 2 ){  
		  print "<br/>Lone photo found: $prev_photo_id<br/>";
		  $fields = new StdClass();
	      $fields->start_photo_id = $prev_photo_id;
		  $fields->end_photo_id = $prev_photo_id;
		  $fields->sequence_length = 1;
	      $fields->upload_id = $upload_id;
	      $sequence_id = codes_insertObject($fields, 'sequence');
	      print "new sequence $sequence_id<br/>";
	      $seq_num = 1; // as we're just starting a new sequence
	      //print "<br/>end of adding lone photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
		  
		  canEdit( $prev_photo_id, 'photo', 1);
          //canEdit( $photo_id, 'photo' , 1);		
		  $fields = new StdClass();
	      $fields->sequence_id = $sequence_id;
	      $fields->sequence_num = 1;
	      $fields->photo_id = $prev_photo_id;
	      print "updating ";
	      print_r($fields);
	      codes_updateObject($fields, 'photo');
		  //print "<br/>end of updating lone photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
		  
		  // Finally, if this is the last photo of the upload and the previous one was a loner, then this one must be too!
		  if ($this_row == $num_results) {
			print "<br/>Final one in upload - lone photo found: $photo_id<br/>";
			$fields = new StdClass();
	        $fields->start_photo_id = $photo_id;
	        $fields->end_photo_id = $photo_id;
		    $fields->sequence_length = 1;
	        $fields->upload_id = $upload_id;
	        $sequence_id = codes_insertObject($fields, 'sequence');
	        print "new sequence $sequence_id<br/>";
	        //$prev_seq_num = 1;
	      
		    //canEdit( $prev_photo_id, 'photo', 1);
            canEdit( $photo_id, 'photo' , 1);
		
		    $fields = new StdClass();
	        $fields->sequence_id = $sequence_id;
	        $fields->sequence_num = 1;
	        $fields->photo_id = $photo_id;
	        print "updating ";
	        print_r($fields);
	        codes_updateObject($fields, 'photo'); 
		  }
	    }
		
	    $sequence_id = 0;
      }
    }
	$prev_photo_id = $photo_id;
    $prev_dateTime = $dateTime;
    $prev_seq_num = $seq_num;
	
	if ( $num_results === 1 ) {
		// Special case for when only one file is uploaded
		print "<br/>Only one file uploaded: $prev_photo_id<br/>";
		  $fields = new StdClass();
	      $fields->start_photo_id = $prev_photo_id;
		  $fields->end_photo_id = $prev_photo_id;
		  $fields->sequence_length = 1;
	      $fields->upload_id = $upload_id;
	      $sequence_id = codes_insertObject($fields, 'sequence');
	      print "new sequence $sequence_id<br/>";
	      $seq_num = 1; // as we're just starting a new sequence
	      //print "<br/>end of adding sole photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
		  
		  canEdit( $prev_photo_id, 'photo', 1);
          //canEdit( $photo_id, 'photo' , 1);		
		  $fields = new StdClass();
	      $fields->sequence_id = $sequence_id;
	      $fields->sequence_num = 1;
	      $fields->photo_id = $prev_photo_id;
	      print "updating photo ";
	      print_r($fields);
	      codes_updateObject($fields, 'photo');
		  //print "<br/>end of updating sole photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
		  
	}
    
  }
  
  // This is the catch all for the ones that have not been sequenced.  
  $query = $db->getQuery(true);
  $fields = array('sequence_id = -1','uploaded=uploaded');
  $conditions = array('upload_id = '.(int)$upload_id, 'sequence_id = 0');
  $query->update("Photo")
    ->set($fields)
    ->where($conditions);
  
  $db->setQuery($query);
  $db->execute();
  
}

function sampleSequences($project_id) {
	// To be added.  
	// Determine overall number of sequences required.
	// Calculate the percentage this will be.
	// Work through the sites in this project.
	// Check that all projects want sampling for this site, if not then skip.
	// Set all sequences to be unavailable, then set the required percentage to be available.
}

function addMsg($type, $msg){
  $app = JFactory::getApplication();
  $msgs = $app->getUserState("com_biodiv.msgs", noMsgs());
	
  $msgs[$type][] = $msg;
  $app->setUserState("com_biodiv.msgs", $msgs);
}

function noMsgs(){
  return array('success' => array(), 'warning' => array(), 'error' => array());
}

function getMsgs(){
  $app = JFactory::getApplication();
  $msgs = $app->getUserState("com_biodiv.msgs", noMsgs());
  return $msgs;
}

function someMsgs($type){
  $msgs = getMsgs();
  return count($msgs[$type]);
}

function showMessages(){
	
  $app = JFactory::getApplication();
  $msgs = $app->getUserState("com_biodiv.msgs", noMsgs());
  foreach(array('success', 'warning', 'error') as $type){
    if($type == 'error'){
      $class = "alert-danger";
    }
    else{
      $class = "alert-$type";
    }
    if(is_array($msgs[$type])){
      foreach($msgs[$type] as $msg){
	print "<div class='alert-dismissable $class'><p class='$class'><b>" . ucfirst($type). "</b> $msg</p></div>\n";
      }
    }
  }
  $app->setUserState("com_biodiv.msgs", noMsgs());
}

// Get all generic filters (eg Common, Mammals and Birds)
function getFilters () {
	
	$filters = codes_getList ( 'filter' );
	
	//print ( "<br> filters: <br>" );
	//print_r ( $filters );
		
	$returnFilters = array();
	
	foreach ($filters as $filter) {
		$returnFilters[$filter[0]] = array('label'=>$filter[1]);
	}
	
	return $returnFilters;
	
	//return array ( 210=>array("label"=>"Project"), 209=>array("label"=>"Common"), 207=>array("label"=>"Mammals"), 208=>array("label"=>"Birds" ) );
}

// Get project specific filters, either based on project_id or photo_id given
function getProjectFilters ( $project_id, $photo_id = null ) {
	
	//print "<br>getProjectFilters called, project_id = " . $project_id . ", photo_id = " . $photo_id;
	$langObject = JFactory::getLanguage();
	$lang = $langObject->getTag();
	
	// Check language is supported.  If not, default to English.
	if (!isLanguageSupported($lang)) $lang = "en-GB";
	
	
	$projectFilters = array();
	// First get the ENglish version
	if ( $project_id ) {
		// Find filter for this project
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("O.option_id, O.option_name")->from("Options O");
		$query->innerJoin("ProjectOptions PO on PO.option_id = O.option_id");
		$query->where("PO.project_id = " . $project_id );
		$query->where("O.struc = 'projectfilter'" );
		$query->order("O.seq");
		$db->setQuery($query);
		$projectFilters = $db->loadRowList();
		//print ( "<br> Project id used - projectFilters: <br>" );
		//print_r ( $projectFilters );
	}
	else if ( $photo_id ) {
		// Use the photo_id to find all projects we want filters for
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("O.option_id, O.option_name")->from("Options O");
		$query->innerJoin("ProjectOptions PO on PO.option_id = O.option_id");
		$query->innerJoin("Photo P on P.photo_id = ". $photo_id);
		$query->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id");
		$query->where("P.photo_id >= PSM.start_photo_id" );
		$query->where("PSM.end_photo_id is NULL" );
		$query->where("PSM.project_id = PO.project_id" );
		$query->where("O.struc = 'projectfilter'" );
		$query->order("O.seq");
		$db->setQuery($query);
		$projectFilters = $db->loadRowList();
		//print ( "<br> Photo id used - projectFilters: <br>" );
		//print_r ( $projectFilters );
	}
	// if we are not in English, use the translated filter names if they exist
	if ( $lang != "en-GB" ) {
		//foreach ($projectFilters as $filter) {
		for ( $i = 0; $i < count($projectFilters); $i++ ) {
			list($id, $name) = $projectFilters[$i];
			$translatedName = codes_getName($id, 'projectfiltertran');
			$projectFilters[$i][1] = $translatedName;
		}
	}
	//print ( "<br> projectFilters: <br>" );
	//print_r ( $projectFilters );
		
	$returnFilters = array();
	
	foreach ($projectFilters as $filter) {
		$returnFilters[$filter[0]] = array('label'=>$filter[1]);
	}
	
	return $returnFilters;
}

function extractLabel ( $v )
{
	return $v['label'];
}

function getFilterNames ( $filterArray ) {
	return array_map ( 'extractLabel', $filterArray );
}

function getFilterId ( $label, $filterArray ) {
	$filterNames = getFilterNames ( $filterArray );
	$labelKey = array_search ( $label, $filterNames );
	return $labelKey;
}

function cmp($a, $b)
{
	return strcmp($a[1], $b[1]);
}

function strucCmp($a, $b)
{
	// If it's mammal, put it first.  notinlist last. otherwise alphabetical.
	if ( $a == "mammal" ) {
		return -1;
	}
	else if ( $b == "mammal" ) {
		return 1;
	}
	else if ( $a == "notinlist" ) {
		return 1;
	}
	else if ( $b == "notinlist" ) {
		return -1;
	}
	else {
		return strcmp($a, $b);
	}
}
	
function getSpecies ( $filterid, $onePage ) {
	
	$speciesList = array();
	  
	//$species = codes_getList ( $filtername );
	$restrict = array();
	$restrict['restriction'] = "value = '" . $filterid . "'";
	//$species = codes_getList ( "filterspecies", $restrict );
	$species = codes_getList ( "filterspeciestran", $restrict );
	  
	// Need to sort this list by name.
	usort($species, "cmp");
	  
	// Sort the data 
	//array_multisort($name, SORT_ASC, $species);

	$features = array();
	$features['restriction'] = "struc='notinlist'";
	//$notInListSpecies = codes_getList ( "species", $features );
	$notInListSpecies = codes_getList ( "speciestran", $features );
	$species = array_merge($species, $notInListSpecies);
	foreach($species as $stuff){
		  
		list($id, $name) = $stuff;
	    //print ( "<br>classify view: species list - " . $id . ", " . $name . "<br>" );
	    $details = codes_getDetails($id, 'species');
		//print ( "details - <br>" );
		//print_r ( $details );
		
		// If we don't have a slot for this struc type yet, create one.
		if ( !in_array($details['struc'], array_keys($speciesList)) ) {
			//print "Creating array for " . $details['struc'];
			$speciesList[$details['struc']] = array();
		}
	    
	    $speciesList[$details['struc']][$id] = array("name" => $name,
					"type" => $details['struc'], // mammal or bird or notinlist
					"page" => $details['seq']);
					
		// For species to all fit on one page - we want them grouped as mammals (alphabetical), birds (alphabetical), notinlist (may want to change this to go to Mammal or Bird list?).
		if ( $onePage && ($details['struc'] == "mammal" or $details['struc'] == "bird") )
		{
			$speciesList[$details['struc']][$id]["page"] = 1;
		}
	}
	
	// Just make sure mammal is the first struc type...
	uksort($speciesList, "strucCmp");
	
	//print_r ( $speciesList );
		
	return $speciesList;
}

function makeControlButton($control_id, $control, $extraClasses=''){
  $disabled = strpos($control, "disabled");
  if($disabled !== false){
    $extras = array('disabled');
  }
  else{
    $extras = array('classify_control');
  }

  $confirm = strpos($control, "biodiv-confirm");

  if($confirm !== false){
    $extras[] = "biodiv-confirm";
  }

  $extraText = implode(" ", $extras);
  //print "<button type='button' class='btn btn-warning btn-block $extraText $extraClasses' id='$control_id'>$control</button>";
  print "<button type='button' class='btn btn-primary $extraText $extraClasses' id='$control_id'>$control</button>";
}




//$useSeq is flag if true uses page numbers given, if false, works pages out alphabetically
//if $largeButtons then use image buttons with larger size as for kiosk mode
function printSpeciesList ( $filterId, $speciesList, $useSeq=false, $largeButtons=false, $includeExtraControls = false, $extraControls = null ) {
	
	// Should store this in the Options table as a system option.
	$numPerPage = 36;
	if ( $largeButtons ) {
		$numPerPage = 36;
	}
	
	//print "<div id='carousel-species' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";

    $carouselItems = array(); // 2D array [page][item]
	$speciesCount = 0;
	foreach ($speciesList as $type=>$all_this_type) {
		foreach($all_this_type as $species_id => $species){
			//print "speciesCount = " . $speciesCount . "<br>";
			$page = $species['page'];
			// Any -1 pages should stay the same - notinlist 
			if ( !$useSeq and $page > 0 ) {
				//print "calculating page.. ";
				$page = intval($speciesCount/$numPerPage) + 1;
			}
			//print ( "page = " . $page . "<br>" );
			// Any page < -1 should be ignored.
			if ( $page < -1 ) continue;
			
			if(!in_array($page, array_keys($carouselItems))){
				//print "creating array for page " . $page . "<br>";
				$carouselItems[$page] = array();
			}
  
			$name = $species['name'];
			$isLongSpeciesName = false;
			if ( $largeButtons && strlen($name) > 12 ) $isLongSpeciesName = true;
			else if ( strlen($name) > 20 ) $isLongSpeciesName = true;
			
			$largeButtonImage = true;
			
			//print ( "name = " . $name . "<br>" );
			switch($species['type']){
			case 'mammal':
				$btnClass = 'btn-warning';
				break;

			case 'bird':  
				$btnClass = 'btn-info';
				break;

			case 'notinlist':
			/*
				if ( $largeButtons ) {
					$btnClass = 'btn-warning';
				}
				else {
					$btnClass = 'btn-primary';
				}
				$largeButtonImage = false;
			*/
				$btnClass = 'btn-primary';
				$largeButtonImage = false;
				break;
			}
	
			if ( $largeButtons ) {
				if ( $largeButtonImage ) {
					$image = codes_getName($species_id,'png');
					$imageText = "";
					if ( $image ) {
						$imageURL = JURI::root().$image;
						$imageText = "<div><img width='50px' src='".$imageURL."'></div>";
					}
					if ( $isLongSpeciesName ) {
						$carouselItems[$page][] =
						"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'>".$imageText."<div><div class='long-species-name'>$name</div></div></button>";
					
					}
					else {
						$carouselItems[$page][] =
						"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'>".$imageText."<div>$name</div></button>";
						//"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'><div><img width='50px' src='http://localhost/rhombus/images/thumbnails/Stoat.png'></div><div>$name</div></button>";
					}
				}
				else {
					$carouselItems[$page][] =
						"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'><div>$name</div></button>";
				
				}
			}
			else {
				$carouselItems[$page][] =
				"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn species_select' data-toggle='modal' data-target='#classify_modal'>$name</button>";
			}
			$speciesCount++;
		}
	}
	
	//print_r ( $carouselItems );

	// Determine how many pages of species we have - remember there will be a "-1" page for notinlist items, could be other - ones too which should be ignored
	$numPages = max(array_keys($carouselItems));
	//print "numPages = " . $numPages . "<br>";
	if ( $numPages > 1 ) {
		print "<ol id='species-indicators' class='carousel-indicators spb'>";
		for ( $i = 0; $i < $numPages; $i++ ) {
			//print "i = " . $i . ", numPages = " . $numPages . "<br>";
			if ( $i == 0 ) {
				print "<li title='' class='active spb' data-original-title='' data-target='#carousel-species-${filterId}' data-slide-to='" . $i . "'></li>";
			}
			else {
				print "<li title='' class='spb' data-original-title='' data-target='#carousel-species-${filterId}' data-slide-to='" . $i . "'></li>";
			}
		}
		print "</ol>";
	}

	//print_r ( $carouselItems[-1] );

	$adjust = "";
	if ( $numPages > 1 ) $adjust = " species-carousel-lower";
	print "<div id='species-carousel-inner' class='carousel-inner" . $adjust . "'>";

	foreach($carouselItems as $pageNum => $carouselPage){
		if($pageNum<0){
			continue;
		}
		
		// Count number of items to organise into columns
		$numSpeciesButtons = count($carouselPage);
  
		$numCols = 2;
		if ( $largeButtons ) {
			$numCols = 4;
		}
  
		$numRows = intval(($numSpeciesButtons + $numCols - 1)/$numCols);
		//print "numRows = " . $numRows . "<br>";
		//print "numCols = " . $numCols . "<br>";
  
		$cols = array();
  
		$carouselPageIndex = 0;
		
		// Read across for large buttons, down for small buttons.
		if ( $largeButtons ) {
			for ( $i = 0; $i < $numRows; $i++ ) {
				//print "col = ". $j . "<br>";
				$cols[] = array();
				for ( $j = 0; $j < $numCols; $j++ ) {
					//print "row = " . $i . "<br>";
					if ( $carouselPageIndex < count($carouselPage) ) {
						//print "setting next value to be " . $carouselPage[$carouselPageIndex] . "<br>";
						$cols[$j][] = $carouselPage[$carouselPageIndex];
						$carouselPageIndex++;
					}
				}
			}
		}
		else {
			for ( $j = 0; $j < $numCols; $j++ ) {
				//print "col = ". $j . "<br>";
				$cols[] = array();
				for ( $i = 0; $i < $numRows; $i++ ) {
					//print "row = " . $i . "<br>";
					if ( $carouselPageIndex < count($carouselPage) ) {
						//print "setting next value to be " . $carouselPage[$carouselPageIndex] . "<br>";
						$cols[$j][] = $carouselPage[$carouselPageIndex];
						$carouselPageIndex++;
					}
				}
			}
		}
  
		//print_r ( $cols );
  
		$active = ($pageNum==1)?" active":"";
		print "<div class='item $active'>\n";
	
		//print "Making buttons<br>";
		
		$columnClass = "col-md-6";
		if ( $largeButtons ) {
			$columnClass = "col-md-3";
		}
		print "<div class='row species-row'>";
		for ( $i = 0; $i < $numCols; $i++ ) {
			print "<div class='" . $columnClass . " species-carousel-col'>";
			//print "col = ". $i . " count = " . count($cols[$i]) . "<br>";
			for ( $j = 0; $j < $numRows; $j++ ) {
				if ( $i < count($cols) and $j < count($cols[$i]) ) {
					print $cols[$i][$j];			
				}
			}
			print "</div> <!-- /species-carousel-col -->\n";
		}
				
		print "</div> <!-- /species-row -->\n";
  
		// Separate row for notinlist items
		print "<div class='row species-row'>";
		
		$widthClass = 'col-md-6';
		if ( $largeButtons ) { $widthClass = 'col-md-3'; }
		foreach ( $carouselItems[-1] as $item ) {
				print "<div class='" . $widthClass . " species-carousel-col'>";
				print $item;
				print "</div>";
			}
		if ( $includeExtraControls and $extraControls ) {
			$extraClasses = '';
			if ( $largeButtons ) { 
				$extraClasses = 'btn-block species-btn-large';
			}
			foreach($extraControls as $control_id => $control){
				print "<div class='" . $widthClass . " species-carousel-col'>";
				makeControlButton($control_id, $control, $extraClasses);
				print "</div>";
			}
			
		}
		
		print "</div> <!-- /species-row -->\n";
		
		// Explicitly include these rows - needed for kiosk design - should be improved!
		/* moved to include on same row as DK and other
		if ( $includeExtraControls and $extraControls ) {
			print "<div class='row species-row'>";
			$extraClasses = '';
			if ( $largeButtons ) { 
				$extraClasses = 'btn-block species-btn-large';
			}
			foreach($extraControls as $control_id => $control){
				print "<div class='col-md-3 species-carousel-col'>";
				makeControlButton($control_id, $control, $extraClasses);
				print "</div>";
			}
			
			print "</div> <!-- /species-row -->\n";
		}
		*/
		print "</div> <!-- / item -->\n";

	}

	print "</div> <!-- /carousel-inner--> \n";

	print "<!-- Controls -->";
	if ( $numPages > 1 ) {
		print "<a class='left carousel-control species-carousel-control' href='#carousel-species-${filterId}' role='button' data-slide='prev'>";
		print "<span class='fa fa-chevron-left'></span>";
		print "</a>";
		print "<a class='right carousel-control species-carousel-control' href='#carousel-species-${filterId}' role='button' data-slide='next'>";
		print "<span class='fa fa-chevron-right'></span>";
		print "</a>";
	}

	//print "</div> <!-- /carousel-species carousel--> \n";
}

function getLogos ( $project_id ) {
	
	$logos = array();
	
	if ( $project_id ) {
		// Find logos for this project
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("OD.value")->from("OptionData OD");
		$query->innerJoin("ProjectOptions PO on PO.option_id = OD.option_id");
		$query->innerJoin("Options O on PO.option_id = O.option_id");
		$query->where("PO.project_id = " . $project_id );
		$query->where("OD.data_type = 'image'" );
		$query->order("O.seq");
		$db->setQuery($query);
		$logos = $db->loadColumn();
	}
	return $logos;
}

function getVideoMeta ( $filename ) {
	// Initialize getID3 engine
	$getID3 = new getID3;
	// Analyze file and store returned data in $ThisFileInfo
	$fileinfo = $getID3->analyze($filename);
	
	/*
	$videoMeta = Array();
	
	$creation_time_unix = $fileinfo['quicktime']['moov']['subatoms'][0]['creation_time_unix'];
	$creation_time = $fileinfo['quicktime']['moov']['subatoms'][0]['creation_time'];
	print_r ($creation_time_unix);
	print ("<br>As date (from Unix): " . date('Y-m-d', $creation_time_unix) . "<br>");
	print_r ($creation_time);
	print ("<br>As date: " . date('Y-m-d', $creation_time - 2082844800) . "<br>");
	*/
	return $fileinfo;

}

function getClassificationButton ( $id, $animalArray ) {
	$label = codes_getName($animalArray[$id]->species, 'contenttran');
     $contentDetails = codes_getDetails($animalArray[$id]->species, 'content');
     $type = $contentDetails['struc'];
     $features = array();
	 $nothingDisabled=false;
     if($type == 'like'){
       // do nothing
     }
     if($type == 'noanimal'){
       $btnClass = 'btn-primary';
	   if ( $animalArray[$id]->species != 86 ) {
		   $nothingDisabled = true;
	   }
	   else {
		   $btnClass .= ' nothing-classification';
	   }
	 }
     else if($type== 'notinlist'){
       $btnClass = 'btn-primary';
	   $nothingDisabled = true;
     }
     else{
       if($animalArray[$id]->number >1){
	     $features[] = $animalArray[$id]->number;
       }
       foreach(array("gender", "age") as $struc){
	     $featureName = codes_getName($animalArray[$id]->$struc, $struc . "tran");
	     if($featureName != "Unknown"){
	       $features[] = $featureName;
	     }
       }
       if ( $type == 'mammal' ) {
			$btnClass = 'btn-warning';
	   }
	   else {
		   $btnClass = 'btn-info';
	   }
       if(count($features) >0){
	     $label .= " (" . implode(",", $features) . ")";
       }
	   $nothingDisabled = true;
     }
	$retString = "<button id='remove_animal_". $id."' type='button' class='remove_animal btn $btnClass'>$label <span aria-hidden='true' class='fa fa-times-circle'></span><span class='sr-only'>Close</span></button>\n";
	if ( $nothingDisabled == true ) {
		$retString .= "<div id='nothingDisabled'></div>\n";
	}
	else {
		$retString .= "<div id='nothingEnabled'></div>\n";
	}
	return $retString;
}

function getSiteDataStrucs ( $projectIds ) {
	//print "getSiteDataStrucs called\n";
	//print_r ( $projectIds );
	$db = JDatabase::getInstance(dbOptions());
		
	$project_ids = implode(',', $projectIds);
	//print "project_ids = " . $project_ids;
	$query = $db->getQuery(true);
	$query->select("DISTINCT PO.project_id, O.option_id, O.option_name as struc")
		->from("ProjectOptions PO")
		->innerJoin("Options O on O.option_id = PO.option_id and O.struc = 'sitedatastruc'")
		->where("PO.project_id in (" . $project_ids . ")");
	$db->setQuery($query);
	$sitedatastrucs = $db->loadAssocList();
	//print_r($sitedatastrucs);
	return $sitedatastrucs;
}

function isLanguageSupported ( $lang ) {
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true);
	$query->select("count(*)")
		->from("Language")
		->where("tag = " . $db->quote($lang) );
	$db->setQuery($query);
	$count = $db->loadResult();
	
	//error_log ( "count = " . $count );
	
	return $count > 0;
}

function getTranslations ( $view ) {
	
	$db = JDatabase::getInstance(dbOptions());
	$langObject = JFactory::getLanguage();
	$lang = $langObject->getTag();
	
	error_log("language = " . $lang);
	
	// Check language is supported.  If not, default to English.
	if (!isLanguageSupported($lang)) $lang = "en-GB";
	
	error_log ("using language " . $lang );
	$query = $db->getQuery(true);
	$query->select("translation_key, translation_text")
		->from("Translation")
		->where("view = " . $db->quote($view))
		->where("language = " . $db->quote($lang));
	$db->setQuery($query);
	$translations = $db->loadAssocList("translation_key");
	
	return $translations;
}

function getOrdinal ( $num ) {
	
	$langObject = JFactory::getLanguage();
	$lang = $langObject->getTag();
	$th = "";
	
	if ( $lang == "en-GB" ) {
		$th = 'th';
	
		$finalDigit = $num - 10*intval($num/10);
		if ( $finalDigit == 1 ) $th = 'st';
		if ( $finalDigit == 2 ) $th = 'nd';
		if ( $finalDigit == 3 ) $th = 'rd';
		
		// Set back to th for 11, 12 and 13, 111, 112, 113, etc
		if ( ($num - 11)%100 == 0 ) $th = 'th';
		if ( ($num - 12)%100 == 0 ) $th = 'th';
		if ( ($num - 13)%100 == 0 ) $th = 'th';
	}
	else if ( $lang == "es-ES" ) {
		$th = 'º';
	
		$finalDigit = $num - 10*intval($num/10);
		if ( $finalDigit == 1 ) $th = 'er';
		// may need this for tercer   if ( $finalDigit == 3 ) $th = 'er';
	}
	return ( "" . $num . $th );
}

function getAssociatedArticleId ( $article_id ) {
	
	$langObject = JFactory::getLanguage();
	$lang = $langObject->getTag();
	
	$assoc_id = null;

	if ($article_id != null)
	{
		$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $article_id);
		
		//print_r($associations);
		$assoc_id = $associations[$lang]->id;
	}
	
	// If no associated article (for this language) return the original one
	if ( $assoc_id == null ) $assoc_id = $article_id;
	
	return $assoc_id;
}


function getSiteLocation($site_id) {
	
	$siteDetails = codes_getDetails($site_id, 'site');	
	return new Location($siteDetails['latitude'], $siteDetails['longitude']);	

}


/*
function makeControlButton($control_id, $control){
  $disabled = strpos($control, "disabled");
  if($disabled !== false){
    $extras = array('disabled');
  }
  else{
    $extras = array('classify_control');
  }

  $confirm = strpos($control, "biodiv-confirm");

  if($confirm !== false){
    $extras[] = "biodiv-confirm";
  }

  $extraText = implode(" ", $extras);
  print "<button type='button' class='btn btn-primary $extraText' id='$control_id'>$control</button>";
}
*/

// Get an instance of the controller prefixed by BioDiv
$controller = JControllerLegacy::getInstance('BioDiv');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
?>