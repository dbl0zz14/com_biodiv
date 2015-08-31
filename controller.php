<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * Hello World Component Controller
 *
 * @since   0.0.1
 */
class BioDivController extends JControllerLegacy
{

  function ajax(){
    $this->input->set('view', 'Ajax');

    parent::display();

  }

  function add_site(){
    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $fields = new stdClass();
    $fields->person_id = userID();
    $fields->site_name = "[New site]";
    codes_insertObject($fields, 'site');

    $this->input->set('view', 'trapper');

    parent::display();
  }

  function get_photo(){
    $app = JFactory::getApplication();
    $photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);

    $action = JRequest::getString('action');
    $actionBits = explode("_", $action);
    $firstBit = array_shift($actionBits);
    if($firstBit == "control"){
      $nextBit = array_shift($actionBits);
      switch($nextBit){
      case "content":
	$content_id = array_shift($actionBits);

	// remove any existing classification
	$db = JDatabase::getInstance(dbOptions());
	$query = $db->getQuery(true);
	$query->delete("Animal")
	  ->where($db->quoteName("photo_id") . " = '$photo_id'")
	  ->where($db->quoteName("person_id") . " = " . (int)userID());
	$db->setQuery($query);
	$success = $db->execute();
	
	// add new control classification (nothing or human)
	$fields = new stdClass();
	$fields->person_id = userID();
	$fields->photo_id = $photo_id;
	$fields->species = $content_id;
	codes_insertObject($fields, 'animal');

	// if human then update the photo to say so
	if($content_id == 87){
	  $fields = new stdClass();
	  $fields->photo_id = $photo_id;
	  $fields->contains_human = 1;
	  // we do note own this object so access db directly
	  $db->updateObject('Photo', $fields, 'photo_id');
	}

	$photoDetails = codes_getDetails($photo_id, "photo");
	if($photoDetails['next_photo']){
	  $photo_id = nextPhoto($photo_id);
	}
	break;

      case "next":
	$photo_id = nextPhoto($photo_id);
	break;

      case "prev":
	$photo_id = prevPhoto($photo_id);
	break;

      case "nextseq":
	$photo_id = nextPhoto(0);
	break;
      }
    }
    $app->setUserState('com_biodiv.photo_id', $photo_id);
    $this->input->set('view', 'Ajax');

