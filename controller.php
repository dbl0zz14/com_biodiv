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
	
	$site_id = addSite();
	
	/*
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
	
	if ( count($result) > 0 ) {
		// Trying to insert site with same name as another site belonging to this person
		JFactory::getApplication()->enqueueMessage('Sorry, you already have a site with that name, could not add the site.');
	}
	else {
		
		error_log ("About to set fields");
		$fields->latitude = JRequest::getString('latitude');
		$fields->longitude = JRequest::getString('longitude');
		$fields->grid_ref = JRequest::getString('grid_ref');
		$fields->habitat_id = JRequest::getInt('habitat_id');
		$fields->water_id = JRequest::getInt('water_id');
		$fields->purpose_id = JRequest::getInt('purpose_id');
		$fields->camera_id = JRequest::getInt('camera_id');
		$fields->camera_height = JRequest::getInt('camera_height');
		$fields->notes = JRequest::getString('notes');
		
		error_log ("About to insert site");
		
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
	*/
	
    $this->input->set('view', 'trapper');

    parent::display();
  }
  
  function add_site_and_upload(){
    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
	
	$site_id = addSite();
	
	if ( $site_id ) {		
	    //error_log("Setting view to upload, site = ". $site_id);
		$this->input->set('view', 'upload');
		$this->input->set('site_id', $site_id);
	}
	else {
		JFactory::getApplication()->enqueueMessage('Sorry, there was a problem adding the site.');
		$this->input->set('view', 'trapper');
	}


    parent::display();
  }
  

  // User has slid to a different photo - update classification to be same
  // NB now using carousel so photo_id is on JRequest - update com_biodiv with this.
  function next_photo(){
	  
    $app = JFactory::getApplication();
    //$photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);

    $photo_id = JRequest::getString('photo');
	
	// Validate here that this photo is on the current sequence.
	// Check for other classifications and copy them across
	$res = updateSequence($photo_id);
          
	$app->setUserState('com_biodiv.photo_id', $photo_id);
    $this->input->set('view', 'Ajax');

    parent::display();
  }
  

  function get_photo(){
    $app = JFactory::getApplication();
    $photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);
	//error_log("Controller.get_photo() photo_id = " . $photo_id);

    $action = JRequest::getString('action');
    $actionBits = explode("_", $action);
    $firstBit = array_shift($actionBits);
	
	$need_page_reload = false;
	
	if($firstBit == "control"){
		
      $nextBit = array_shift($actionBits);
	  
      switch($nextBit){
      case "content":
		$content_id = array_shift($actionBits);

		// remove any existing classification
		// no longer remove existing classifications, all coexist except
		// Nothing which is disabled if there's any other classification - in Javascript
		//$db = JDatabase::getInstance(dbOptions());
		//$query = $db->getQuery(true);
		//$query->delete("Animal")
		//	->where($db->quoteName("photo_id") . " = '$photo_id'")
		//	->where($db->quoteName("person_id") . " = " . (int)userID());
		//$db->setQuery($query);
		//$success = $db->execute();
	
		// add new control classification (nothing or human)
		$fields = new stdClass();
		$fields->person_id = userID();
		$fields->photo_id = $photo_id;
		$fields->species = $content_id;
		codes_insertObject($fields, 'animal');

		// if human then update the photo to say so and remove any existing Nothing classification
		$db = JDatabase::getInstance(dbOptions());
		if($content_id == 87){
			$fields = new stdClass();
			$fields->photo_id = $photo_id;
			$fields->contains_human = 1;
			// we do note own this object so access db directly
			$db->updateObject('Photo', $fields, 'photo_id');
			
			deleteNothingClassification($photo_id);
		}

		// no longer want Nothing or Human to move the photo on.  This is now controlled by the user 
		//$photoDetails = codes_getDetails($photo_id, "photo");
		//if($photoDetails['next_photo']){
		//  $photo_id = nextPhoto($photo_id);
		//}
		$need_page_reload = true;
		
		break;

      case "next":
		$photo_id = nextPhoto($photo_id);
		break;

      case "goto":
	    $new_photo_id = JRequest::getString('photo');
		$photo_id = $new_photo_id;
		break;

      case "prev":
		$photo_id = prevPhoto($photo_id);
		break;

      case "startseq":
	    $photo_id = photoSequenceStart($photo_id);
	    break;

      case "nextseq":
	    //error_log("Controller.get_photo() nextseq found photo_id = " . $photo_id);
		/*
	    //$photo_id = nextPhoto(0);
	    $sequence = nextSequence();
	    //$photo_id = $sequence[0];
		$firstPhoto = $sequence[0];
		$photo_id = $firstPhoto["photo_id"];
		*/
		//error_log("Controller.get_photo() nextseq setting photo_id to 0");
		
		$app->setUserState('com_biodiv.photo_id', 0);
		$app->setUserState('com_biodiv.animal_ids', 0);
		
		$classifyCount = JRequest::getInt('classify_count');
		if ( $classifyCount ) $app->setUserState('com_biodiv.classify_count', $classifyCount);
		else $app->setUserState('com_biodiv.classify_count', 0);
		
		$isToggled = JRequest::getInt('toggled');
		if ( $isToggled ) $app->setUserState('com_biodiv.toggled', 1 );
		else $app->setUserState('com_biodiv.toggled', 0 );
		
		// If used this stores all those classified by the user.
		$this->all_animal_ids =
	        $app->getUserStateFromRequest('com_biodiv.all_animal_ids', 'all_animal_ids', 0);
		//error_log("Controller: nextseq got all_animal_ids " . $this->all_animal_ids );
		
		
	    break;
      }
    }
	
	if ( $need_page_reload ) {
		$app->setUserState('com_biodiv.photo_id', $photo_id);
		$this->input->set('view', 'Ajax');

		parent::display();
	}
  }
  
  function get_species () {
	  $filterid = JRequest::getString('filterid');
	  
	  //print "filtername = " . $filterid . "<br>";
	  //error_log ( "controller, get_species filterid = " . $filterid  );
	  
	  if ( !$filterid ) {
		  error_log ( "controller, get_species no filterid"  );
	
	  }
	  
	  $app = JFactory::getApplication();
	  $app->setUserState('com_biodiv.filterid', $filterid);
	  
	  $this->input->set('view', 'filter');

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

  // update the projects for this site according to the checklist of values
  function ajax_update_site_projects(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $site_id = JRequest::getInt('pk');
    $field = JRequest::getString('name');
    $value = JRequest::getString('value');
	
    $fields = new stdClass();
    $fields->site_id = $site_id;
    $fields->$field = $value;
	codes_updateSiteProjects($fields, 'site');

    $this->input->set('view', 'Ajax');
    $this->message = "Updated";

    parent::display();
  }

  function set_site_grid_reference(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $site_id = JRequest::getInt('site_id');
    $lat = JRequest::getString('latitude');
    $lng = JRequest::getString('longitude');
	$grid_ref = JRequest::getString('grid_ref');
	
	// If no local ref set to be the lat long
	if ( !$grid_ref ) {
		$short_lat = substr($lat, 0, 5);
		$short_lng = substr($lng, 0, 5);
		$grid_ref = "[" . $short_lat . "," . $short_lng . "]";
	}
    
    $fields = new stdClass();
    $fields->site_id = $site_id;
    $fields->grid_ref = $grid_ref;
	$fields->latitude = $lat;
    $fields->longitude = $lng;
    
    codes_updateObject($fields, 'site');

    $this->input->set('view', 'trapper');

    parent::display();
  }
  
  function update_lat_long() {
	  //error_log ("update_lat_long called" );
	  
	  $jinput = JFactory::getApplication()->input;
	  
	  $sitesArray = $jinput->getArray();
	  
	  $site_ids = array_keys($sitesArray["site"]);
	  
	  foreach ( $site_ids as $site_id ) {
		  
		  $lat = $sitesArray["lat"][$site_id];
		  $lon = $sitesArray["lon"][$site_id];
		  
		  //error_log ("updating site " . $site_id .", lat " . $lat . ", long ".$lon  );
		  
		  update_siteLatLong ( $site_id, $lat, $lon );
	  }
	  
	  $this->input->set('view', 'updatesites');

	  parent::display();
  }

  function add_animal(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

    $fields = new stdClass();
    $fields->person_id = userID();
	// Now set the photo_id from the form value, but check it is in the same sequence as the "current" one.
    //$fields->photo_id = $app->getUserState('com_biodiv.photo_id', 0);
    $formFields = array("photo_id", "species", "gender", "age", "number");
    foreach($formFields as $formField){
      $fields->$formField = JRequest::getInt($formField);
    }
    $animal_id = codes_insertObject($fields, 'animal');
	
	// Remove any existing Nothing classification
	deleteNothingClassification($fields->photo_id);
	
    $this->input->set('view', 'tags');
    parent::display();
  }

  function add_animal_single_tag(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

    $fields = new stdClass();
    $fields->person_id = userID();
	
	// Only do the work if user is logged in
	if ( $fields->person_id ) {
		// Now set the photo_id from the form value, but check it is in the same sequence as the "current" one.
		//$fields->photo_id = $app->getUserState('com_biodiv.photo_id', 0);
		$formFields = array("photo_id", "species", "gender", "age", "number", "sure");
		foreach($formFields as $formField){
		  $fields->$formField = JRequest::getInt($formField, 0);
		}
		// Notes is a text field
		$fields->notes = JRequest::getString("notes", 0);
		// If no photo_id, add it from the request:
		if ( !$fields->photo_id ) $fields->photo_id = $app->getUserState('com_biodiv.photo_id', 0);
		
		$animal_id = codes_insertObject($fields, 'animal');
		
		$app->setUserState('com_biodiv.animal_id', $animal_id);
		
		// add new control classification (nothing or human)
		//	$fields = new stdClass();
		//	$fields->person_id = userID();
		//	$fields->photo_id = $photo_id;
		//	$fields->species = $content_id;
		//	codes_insertObject($fields, 'animal');

		// if human then update the photo to say so
		if($fields->species == 87){
			$db = JDatabase::getInstance(dbOptions());	
			$fields2 = new stdClass();
			$fields2->photo_id = $fields->photo_id;
			$fields2->contains_human = 1;
			// we do note own this object so access db directly
			$db->updateObject('Photo', $fields2, 'photo_id');
		}
		
		$animal_ids = $app->getUserState('com_biodiv.animal_ids', 0);
		
		// Sometimes we store all the animal ids spotted by the user.
		// In original classify mode this is set to 0 on each load of the page
		// In kiosk mode we keep track of the animals in order to give feedback.
		$all_animal_ids = $app->getUserState('com_biodiv.all_animal_ids', 0);
		
		// If anything other than nothing, remove any existing Nothing classification
		// And if there is an animal_id for a nothing classification delete this too.
		if ( $fields->species != 86 ) {
			$deleted_id = deleteNothingClassification($fields->photo_id, $animal_ids);
			//error_log ( "Found deleted nothing id = " . $deleted_id );
			if ( $deleted_id ) removeAnimalId ($deleted_id);
			$animal_ids = $app->getUserState('com_biodiv.animal_ids', 0);
		}
		
		// And add the new animal_id
		if ( !$animal_ids ) {
			//error_log("Setting animal_ids to " . $animal_id);
			$app->setUserState('com_biodiv.animal_ids', $animal_id);
		}
		else {
			//error_log("Setting animal_ids to " . $animal_ids . "_" . $animal_id);
			$app->setUserState('com_biodiv.animal_ids', $animal_ids . "_" . $animal_id);
		}
		
		// And add the new animal_id
		if ( !$all_animal_ids ) {
			//error_log("Setting all_animal_ids to " . $animal_id);
			$app->setUserState('com_biodiv.all_animal_ids', $animal_id);
		}
		else {
			//error_log("Setting all_animal_ids to " . $all_animal_ids . "_" . $animal_id);
			$app->setUserState('com_biodiv.all_animal_ids', $all_animal_ids . "_" . $animal_id);
		}
    }
	$this->input->set('view', 'singletag');
    parent::display();
  }

  function add_bird_single_tag(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

    $fields = new stdClass();
    $fields->person_id = userID();
	
	// Only do the work if user is logged in
	if ( $fields->person_id ) {
		// Now set the photo_id from the form value, but check it is in the same sequence as the "current" one.
		//$fields->photo_id = $app->getUserState('com_biodiv.photo_id', 0);
		$formFields = array("photo_id", "species", "gender", "age", "number", "sure");
		foreach($formFields as $formField){
		  $fields->$formField = JRequest::getInt($formField, 0);
		}
		// Notes is a text field
		$fields->notes = JRequest::getString("notes", 0);
		// If no photo_id, add it from the request:
		if ( !$fields->photo_id ) $fields->photo_id = $app->getUserState('com_biodiv.photo_id', 0);
		
		$animal_id = codes_insertObject($fields, 'animal');
		
		$app->setUserState('com_biodiv.animal_id', $animal_id);
		
		// add new control classification (nothing or human)
		//	$fields = new stdClass();
		//	$fields->person_id = userID();
		//	$fields->photo_id = $photo_id;
		//	$fields->species = $content_id;
		//	codes_insertObject($fields, 'animal');

		// if human then update the photo to say so
		if($fields->species == 87){
			$db = JDatabase::getInstance(dbOptions());	
			$fields2 = new stdClass();
			$fields2->photo_id = $fields->photo_id;
			$fields2->contains_human = 1;
			// we do note own this object so access db directly
			$db->updateObject('Photo', $fields2, 'photo_id');
		}
		
		$animal_ids = $app->getUserState('com_biodiv.animal_ids', 0);
		
		// Sometimes we store all the animal ids spotted by the user.
		// In original classify mode this is set to 0 on each load of the page
		// In kiosk mode we keep track of the animals in order to give feedback.
		$all_animal_ids = $app->getUserState('com_biodiv.all_animal_ids', 0);
		
		// If anything other than nothing, remove any existing Nothing classification
		// And if there is an animal_id for a nothing classification delete this too.
		if ( $fields->species != 86 ) {
			$deleted_id = deleteNothingClassification($fields->photo_id, $animal_ids);
			//error_log ( "Found deleted nothing id = " . $deleted_id );
			if ( $deleted_id ) removeAnimalId ($deleted_id);
			$animal_ids = $app->getUserState('com_biodiv.animal_ids', 0);
		}
		
		// And add the new animal_id
		if ( !$animal_ids ) {
			//error_log("Setting animal_ids to " . $animal_id);
			$app->setUserState('com_biodiv.animal_ids', $animal_id);
		}
		else {
			//error_log("Setting animal_ids to " . $animal_ids . "_" . $animal_id);
			$app->setUserState('com_biodiv.animal_ids', $animal_ids . "_" . $animal_id);
		}
		
		// And add the new animal_id
		if ( !$all_animal_ids ) {
			//error_log("Setting all_animal_ids to " . $animal_id);
			$app->setUserState('com_biodiv.all_animal_ids', $animal_id);
		}
		else {
			//error_log("Setting all_animal_ids to " . $all_animal_ids . "_" . $animal_id);
			$app->setUserState('com_biodiv.all_animal_ids', $all_animal_ids . "_" . $animal_id);
		}
    }
	$this->input->set('view', 'birdtag');
    parent::display();
  }
  
  function remove_animal(){
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

    $photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);
    $animal_id = JRequest::getInt("animal_id");
	
	$db = JDatabase::getInstance(dbOptions());
    
	$animal = codes_getDetails($animal_id, "animal");
	if ( $animal['species'] == 87 ) {
		$fields = new stdClass();
		$fields->photo_id = $photo_id;
		$fields->contains_human = 0;
		// we do note own this object so access db directly
		$db->updateObject('Photo', $fields, 'photo_id');
	}
	
    $conditions = array('person_id = ' . userID(),
			 'photo_id = ' . $photo_id,
			 'animal_id = ' . $animal_id);

    $query = $db->getQuery(true);
    $query->delete($db->quoteName('Animal'));
    $query->where($conditions);
 
    $db->setQuery($query);
 
 
    $result = $db->execute();
	
    $this->input->set('view', 'tags');
  
    parent::display();
  }

  function remove_animal_single_tag(){
	  
	//error_log ("remove_animal_single_tag called" );
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

	// Only do the work if user is logged in
	if ( userID() ) {

		$photo_id = (int)$app->getUserState('com_biodiv.photo_id', 0);
		//error_log ("photo_id = " . $photo_id );
		
		$animal_id = JRequest::getInt("animal_id");
		//error_log ("animal_id = " . $animal_id );
		
		$db = JDatabase::getInstance(dbOptions());
		
		$animal = codes_getDetails($animal_id, "animal");
		if ( $animal['species'] == 87 ) {
			$fields = new stdClass();
			$fields->photo_id = $photo_id;
			$fields->contains_human = 0;
			// we do note own this object so access db directly
			$db->updateObject('Photo', $fields, 'photo_id');
		}
		
		$conditions = array('person_id = ' . userID(),
				 'photo_id = ' . $photo_id,
				 'animal_id = ' . $animal_id);

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('Animal'));
		$query->where($conditions);
	 
		$db->setQuery($query);
	 
	 
		$result = $db->execute();
	
		$app->setUserState('com_biodiv.animal_id', 0);
		
		//error_log ("About to remove animal_id = " . $animal_id );
    
		removeAnimalId ($animal_id);
	
	}
    
	$this->input->set('view', 'singletag');
  
    parent::display();
  }
  
  // Simplified version for kiosk where there are no additional data, just one species id, then get next sequence
  function kiosk_add_animal_next(){
	  
	//error_log ( "kiosk_add_animal_next called" );
    
	$this->kiosk_add_animal ( 'kioskmediacarousel' );
	
  }

  
  // Simplified version for kiosk where there are no additional data, just one species id, then return a results template
  function kiosk_add_animal_finish(){
	  
	$this->kiosk_add_animal ( 'kioskfeedback' );
	
  }
  
  // Kiosk version (for audio) where more than one species can be added
  function kiosk_add_animal_multi(){
	  
	//error_log ( "kiosk_add_animal_multi called" );
    
	$this->kiosk_add_animal ( 'kiosksingletag' );
	
  }

  
  // Kiosk version (for audio) where finished classifying and want next clip
  function kiosk_next_clip(){
	  
	//error_log ( "kiosk_next_clip called" );
	
	$this->input->set('view', 'kioskmediacarousel' );
    parent::display();
	
  }

  
  // Kiosk version (for audio) where finished classifying and want feedback
  function kiosk_get_feedback(){
	  
	//error_log ( "kiosk_get_feedback called" );
	
	$this->input->set('view', 'kioskfeedback' );
    parent::display();
	
  }

  
  
  function kiosk_add_animal ($next_view) {
	
	$app = JFactory::getApplication();

    $fields = new stdClass();
    $fields->person_id = userID();
	
	// Only do the work if user is logged in
	if ( $fields->person_id ) {
		
		// Now set the photo_id from the form value, but check it is in the same sequence as the "current" one.
		$fields->photo_id = $this->input->get('photo_id', 0);
		$fields->species = $this->input->get('species', 0);
		
		//error_log ("Photo id = " . $fields->photo_id);
		//error_log ("Species id = " . $fields->species);
		
		if ( $fields->photo_id == 0 or $fields->species == 0 ) {
			error_log ("add_animal_kiosk_next no photo_id or species");
		}
		else {
			
			$animal_id = codes_insertObject($fields, 'animal');
			
			$app->setUserState('com_biodiv.animal_id', $animal_id);
			
			// if human then update the photo to say so
			if($fields->species == 87){
				$db = JDatabase::getInstance(dbOptions());	
				$fields2 = new stdClass();
				$fields2->photo_id = $fields->photo_id;
				$fields2->contains_human = 1;
				// we do note own this object so access db directly
				$db->updateObject('Photo', $fields2, 'photo_id');
			}
			
			$animal_ids = $app->getUserState('com_biodiv.animal_ids', 0);
			
			// In kiosk mode we keep track of the animals in order to give feedback.
			$all_animal_ids = $app->getUserState('com_biodiv.all_animal_ids', 0);
			
			// And add the new animal_id
			if ( !$animal_ids ) {
				//error_log("Setting animal_ids to " . $animal_id);
				$app->setUserState('com_biodiv.animal_ids', $animal_id);
			}
			else {
				//error_log("Setting animal_ids to " . $animal_ids . "_" . $animal_id);
				$app->setUserState('com_biodiv.animal_ids', $animal_ids . "_" . $animal_id);
			}
			
			// And add the new animal_id
			if ( !$all_animal_ids ) {
				//error_log("Setting all_animal_ids to " . $animal_id);
				$app->setUserState('com_biodiv.all_animal_ids', $animal_id);
			}
			else {
				//error_log("Setting all_animal_ids to " . $all_animal_ids . "_" . $animal_id);
				$app->setUserState('com_biodiv.all_animal_ids', $all_animal_ids . "_" . $animal_id);
			}
		}
    }
	
	//error_log ( "About to call display for view " . $next_view );
	$this->input->set('view', $next_view);
    parent::display();
	
  }

  
    function kiosk_remove_animal(){
	  
	//error_log ("kiosk_remove_animal called" );
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();

	// Only do the work if user is logged in
	if ( userID() ) {

		$animal_id = $this->input->get('animal_id', 0);
		$photo_id = $this->input->get('photo_id', 0);
		
		//error_log ("animal_id = " . $animal_id );
		//error_log ("photo_id = " . $photo_id );
		
		$db = JDatabase::getInstance(dbOptions());
		
		$animal = codes_getDetails($animal_id, "animal");
		if ( $animal['species'] == 87 ) {
			$fields = new stdClass();
			$fields->photo_id = $photo_id;
			$fields->contains_human = 0;
			// we do note own this object so access db directly
			$db->updateObject('Photo', $fields, 'photo_id');
		}
		
		$conditions = array('person_id = ' . userID(),
				 'photo_id = ' . $photo_id,
				 'animal_id = ' . $animal_id);

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('Animal'));
		$query->where($conditions);
	 
		$db->setQuery($query);
	 
	 
		$result = $db->execute();
	
		$app->setUserState('com_biodiv.animal_id', 0);
		
		//error_log ("About to remove animal_id = " . $animal_id );
    
		removeAnimalId ($animal_id);
	
	}
    
	$this->input->set('view', 'singletag');
  
    parent::display();
  }
  
  
  
  function add_challenge(){
	  
    //    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
    $app = JFactory::getApplication();
	
	$fields = new stdClass();
    $fields->person_id = userID();
	
	// Only do the work if user is logged in
	if ( $fields->person_id ) {
		
		$db = JDatabase::getInstance(dbOptions());
				
		$fields->sequence_id = JRequest::getInt("sequence_id", 0);
		$fields->expert_species = JRequest::getString("expert_species", 0);
		$fields->user_species = JRequest::getString("user_species", 0);
		$fields->notes = JRequest::getString("notes", 0);
		
		$success = $db->insertObject("Challenge", $fields);
		
		if(!$success){
			error_log ( "Challenge insert failed" );
		}
				
    }
		
	$this->input->set('view', 'challenge');
    parent::display();
  }
  
  function kiosk_timeout_v1() {
	
	$app = JFactory::getApplication();
	
	$project_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
	
	if ( !$project_id ) {
		  $project_id = JRequest::getString("project_id");
	}
	
	$user_key =
	    $app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);
	
	if ( !$user_key ) {
		  $user_key = JRequest::getString("user_key");
	}
	  
	//error_log ("Controller - project_id = " . $project_id );  
	//error_log ("Controller - user_key = " . $user_key );	
	
	$app->setUserState('com_biodiv.project_id', $project_id);
	$app->setUserState('com_biodiv.user_key', $user_key);
	
	$this->input->set('view', 'startkioskv1');
  
    parent::display();
  }
  
  
  function kiosk_timeout() {
	
	$app = JFactory::getApplication();
	
	$project_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
	
	if ( !$project_id ) {
		  $project_id = JRequest::getString("project_id");
	}
	
	$user_key =
	    $app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);
	
	if ( !$user_key ) {
		  $user_key = JRequest::getString("user_key");
	}
	  
	//error_log ("Controller - project_id = " . $project_id );  
	//error_log ("Controller - user_key = " . $user_key );	
	
	$app->setUserState('com_biodiv.project_id', $project_id);
	$app->setUserState('com_biodiv.user_key', $user_key);
	
	$this->input->set('view', 'startkiosk');
  
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
		
		$success = uploadFile ( $upload_id, $site_id, $tmpName, $clientName, $fileSize, $fileType );
		
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

  function verify_resource_set(){
    
	$app = JFactory::getApplication();
    $set_id = $app->getUserStateFromRequest("com_biodiv.resource_set_id", "resource_set_id", 0);
    
	if(!canEdit($set_id, 'resourceset')){
      die("Cannot edit resource set " . $set_id);
    }
	
	
    $guid = JRequest::getString('guid');
    if(!$guid){
      die("No guid");
    }
	
	$done = JRequest::getBool('done');
	
	
    $db = JDatabase::getInstance(dbOptions());


    if($done){
      $query = $db->getQuery(true);
      $query->delete($db->quoteName("ResourceSetVerify"));
      $query->where("set_id = " . (int)$set_id . " AND guid = '$guid'");
      $db->setQuery($query);
      $success = $db->execute();
    }
    else{
      $verify = new stdClass();
      $verify->set_id = $set_id;
      $verify->guid = $guid;
      $success = $db->insertObject("ResourceSetVerify", $verify);
    }

  }
  
  // handle individual or multiple files 
  function upload_resource_set(){
	  
	$app = JFactory::getApplication();
	$set_id = $app->getUserState('com_biodiv.resource_set_id', 0);
    $isSchoolUpload = JRequest::getInt('school');
	
	if ( $set_id and canEdit($set_id, "resourceset") ) {
	
		$problem = false;
	
		$setDetails = codes_getDetails($set_id, "resourceset");
	
		// $errMsg = print_r ( $setDetails, true );
		// error_log ( "Got resource set details:" );
		// error_log ( $errMsg );
	
		$resource_type = $setDetails['resource_type'];
		$resource_type or die ("No resource_type");

		$fail = 0;
	
		$files = JRequest::getVar('myfile', null, 'files', 'array'); 
		
		// $errMsg = print_r ( $files, true );
		// error_log ( "Got files: " );
		// error_log ( $errMsg );
		
		
		if(!isset($files['tmp_name'])){
		  error_log ( "No file uploaded" );
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
			
			$resourceSet = new Biodiv\ResourceSet ( $set_id );
			
			//$details = codes_getDetails($set_id, "resourceset");
			$resourceType = $resourceSet->getResourceType();
			$dirPath = $resourceSet->getDirPath();
			$dirName = $resourceSet->getDirName();
			//$uploadParamsJson = $resourceSet->getUploadParamsJson();
			//$uploadParams = json_decode($uploadParamsJson);
			
			foreach($tmpNames as $index => $tmpName){
			  
				$clientName = $clientNames[$index];
				$fileSize = $fileSizes[$index];
				$fileType = $fileTypes[$index];
				
				//error_log ("Uploading $clientName: tmp name = $tmpName, filesize = $fileSize, fileType = $fileType" );
				
				//$success = $resourceSet->uploadResourceFile ( $resource_type, $tmpName, $clientName, $fileSize, $fileType );
				
				if($fileSize > BIODIV_MAX_FILE_SIZE){
					error_log ( "Filesize too big" );
					addMsg("error",  "File " . $clientName ." too large: max " . BIODIV_MAX_FILE_SIZE);
					$problem = true;
				}
				
				
				
				//	$tmpName = JFile::makeSafe($tmpName);
				// NB can get this error if PHP max dilsize and upload size are too small:
				else if(!is_uploaded_file($tmpName)){
					//error_log ( "Not an uploaded file $tmpName ( $clientName ) - check file size is below PHP limits, or there may be a network problem" );
					addMsg("error", "$clientName could not be uploaded - file may be too large (max filesize is ". BIODIV_MAX_FILE_SIZE . ") or there may be a network problem");
					$problem = true;
				}
				else {
					
					$ext = strtolower(JFile::getExt($clientName));
					$newName = md5_file($tmpName) . "." . $ext;

					//$newFullName = "$dirPath/$newName";
					$newFullName = "$dirName/$newName";
					
					if(JFile::exists($newFullName)){
						addMsg("warning", "File already uploaded: $clientName");
						$problem = true;
					}
					else {
						
						$tmpName . ", newFullName: " . $newFullName  );

						$exists = JFile::exists($tmpName);
						if ( !$exists ) {
							error_log ( "tmpName file does not exist" );
						}
						else {
							error_log ( "tmpName file does exist" );
						}
						$success=	JFile::upload($tmpName, $newFullName);
						if(!$success){
							error_log ( "New file - upload failed... "   );
							addMsg("error","File upload unsuccessful for $clientName");
							$problem = true;
						}	
						else {
							
							// $accessLevel = Biodiv\SchoolCommunity::PERSON;
							// if ( $isSchoolUpload ) $accessLevel = Biodiv\SchoolCommunity::SCHOOL;
							
							//$accessLevel = $uploadParams->shareLevel;
							
							//error_log ( "Got access level to be " . $accessLevel );
							
							$resourceFile = Biodiv\ResourceFile::createResourceFile ( $set_id, $resourceType, $clientName, $newName, 
												$dirName, $fileSize, $fileType );
												
							if ( !$resourceFile ) {
								error_log ("Problem creating resource file instance" );
								JFile::delete($newFullName);
								$problem = true;
							}
						}
					}
				}
				
				if ( !$success or $problem ) addMsg("error", "Failed to upload resource " . $clientName );
			
			}
		}
	}
	else {
		error_log ("No set id or cannot edit set_id $set_id");
	}

  }
  
  function save_resource () {
	  
	$success = true;
	
	$resourceId = $this->input->getInt('resourceId', 0 );
	
	if ( $resourceId ) {
		
		if ( Biodiv\ResourceFile::canEdit ( $resourceId ) ) {
			
			$school = $this->input->getInt('school', 0 );
			$title = $this->input->getString('uploadName', 0 );
			$description = $this->input->getString('uploadDescription', 0 );
			$resourceType = $this->input->getInt('resourceType', 0 );
			$source = $this->input->getString('source', 0 );
			$externalText = $this->input->getString('externalText', 0 );
			$shareLevel = $this->input->getInt('shareLevel', 0 );
			
			$fields = new stdClass();
			$fields->resource_id = $resourceId;
			if ($school ) $fields->school_id = $school;
			if ($title ) $fields->title = $title;
			if ($description ) $fields->description = $description;
			if ($resourceType ) $fields->resource_type = $resourceType;
			if ($source ) $fields->source = $source;
			
			if ( $source == "external" && $externalText ) {
				$fields->external_text = $externalText;
			}
			if ($shareLevel ) $fields->access_level = $shareLevel;
						
			$db = JDatabase::getInstance(dbOptions());

			$success = $db->updateObject('Resource', $fields, 'resource_id');
			if(!$success){
				error_log ( "Resource update failed" );
			}
			
			$tags = $this->input->get('tag', array(), 'ARRAY');
			
			if ( $tags ) {
				$conditions = array(
					$db->quoteName('resource_id') . ' = ' . $resourceId );
				$query = $db->getQuery(true)
					->delete($db->quoteName('ResourceTag'))
					->where($conditions);
				$db->setQuery($query);
				$result = $db->execute();
				
				foreach ( $tags as $tagId ) {
					$tagFields = (object) [
						'resource_id' => $resourceId,
						'tag_id' => $tagId ];
						
					$success = $db->insertObject("ResourceTag", $tagFields);
					if(!$success){
						error_log ( "ResourceTag insert failed" );
					}
					
				}
			}
			
			if ( $success ) {
				$success = Biodiv\ResourceFile::updateReadable ( $resourceId );
			}
		}
	}

	return $success;
	  
  }

  function sequence_photos(){
    $app = JFactory::getApplication();
    if($upload_id = $app->getUserStateFromRequest("com_biodiv.upload_id", "upload_id", 0)){
      sequencePhotos($upload_id);
      print "Sequenced photos for $upload_id";
    }
  }
  
  
  function no_survey () {
	  
	//error_log ( "no_survey called" );
	
	// Get all the form data and write to db
	
	$person_id = userID();
	
	// Only do the work if user is logged in
	if ( $person_id ) {
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$survey_id = $input->get("survey", 0, 'int');
		//error_log ( "survey id: " . $survey_id );
		
		// Dissent added for all levels
		$db = JDatabase::getInstance(dbOptions());
		
		// Add snapshot of the number of classifications at the time of consent
		$query = $db->getQuery(true);
		$query->select("count(distinct photo_id)")
			->from( "Animal" )
			->where( "person_id=".$person_id )
			->where( "species != 97" );
		$db->setQuery($query);
		$numAnimals = $db->loadResult();
	
		$fields = new stdClass();
		$fields->survey_id = $survey_id;
		$fields->person_id = $person_id;
		$fields->consent_given = 0;
		$fields->num_animals = $numAnimals;
		
		$success = $db->insertObject("UserConsent", $fields);
		
		if(!$success){
			$err_str = print_r ( $fields, true );
			error_log ( "UserConsent insert failed: " . $err_str );
		}
    }
	
  }
  
  
  function take_survey () {
	  
	//error_log ( "take_survey called" );
	
	// Get all the form data and write to db
	
	$person_id = userID();
	
	// Only do the work if user is logged in
	if ( $person_id ) {
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$survey_id = $input->get("survey", 0, 'int');
		//error_log ( "survey id: " . $survey_id );
		
		// Consent only added for top level surveys
		$isFollowUp = BiodivSurvey::isFollowUp($survey_id);
		
		if ( !$isFollowUp ) {
		
			$consent_given = $input->get("consent", 0, 'int');
			//error_log ( "consent_given: " . $consent_given );
		
			$db = JDatabase::getInstance(dbOptions());
			
			// Add snapshot of the number of classifications at the time of consent
			$query = $db->getQuery(true);
			$query->select("count(distinct photo_id)")
				->from( "Animal" )
				->where( "person_id=".$person_id )
				->where( "species != 97" );
			$db->setQuery($query);
			$numAnimals = $db->loadResult();
		
			
			$fields = new stdClass();
			$fields->survey_id = $survey_id;
			$fields->person_id = $person_id;
			$fields->consent_given = $consent_given;
			$fields->num_animals = $numAnimals;
			
			$success = $db->insertObject("UserConsent", $fields);
			
			if(!$success){
				$err_str = print_r ( $fields, true );
				error_log ( "UserConsent insert failed: " . $err_str );
			}
		}
    }
	
	$this->input->set('view', 'survey');
  
    parent::display();
  }
  
  
  function add_response() {
	
	
	//error_log ( "add_response called" );
	
	// Get all the form data and write to db
	
	$person_id = userID();
	
	// Only do the work if user is logged in
	if ( $person_id ) {
		
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$survey_id = $input->get("survey", 0, 'int');
		//error_log ( "survey id: " . $survey_id );
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Has the user already responded to this survey? - check
		$query = $db->getQuery(true);
		$query->select("count(distinct ur_id)")
			->from( "UserResponse" )
			->where( "person_id = " . $person_id )
			->where( "survey_id = " . $survey_id );
		$db->setQuery($query);
		$numSurveyResponses = $db->loadResult();
		
		if ( $numSurveyResponses == 0 ) {
		

			// Get the response types and option translations to refer to
			$responseTypes = BiodivSurvey::getResponseTypes($survey_id);
			$responseTrns = BiodivSurvey::getResponseTranslations();
			
			//$err_str = print_r ( $responseTypes, true );
			//error_log ( "Response types: " . $err_str );
			
			// get the array of responses
			$sqArr = $input->get('sq', 'xxx', 'array');
			
			//$err_str = print_r ( $sqArr, true );
			//error_log ( "sq filtered as array: " . $err_str );
			
			
			foreach ( $sqArr as $sq_id => $response ) {
				
				$fields = new stdClass();
				$fields->survey_id = $survey_id;
				$fields->person_id = $person_id;
				$fields->sq_id = $sq_id;
				
				$responseType = $responseTypes[$sq_id];
				
				if ( $responseType == BiodivSurvey::TEXT ) {
					$fields->response_text = $sqArr[$sq_id];
				}
				else if ( $responseType == BiodivSurvey::OPTION ) {
					$resp_id = $sqArr[$sq_id];
					$fields->response_id = $resp_id;
					$fields->response_text = $responseTrns[$resp_id];
				}
				else if ( $responseType == BiodivSurvey::SCALE10 ) {
					$fields->response_num = $sqArr[$sq_id];
					$fields->response_text = strval($sqArr[$sq_id]); 
				}			
				else if ( $responseType == BiodivSurvey::NUMBER ) {
					$fields->response_num = $sqArr[$sq_id];
					$fields->response_text = strval($sqArr[$sq_id]);
				}
				else if ( $responseType == BiodivSurvey::SCALE10NA ) {
					$fields->response_num = $sqArr[$sq_id];
					$fields->response_text = strval($sqArr[$sq_id]); 
				}			
				
				
				$success = $db->insertObject("UserResponse", $fields);
			
				if(!$success){
					$err_str = print_r ( $fields, true );
					error_log ( "UserResponse insert failed: " . $err_str );
				}
				
			}
			
		}
	}
	
	$this->input->set('view', 'surveydebrief');
  
    parent::display();
	
  }

  
  // upload more files with same deployment, collection dates as those given
  function upload_more () {
	  $app = JFactory::getApplication();
	  $upload_id = JRequest::getInt('upload_id');
	  
	  // Get the upload details
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $query->select("site_id, person_id, camera_tz, is_dst, utc_offset, deployment_date, collection_date")
	    ->from($db->quoteName("Upload"))
	    ->where("upload_id = " .(int)$upload_id)
	    ->where("person_id = " .(int)userID());
	  $db->setQuery($query); 
	  $uploadDetails = $db->loadAssoc();
	  
	  canEdit($uploadDetails['site_id'], "site") or die("Cannot edit site_id $site_id");
	
	  if ( $uploadDetails && count($uploadDetails) > 0 ) {
	  
		$fields = new StdClass();
	  
		$fields->site_id = $uploadDetails['site_id'];
		$fields->person_id = $uploadDetails['person_id'];
		$fields->camera_tz = $uploadDetails['camera_tz'];
		$fields->is_dst = $uploadDetails['is_dst'];
		$fields->utc_offset = $uploadDetails['utc_offset'];
		$fields->deployment_date = $uploadDetails['deployment_date'];
		$fields->collection_date = $uploadDetails['collection_date'];
		
		$new_upload_id = codes_insertObject($fields, 'upload');
		
		//error_log ( "new_upload_id = " . $new_upload_id );
		
		//$app->getUserStateFromRequest('com_biodiv.upload_id', $new_upload_id);
	    $app->setUserState('com_biodiv.upload_id', $new_upload_id);
	    $this->input->set('view', 'uploadm');
	  }
	  else {
		  addMsg('error', "Can't use previous upload");
		  $this->input->set('view', 'upload');
	  }
	  
	  parent::display();
	
  }

  // add new upload
  function add_upload(){
	  
	//error_log ( "Controller: add_upload called");
    
	// Get setting that shows whether this is camera deployment site (eg MammalWeb) or audio (eg NaturesAudio)
	$isCamera = getSetting("camera") == "yes";	  

    $site_id = JRequest::getInt('site_id');
    $site_id or die("No site_id");
    
	$upload_id = addUpload ( $isCamera, $site_id );
	
	if(!$upload_id){
	  $this->input->set('view', 'upload');
	}
	else{
	  $this->input->set('view', 'uploadm');
	}
	
	/*
	$fields = new StdClass();
	
	// For audio no need for deployment start and end times, camera only
	if ( $isCamera ) {
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
		
		$fields->deployment_date = $datetime['deployment'];
		$fields->collection_date = $datetime['collection'];
		
	}
	
	// These are the common fields
	$camera_tz = JRequest::getString('timezone');
	$tz = 0;
	$is_dst = 0;
	$utc_offset = 0;
	if(!strlen($camera_tz)){
		addMsg('error', "No camera timezone specified");
	}
	// Get the offset
	$tz = IntlTimeZone::createTimeZone($camera_tz);
	$utc_offset = $tz->getRawOffset()/60000;
	
	$is_dst = JRequest::getInt('dst');
	if ($is_dst == 1 ) {
		// Adjust the offset from UTC for daylight saving time
		$utc_offset += $tz->getDSTSavings()/60000;
	}
	
		
	if(someMsgs("error")){
	  $this->input->set('view', 'upload');
	}
	else{
	  $fields->person_id = userID();
	  $fields->site_id = $site_id;
	  $fields->camera_tz = $camera_tz;
	  $fields->is_dst = $is_dst;
	  $fields->utc_offset = $utc_offset;
	  $upload_id = codes_insertObject($fields, 'upload');
	  $app->setUserState('com_biodiv.upload_id', $upload_id);
	  $this->input->set('view', 'uploadm');
	}
	*/
	
	parent::display();

  }
  
  function check_like($photo_id = 0){
	$app = JFactory::getApplication();
    if(!$photo_id){
	  $photo_id = JRequest::getInt('photo_id');
      //$photo_id = $app->getUserState('com_biodiv.photo_id', 0);
    }
	$app->setUserState('com_biodiv.like_photo_id', $photo_id);
    $this->input->set('view', 'like');
    parent::display();
  }
  
  function like_photo($photo_id = 0){
    $app = JFactory::getApplication();
    if(!$photo_id){
	  $photo_id = JRequest::getInt('photo_id');
      //$photo_id = $app->getUserState('com_biodiv.photo_id', 0);
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

  function unlike_photo($photo_id = 0){
    $app = JFactory::getApplication();
	
	if(!$photo_id){
	  $photo_id = JRequest::getInt('photo_id');
      //$photo_id = $app->getUserState('com_biodiv.photo_id', 0);
    }
    
    print "Unliked photo_id $photo_id";
    $restrictions = array('person_id' => (int)userID(),
			 'photo_id' => $photo_id);

    foreach(likes($restrictions) as $animal_id => $details){
      print "Deleting classification animal_id $animal_id";
      codes_deleteObject($animal_id, "animal");
    }
    
    print "Classifications for $photo_id";
    print_r(myClassifications($photo_id));
  }
}

?>