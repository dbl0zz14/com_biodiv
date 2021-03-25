<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// Needed to ensure pick up the correct component files
set_include_path(JPATH_COMPONENT . get_include_path());

include_once "local.php";
include_once "Project.php";
include_once "Location.php";
include_once "Sequence.php";
include_once "MediaCarousel.php";
include_once "SpeciesCarousel.php";
include_once "SiteHelper.php";
include_once "BiodivHelper.php";
include_once "BiodivFFMpeg.php";
include_once "BiodivFile.php";
include_once "BiodivSurvey.php";
include_once "BiodivReport.php";


define('BIODIV_MAX_FILE_SIZE', 50000000);

// link to javascript stuff
$document = JFactory::getDocument();
$document->addScriptDeclaration('
var BioDiv = {};
BioDiv.root = "'. BIODIV_ROOT . '";');

JHtml::_('bootstrap.framework');
JHTML::stylesheet("bootstrap3-editable/bootstrap-editable.css", array(), true);
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

function userTimezone() {
  $user = JFactory::getUser();
  if($user->guest){
    return null;
  }
  else{
    return $user->getTimezone();
  }
}


$dbOptions = dbOptions();
codes_parameters_addTable("StructureMeta", $dbOptions['database']);



function codes_insertObject($fields, $struc){
	if(!canCreate($struc, $fields)){
	print "Cannot create $struc";
		return false;
	}
  
	$success = false;
	$db = JDatabase::getInstance(dbOptions());
  
	// photo is handled as special case as split over two tables, as is origfile
	if ( ($struc == "photo") || ($struc == "splitaudio") || ($struc == "origfile") ) {
		// Move and store the exif field as this is in second table
		$exif = $fields->exif;
		unset($fields->exif);
	  
		// Use a transaction as we want to update two tables, and need 
		// both these operations to succeed or fail.
		try
		{
			$db->transactionStart();
			
			$table = codes_getTable($struc);
			$id = null;

			$success = $db->insertObject($table, $fields);
			
			if($success){
				$id = $db->insertid();
			}
			else{
				print "Insert failed";
			}
			
			if ( $success ) {
				$exifFields = new stdClass();
				$exifFields->exif = $exif;
				if ( $struc == "origfile" ) {
					$table = "OriginalFilesExif" ;
					$exifFields->of_id = $id;
				}
				else {
					$table = "PhotoExif";
					$exifFields->photo_id = $id;
				
				}

				$success = $db->insertObject($table, $exifFields);
				if($success){
					print "Insert succeeded";
					error_log("Insert succeeded, id = " . $id );
				}
				else{
					print "Insert failed";
					error_log("Photo insert failed due to " . $e);
					$db->transactionRollback();
					$success = false;
				}
			}
		
			$db->transactionCommit();
			
			return $id;
		}
		catch (Exception $e)
		{
			error_log("Photo insert failed due to " . $e);
			
			// catch any database errors.
			$db->transactionRollback();
			
			$success = false;
		}
	  
	}
	else {
		$table = codes_getTable($struc);
		
		error_log ( "codes_insertObject, struc = " . $struc . ", got table " . $table );

		$success = $db->insertObject($table, $fields);
		if($success){
			$id = $db->insertid();
			return $id;
		}
		else{
			error_log ("Insert failed for struc " . $struc );
			print "Insert failed";
		}
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
	
	$lang = langTag();
	
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

function langTag() {
	
	$langObject = JFactory::getLanguage();
	$lang = $langObject->getTag();
	
	if (!isLanguageSupported($lang)) $lang = "en-GB";
	
	return $lang;
}

function getSetting ( $key ) {
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true);
	$query->select("s_value")->from("Settings")
		->where("s_key = " . $db->quote($key) );
	$db->setQuery($query);
	$value = $db->loadResult();
	
	return $value;
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
  case "help":
    return "<i class='fa fa-question'></i> $str $what";
    break;
  case "list":
    return "<i class='fa fa-list'></i> $str $what";
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
	 if($struc=="sequence" || $struc=="splitaudio"){
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
  case 'origfile':
  case 'report':
    return $fields->person_id == userID();   
    break;

  case 'sequence':
  case 'splitaudio':
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
    //return "/var/www/html/biodivimages";
	return JPATH_SITE."/biodivimages";
}

function reportRoot(){
//  return JPATH_COMPONENT . "/uploads";
    //return "/var/www/html/biodivimages";
	return JPATH_SITE."/biodivimages/reports";
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
	if ( strpos(strtolower($filename), '.avi') !== false ) {
		return true;
	}
	return false;
}

function isAudio($photo_id) {
	$details = codes_getDetails($photo_id, 'photo');
	$filename = $details['filename'];
	if ( strpos(strtolower($filename), '.mp3') !== false ) {
		return true;
	}
	if ( strpos(strtolower($filename), '.m4a') !== false ) {
		return true;
	}
	if ( strpos(strtolower($filename), '.wav') !== false ) {
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

function waveURL($photo_id){
  $details = codes_getDetails($photo_id, 'photo');
  if ( $details['s3_status'] == 1 ) {
	  // File has been transferred to s3 so get AWS S3 url
	  return s3WaveURL($details);
  }
  else {
	  // debug
	  // echo siteURL($details['site_id']) . "/". $details['filename'];
	  // debug end
	  $wavefilename = JFile::stripExt($details['filename']) . "_wave.png";
			
	  return siteURL($details['site_id']) . "/". $wavefilename;
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

function imageURL($option_id) {
  
  	// Find logos for this project
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("OD.value")->from("OptionData OD")
		->innerJoin("Options O on O.option_id = OD.option_id and O.option_id = " . $option_id )
		->where("OD.data_type = 'image'" );
		
	$db->setQuery($query);
	$image = $db->loadResult();

	return JURI::root().$image;
}

function getOptionData($option_id, $data_type) {
  
  	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("OD.value")->from("OptionData OD")
		//->where("OD.data_type = " . $db->quoteName($data_type) )
		->where("OD.data_type = '" . $data_type . "'"  )
		->where("OD.option_id = " . $option_id );
		
	$db->setQuery($query);
	$option_data = $db->loadColumn();
	
	$err_msg = print_r ( $option_data, true );
	error_log ( "getOptionData id= " . $option_id . ", type = " . $data_type . ", result = " . $err_msg );
	return $option_data;

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
  
  //$err_str = print_r ($myprojects, true);
  //error_log("myTrappingProjects myprojects = " . $err_str );
  
  //print "<br/>Got " . count($myprojects) . " all projects user has access to<br/>They are:<br>";
  //print implode(",", $myprojects);
  /*
  usort($myprojects, function ($a, $b) {
	  $err_str = print_r ($a, true);
	  error_log("myTrappingProjects usort a = " . $err_str);
	  $err_str = print_r ($b, true);
	  error_log("myTrappingProjects usort b = " . $err_str);
	  
      return $a['proj_prettyname'] - $b['proj_prettyname'];
	}
  );
  */
  asort($myprojects);
  
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

function myAdminProjects () {
	
	$person_id = (int)userID();
  
	// for now, user must be specifically admin for all subprojects so the select is simple
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("P.project_id as project_id, P.project_prettyname as project_name")->from("Project P")
		->innerJoin("ProjectUserMap PUM on P.project_id = PUM.project_id")
		->where("PUM.role_id = 1")
		->where("PUM.person_id = " . $person_id)
		->order("P.project_prettyname");
	$db->setQuery($query);
	$projects = $db->loadAssocList();
	
	return $projects;
  
}

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


function getTrapperStatistics () {
	
	// Get all the text snippets for trapperdash view
	$translations = getTranslations("dashtrapper");

	$personId = (int)userID();
	
	$db = JDatabase::getInstance(dbOptions());
	
	$statRows = array();

	$query = $db->getQuery(true);
    $query->select("count(*) ")
		->from("Site");
	$db->setQuery($query); 
	$numSites = $db->loadResult();
	
	array_push( $statRows, array($translations['tot_sites']['translation_text'], $numSites) );
	
	$query = $db->getQuery(true);
    $query->select("count(*) ")
		->from("Site")
		->where("person_id = " . $personId);
	$db->setQuery($query); 
	$numSites = $db->loadResult();
	
	array_push( $statRows, array($translations['your_sites']['translation_text'], $numSites) );
	
	$query = $db->getQuery(true);
    $query->select("end_date, num_uploaded as uploaded, num_classified as classified ")
		->from("Statistics")
		->where("project_id = 0")
		->order("end_date DESC");
	$db->setQuery($query, 0, 1); // LIMIT 1
	$row = $db->loadAssoc();
	
	array_push( $statRows, array($translations['tot_seq']['translation_text'], $row['uploaded']) );
	$query = $db->getQuery(true);
    
	
	$endDate = date('Ymd', strtotime("last day of this month"));
	$query->select("sum(num_uploaded) ")
		->from("SiteStatistics SS")
		->innerJoin("Site S on S.site_id = SS.site_id and SS.end_date = " . $endDate)
		->where("S.person_id = " . $personId);
		
	$db->setQuery($query); 
	
	error_log("Seq select query created: " . $query->dump());
	
	$mySeqs = $db->loadResult();
	
	$query = $db->getQuery(true);
    $query->select("sum(num_uploaded) ")
		->from("PrivateSiteStatistics SS")
		->innerJoin("Site S on S.site_id = SS.site_id and SS.end_date = " . $endDate)
		->where("S.person_id = " . $personId);
		
	$db->setQuery($query); 
	
	error_log("Seq select query created: " . $query->dump());
	
	$myPrivateSeqs = $db->loadResult();
	
	
	array_push( $statRows, array($translations['your_seq']['translation_text'], $mySeqs+$myPrivateSeqs) );
	
	

		
	return $statRows;
}	
	
	
function getSpotterStatistics () {
	
	// Get all the text snippets for status view
	$translations = getTranslations("status");

	$db = JDatabase::getInstance(dbOptions());
	
	$statRows = array();

	$query = $db->getQuery(true);
    $query->select("end_date, num_uploaded as uploaded, num_classified as classified ")
		->from("Statistics")
		->where("project_id = 0")
		->order("end_date DESC");
	$db->setQuery($query, 0, 1); // LIMIT 1
	$row = $db->loadAssoc();
	
	array_push( $statRows, array($translations['tot_system']['translation_text'], $row['uploaded']) );
	array_push( $statRows, array($translations['tot_class']['translation_text'], $row['classified']) );
	
	
	$query = $db->getQuery(true);
    $query->select("person_id, num_classified")
		->from("LeagueTable")
		->order("num_classified desc");
    $db->setQuery($query);
	
    $leagueTable = $db->loadAssocList();
	
	$lTable = array_column($leagueTable, 'person_id');
	$tSpotters = count($lTable);
	
	$personId = (int)userID();
	$userPos = array_search ( $personId, $lTable );
	
	if ( $userPos === False  ) {
		//print ( "Not in league table" );
		$userPos = count($lTable);
		$tSpotters += 1;
		
		array_push( $statRows, array($translations['num_you']['translation_text'], 0) );
	
	}
	else {
		array_push( $statRows, array($translations['num_you']['translation_text'], $leagueTable[$userPos]['num_classified']) );
	}
	
	array_push( $statRows, array($translations['tot_spot']['translation_text'], $tSpotters) );
	array_push( $statRows, array($translations['you_curr']['translation_text'] . ' ' . getOrdinal($userPos + 1) . ' ' . $translations['contrib']['translation_text'], '') );
		
	return $statRows;
}	
	
	
// Use the helper class to get the expertise details
function userExpertise( $person_id ) {
	
	$helper = new BiodivHelper();
  
	return $helper->userExpertise($person_id);
}


// Calculate user expertise based on gold standard sequences
function calculateUserExpertise () {
	
	$db = JDatabase::getInstance(dbOptions());
	
	// Get all users who have classified (exclude likes)
	$query = $db->getQuery(true)
		->select("distinct person_id from Animal")
		->where("species != 97" );
		
	$db->setQuery($query);
	
	$users = $db->loadColumn();
	
	// Get all the calctopics that are included
	$query = $db->getQuery(true)
		->select("distinct O.option_id from Options O")
		->innerJoin("OptionData OD on O.option_id = OD.option_id and O.struc = 'calctopic'")
		->where("OD.data_type = 'include' and OD.value = 'yes'" );
		
	$db->setQuery($query);
	
	$topics = $db->loadColumn();
	
	print("<br>Topics:<br>");
	print_r($topics);
	
	// Allow for >1 topics
	foreach ($topics as $topic_id ) {
		// For each user, get all their classifications which match an expert sequence
		// NB we are not taking number into account 
		foreach ($users as $user ) {
			
			print("<br><br>Calculating expertise of user " . $user );
			
			$query = $db->getQuery(true)
				->select("distinct P.sequence_id, A.species from Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id")
				->innerJoin("OptionData OD on OD.value = P.sequence_id and OD.data_type = 'sequence'")
				->where("OD.option_id = " . $topic_id)
				->where("A.species != 97" )
				->where("A.person_id = " . $user)
				->order("P.sequence_id, A.species");
				
			$db->setQuery($query);
		
			$animals = $db->loadAssocList();
			
			if ( count($animals) > 0 ) {
			
				$user_seqs = array_unique(array_map(function ($a) { return $a['sequence_id']; }, $animals));
			
				print("<br>User animals<br>");
				print_r($animals);
				print("<br>User sequences<br>");
				print_r($user_seqs);
				print("<br>Sequence string = " . implode(',',$user_seqs) . "<br>");
				// Get the relevant expert sequences
				$query = $db->getQuery(true)
					->select("distinct sequence_id, species_id from ExpertSequences where sequence_id in (" . implode(',',$user_seqs) . ")" )
					->order("sequence_id, species_id");
					
				$db->setQuery($query);
			
				$expert_animals = $db->loadAssocList();
				
				print("<br>Expert animals<br>");
				print_r($expert_animals);
				
				$total_expert = count($expert_animals);
				$score = 0;
				$user_index = 0;
				$prev_seq = null;
				$seq_score = 0;
				// For each classification, check whether it's correct.
				foreach ( $expert_animals as $expert_animal ) {		
				
					$seq = $expert_animal['sequence_id'];
					$species = $expert_animal['species_id'];
					
					print("<br>Expert classification: " . $seq . ", " . $species );
					
					if ( $seq != $prev_seq ) {
						print("<br>New sequence: " . $seq );
						
						$animal = $animals[$user_index];
						print("<br>Next user classification: " . $animal['sequence_id'] . ", " . $animal['species'] );
						
						// Is the user still on the previous sequence?
						while ( $animal['sequence_id'] == $prev_seq) {
							// User must have more classifications than expert, so prev seq was not correctly classified
							// Wind	forward until pass that sequence
							$seq_score = 0;
							$user_index += 1;
							print("<br>user index = " . $user_index);
							$animal = $animals[$user_index];
						
						}
						
						$score += $seq_score;
						$seq_score = 0;
						if ( $animal['sequence_id'] == $seq ) {
							// User now has a new sequence which matches, can update score with prev sequence
							print ( "<br>Sequence matches");
							if ( $animal['species'] == $species ) {
								print("<br>Species matches");
								// species matches
								$seq_score = 1;
								$user_index += 1;
							}
						}
						$prev_seq = $seq;
					}
					else {
						print("<br>Same sequence, new classification: " . $species);
						
						// Only need to check if user is correct so far
						if ( $seq_score == 1 ) {
							
							// Check we're not finished with user classifications
							if ( $user_index < count($animals) ) {
						
								$animal = $animals[$user_index];
								print("<br>Next user classification: " . $animal['sequence_id'] . ", " . $animal['species'] );
								
								if ( ($animal['sequence_id'] != $seq) || ($animal['species'] != $species) ) { 
									print ( "<br>Sequence and species don't both match" );
									$seq_score = 0;
								}
								else { 
									print ( "<br>Sequence and species match");
									// both correct - move user index on
									$user_index += 1;
								}
							}
							else {
								print("<br>Missing a classification");
								// Missing a classification so incorrect
								$seq_score = 0;
							}
						}
					}
					
				}
				// Add the score for the last sequence
				$score += $seq_score;
				
				$score_percent = 100 * $score/count($user_seqs);
				print("<br>User score = " . $score_percent);
				
				// Do we need to update or delete
				$query = $db->getQuery(true)
					->select("ue_id from UserExpertise")
					->where("person_id = " . $user )
					->where("topic_id = " . $topic_id );
					
				$db->setQuery($query);
				
				$ue_id = $db->loadResult();
				
				if ( $ue_id != null ) {
					$fields = new stdClass();
					$fields->ue_id = $ue_id;
					$fields->num_sequences = count($user_seqs);
					$fields->score = $score_percent;
					$success = $db->updateObject('UserExpertise', $fields, 'ue_id');
					if(!$success){
						error_log ( "UserExpertise update failed" );
					}
				}
				else {
					$fields = new StdClass();
					$fields->person_id = $user;
					$fields->topic_id = $topic_id;
					$fields->num_sequences = count($user_seqs);
					$fields->score = $score_percent;
					$success = $db->insertObject("UserExpertise", $fields);
					if(!$success){
						error_log ( "UserExpertise insert failed" );
					}
				}
				
			}
			else {
				print("<br>User has not classified any gold standard sequences for topic " . $topic_id);
			}
		}	
	}
}

// Calculate the number of sequences uploaded and the number of sequences with at least one classification for every site for end date given or end of this month
// Keep it simple by removing all enries for this end date then insert new.
// Miss out any sites which have ever been in a private project.
function calculateSiteStats ( $end_date = null ) {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$endDatePlus1 = strtotime("first day of next month" );
	$endDate = date('Ymd', strtotime("last day of this month"));
	if ( $end_date ) {
		$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
		$endDate = date('Ymd', strtotime($end_date));
	}
	
	$dateToUse = date('Ymd', $endDatePlus1);
	
	$query = $db->getQuery(true)
		->select("site_id, count(distinct sequence_id) as num from Photo P")
		->where("uploaded < " . $dateToUse )
		->where('site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
		->group("site_id");
		
	$db->setQuery($query);
	
	$uploaded = $db->loadAssocList("site_id", "num");
	
	// Handle pric=ate sites - this data goes in a different table
	$query = $db->getQuery(true)
		->select("site_id, count(distinct sequence_id) as num from Photo P")
		->where("uploaded < " . $dateToUse )
		->where('site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
		->group("site_id");
		
	$db->setQuery($query);
	
	$privateUploaded = $db->loadAssocList("site_id", "num");
	
	$query = $db->getQuery(true)
		->select("P.site_id, count(distinct P.sequence_id) as num from Photo P")
		->innerJoin("Animal A on A.photo_id = P.photo_id")
		->where("A.species != 97")
		->where("A.timestamp < " . $dateToUse )
		->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
		->group("site_id");
		
	$db->setQuery($query);
	
	$classified = $db->loadAssocList("site_id", "num");
	
	// Handle private sites
	$query = $db->getQuery(true)
		->select("P.site_id, count(distinct P.sequence_id) as num from Photo P")
		->innerJoin("Animal A on A.photo_id = P.photo_id")
		->where("A.species != 97")
		->where("A.timestamp < " . $dateToUse )
		->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
		->group("site_id");
		
	$db->setQuery($query);
	
	$privateClassified = $db->loadAssocList("site_id", "num");
	
	$query = $db->getQuery(true)
		->delete("SiteStatistics")
		->where("end_date = " . $endDate);
		
	$db->setQuery($query);
	$result = $db->execute();
	
	// Insert using the uploaded sites.
	foreach ($uploaded as $site_id=>$numLoaded) {
		$query = $db->getQuery(true)
			->insert("SiteStatistics")
			->columns($db->quoteName(array('site_id', 'end_date', 'num_uploaded')))
			->values("" . $site_id . ", '" . $endDate . "', " . $numLoaded  );
			
		$db->setQuery($query);
		$result = $db->execute();
	}
	
	// And update with the num classified
	foreach ($classified as $site_id=>$numClassified) {
		$query = $db->getQuery(true)
			->update("SiteStatistics")
			->set("num_classified = " . $numClassified )
			->where("site_id = " . $site_id)
			->where("end_date = '" . $endDate . "'" );
			
		$db->setQuery($query);
		$result = $db->execute();
	}
	
	// Do similar for private sites
	$query = $db->getQuery(true)
		->delete("PrivateSiteStatistics")
		->where("end_date = " . $endDate);
		
	$db->setQuery($query);
	$result = $db->execute();
	
	// Insert using the uploaded sites.
	foreach ($privateUploaded as $site_id=>$numLoaded) {
		$query = $db->getQuery(true)
			->insert("PrivateSiteStatistics")
			->columns($db->quoteName(array('site_id', 'end_date', 'num_uploaded')))
			->values("" . $site_id . ", '" . $endDate . "', " . $numLoaded  );
			
		$db->setQuery($query);
		$result = $db->execute();
	}
	
	// And update with the num classified
	foreach ($privateClassified as $site_id=>$numClassified) {
		$query = $db->getQuery(true)
			->update("PrivateSiteStatistics")
			->set("num_classified = " . $numClassified )
			->where("site_id = " . $site_id)
			->where("end_date = '" . $endDate . "'" );
			
		$db->setQuery($query);
		$result = $db->execute();
	}
}

// Recalculate site statistics for all sites
// Use with care this will overwrite existing figures.
function calculateSiteStatsHistory ( $num_months = null) {
	
	$endDate = strtotime("last day of this month");
	$endDatePlus1 = strtotime("first day of next month" );

	$datesArray = array();
	
	// Default to 3 years of data.
	$numDisplayedMonths = 13;
	if ( $num_months ) {
		$numDisplayedMonths = $num_months + 3;
	}
  
	for ( $i=$numDisplayedMonths; $i>=0; $i-- ) {
		$minusMonths = "-" . $i . " months";
		$firstOfMonth = strtotime($minusMonths, $endDatePlus1);
		print("<br>Calculating for end date " . date('Ymd', strtotime("-1 day", $firstOfMonth)) );
		calculateSiteStats(date('Ymd', strtotime("-1 day", $firstOfMonth)) );
	}
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


// Update the SiteAnimals table with count of sightings by site and species where a sighting is at least one classification on a sequence
function calculateSiteAnimalStatistics () {

  $db = JDatabase::getInstance(dbOptions());
  
  print "<br>Inserting new data";
  
  $query = $db->getQuery(true);
  
  // Call the stored procedure to calculate the stats and populate the SiteAnimals table.
  $query->call("CalculateSiteAnimals");
  $db->setQuery($query);
  $result = $db->execute();
  
  // Truncate the FeatureSites table as we're about to recalculate 
  $db->truncateTable('FeatureSites');
  
  // get all the sites we are displaying data for
  // Don't include any which have ever been in a private project
  $query = $db->getQuery(true)
             ->select('S.site_id as site_id, S.latitude as lat, S.longitude as lon')
			 ->from('Site S')
			 ->where('S.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
  $db->setQuery($query);		 
  $sites = $db->loadAssocList('site_id');
  
    // For each site, work out which feature it is in and store in the FeatureSites table
	// Currently only have display_type "site" but this could be extended for different zoom levels or other types of display eg by country
  foreach ($sites as $site_id=>$coords) {
	  print ("<br>site_id: " . $site_id . "coords: " . $coords['lat'] . "," . $coords['lon'] );
	  
	  $query = $db->getQuery(true)
            ->select("F.feature_id")
			->from("Features F")
			->where("(".$coords['lon']." >= F.west and " . $coords['lon'] . " < F.east)")
			->where("(".$coords['lat']." >= F.south and " . $coords['lat'] . " < F.north)")
			->where("F.display_type = 'site'");
			 
	  $db->setQuery($query);		 
	  $feature_ids = $db->loadColumn();
	  
	  $num_features = count($feature_ids);
	  $feature_site_id = null;
	  
	  if ( $num_features == 0 ) {
		  
		  print("No existing feature for site,creating new one");
		  
		   // Use Location class to get ewns
		  $loc = new Location($coords['lat'],$coords['lon']);
		  $e = $loc->getEast();
		  $w = $loc->getWest();
		  $s = $loc->getSouth();
		  $n = $loc->getNorth();
		  
		  /*
		  $query = $db->getQuery(true)
			->insert("Features")
			->columns($db->quoteName(array('east', 'west', 'south', 'north', 'display_type')))
			->values("" . $e . ", " . $w . ", " . $s . ", " . $n . ", 'site'" );
		  $db->setQuery($query);
		  $feature_site_id = $db->execute();
		  */
		  
		  // Create and populate an object.
		  $feature = new stdClass();
		  $feature->feature_id = null;
		  $feature->east = $e;
		  $feature->west = $w;
		  $feature->south = $s;
		  $feature->north = $n;
		  $feature->display_type = 'site';
		  
		  // Insert the object into the Features table.
		  $result = $db->insertObject('Features', $feature, 'feature_id');
		  
		  $feature_site_id = $feature->feature_id;
		  
		  print ( "<br>Inserted new Feature " . $feature_site_id );
	  }
	  else if ( $num_features > 1 ) {
		  print("<br> Site " . $site_id . " appears in more than one site feature!  Just using first in stats." );
		  print_r($feature_ids);
		  $feature_site_id = $feature_ids[0];
	  }
	  else {
		  $feature_site_id = $feature_ids[0];
	  }
	  
	  // Now can insert into FeatureSites
	  print ( "<br>inserting " . $feature_site_id . ", " . $site_id);
	
	  $query = $db->getQuery(true)
		->insert("FeatureSites")
		->columns($db->quoteName(array('feature_id', 'site_id')))
		->values("" . $feature_site_id . ", " . $site_id );
	  $db->setQuery($query);
	  $result = $db->execute();

  }
  
  print "<br>Complete";
  
  
}


function getFeatureSpecies() {
	
	$db = JDatabase::getInstance(dbOptions());
	  
	// return all species which appear in the SiteAnimals table
	$query = $db->getQuery(true)
		->select("distinct O.option_name as name, SA.species as id FROM SiteAnimals SA")
		->innerJoin("Options O on O.option_id = SA.species")
		->order("name");
		
	$db->setQuery($query);
	$fs = $db->loadAssocList("id", "name");
	
	//Update to be the name in correct language
	foreach ( $fs as $id=>$name ) {
		error_log("Updating $fs[$id]");
		$tr_name = codes_getOptionTranslation ( $id );
		$fs[$id] = $tr_name;
		error_log(" new name = " . $tr_name . ", in array: " . $fs[$id]);
	}
	
	// Reorder list after translating
	asort($fs);
	
	return $fs;
}


// Return all sites which are in the FeatureSites table - ir they are within a feature and in a non-private project
function discoverSites () {
	
	error_log("discoverSites");
	
	// Get all the text snippets for this view in the current language
	$translations = getTranslations("discover");
	 
	$sites = $translations["sites"]["translation_text"];
	  
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	  
	// Want to get a count of sites for each feature
	$query->select("F.feature_id, F.west, F.east, F.south, F.north, count(FS.site_id) as num_sites from FeatureSites FS")
		->innerJoin("Features F on FS.feature_id = F.feature_id and F.display_type = 'site'")
		->group("F.feature_id");

	$db->setQuery($query);
	  
	// Include id...
	$sites_by_feature = $db->loadAssocList();
	  
	$features = array();
	foreach ( $sites_by_feature as $row ) {
		  $fid = "" . $row["feature_id"];
		  $num = $row["num_sites"];
		  $e = $row["east"];
		  $w = $row["west"];
		  $s = $row["south"];
		  $n = $row["north"];
		  error_log( "row: " . $fid . ", " . $num );
		  
		  // New feature each row as summed over years.
		  error_log( "adding new feature " . $fid . ", " . $num );
		  $features[$fid] = array();
		  $features[$fid]["type"] = "Feature";
		  $features[$fid]["properties"] = array();
		  $features[$fid]["properties"]["stroke"] = false;
		  $features[$fid]["properties"]["site_count"] = $num;
		  $features[$fid]["geometry"] = array();
		  $features[$fid]["geometry"]["type"] = "Polygon";
		  $features[$fid]["geometry"]["coordinates"] = [[[$e,$s],[$w,$s],[$w,$n],[$e,$n],[$e,$s]]];
		  error_log( "added new feature " . $fid . ", " . $num );
		  
	}
	  
	return array (
			"features" => array_values($features),
			"sites" => $sites
			);
}

// Return some animal sightings data for the sites within this area
function discoverData ( $lat_start, $lat_end, $lon_start, $lon_end, $num_months = 12  ) {
	
	error_log("discoverData(" . $lat_start . ", ". $lat_end . ", ". $lon_start . ", ". $lon_end . ")");
	
	// Get all the text snippets for this view in the current language
	$translations = getTranslations("discover");
	  
	  
	//$coords = "(".$lat_start.$translations["s"]["translation_text"].",".$lon_start.$translations["w"]["translation_text"]." to ";
	//$coords .= $lat_end.$translations["n"]["translation_text"].",".$lon_end.$translations["e"]["translation_text"] . ")";
	$coords = $translations["lat"]["translation_text"]. " " . $lat_start . " " . $translations["to"]["translation_text"] . " " . $lat_end . ", ";
	$coords .= $translations["lon"]["translation_text"]. " " . $lon_start . " " . $translations["to"]["translation_text"] . " " . $lon_end ;
	$title = $translations["up_class"]["translation_text"] . " " . $coords ;
	
	//$numDisplayedMonths = 6;
	$interval_in_months = 1;
    $numDisplayedMonths = $num_months + $interval_in_months;
	$end_date = null;
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
  
	$uploadedArray = array();
	$classifiedArray = array();
	$db = JDatabase::getInstance(dbOptions());
  
	for ( $j=0; $j<$numIntervals; $j++ ) {
		$query = $db->getQuery(true)
			->select("sum(num_uploaded), sum(num_classified) from SiteStatistics SS")
			->innerJoin("Site S on SS.site_id = S.site_id")
			->where("S.latitude >= " . $lat_start . " and S.latitude < " . $lat_end )
			->where("S.longitude >= " . $lon_start . " and S.longitude < " . $lon_end )
			->where("end_date = " . $datesArray[$j+1] );
		$db->setQuery($query);
	
		$row = $db->loadRow();
		
		array_push ( $uploadedArray, $row['0'] ? $row['0'] : 0 );
		array_push ( $classifiedArray, $row['1'] ? $row['1'] : 0 );
	}
	
	// If all uploaded entries are 0 there are no uploads so reflect in title
	if ( array_sum($uploadedArray) == 0 ) {
		$title = $translations["no_up_class"]["translation_text"] . " " . $coords ;
	}

  
	$discoverData = array ( 
		"labels" => $labelsArray,
		"uploaded" => $uploadedArray,
		"classified" => $classifiedArray,
		"cla_label" => $translations["classified"]["translation_text"],
		"upl_label" => $translations["uploaded"]["translation_text"],
		"title" => $title
		
		);
  
  
	//print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
	//print implode(",", $projectdetails);
  
	return $discoverData;
}


// Return some animal sightings data for the sites within this area
function discoverAnimals ( $lat_start, $lat_end, $lon_start, $lon_end, $num_species = null, $include_dontknow = false, $include_human = false, $include_nothing = false  ) {
	
	error_log("discoverAnimals(" . $lat_start . ", ". $lat_end . ", ". $lon_start . ", ". $lon_end . ")");
	
	// Get all the text snippets for this view in the current language
	  $translations = getTranslations("discover");
	  
	  
	  //$coords = "(".$lat_start.$translations["s"]["translation_text"].",".$lon_start.$translations["w"]["translation_text"]." to ";
	  //$coords .= $lat_end.$translations["n"]["translation_text"].",".$lon_end.$translations["e"]["translation_text"] . ")";
	  $coords = $translations["lat"]["translation_text"]. " " . $lat_start . " " . $translations["to"]["translation_text"] . " " . $lat_end . ", ";
	  $coords .= $translations["lon"]["translation_text"]. " " . $lon_start . " " . $translations["to"]["translation_text"] . " " . $lon_end ;
	  $title = $translations["sights"]["translation_text"] . " " . $coords ;
	
	
	  // Note that for now we are summing over the years to get total sightings
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  
	  $query->select("SA.species as option_id, O.option_name as species, sum(num_sightings) as num_sightings FROM SiteAnimals SA")
		->innerJoin("Options O on SA.species = O.option_id")
		->innerJoin("Site S on SA.site_id = S.site_id")
		->where("S.latitude between " . $lat_start . " and " . $lat_end . " and S.longitude between " . $lon_start . " and " . $lon_end)
		->group("SA.species")
		->order("num_sightings  DESC");
		
	  $db->setQuery($query);
	  
	  // Include id...
	  $animals_w_id = $db->loadAssocList('species');
	  
	  $animals = $db->loadAssocList('species', 'num_sightings');
	  //$animals = array_column($animals_w_id, 'num_animals', 'species');
	  
	  // Ensure there is a variable - getting errors here
	  if ( !$animals ) $animals = array();
	  
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
		  $animals_to_return_en['Other Species'] = "" . $total_other;
	  }
	  else {
		  $animals_to_return_en = $animals;
	  }
	  
	  $animals_to_return = array();
	  foreach ( $animals_to_return_en as $sp=>$num ) {
		  error_log( "animal row: " . $sp . ", " . $num );
		  if ( $sp == "Other Species" ) {
			  $animals_to_return[$translations["other_sp"]["translation_text"]] = $num;
		  }
		  else {
			  $animals_to_return[codes_getName($animals_w_id[$sp]["option_id"], "speciestran")] = $num;
		  }
	  }
	  
	  // Quick fix?
	  if ( count($animals_to_return) == 0 ) {
		  $animals_to_return["All"] = 0;
		  $title = $translations["no_sights"]["translation_text"] . " " . $coords ;
	
	  }
	  
	  return array (
			"labels" => array_keys($animals_to_return),
			"animals" => array_values($animals_to_return),
			"title" => $title
			);

}

// Return some animal sightings data for the sites within this area
function discoverSpecies ( $species_id, $year = null  ) {
	
	error_log("discoverSpecies(" . $species_id . ", ". $year . ")");
	
	// Get all the text snippets for this view in the current language
	  $translations = getTranslations("discover");
	  
	  $tr_sp_name = codes_getOptionTranslation($species_id);
	  
	  $title = $translations["species"]["translation_text"] . " - " . $tr_sp_name;
	
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  
	  // Let's do each year plus sum over all years
	  // If no year is specified, sum over all years
	  $query->select("F.feature_id, F.west, F.east, F.south, F.north, sum(SA.num_sightings) as num_sightings, SA.year_taken from SiteAnimals SA")
			->innerJoin("FeatureSites FS on FS.site_id = SA.site_id")
			->innerJoin("Features F on FS.feature_id = F.feature_id and F.display_type = 'site'")
			->where("SA.species = " . $species_id )
			->group("F.feature_id, SA.year_taken");
	  $db->setQuery($query);
	  
	  $sightings_by_year = $db->loadAssocList();
	  
	  $queryall = $db->getQuery(true)
	    ->select("F.feature_id, F.west, F.east, F.south, F.north, sum(SA.num_sightings) as num_sightings from SiteAnimals SA")
		->innerJoin("FeatureSites FS on FS.site_id = SA.site_id")
		->innerJoin("Features F on FS.feature_id = F.feature_id and F.display_type = 'site'")
		->where("SA.species = " . $species_id )
		->group("F.feature_id");
	  $db->setQuery($queryall);
	  
	  $all_sightings = $db->loadAssocList();
	  
	  $queryall = $db->getQuery(true)
	    ->select("SA.year_taken as year, sum(SA.num_sightings) as num_sightings from SiteAnimals SA")
		->where("SA.species = " . $species_id )
		->group("SA.year_taken");
  
	  $db->setQuery($queryall);
	  
	  // Include id...
	  $totals_by_year = $db->loadAssocList();
	  
	  //error_log ("Found " . count($all_sightings) . " for all years for species " . $species_id);
	  
	  $features = array();
	  foreach ( $all_sightings as $sighting ) {
		  $fid = "" . $sighting["feature_id"];
		  $num = $sighting["num_sightings"];
		  $e = $sighting["east"];
		  $w = $sighting["west"];
		  $s = $sighting["south"];
		  $n = $sighting["north"];
		  //error_log( "sighting: " . $fid . ", " . $num );
		  
		  // New feature each row as summed over years.
		  //error_log( "adding new feature " . $fid . ", " . $num );
		  $features[$fid] = array();
		  $features[$fid]["type"] = "Feature";
		  $features[$fid]["properties"] = array();
		  $features[$fid]["properties"]["stroke"] = false;
		  $features[$fid]["properties"]["all"] = $num;
		  $features[$fid]["geometry"] = array();
		  $features[$fid]["geometry"]["type"] = "Polygon";
		  $features[$fid]["geometry"]["coordinates"] = [[[$e,$s],[$w,$s],[$w,$n],[$e,$n],[$e,$s]]];
		  //error_log( "added new feature " . $fid . ", " . $num );
		  
	  }
	  
	  // By this point should have a feature for all years for all features.  Now add the year by year counts.
	  foreach ( $sightings_by_year as $sighting ) {
		  $fid = "" . $sighting["feature_id"];
		  $num = $sighting["num_sightings"];
		  $e = $sighting["east"];
		  $w = $sighting["west"];
		  $s = $sighting["south"];
		  $n = $sighting["north"];
		  $y = $sighting["year_taken"];
		  //error_log( "sighting: " . $fid . ", " . $num );
		  
		  // Just add the year as rest should be there.
		  //error_log( "adding new year " . $fid . ", " . $num . ", " . $y );
		  $features[$fid]["properties"]["".$y] = $num;
		  //error_log( "added new year for feature " . $fid . ", " . $num . ", " . $y);		  
	  }
	  
	  $totals = array();
	  foreach ( $totals_by_year as $total ) {
		  $year = $total["year"];
		  $num = $total["num_sightings"];
		  		  
		  // New total for each year, add zeros as unknown
		  //error_log( "adding new total for year " . $year . ", " . $num );
		  if ( $year == 0 ) {
			$totals["unknown"] = $num;
		  }
		  else {
			$totals["" . $year] = $num;
		  }
	  }
	  
	  
	  //error_log("discoverSpecies about to return "  );
	  
	  return array (
			"features" => array_values($features),
			"totals" => $totals,
			"title" => $title
			);
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


// Return number of uploads and classifications for the user's sites
function discoverUserUploads ( $site_id = null, $num_months = 12  ) {
	
	error_log("discoverUserUploads(" . $site_id . ", ". $num_months . ")");
	
	// Get all the text snippets for this view in the current language
	$translations = getTranslations("discover");
	  
	// Default to All sites
	$siteText = $translations["all_sites"]["translation_text"];
	if ( $site_id ) {
		
		$siteDetails = codes_getDetails($site_id, 'site');
		$siteText = $translations["for_site"]["translation_text"] . ' ' . $siteDetails['site_name'];
	}
	$title = $translations["up_class"]["translation_text"] . " " . $siteText ;
	
	$interval_in_months = 1;
    $numDisplayedMonths = $num_months + $interval_in_months;
	$end_date = null;
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
	  $minusMonths = "-" . $i . " months";
	  $months = $i-$interval_in_months+1;
	  $labelStr = "- " . $months . " months";
	  $dateMinusMonths = strtotime($minusMonths, $endDatePlus1);
	  array_push ( $datesArray, date('Ymd', strtotime("-1 day", $dateMinusMonths)) );
	  array_push ( $labelsArray, date('M Y', strtotime($labelStr, $endDatePlus1)) );
	}
	array_push ( $datesArray, date('Ymd', strtotime("-1 day", $endDatePlus1)) );
  
	// Select number of sequences uploaded and number classified up to each of our dates.
	$numIntervals = count($datesArray)-1;
  
	$uploadedArray = array();
	$classifiedArray = array();
	$db = JDatabase::getInstance(dbOptions());
	
	// Include data from private sites as this is user data
	for ( $j=0; $j<$numIntervals; $j++ ) {
		
		$query = $db->getQuery(true)
			->select("sum(num_uploaded), sum(num_classified) from SiteStatistics SS")
			->innerJoin("Site S on SS.site_id = S.site_id")
			->where("S.person_id = " . userID() )
			->where("end_date = " . $datesArray[$j+1] );
			
		if ( $site_id ) $query->where("S.site_id = " . $site_id);
		
		$db->setQuery($query);
	
		$row = $db->loadRow();
		
		$query = $db->getQuery(true)
			->select("sum(num_uploaded), sum(num_classified) from PrivateSiteStatistics SS")
			->innerJoin("Site S on SS.site_id = S.site_id")
			->where("S.person_id = " . userID() )
			->where("end_date = " . $datesArray[$j+1] );
			
		if ( $site_id ) $query->where("S.site_id = " . $site_id);
		
		$db->setQuery($query);
	
		$privateRow = $db->loadRow();
		
		$uploadedNum = $row['0'] ? $row['0'] : 0;
		$uploadedNum += $privateRow['0'] ? $privateRow['0'] : 0;
		
		$classifiedNum = $row['1'] ? $row['1'] : 0;
		$classifiedNum += $privateRow['1'] ? $privateRow['1'] : 0;
		
		
		array_push ( $uploadedArray, $uploadedNum );
		array_push ( $classifiedArray, $classifiedNum );
	}
	
	// If all uploaded entries are 0 there are no uploads so reflect in title
	if ( array_sum($uploadedArray) == 0 ) {
		$title = $translations["no_up_class"]["translation_text"] . " " . $siteText ;
	}

  
	$discoverData = array ( 
		"labels" => $labelsArray,
		"uploaded" => $uploadedArray,
		"classified" => $classifiedArray,
		"cla_label" => $translations["classified"]["translation_text"],
		"upl_label" => $translations["uploaded"]["translation_text"],
		"title" => $title
		
		);
  
  
	//print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
	//print implode(",", $projectdetails);
  
	return $discoverData;
}


// Return number of each species classified for the user's sites 
// If top is false the rarest ones (non-zero) are returned 
// NB here we include private sites as they belong to the user
function discoverUserAnimals ( $site_id = null, $top=true, $num_species = 10, $include_dontknow = false, $include_human = false, $include_nothing = false  ) {
	
	// Get all the text snippets for this view in the current language
	$translations = getTranslations("dashcharts");

	$title = $translations["by_sp"]["translation_text"];
	
	$personId = userID();

	//$projects = getSubProjectsById( $project_id );
	//$id_string = implode(",", array_keys($projects));
	
	if ( $top === true ) $order = 'DESC';
	else $order = 'ASC';

	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("SA.species as species_id, O.option_name as species, sum(SA.num_sightings) as num_animals FROM SiteAnimals SA")
		->innerJoin("Site S on SA.site_id = S.site_id and S.person_id = " . $personId)
		->innerJoin("Options O on O.option_id = SA.species")
		->group("SA.species")
		->order("num_animals, species " . $order);
	
	if ( $site_id ) $query->where("S.site_id = " . $site_id);
	
	$db->setQuery($query);
	
	//error_log ( "discoverUserAnimals, query: " . $query->dump() );


	// Include id...
	$animals_w_id = $db->loadAssocList('species');
	
	//$errMsg = print_r($animals_w_id, true);
	//error_log ( "Animals w id: " . $errMsg );

	$animals = $db->loadAssocList('species', 'num_animals');
	
	//$errMsg = print_r($animals, true);
	//error_log ( "Animals: " . $errMsg );
	
	
	// Get the same for private sites
	$query = $db->getQuery(true);
	$query->select("SA.species as species_id, O.option_name as species, sum(SA.num_sightings) as num_animals FROM PrivateSiteAnimals SA")
		->innerJoin("Site S on SA.site_id = S.site_id and S.person_id = " . $personId)
		->innerJoin("Options O on O.option_id = SA.species")
		->group("SA.species")
		->order("num_animals, species " . $order);
	
	if ( $site_id ) $query->where("S.site_id = " . $site_id);
	
	$db->setQuery($query);
	
	//error_log ( "discoverUserAnimals, pquery: " . $query->dump() );


	// Include id...
	$p_animals_w_id = $db->loadAssocList('species');
	
	//$errMsg = print_r($p_animals_w_id, true);
	//error_log ( "Private animals w id: " . $errMsg );

	$p_animals = $db->loadAssocList('species', 'num_animals');
	
	//$errMsg = print_r($p_animals, true);
	//error_log ( "Private animals: " . $errMsg );
	
	// If there are any private sites need to add those numbers in
	foreach ($p_animals_w_id as $pAnimal) {
		$pSpecies = $pAnimal['species'];
		if ( array_key_exists($pSpecies, $animals_w_id) ) {
			$animals[$pSpecies] += $pAnimal['num_animals'];
			$animals_w_id[$pSpecies]['num_animals'] += $pAnimal['num_animals'];
		}
		else {
			$array_changed = true;
			$animals[$pSpecies] = $pAnimal['num_animals'];
			$animals_w_id[$pSpecies] = $pAnimal;
		}
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
		if ( $top ) {
			arsort($animals);
		}
		else {
			asort($animals);
		}
	}
	
	//$errMsg = print_r($animals, true);
	//error_log ( "Animals after sort: " . $errMsg );
	

	

	$animals_to_return_en = array();
	// Finally handle the number of species, if we have more than required
	// NB Other is a possible option so combine this with Other Species if we have both
	$num_other = 0;

	if ( key_exists("Other", $animals) ) {
	$num_other = $animals["Other"];
	$animals = akrem($animals, "Other");
	}
	
	if ( $top ) {
		
		if ( $num_other > 0 ) {
		
			if ( $num_species && (count($animals) > $num_species-1) ) {
				$animals_to_return_en = array_slice($animals, 0, $num_species-1);
				$total_other = array_sum(array_slice($animals, $num_species-1)) + $num_other;
				$animals_to_return_en['Other Species'] = "" + $total_other;
			}
			else {
				$animals_to_return_en = $animals ;
			}
		}
		else {
			if ( $num_species && (count($animals) > $num_species) ) {
				$animals_to_return_en = array_slice($animals, 0, $num_species-1);
				$total_other = array_sum(array_slice($animals, $num_species-1));
				$animals_to_return_en['Other Species'] = "" + $total_other;
			}
			else {
				$animals_to_return_en = $animals ;
			}
		}
	}
	else {
		// Don't include Other for rare species, number could be large and make graph unreadable, so this is much simpler
		$animals_to_return_en = array_slice($animals, 0, $num_species);
	}

	//$errMsg = print_r($animals_to_return_en, true);
	//error_log ( "animals_to_return_en: " . $errMsg );
	

	$animals_to_return = array();
	foreach ( $animals_to_return_en as $sp=>$num ) {
	  //error_log( "animal row: " . $sp . ", " . $num );
	  if ( $sp == "Other Species" ) {
		  //error_log ( "key = " . $translations["other_sp"]["translation_text"] );
		  $animals_to_return[$translations["other_sp"]["translation_text"]] = $num;
	  }
	  else {
		  //error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
		  $animals_to_return[codes_getName($animals_w_id[$sp]["species_id"], "speciestran")] = $num;
	  }
	}

	return array (
		"labels" => array_keys($animals_to_return),
		"animals" => array_values($animals_to_return),
		"title" => $title
		);
		


}


// Return number of sequences where each species has been classified at least once for the user's sites 
// If top is false the rarest ones (non-zero) are returned 
// NB here we include private sites as they belong to the user
function discoverUserSequenceAnimals ( $site_id = null, $top=true, $num_species = 10, $include_dontknow = false, $include_human = false, $include_nothing = false  ) {
	
	// Get all the text snippets for this view in the current language
	$translations = getTranslations("dashcharts");

	$title = $translations["by_sp"]["translation_text"];
	
	$personId = userID();

	//$projects = getSubProjectsById( $project_id );
	//$id_string = implode(",", array_keys($projects));
	
	if ( $top === true ) $order = 'DESC';
	else $order = 'ASC';

	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("A.species as species_id, O.option_name as species, count(distinct A.photo_id) as num_sequences FROM `Animal` A")
		->innerJoin("Photo P on P.photo_id = A.photo_id and P.person_id = " . $personId)
		->innerJoin("Options O on O.option_id = A.species")
		->group("A.species")
		->order("num_sequences, species " . $order);
	
	if ( $site_id ) $query->where("P.site_id = " . $site_id);
	
	$db->setQuery($query);
	
	error_log ( "discoverUserSequenceAnimals, query: " . $query->dump() );


	// Include id...
	$animals_w_id = $db->loadAssocList('species');
	
	$errMsg = print_r($animals_w_id, true);
	error_log ( "Animals w id: " . $errMsg );

	$animals = $db->loadAssocList('species', 'num_sequences');
	
	$errMsg = print_r($animals, true);
	error_log ( "Animals: " . $errMsg );
	
	
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
		if ( $top ) {
			arsort($animals);
		}
		else {
			asort($animals);
		}
	}
	
	//$errMsg = print_r($animals, true);
	//error_log ( "Animals after sort: " . $errMsg );

	$animals_to_return_en = array();
	// Finally handle the number of species, if we have more than required
	// NB Other is a possible option so combine this with Other Species if we have both
	$num_other = 0;

	if ( key_exists("Other", $animals) ) {
	$num_other = $animals["Other"];
	$animals = akrem($animals, "Other");
	}
	
	if ( $top ) {
		
		if ( $num_other > 0 ) {
		
			if ( $num_species && (count($animals) > $num_species-1) ) {
				$animals_to_return_en = array_slice($animals, 0, $num_species-1);
				$total_other = array_sum(array_slice($animals, $num_species-1)) + $num_other;
				$animals_to_return_en['Other Species'] = "" + $total_other;
			}
			else {
				$animals_to_return_en = $animals ;
			}
		}
		else {
			if ( $num_species && (count($animals) > $num_species) ) {
				$animals_to_return_en = array_slice($animals, 0, $num_species-1);
				$total_other = array_sum(array_slice($animals, $num_species-1));
				$animals_to_return_en['Other Species'] = "" + $total_other;
			}
			else {
				$animals_to_return_en = $animals ;
			}
		}
	}
	else {
		// Don't include Other for rare species, number could be large and make graph unreadable, so this is much simpler
		$animals_to_return_en = array_slice($animals, 0, $num_species);
	}

	$errMsg = print_r($animals_to_return_en, true);
	error_log ( "animals_to_return_en: " . $errMsg );
	

	$animals_to_return = array();
	foreach ( $animals_to_return_en as $sp=>$num ) {
	  //error_log( "animal row: " . $sp . ", " . $num );
	  if ( $sp == "Other Species" ) {
		  //error_log ( "key = " . $translations["other_sp"]["translation_text"] );
		  $animals_to_return[$translations["other_sp"]["translation_text"]] = $num;
	  }
	  else {
		  //error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
		  $animals_to_return[codes_getName($animals_w_id[$sp]["species_id"], "speciestran")] = $num;
	  }
	}

	return array (
		"labels" => array_keys($animals_to_return),
		"animals" => array_values($animals_to_return),
		"title" => $title
		);
		


}


// Return nummber of Nothing, Human and total of species classifications 
function discoverUserNothingHuman ( $site_id = null  ) {
	
	error_log ( "discoverUserNothingHuman(" . $site_id . ")" );
	// Get all the text snippets for this view in the current language
	$translations = getTranslations("dashcharts");

	$title = $translations["noth_hum"]["translation_text"];
	
	$personId = userID();

	//$projects = getSubProjectsById( $project_id );
	//$id_string = implode(",", array_keys($projects));
	
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("SA.species as species_id, O.option_name as species, sum(SA.num_sightings) as num_animals FROM SiteAnimals SA")
		->innerJoin("Site S on SA.site_id = S.site_id and S.person_id = " . $personId)
		->innerJoin("Options O on O.option_id = SA.species")
		->group("SA.species");
	
	if ( $site_id ) $query->where("S.site_id = " . $site_id);
		
	$db->setQuery($query);
	
	//error_log ( "discoverUserNothingHuman, query: " . $query->dump() );


	// Include id...
	$animals_w_id = $db->loadAssocList('species');
	
	//$errMsg = print_r($animals_w_id, true);
	//error_log ( "discoverUserNothingHuman Animals w id: " . $errMsg );

	$animals = $db->loadAssocList('species', 'num_animals');
	
	//$errMsg = print_r($animals, true);
	//error_log ( "discoverUserNothingHuman Animals: " . $errMsg );
	
	// Get the same for private sites
	$query = $db->getQuery(true);
	$query->select("SA.species as species_id, O.option_name as species, sum(SA.num_sightings) as num_animals FROM PrivateSiteAnimals SA")
		->innerJoin("Site S on SA.site_id = S.site_id and S.person_id = " . $personId)
		->innerJoin("Options O on O.option_id = SA.species")
		->group("SA.species");
	
	if ( $site_id ) $query->where("S.site_id = " . $site_id);
	
	$db->setQuery($query);
	
	//error_log ( "discoverUserAnimals, pquery: " . $query->dump() );


	// Include id...
	$p_animals_w_id = $db->loadAssocList('species');
	
	//$errMsg = print_r($p_animals_w_id, true);
	//error_log ( "discoverUserNothingHuman Private animals w id: " . $errMsg );

	$p_animals = $db->loadAssocList('species', 'num_animals');
	
	//$errMsg = print_r($p_animals, true);
	//error_log ( "discoverUserNothingHuman Private animals: " . $errMsg );
	
	// If there are any private sites need to add those numbers in
	foreach ($p_animals_w_id as $pAnimal) {
		$pSpecies = $pAnimal['species'];
		if ( array_key_exists($pSpecies, $animals_w_id) ) {
			$animals[$pSpecies] += $pAnimal['num_animals'];
			$animals_w_id[$pSpecies]['num_animals'] += $pAnimal['num_animals'];
		}
		else {
			$animals[$pSpecies] = $pAnimal['num_animals'];
			$animals_w_id[$pSpecies] = $pAnimal;
		}
	}
	

	// Remove Likes
	unset($animals["Like"]);
	
	$animals_to_return = array();
	
	if ( count($animals) > 0 ) {

		$animals_to_return_en = array();
		
		// Want the number of Nothings and Humans, then a sum of all other classifications
		// Handle zero Nothings/Humans
		foreach (array("Nothing", "Human") as $optName ) {
			if ( key_exists($optName, $animals) ) {
				
				$animals_to_return_en[$optName] = $animals[$optName];
			}
			else {
				$animals_to_return_en[$optName] = 0;
				$spId = codes_getCode($optName,'noanimal');
				$animals_w_id[$optName]=array("species_id" => $spId, "species"=>$optName, "num_animals"=>0);
			}
		
		}
		$animals = akrem($animals, 'Nothing');
		$animals = akrem($animals, 'Human');
		
		$animals_to_return_en['Animals'] = array_sum($animals);
		
		//$errMsg = print_r ( $animals, true );
		//error_log ( "discoverUserNothingHuman animals array = " . $errMsg );
		
		//$errMsg = print_r ( $animals_w_id, true );
		//error_log ( "discoverUserNothingHuman animals_w_id array = " . $errMsg );
		
		//$errMsg = print_r ( $animals_to_return_en, true );
		//error_log ( "discoverUserNothingHuman animals_to_return_en array = " . $errMsg );
		
		
		
		foreach ( $animals_to_return_en as $sp=>$num ) {
		  //error_log( "animal row: " . $sp . ", " . $num );
		  if ( $sp == 'Animals' ) {
			  //error_log ( "key = " . $translations["other_sp"]["translation_text"] );
			  $animals_to_return[$translations["all_sp"]["translation_text"]] = $num;
		  }
		  else {
			  //error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
			  $animals_to_return[codes_getName($animals_w_id[$sp]["species_id"], "speciestran")] = $num;
		  }
		}
	}

	return array (
		"labels" => array_keys($animals_to_return),
		"animals" => array_values($animals_to_return),
		"title" => $title
		);
		


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
	  //error_log( "animal row: " . $sp . ", " . $num );
	  if ( $sp == "Other Species" ) {
		  //error_log ( "key = " . $translations["other_sp"]["translation_text"] );
		  $animals_to_return[$translations["other_sp"]["translation_text"]] = $num;
	  }
	  else {
		  //error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
		  $animals_to_return[codes_getName($animals_w_id[$sp]["option_id"], "speciestran")] = $num;
	  }
  }
  
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


function getSubProjectsById($project_id, $exclude_private = false){
  
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
		    /*
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
		*/
		
		$q1->select("count(*)")
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
			->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)");
		
		$db->setQuery($q1); 
		$num_rows = $db->loadResult();
		
		error_log("chooseMultiple: num rows = " . $num_rows);
		
		// Get a random integer between 0 and $num_rows-1
		$row_num = rand(0, $num_rows - 1);
		
		error_log("chooseMultiple: chosen row = " . $row_num);
		
		$q2 = $db->getQuery(true);
		$q2->select("P.photo_id, P.sequence_id")
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
			->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)");
			
		$db->setQuery($q2, $row_num, 1); // LIMIT 1 sarting at the n = $row_num
		
		$photo = $db->loadObject();
		if ( $photo ) {
			
			$photo_id = $photo->photo_id;
			//print "<br>chooseMultiple, photo found with id " . $photo_id . " <br>";
			error_log("chooseMultiple: got photo = " . $photo_id);
		}	
		else {
			error_log("chooseMultiple: no photo ");
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
	/* order by rand() is inefficient  
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
	*/

	$q1->select("count(*)")
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
		->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)");
		
	$db->setQuery($q1); 
	$num_rows = $db->loadResult();
		
	error_log("chooseSingle: num rows = " . $num_rows);
		
	// Get a random integer between 0 and $num_rows-1
	$row_num = rand(0, $num_rows - 1);
		
	error_log("chooseSingle: chosen row = " . $row_num);
		
	$q2 = $db->getQuery(true);
	$q2->select("P.photo_id, P.sequence_id")
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
		->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)");
	
	$db->setQuery($q2, $row_num, 1); // LIMIT 1 sarting at the n = $row_num
	
	$photo = $db->loadObject();
	if ( $photo ) {
		$photo_id = $photo->photo_id;
		error_log("chooseSingle: got photo = " . $photo_id);
	}	
	else {
		error_log("chooseSingle: no photo = ");
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
        /*    
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
		
		*/
		
		$q1->select("count(*)")
			->from("Photo P")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
			->where("P.status = 1")
			->where("P.contains_human =0" . $own_string )
			->where("P.sequence_id > 0")
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)");
			
		$db->setQuery($q1); 
		$num_rows = $db->loadResult();
			
		error_log("chooseSingle: num rows = " . $num_rows);
			
		// Get a random integer between 0 and $num_rows-1
		$row_num = rand(0, $num_rows - 1);
			
		error_log("chooseSingle: chosen row = " . $row_num);
			
		$q2 = $db->getQuery(true);
		$q2->select("P.photo_id, P.sequence_id")
			->from("Photo P")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
			->where("P.status = 1")
			->where("P.contains_human =0" . $own_string )
			->where("P.sequence_id > 0")
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)");
		
		$db->setQuery($q2, $row_num, 1); // LIMIT 1 sarting at the n = $row_num	
			
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
	->where("sequence_id = 0")
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
	
	$isAudio = isAudio($photo_id);
	$isVideo = isVideo($photo_id);
	
	if($prev_dateTime !== null){
      $diff = $dateTime->diff($prev_dateTime);
      print "photo_id $photo_id prev_photo_id $prev_photo_id diff ". $diff->s;
      //if((abs($diff->s) <10) && ($diff->i==0) && ($diff->h==0) && ($diff->d==0) & ($diff->m==0) & ($diff->y ==0)){ // less than 10 seconds between photos
	  if(!$isAudio && !$isVideo && (abs($diff->s) <10) && ($diff->i==0) && ($diff->h==0) && ($diff->d==0) & ($diff->m==0) & ($diff->y ==0)){ // less than 10 seconds between photos
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
	
	$lang = langTag();
	
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

// Get project specific filters for the topic given
function getTopicFilters ( $topic_id ) {
	
	$lang = langTag();
	
	$topicFilters = array();
	
	// First get the English version
	if ( $topic_id ) {
		// Find filter for this topic
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("O.option_id, O.option_name")->from("Options O")
			->innerJoin("OptionData OD on OD.value = O.option_id and OD.data_type = 'topicfilter'")
			->where("OD.option_id = " . $topic_id )
			->order("O.seq");
		$db->setQuery($query);
		$topicFilters = $db->loadRowList();
	}
	
	// if we are not in English, use the translated filter names if they exist
	if ( $lang != "en-GB" ) {
		for ( $i = 0; $i < count($topicFilters); $i++ ) {
			list($id, $name) = $topicFilters[$i];
			$translatedName = codes_getName($id, 'projectfiltertran');
			$topicFilters[$i][1] = $translatedName;
		}
	}
	//print ( "<br> projectFilters: <br>" );
	//print_r ( $projectFilters );
		
	$returnFilters = array();
	
	foreach ($topicFilters as $filter) {
		$returnFilters[$filter[0]] = array('label'=>$filter[1]);
	}
	
	return $returnFilters;
}


function getTrainingSequences( $topic_id, $max_number=10 ) {
	
	$seq_ids = array();
	
	if ( $topic_id ) {
		
		$db = JDatabase::getInstance(dbOptions());
		
		
		// Get a list of distinct species for this topic so we can ensure we have a range 
		// Check they have a primary species otherwise leave out
		$query = $db->getQuery(true);
		$query->select("ES.species_id, COUNT(*) as num_seqs, GROUP_CONCAT(ES.sequence_id order by rand()) as seqs")->from("ExpertSequences ES")
			->innerJoin("OptionData OD on OD.value = ES.es_id")
			->where("OD.option_id = " . $topic_id . " and OD.data_type = 'expertsequence'" )
			->where("ES.is_primary = 1" )
			->group("species_id");
		$db->setQuery($query);
		$sequences_by_species = $db->loadAssocList('species_id');
		
		$err_str = print_r($sequences_by_species, true);
		error_log("sequences by species: " . $err_str);

		$used_ids = array();
		
		$num_seqs = 0;
		foreach ( $sequences_by_species as $sp_id=>$sp ) {
			
			$sp_count = $sp['num_seqs'];
			
			// At this point make sure each sequence_id only appears once.  If there is more than one species in a sequence it will be on more than one row
			$ids = explode ( ',', $sp['seqs'] );
			
			$already_used = array_intersect($used_ids, $ids);
			
			if ( count($already_used) > 0 ) {
				error_log ("Got already used sequence(s) for species " . $sp_id );
				foreach ( $already_used as $used_id ) {
					$id_key = array_search($used_id, $ids);
					unset($ids[$id_key]);
					$sp_count -= 1;
				}
			}
			
			// If no sequences left, remove this species from the list.
			if ( $sp_count <= 0 ) {
				error_log ("Got no sequences left for species " . $sp_id );
				unset($sequences_by_species[$sp_id]);
			}
			else {
				// Only change if necessary
				if ( $sp_count != $sp['num_seqs'] ) {
					error_log ("Removed sequence(s) so resetting list for species " . $sp_id );
				
					$sp['num_seqs'] = $sp_count;
					$sp['seqs'] = implode ( ',', $ids );
				}
				$used_ids = array_merge ( $used_ids, $ids );
				$num_seqs += $sp_count;
			}
			
		}
		
		$err_str = print_r($sequences_by_species, true);
		error_log("sequences by species: " . $err_str);

		// Need at least one species and at least max_number of sequences
		$num_species = count($sequences_by_species);
		if ( $num_species > 0 && $num_seqs >= $max_number ) {
		
			$species = array_keys ( $sequences_by_species );
			
			shuffle( $species );
			
			$err_str = print_r($species, true);
			//error_log("species: " . $err_str);
			
			$species_to_use = null;
			
			if ( $num_species >= $max_number ) {
				$species_to_use = array_slice ( $species, 0, $max_number );
			}
			else {
				$species_to_use = array_merge ( $species  );
				
				// Now we need to pad the array with repeating species.
				// However we have to make sure there are enough sequences in the padding species.
				while ( count($species_to_use) < $max_number ) {
					$try = array_rand ( $species );
					$try_key = $species[$try];
					
					$num_already = array_count_values ( $species_to_use )[$try_key];
					
					$num_seqs_for_species = $sequences_by_species[$try_key]['num_seqs'];
					
					if ( $num_already < $num_seqs_for_species ) {
						$species_to_use[] = $try_key;
					}
				}
			}
			
			$err_str = print_r($species_to_use, true);
			//error_log("species to use: " . $err_str);
			
			// Set up an assoc array with species and sequence count required
			$species_with_count = array_count_values($species_to_use);
			$err_str = print_r($species_with_count, true);
			//error_log("species with count: " . $err_str);

			
			foreach ( $species_with_count as $species_id=>$seq_count ) {
				$seqs = $sequences_by_species[$species_id];
				$ids = explode ( ',', $seqs['seqs'] );
				
				// randomise the ids
				shuffle($ids);
				
				// and take the first one(s)
				$seq_ids = array_merge ( $seq_ids, array_slice($ids, 0, $seq_count) );
				
							
				$err_str = print_r($seq_ids, true);
				error_log("seq_ids: " . $err_str);
				
			}
		}
	}
	
	shuffle($seq_ids);
	
	return $seq_ids;
}


function getTrainingSequence( $sequence_id, $topic_id = null ) {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$seq = new Sequence ( $sequence_id );
	
	$esObjects = null;
	
	// If no topic, then get for all topics
	if ( !$topic_id ) {
		$query = $db->getQuery(true);
		$query->select("distinct ES.species_id as id, ES.gender, ES.age, sum(ES.number) as number, is_primary")->from("ExpertSequences ES")
			->where("ES.sequence_id = ".$sequence_id )
			->group("ES.sequence_id, ES.species_id, ES.gender, ES.age, ES.is_primary");
		$db->setQuery($query);
		$esObjects = $db->loadObjectList();
	}
	else {
		$query = $db->getQuery(true);
		$query->select("ES.species_id as id, ES.gender, ES.age, sum(ES.number) as number, is_primary")->from("ExpertSequences ES")
			->innerJoin("OptionData OD on OD.value = ES.es_id")
			->where("OD.option_id = " . $topic_id . " and OD.data_type = 'expertsequence'" )
			->where("ES.sequence_id = ".$sequence_id )
			->group("ES.sequence_id, ES.species_id, ES.gender, ES.age, ES.is_primary");
		$db->setQuery($query);
		$esObjects = $db->loadObjectList();
	}
	
	foreach ( $esObjects as $es ) {
		if ( $es->is_primary == 1 ) {
			$seq->addPrimarySpecies ( $es );
		}
		else {
			$seq->addSecondarySpecies ( $es );
		}
	
	}
		
	return $seq;
}


function extractLabel ( $v ){
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

function allSpecies () {
		
		error_log ("allSpecies called");
		
		$db = JDatabase::getInstance(dbOptions());
	
		$speciesArray = null;
		
		// Check the language tag and work differently if English
		$lang = langTag();
		if ( $lang == 'en-GB' ) {
			
			error_log ( "Language is English so no join to OptionData" );
			$query = $db->getQuery(true);
			
			$query->select("O.option_id as id, O.option_name as name")
				->from("Options O")
				->where( "O.struc in ( 'mammal', 'bird', 'notinlist' )" );
				
			$db->setQuery($query);
			
			$speciesArray = $db->loadRowList();
			
		}
		else {
			
			$query = $db->getQuery(true);
			
			$query->select("OD.option_id as id, OD.value as name")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc in ( 'mammal','bird','notinlist' )")
				->where("OD.data_type = " . $db->quote($lang) );
				
			$db->setQuery($query);
			
			$speciesArray = $db->loadRowList();
			
		}
		$err_msg = print_r ( $speciesArray, true );
		error_log ( $err_msg );
		
		return $speciesArray;
}
	
function getSpecies ( $filterId, $onePage ) {
	
	$speciesList = array();
	
	/* Bypass the codes stuff with a set of selects directly in the database for speed
	
	//$species = codes_getList ( $filtername );
	$restrict = array();
	//$restrict['restriction'] = "value = '" . $filterid . "'";
	$restrict['restriction'] = "list_id = '" . $filterid . "'";
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
	
	*/
	
	$db = JDatabase::getInstance(dbOptions());
	
	$mammalArray = null;
	$birdArray = null;
	$notinlistArray = null;
	
	// Check the language tag and work differently if English
	$lang = langTag();
	if ( $lang == 'en-GB' ) {
		
		error_log ( "Language is English so no join to OptionData" );
		$query = $db->getQuery(true);
		
		$query->select("O.option_id as id, O.option_name as name, O.struc as type")
			->from("Options O")
			->innerJoin("SpeciesList SL on O.option_id = SL.species_id")
			->where( "O.struc = 'mammal'" )
			->where( "SL.list_id = " . $filterId )
			->order("O.option_name");
			
		$db->setQuery($query);
		
		$mammalArray = $db->loadAssocList("id");
		
		error_log ( "Got mammal names" );
		
		$query = $db->getQuery(true);
		
		$query->select("O.option_id as id, O.option_name as name, O.struc as type")
			->from("Options O")
			->innerJoin("SpeciesList SL on O.option_id = SL.species_id")
			->where( "O.struc = 'bird'" )
			->where( "SL.list_id = " . $filterId )
			->order("O.option_name");
			
		$db->setQuery($query);
		
		$birdArray = $db->loadAssocList("id");
		
		error_log ( "Got bird names" );
		
	}
	else {
		
		$query = $db->getQuery(true);
		
		$query->select("OD.option_id as id, OD.value as name, O.struc as type")
			->from("OptionData OD")
			->innerJoin("SpeciesList SL on OD.option_id = SL.species_id")
			->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'mammal'")
			->where("OD.data_type = " . $db->quote($lang) )
			->where("SL.list_id = " . $filterId )
			->order("O.option_name");
			
		$db->setQuery($query);
		
		$mammalArray = $db->loadAssocList("id");
		
		$query = $db->getQuery(true);
		
		$query->select("OD.option_id as id, OD.value as name, O.struc as type")
			->from("OptionData OD")
			->innerJoin("SpeciesList SL on OD.option_id = SL.species_id")
			->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'bird'")
			->where("OD.data_type = " . $db->quote($lang) )
			->where( "SL.list_id = " . $filterId )
			->order("O.option_name");
			
		$db->setQuery($query);
		
		$birdArray = $db->loadAssocList("id");
		
	}
	
	if ( $mammalArray ) $speciesList['mammal'] = $mammalArray;
	if ( $birdArray ) $speciesList['bird'] = $birdArray;
	
	$err_msg = print_r ( $mammalArray, true );
	error_log ( "mammal list for filter " . $filterId . ": " . $err_msg );
	
	$err_msg = print_r ( $birdArray, true );
	error_log ( "bird list for filter " . $filterId . ": " . $err_msg );
	
	
	return $speciesList;
}

function getClassifyInputs () {
	
	$translations = getTranslations("classify");
	$classifyInputs = array();
	
	foreach(array("gender", "age") as $struc){
		$title_tran = $translations[codes_getTitle($struc)]['translation_text'];
		$input = "<label for ='classify_$struc'>" . $title_tran . "</label><br />\n";
		//$input .= "<select id='classify_$struc' name='$struc'>\n";
		//$input .= codes_getOptions(1, $struc);
		// set default to be unknown:
		$features = array("gender"=>84, "age"=>85);
		$input .= codes_getRadioButtons($struc, $struc."tran", $features);
		//$input .= "\n</select>\n";
		$classifyInputs[] = $input;	    
	}
	$number = "<label for ='classify_number'>How many?</label>\n";
	$number .= "<div id='classify_how_many'><button type='button' class='btn' id='classify_decrease'><span class='fa fa-minus'/></button>";
	$number .= "<input id='classify_number' type='number' min='1' max='99' value='1' name='number'/><button type='button' class='btn' id='classify_increase'><span class='fa fa-plus'/></button></div>\n";
	$classifyInputs[] = $number;
		
	//$number = "<label for ='classify_number'>" . $translations['how_many']['translation_text'] . "</label>\n";
	//$number .= "<input id='classify_number' type='number' min='1' value='1' name='number'/>\n";
	//$classifyInputs[] = $number;
	
	$sure = "<label for ='classify_sure'>" . $translations['sure']['translation_text'] . "</label>\n";
	// See what happens if we don;t specify what to check - want it to default to first option
	$sure .= codes_getRadioButtons("sure", "suretran", null);
	$classifyInputs[] = $sure;
	
	$notes = "<label for ='classify_notes'>" . $translations['notes']['translation_text'] . "</label>\n";
	$notes .= "<input id='classify_notes' type='text' maxlength='100' name='notes'/>\n";
	$classifyInputs[] = $notes;
		
	return $classifyInputs;
}

function getClassifyBirdInputs () {
	
	$translations = getTranslations("classify");
	$classifyInputs = array();
	
	// The song/call is actually stored in notes for now.
	// Possibly should have a column for this and a struc or even a separate table for bird classifications
	// but maybe do as part of project specific data..
	$songcall = "<label for ='classify_songcall'>" . $translations['songcall']['translation_text'] . "</label>\n";
	
	$songcall .= "<div class='species-radio'><input name='notes' type='radio' value='song' id='songcall_0' checked='checked'/><label for='songcall_0'>Song</label></div>";
	$songcall .= "<div class='species-radio'><input name='notes' type='radio' value='call' id='songcall_1' /><label for='songcall_1'>Call</label></div>";
	
	$classifyInputs[] = $songcall;
	
	$sure = "<label for ='classify_sure'>" . $translations['sure']['translation_text'] . "</label>\n";
	// See what happens if we don;t specify what to check - want it to default to first option
	$sure .= codes_getRadioButtons("sure", "suretran", null);
	$classifyInputs[] = $sure;
	
	
	return $classifyInputs;
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

/* half changed...??
function makeControlButton($control_id, $control, $extraClasses='', $dataToggle = false){
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
  print "<button type='button' class='btn btn-primary $extraText $extraClasses' id='$control_id' >$control</button>";
}
*/

//$useSeq is flag if true uses page numbers given, if false, works pages out alphabetically
//if $largeButtons then use image buttons with larger size as for kiosk mode
// if dataToggle is false then classify modal is not shown - needed for quickclassify
function printSpeciesList ( $filterId, $speciesList, $useSeq=false, $largeButtons=false, $includeExtraControls = false, $extraControls = null, $dataToggle = true ) {
	
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
			
			$toggleExtras = "";
			if ( $dataToggle == true ) {
				$toggleExtras = " data-toggle='modal' data-target='#classify_modal'";
			}
			
			//error_log ( "toggleExtras = " . $toggleExtras );
	
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
						"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select'".$toggleExtras.">".$imageText."<div><div class='long-species-name'>$name</div></div></button>";
					
					}
					else {
						$carouselItems[$page][] =
						"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select'".$toggleExtras.">".$imageText."<div>$name</div></button>";
						//"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'><div><img width='50px' src='http://localhost/rhombus/images/thumbnails/Stoat.png'></div><div>$name</div></button>";
					}
				}
				else {
					$carouselItems[$page][] =
						"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' ".$toggleExtras."><div>$name</div></button>";
				
				}
			}
			else {
				$carouselItems[$page][] =
				"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn species_select' ".$toggleExtras.">$name</button>";
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

// This version is used by non-kiosk MammalWeb
function printSpeciesListSearch ( $filterId, $speciesList, $useSeq=false, $dataToggle = true ) {
	
	// Should store this in the Options table as a system option. 
	$numPerPage = 36;
	
	$column0Class = "col-xs-12 col-sm-12 col-md-12";
	$dkOtherClass = "col-xs-12 col-sm-12 col-md-12";
	
	// Add pagination
	$numSpecies = 0;
	foreach ($speciesList as $type=>$all_this_type) {
		$numSpecies = $numSpecies + count($all_this_type);
	}
	
	error_log ("printSpeciesListSearch: num per page = " . $numPerPage );
	error_log ("printSpeciesListSearch: num species = " . $numSpecies );
	
	$numPages = ceil($numSpecies/$numPerPage);
	
	error_log ("printSpeciesListSearch: num pages = " . $numPages );
	
	
	
	if ( $numPages > 1 ) {
		print '<div class="species_pagination col-xs-12 col-sm-12 col-md-12">';
		print '<nav aria-label="Species pagination">';
		print '<ul class="pagination btn-group">';
		print '<li class="btn btn-info prev-page">';
		print '<i class="fa fa-backward"></i>';
		print '</li>';
		for ( $i = 0; $i < $numPages; $i++ ) {	
			print '    <li class="btn btn-info">';
			print strVal($i+1);
			print '   </li>';
		}
		print '<li class="btn btn-info next-page">';
		print '<i class="fa fa-forward"></i>';
		print '</li>';
		
		print '</ul>';
		print '</nav>';
		print '</div>';
	}
		
	$toggleExtras = "";
	if ( $dataToggle == true ) {
		$toggleExtras = " data-toggle='modal' data-target='#classify_modal'";
	}
	
	// Default
	$btnClass = 'btn-warning';
		
	
	// Now add the birds
	foreach ($speciesList as $type=>$all_this_type) {
		foreach($all_this_type as $species_id => $species){
			
			$name = $species['name'];
			$isLongSpeciesName = false;
			if ( strlen($name) > 20 ) $isLongSpeciesName = true;
			
			//print ( "name = " . $name . "<br>" );
			switch($species['type']){
			case 'mammal':  
				$btnClass = 'btn-warning';
				
				// For birds have a button to view the article and a song and call quick classify button
				$btnText = "<button type='button' id='species_select_${filterId}_${species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select species_select_name $column0Class'".$toggleExtras."  >$name</button>";
				
				print '<div id="species_group_'.$filterId.'_'.$species_id.'" class="col-xs-6 col-sm-6 col-md-6 btn-group species_group match" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
				
				break;

			case 'bird':  
				$btnClass = 'btn-info';
				
				// For birds have a button to view the article and a song and call quick classify button
				$btnText = "<button type='button' id='species_select_${filterId}_${species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select species_select_name $column0Class'".$toggleExtras."  >$name</button>";
				
				print '<div id="species_group_'.$filterId.'_'.$species_id.'" class="col-xs-6 col-sm-6 col-md-6 btn-group species_group match" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
				
				break;

			default :
				// Do nothing, handle Don't know and Other separately, no mammals allowed in the bird species lists
				break;
			}
		}
	}
	
	// Add a padding disabled button for when the number of species is odd...
	$btnText = "<button type='button' id='species_select_blank_${filterId}' class='btn $btnClass btn-sm btn-wrap-text species-btn $dkOtherClass' disabled".$toggleExtras." style='color:transparent;' >Blank</button>";
	print '<div id="species_group_blank_${filterId}" class="col-xs-6 col-sm-6 col-md-6 btn-group species_group_blank" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	
	// Explicitly add the notinlist (Don't know and Other) buttons at the bottom
	//Get the option ids  
	error_log("About to get dk and other ids" );
	$otherId = codes_getCode("Other",'species');
	error_log("printBirdSpeciesList: otherId = " . $otherId );
	$dkId = codes_getCode("Don\'t Know",'species');
	error_log("printBirdSpeciesList: dkId = " . $dkId );
	
	$btnClass = 'btn-primary';
	
	$name = codes_getOptionTranslation($dkId);			
	$btnText = "<button type='button' id='species_select_${filterId}_${dkId}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select $dkOtherClass'".$toggleExtras."  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$dkId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	$name = codes_getOptionTranslation($otherId);			
	$btnText = "<button type='button' id='species_select_${filterId}_${otherId}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select $dkOtherClass'".$toggleExtras."  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$otherId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	// Add Nothing and Human 
	error_log("About to get nothing and human ids" );
	$nothingId = codes_getCode("Nothing",'noanimal');
	error_log("printBirdSpeciesList: nothingId = " . $nothingId );
	$humanId = codes_getCode("Human",'noanimal');
	error_log("printBirdSpeciesList: humanId = " . $humanId );
	
	$btnClass = 'btn-primary';
	
	$name = codes_getOptionTranslation($nothingId);			
	$btnText = "<button type='button' id='control_content_${filterId}_${nothingId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control nothing $dkOtherClass'  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$nothingId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	//print "<button type='button' class='btn btn-primary classify_control' id='control_content_$nothingId'>$name</button>";
	
	$name = codes_getOptionTranslation($humanId);			
	$btnText = "<button type='button' id='control_content_${filterId}_${humanId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control $dkOtherClass'  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$humanId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	
	
}

// This version prints column of species 
function printBirdSpeciesListOrig ( $filterId, $speciesList, $useSeq=false, $dataToggle = true ) {
	
	// Should store this in the Options table as a system option. 
	$numPerPage = 36;
	
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
			
			$toggleExtras = "";
			if ( $dataToggle == true ) {
				$toggleExtras = " data-toggle='modal' data-target='#classify_modal'";
			}
  
			$name = $species['name'];
			$isLongSpeciesName = false;
			if ( strlen($name) > 20 ) $isLongSpeciesName = true;
			
			//print ( "name = " . $name . "<br>" );
			switch($species['type']){
			case 'mammal':
				// Mammals not allowed here so no buttons
				$btnClass = 'btn-warning';
				break;

			case 'bird':  
				$btnClass = 'btn-info';
				
				// For birds have a button to view the article and a song and call quick classify button
				$carouselItems[$page][] = "<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-sm btn-block btn-wrap-text species-btn species_select'".$toggleExtras." >$name</button>";
				
				$carouselItems[$page][] = "<button type='button' id='song_select_${species_id}' class='btn $btnClass btn-sm btn-block btn-wrap-text species-btn song_select' >Song</button>";
				$carouselItems[$page][] = "<button type='button' id='call_select_${species_id}' class='btn $btnClass btn-sm btn-block btn-wrap-text species-btn call_select' >Call</button>";
				
				break;

			case 'notinlist':
				$btnClass = 'btn-primary';
				$largeButtonImage = false;
				$carouselItems[$page][] = "<button type='button' id='species_select_${species_id}' class='btn $btnClass  btn-sm btn-block btn-wrap-text species-btn species_select'".$toggleExtras." >$name</button>";
				break;
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
  
		$numCols = 6;
		  
		$numRows = intval(($numSpeciesButtons + $numCols - 1)/$numCols);
		//print "numRows = " . $numRows . "<br>";
		//print "numCols = " . $numCols . "<br>";
  
		$cols = array();
  
		$carouselPageIndex = 0;
		
		// Read across for bird buttons.
		/* for birds just want the buttons in order
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
		*/
  
		//print_r ( $cols );
  
		$active = ($pageNum==1)?" active":"";
		print "<div class='item $active'>\n";
	
		//print "Making buttons<br>";
		
		$column0Class = "col-xs-8 col-sm-8 col-md-4";
		$songCallClass = "col-xs-2 col-sm-2 col-md-1";
		$columnClass = $column0Class;
		print "<div class='row species-row'>";
		
		/* works, reading across but non alphabetical for small screen so let's try different.
		for ( $i = 0; $i < $numCols; $i++ ) {
			if ( $i%3 == 0 ) $columnClass = $column0Class;
			else $columnClass = $songCallClass;
			print "<div class='" . $columnClass . " species-carousel-col'>";
			//print "col = ". $i . " count = " . count($cols[$i]) . "<br>";
			for ( $j = 0; $j < $numRows; $j++ ) {
				if ( $i < count($cols) and $j < count($cols[$i]) ) {
					print $cols[$i][$j];			
				}
			}
			print "</div> <!-- /species-carousel-col -->\n";
		}
		*/
		
		for ( $i = 0; $i < $numPerPage*3; $i++ ) {
			if ( $i%3 == 0 ) $columnClass = $column0Class;
			else $columnClass = $songCallClass;
			
			if ( $i < $numSpeciesButtons ) {
				print "<div class='" . $columnClass . " species-carousel-col'>";
				print $carouselPage[$i];	
				print "</div> <!-- /species-carousel-col -->\n";				
			}
			
		}
				
		print "</div> <!-- /species-row -->\n";
  
		// Separate row for notinlist items
		print "<div class='row species-row'>";
		
		$widthClass = 'col-md-6';
		foreach ( $carouselItems[-1] as $item ) {
				print "<div class='" . $widthClass . " species-carousel-col'>";
				print $item;
				print "</div>";
			}
		
		
		print "</div> <!-- /species-row -->\n";
		
		
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


// This version prints column of species 
function printBirdSpeciesList ( $filterId, $speciesList, $useSeq=false, $dataToggle = true ) {
	
	// Should store this in the Options table as a system option. 
	$numPerPage = 36;
	
	$column0Class = "col-xs-8 col-sm-8 col-md-8";
	$songCallClass = "col-xs-2 col-sm-2 col-md-2";
	$dkOtherClass = "col-xs-12 col-sm-12 col-md-12";
	
	// Add pagination
	$numSpecies = 0;
	foreach ($speciesList as $type=>$all_this_type) {
		$numSpecies = $numSpecies + count($all_this_type);
	}
	
	error_log ("printBirdSpeciesList: num per page = " . $numPerPage );
	error_log ("printBirdSpeciesList: num species = " . $numSpecies );
	
	$numPages = ceil($numSpecies/$numPerPage);
	
	error_log ("printBirdSpeciesList: num pages = " . $numPages );
	
	
	
	if ( $numPages > 1 ) {
		print '<div class="species_pagination col-xs-12 col-sm-12 col-md-12">';
		print '<nav aria-label="Species pagination">';
		print '<ul class="pagination btn-group">';
		print '<li class="btn btn-info prev-page">';
		print '<i class="fa fa-backward"></i>';
		print '</li>';
		for ( $i = 0; $i < $numPages; $i++ ) {	
			print '    <li class="btn btn-info">';
			print strVal($i+1);
			print '   </li>';
		}
		print '<li class="btn btn-info next-page">';
		print '<i class="fa fa-forward"></i>';
		print '</li>';
		
		print '</ul>';
		print '</nav>';
		print '</div>';
	}
		
	$toggleExtras = "";
	if ( $dataToggle == true ) {
		$toggleExtras = " data-toggle='modal' data-target='#classify_modal'";
	}
	
	// Default
	$btnClass = 'btn-info';
	
	// Now add the birds
	foreach ($speciesList as $type=>$all_this_type) {
		foreach($all_this_type as $species_id => $species){
			
			$name = $species['name'];
			$isLongSpeciesName = false;
			if ( strlen($name) > 20 ) $isLongSpeciesName = true;
			
			//print ( "name = " . $name . "<br>" );
			switch($species['type']){
			case 'bird':  
				$btnClass = 'btn-info';
				
				// For birds have a button to view the article and a song and call quick classify button
				$btn1 = "<button type='button' id='species_select_${filterId}_${species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select species_select_name $column0Class'".$toggleExtras."  >$name</button>";
				
				$btn2 = "<button type='button' id='song_select_${filterId}_${species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn song_select $songCallClass' >Song</button>";
				$btn3 = "<button type='button' id='call_select_${filterId}_${species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn call_select $songCallClass' >Call</button>";
				
				$btnText = $btn1.$btn2.$btn3;
				
				print '<div id="species_group_'.$filterId.'_'.$species_id.'" class="col-xs-12 col-sm-12 col-md-6 btn-group species_group match" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
				
				break;

			default :
				// Do nothing, handle Don't know and Other separately, no mammals allowed in the bird species lists
				break;
			}
		}
	}
	
	// Add a padding disabled button for when the number of species is odd...
	$btnText = "<button type='button' id='species_select_blank_${filterId}' class='btn $btnClass btn-sm btn-wrap-text species-btn $dkOtherClass' disabled".$toggleExtras." style='color:transparent;' >Blank</button>";
	print '<div id="species_group_blank_${filterId}" class="col-xs-12 col-sm-12 col-md-6 btn-group species_group_blank" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	
	// Explicitly add the notinlist (Don't know and Other) buttons at the bottom
	//Get the option ids  
	error_log("About to get dk and other ids" );
	$otherId = codes_getCode("Other",'species');
	error_log("printBirdSpeciesList: otherId = " . $otherId );
	$dkId = codes_getCode("Don\'t Know",'species');
	error_log("printBirdSpeciesList: dkId = " . $dkId );
	
	$btnClass = 'btn-primary';
	
	$name = codes_getOptionTranslation($dkId);			
	$btnText = "<button type='button' id='species_select_${filterId}_${dkId}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select $dkOtherClass'".$toggleExtras."  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$dkId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	$name = codes_getOptionTranslation($otherId);			
	$btnText = "<button type='button' id='species_select_${filterId}_${otherId}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select $dkOtherClass'".$toggleExtras."  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$otherId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	// Add Nothing and Human 
	error_log("About to get nothing and human ids" );
	$nothingId = codes_getCode("Nothing",'noanimal');
	error_log("printBirdSpeciesList: nothingId = " . $nothingId );
	$humanId = codes_getCode("Human",'noanimal');
	error_log("printBirdSpeciesList: humanId = " . $humanId );
	
	$btnClass = 'btn-primary';
	
	$name = codes_getOptionTranslation($nothingId);			
	$btnText = "<button type='button' id='control_content_${filterId}_${nothingId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control nothing $dkOtherClass'  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$nothingId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	//print "<button type='button' class='btn btn-primary classify_control' id='control_content_$nothingId'>$name</button>";
	
	$name = codes_getOptionTranslation($humanId);			
	$btnText = "<button type='button' id='control_content_${filterId}_${humanId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control $dkOtherClass'  >$name</button>";
	print '<div id="species_group_'.$filterId.'_'.$humanId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
	
	
	
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

// Used to write details of a generated file (eg split or sonogram) to Photo table.
function writeSplitFile ( $ofId, $newFile, $delay = 0 ) {
	
	// Note that the original exif will be stored in OriginalFilesExif.
	
	// Get the data from the original file
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true);
	$query->select("*")
		->from("OriginalFiles")
		->where("of_id = " . $ofId);
	$db->setQuery($query);
	$orig = $db->loadAssoc();
	
	$struc = 'splitaudio';
	
	$dirName = dirname($newFile);
	$fileName = basename($newFile);
	//$taken = $orig['taken'] + $delay;
	$unixTime = strtotime ( $orig['taken'] );
	$newTime = $unixTime + $delay;
	$taken = date('Y-m-d H:i:s', $newTime);
	$fileSize = filesize($newFile);
	
	$biodivFile = new BiodivFile($newFile, $newFile);
	$exif = $biodivFile->exif();
	
	//$exif_extract = getVideoMeta ( $newFile );  
	//$exif = serialize($exif_extract);
		
	$photoFields = new stdClass();
	$photoFields->filename = $fileName;
	$photoFields->upload_filename = $orig['upload_filename'];
	$photoFields->dirname = $dirName;
	$photoFields->site_id = $orig['site_id'];
	$photoFields->upload_id = $orig['upload_id'];
	$photoFields->person_id = $orig['person_id'];
	$photoFields->taken = $taken;
	$photoFields->size = $fileSize;
	$photoFields->exif = $exif;
	
	//$photoId = $db->insertObject('Photo', $photoFields);
	$photoId = codes_insertObject($photoFields, $struc);
	
	if($photoId){
		error_log("Success writing split file " . $newFile . ", photo_id = " . $photoId );
	}
	else {
		error_log("Failed writing split file " . $newFile );
	}
	
	return $photoId;
	
}


function setOriginalFileStatus ($tsId, $success) {
	
	$status = $success ? 1 : 2;
	
	$db = JDatabase::getInstance(dbOptions());
	
	$fields = new stdClass();
	$fields->of_id = $tsId;
	$fields->status = $status;
	$db->updateObject('OriginalFiles', $fields, 'of_id');
	
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

function getBirdClassificationButton ( $id, $animalArray ) {
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
		$features[] = $animalArray[$id]->notes;

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
	
	//error_log("language = " . $lang);
	
	// Check language is supported.  If not, default to English.
	if (!isLanguageSupported($lang)) $lang = "en-GB";
	
	//error_log ("using language " . $lang );
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
	else if ( $lang == "hu-HU" ) {
		$th = '.';
	}
	return ( "" . $num . $th );
}

function getAssociatedArticleId ( $article_id ) {
	
	$lang = langTag();
	
	$assoc_id = null;

	if ($article_id != null)
	{
		$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $article_id);
		
		//print_r($associations);
		if ( $associations ) {
			$assoc_id = $associations[$lang]->id;
		}
	}
	
	// If no associated article (for this language) return the original one
	if ( $assoc_id == null ) $assoc_id = $article_id;
	
	return $assoc_id;
}


// Get the article associated with this option, in the correct language if available.
function getArticle ( $option_id ) {
	
	$option = codes_getDetails($option_id, "optiontran");
	
	//Set up the defaults
	$article_id = $option['article_id'];
	$title = $option['option_name'];
	$introtext = null;
	$fulltext = null;
	
	if( $article_id != 0 ) {
		
		$assoc_id = getAssociatedArticleId ( $article_id );
		
		$jarticle = JTable::getInstance("content");

		$jarticle->load($assoc_id); 
	
		$title = $jarticle->title;
		$introtext = $jarticle->introtext;
		$fulltext = $jarticle->fulltext;
		
	}

	$article = new stdClass();
	$article->id = $article_id;
	$article->title = $title;
	$article->introtext = $introtext;
	$article->fulltext = $fulltext;
	
	return $article;

}

// Get the article associated with this option, in the correct language if available.
function getArticleById ( $article_id ) {
	
	$title = null;
	$introtext = null;
	$fulltext = null;
	
	if( $article_id != 0 ) {
		
		$assoc_id = getAssociatedArticleId ( $article_id );
		
		$jarticle = JTable::getInstance("content");

		$jarticle->load($assoc_id); 
	
		$title = $jarticle->title;
		$introtext = $jarticle->introtext;
		$fulltext = $jarticle->fulltext;
		
	}

	$article = new stdClass();
	$article->id = $article_id;
	$article->title = $title;
	$article->introtext = $introtext;
	$article->fulltext = $fulltext;
	
	return $article;

}



function addSite () {
	
// Get all the data
	$fields = new stdClass();
    $fields->person_id = userID();
	$fields->site_name = JRequest::getString('site_name');
	
	// Validate on person and sitename - don't add if already exists - add message
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->select("site_id")
		->from("Site")
		->where("person_id = " . $fields->person_id . " and site_name = '" . $fields->site_name . "'" );
	
	$db->setQuery($query);
	$result = $db->loadRow();
	
	$site_id = null;
	
	if ( $result && count($result) > 0 ) {
		// Trying to insert site with same name as another site belonging to this person
		JFactory::getApplication()->enqueueMessage('Sorry, you already have a site with that name, could not add the site.');
	}
	else {
		$fields->latitude = JRequest::getString('latitude');
		$fields->longitude = JRequest::getString('longitude');
		$fields->grid_ref = JRequest::getString('grid_ref');
		$fields->habitat_id = JRequest::getInt('habitat_id');
		$fields->water_id = JRequest::getInt('water_id');
		$fields->purpose_id = JRequest::getInt('purpose_id');
		$fields->camera_id = JRequest::getInt('camera_id');
		$fields->camera_height = JRequest::getInt('camera_height');
		$fields->notes = JRequest::getString('notes');
		
		// Insert into the Site table
		$site_id = codes_insertObject($fields, 'site');
		
		//Update the ProjectSiteMap table with the projects this site is in
		$project_ids = JRequest::getVar('project_ids');  
		$fields2 = new stdClass();
		$fields2->site_id = $site_id;
		$fields2->projects = implode(',', $project_ids);
		codes_updateSiteProjects($fields2, 'site');
		
		// Get any project specific data
		$projectsitedata = getSiteDataStrucs($project_ids);
		$unique_strucs = array_unique(array_column($projectsitedata, 'struc'));
		foreach ( $unique_strucs as $struc ) {
			$fields = new stdClass();
			$fields->person_id = userID();
			$fields->site_id = $site_id;
			$fields->option_id = JRequest::getInt($struc."_id");
			// Insert into the SiteData table
			$sitedata_id = codes_insertObject($fields, 'sitedata');
		}
	}
	return $site_id;
}


function getSiteLocation($site_id) {
	
	$siteDetails = codes_getDetails($site_id, 'site');	
	return new Location($siteDetails['latitude'], $siteDetails['longitude']);	

}


// Get an instance of the controller prefixed by BioDiv
$controller = JControllerLegacy::getInstance('BioDiv');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
?>