    parent::display();
  }

  // update a single field
  function ajax_update_site(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $site_id = JRequest::getInt('pk');
    $field = JRequest::getString('name');
    $value = JRequest::getString('value');
    
    $fields = new stdClass();
    $fields->site_id = $site_id;
    $fields->$field = $value;
    codes_updateObject($fields, 'site');

    $this->input->set('view', 'Ajax');
    $this->message = "Updated";

    parent::display();
  }

  function set_site_grid_reference(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $site_id = JRequest::getInt('site_id');
    $grid_ref = JRequest::getString('grid_ref');
    $fields = new stdClass();
    $fields->site_id = $site_id;
    $fields->grid_ref = $grid_ref;
    codes_updateObject($fields, 'site');

    $this->input->set('view', 'trapper');

    parent::display();
  }

  function add_animal(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

    $fields = new stdClass();
    $fields->person_id = userID();
    $fields->photo_id = $app->getUserState('com_biodiv.photo_id', 0);
    $formFields = array("species", "gender", "age", "number");
    foreach($formFields as $formField){
      $fields->$formField = JRequest::getInt($formField);
    }
    $animal_id = codes_insertObject($fields, 'animal');
    $this->input->set('view', 'tags');
    parent::display();
  }

  function remove_animal(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

    $photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);
    $animal_id = JRequest::getInt("animal_id");
    $conditions = array('person_id = ' . userID(),
			 'photo_id = ' . $photo_id,
			 'animal_id = ' . $animal_id);

    $db = JDatabase::getInstance(dbOptions());
    $query = $db->getQuery(true);
    $query->delete($db->quoteName('Animal'));
    $query->where($conditions);
 
    $db->setQuery($query);
 
 
    $result = $db->execute();

    $this->input->set('view', 'tags');
  
    parent::display();
  }

  // handle individual or multiple files uploaded as images
  function uploadm(){
    $upload_id = JRequest::getInt('upload_id');
    $upload_id or die("No upload_id");
    canEdit($upload_id, "upload") or die("Cannot edit upload_id $upload_id");
    
    $uploadDetails = codes_getDetails($upload_id, "upload");
    $site_id = $uploadDetails['site_id'];
    $site_id or die ("No site_id");

    $fail = 0;

    $files = JRequest::getVar('myfile', null, 'files', 'array'); 
    if(!isset($files['tmp_name'])){
      addMsg("error", "No file uploaded");
      $fail = 1;
    }
    $tmpNames = $files['tmp_name'];  // assuming multiple upload
    $clientNames = $files['name'];  // assuming multiple upload
    $fileSizes = $files['size'];  // assuming multiple upload
    $fileTypes = $files['type'];  // assuming multiple upload
    if(!is_array($tmpNames)){  // if single upload
      $tmpNames = array($tmpNames);
      $clientNames = array($clientNames);
      $fileSizes = array($fileSizes);
      $fileTypes = array($fileTypes);
    }
    if(!$fail){
      foreach($tmpNames as $index => $tmpName){
	$clientName = $clientNames[$index];
	$fileSize = $fileSizes[$index];
	$fileType = $fileTypes[$index];
	if($fileSize > BIODIV_MAX_FILE_SIZE){
	  addMsg("error",  "File " . $clientName ." too large: max " . BIODIV_MAX_FILE_SIZE);
	  continue;
	} 
	//	$tmpName = JFile::makeSafe($tmpName);
	if(!is_uploaded_file($tmpName)){
	  addMsg("error", "Not an uploaded file $tmpName");
	  continue;
	}
	//	JHelperMedia::isImage($tmpName) or die("Not an image");

	$ext = strtolower(JFile::getExt($clientName));
	$newName = md5_file($tmpName) . "." . $ext;
	
	$dirName = siteDir($site_id);
	$newFullName = "$dirName/$newName";
	if(JFile::exists($newFullName)){
	  addMsg("warning", "File already uploaded: $clientName");
	  continue;
	}
	
	$exif_extract = exif_read_data($tmpName);
	$taken = $exif_extract['DateTimeOriginal'];
	$manufacturer = $exif_extract['Manufacturer'];
	$exif = serialize($exif_extract);
	// check exif headers for camera type?
	// check dates defined and photos in range

	JFile::upload($tmpName, $newFullName);
	$photoFields = new stdClass();
	$photoFields->filename = $newName;
	$photoFields->upload_filename = $clientName;
	$photoFields->dirname = $dirName;
	$photoFields->site_id = $site_id;
	$photoFields->upload_id = $upload_id;
	$photoFields->person_id = userID();
	$photoFields->taken = $taken;
	$photoFields->size = $fileSize;
	$photoFields->exif = $exif;
	if(codes_insertObject($photoFields, 'photo')){
	  addMsg('success', "Uploaded $clientName");
	}
      }
    }

    $this->input->set('view', 'trapper');
    parent::display();
  }

  function verify_upload(){
    $app = JFactory::getApplication();
    $upload_id = $app->getUserStateFromRequest("com_biodiv.upload_id", "upload_id", 0);
    if(!canEdit($upload_id, 'upload')){
      die("Cannot edit upload " . $upload_id);
    }
    $guid = JRequest::getString('guid');
    if(!$guid){
      die("No guid");
    }
    $done = JRequest::getBool('done');

    $db = JDatabase::getInstance(dbOptions());


    if($done){
      $query = $db->getQuery(true);
      $query->delete($db->quoteName("UploadVerify"));
      $query->where("upload_id = " . (int)$upload_id . " AND guid = '$guid'");
      $db->setQuery($query);
      $success = $db->execute();
    }
    else{
      $verify = new stdClass();
      $verify->upload_id = $upload_id;
      $verify->guid = $guid;
      $success = $db->insertObject("UploadVerify", $verify);
    }
  }

  function sequence_photos(){
    $app = JFactory::getApplication();
    if($upload_id = $app->getUserStateFromRequest("com_biodiv.upload_id", "upload_id", 0)){
      sequencePhotos($upload_id);
      print "Sequenced photos for $upload_id";
    }
  }

  // add new upload
  function add_upload(){
    $app = JFactory::getApplication();

    $site_id = JRequest::getInt('site_id');
    $site_id or die("No site_id");
    canEdit($site_id, "site") or die("Cannot edit site_id $site_id");
    
    foreach(array("deployment", "collection") as $dt){
      $date = JRequest::getString("${dt}_date");
      if(!strlen($date)){
	addMsg('error', "No $dt date specified");
      }
      $hours = JRequest::getString("${dt}_hours");
      if(!strlen($hours)){
	addMsg('error', "No $dt hours specified");
      }
      $mins = JRequest::getString("${dt}_mins");
      if(!strlen($mins)){
	addMsg('error', "No $dt mins specified: $mins ");
      }
      $datetime[$dt] = $date . " " . $hours . ":" . $mins;
    }

    if(someMsgs("error")){
      $this->input->set('view', 'upload');
    }
    else{
      $fields = new StdClass();
      $fields->person_id = userID();
      $fields->site_id = $site_id;
      $fields->deployment_date = $datetime['deployment'];
      $fields->collection_date = $datetime['collection'];
      $upload_id = codes_insertObject($fields, 'upload');
      $app->setUserState('com_biodiv.upload_id', $upload_id);
      $this->input->set('view', 'uploadm');
    }

    parent::display();

  }

  function like_photo($photo_id = 0){
    $app = JFactory::getApplication();
    if(!$photo_id){
      $photo_id = $app->getUserState('com_biodiv.photo_id', 0);
    }
    $this->unlike_photo($photo_id);

    $fields = new stdClass();
    $fields->person_id = userID();
    $fields->photo_id = $photo_id;
    $fields->species = 97; // like
    $animal_id = codes_insertObject($fields, 'animal');
    print "Liked animal_id $animal_id";
    print "Classifications for $photo_id";
    print_r(myClassifications($fields->photo_id));
  }

  function unlike_photo(){
    $app = JFactory::getApplication();
    $photo_id = $app->getUserState('com_biodiv.photo_id', 0);

    print "Unliked photo_id $photo_id";
    $restrictions = array('person_id' => (int)userID(),
			 'photo_id' => $photo_id,
			 'species' => 97);

    foreach(classifications($restrictions) as $animal_id => $details){
      print "Deleting classification animal_id $animal_id";
      codes_deleteObject($animal_id, "animal");
    }
    
    print "Classifications for $photo_id";
    print_r(myClassifications($photo_id));
  }
}

?>