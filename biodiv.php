<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

include "local.php";

define('BIODIV_MAX_FILE_SIZE', 4000000);

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
	
	print "codes_updateSiteProjects called ";
	if ( $struc != 'site' ) {
		return false;
	}
	$codeField = codes_getCodeField($struc); // "site_id"
	$code = $fields->$codeField; // the actual site_id
	print "codes_updateSiteProjects site_id = " . $code;
	
	if(!canEdit($code, $struc)){
		return false;
	}
	
	$name = 'projects';
	//$value = $fields->$name; // the new list of values from checklist as a JSON string.
	//$valuesArray = json_decode($value); // The values from JSON string decoded back to array
	//$valuesArray = $fields->$name;
	$valuesArray=explode(",", $fields->$name);
	print "<br>Got the following projects for site_id " . $code . ":  ";
	print_r($valuesArray);
	
	//$valuesArray = explode(",", $val);
	
	
	if ( $valuesArray == null ) $valuesArray = array(1);
	
	$testArray = array(7);
		
	$success = false;
	
	$db = JDatabase::getInstance(dbOptions());
	
	// Use a transaction as we want to delete all previous entries then add the new ones, but need 
	// both these operations to succeed or fail.
	try
	{
		$db->transactionStart();
	
		$query = $db->getQuery(true);
    
		//$values = array($db->quote('TEST_CONSTANT'), $db->quote('Custom'), $db->quote('/path/to/translation.ini'));
    
		//$query->insert($db->quoteName('#__overrider'));
		//$query->columns($db->quoteName(array('constant', 'string', 'file')));
		//$query->values(implode(',',$values));

		//$db->setQuery($query);
		//$result = $db->execute();
		
		$table = "ProjectSiteMap";

		$query->delete($db->quoteName($table));
		$query->where("site_id = " . $code);
		$db->setQuery($query);
		$result = $db->execute();
		
		// Need to loop through the values inserting each in turn for this site_id.
		foreach ( $valuesArray as $thisValue ) {
			$query2 = $db->getQuery(true);
			$query2->insert($db->quoteName($table));
			$query2->columns($db->quoteName(array('project_id', 'site_id')));
			$query2->values("" . $thisValue . ", " . $code);
			$db->setQuery($query2);
			$result = $db->execute();
		}
		
		$db->transactionCommit();
		
		$success = true;
	}
	catch (Exception $e)
	{
		// catch any database errors.
		$db->transactionRollback();
	}
	
    return $success;
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

function photoURL($photo_id){
  $details = codes_getDetails($photo_id, 'photo');
  // debug
  // echo siteURL($details['site_id']) . "/". $details['filename'];
  // debug end
  return siteURL($details['site_id']) . "/". $details['filename'];
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

// Helper function to recursively add child projects.
//function addSubProjects (&$projects, &$pairs) {
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

function myProjectDetails(){
  // Call myprojects to get the project list, then get details for each.
  $myprojects = myProjects();
  
  $id_string = implode(",", array_keys($myprojects));
  
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.dirname, P.image_file, P.project_text")->from("Project P");
  $query->where("P.project_id in (".$id_string.")");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $projectdetails = $db->loadObjectList();
  
  
  //print "<br/>Got " . count($projectdetails) . " all project details user has access to<br/>They are:<br>";
  //print implode(",", $projectdetails);
  
  return $projectdetails;
}

// Return a list of this project and all its children.
// Called with proj prettyname for now.  Refactor later if necessary.
function getSubProjects($project_prettyname){
  //print "<br/>getSubProjects called<br/>";
  
  // first select all project/parent pairs into memory
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("project_id, parent_project_id, project_prettyname")->from("Project");
  $db->setQuery($query);
  $allpairs = $db->loadAssocList();
  //print "<br/>Got " . count($allpairs) . " project/parent pairs<br/>\n";
  
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

// return all projects that are/should be listed on the website
function listedProjects(){
  // what user am I?
  $person_id = (int)userID();
  
  // For now we'll load all projects
  $db = JDatabase::getInstance(dbOptions());
  $query = $db->getQuery(true);
  $query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description")->from("Project P");
  $query->where("P.access_level < 2");
  $query->order("P.project_id" );
  $db->setQuery($query);
  $listedprojects = $db->loadObjectList();
  
  //print "<br/>Got " . count($listedprojects) . " non-private projects<br/>They are:<br>";
  //print_r($listedprojects);
  
  return $listedprojects;
}


function isFavourite($photo_id){
  foreach(myClassifications($photo_id) as $details){
    if($details->species == 97){
      return true;
    }
  }
  return false;
}

function nextSequence($prev_photo_id){
	
  //print "<br/>nextSequence called<br/>";
	
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
	/* continue causes error when used in included file so avoid this..
    if(rand(0,10)>7){
      continue;
    }
	*/
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
	  /*
    if(!$photo_id){
      $query = $db->getQuery(true);
      $query->select("P.photo_id, P.sequence_id")
	->from("Photo P")
	->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
	->where("A.photo_id IS NULL")
	->where("P.contains_human =0")
      ->order("rand()");
      $db->setQuery($query, 0, 1); // LIMIT 1
      $photo = $db->loadObject();
      $photo_id = $photo->photo_id;
    }
	*/

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
	/* continue causes error when used in included file so avoid this..
    if(rand(0,10)>7){
      continue;
    }
	*/
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
	  /*
    if(!$photo_id){
      $query = $db->getQuery(true);
      $query->select("P.photo_id, P.sequence_id")
	->from("Photo P")
	->leftJoin("Animal A ON P.photo_id = A.photo_id AND A.person_id = " . (int)userID())
	->where("A.photo_id IS NULL")
	->where("P.contains_human =0")
      ->order("rand()");
      $db->setQuery($query, 0, 1); // LIMIT 1
      $photo = $db->loadObject();
      $photo_id = $photo->photo_id;
    }
	*/

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
      else{  // end current sequence
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


function fbInit(){
?>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
    FB.init({
      appId      : '1612663612328391',
	  xfbml      : true,
	  version    : 'v2.4'
	  });
  };

  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>

<?php
}

function fbLikePhoto($photo_id){
?>
<div class="fb-like" data-href="<?php print BIODIV_ROOT;?>&amp;view=show&amp;photo_id=<?php print $photo_id;?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
<?php
}

// Get an instance of the controller prefixed by BioDiv
$controller = JControllerLegacy::getInstance('BioDiv');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
?>