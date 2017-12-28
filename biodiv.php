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
  return siteURL($details['site_id']) . "/". $details['filename'];
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

function isFavourite($photo_id){
  foreach(myClassifications($photo_id) as $details){
    if($details->species == 97){
      return true;
    }
  }
  return false;
}

function nextPhoto($prev_photo_id){
  $db = JDatabase::getInstance(dbOptions());
  
  // Initialise photo_id to null
  $photo_id = null;

  $pdetails = codes_getDetails($prev_photo_id, 'photo');
  $next_photo = $pdetails['next_photo'];
  if($next_photo){
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
  if(!$photo_id){
    // choose random picture
    $app = JFactory::getApplication();
    if($app->getUserState("com_biodiv.classify_self")){
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