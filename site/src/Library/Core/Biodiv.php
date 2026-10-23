<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/ 

namespace MammalWeb\Component\Biodiv\Site\Library\Core;


use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;


// No direct access to this file
defined('_JEXEC') or die;


//set_include_path('' . JPATH_COMPONENT . ';' . JPATH_COMPONENT . '/src/Library;' . get_include_path());

//require_once "local.php";
//require_once "codes.php";
//require_once "biodiv.php";

require __DIR__ . '/../../../vendor/autoload.php';
use \JamesHeinrich\getid3;


use MammalWeb\Component\Biodiv\Site\Library\Core\Location;
use MammalWeb\Component\Biodiv\Site\Library\Core\Sequence;
use MammalWeb\Component\Biodiv\Site\Library\Core\BiodivFile;
use MammalWeb\Component\Biodiv\Site\Library\Core\Project;
use MammalWeb\Component\Biodiv\Site\Library\School\ResourceSet;
use MammalWeb\Component\Biodiv\Site\Library\School\ResourceFile;
use MammalWeb\Component\Biodiv\Site\Library\Wrapper\AWS;



class Biodiv {
	
	public static function userID(){
		$user = Factory::getUser();
		if($user->guest){
			return null;
		}
		else{
			return $user->id;
		}
	}

	public static function userTimezone() {
		$user = Factory::getUser();
		if($user->guest){
			return null;
		}
		else{
			return $user->getTimezone();
		}
	}
	
	public static function userTimezoneCode() {
		$user = Factory::getUser();
		$tzStr = $user->getParam('timezone', 'UTC');
		// if($user->guest){
			// return null;
		// }
		// else{
			// $tzStr = $user->getParam('timezone', 'UTC');
			// return $tzStr;
		// }
		return $tzStr;
	}
	
	public static function langTag() {
	
		return langTag();
	}

	public static function isLanguageSupported ( $lang ) {
		
		return isLanguageSupported ( $lang );
	}

	public static function getSetting ( $key ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$query = $db->getQuery(true);
		$query->select("s_value")->from("Settings")
			->where("s_key = " . $db->quote($key) );
		$db->setQuery($query);
		$value = $db->loadResult();
		
		return $value;
	}
		
	public static function canEdit($id, $struc, $allow = 0){
		
		static $allows;

		if($struc == "sequence"){
			return true;
		}
		if($allow){
			$allows[$struc][$id] = 1;
		}
		if( isset($allows[$struc][$id]) && $allows[$struc][$id] ){
			return true;
		}
  
		$details = codes_getDetails($id, $struc);
		if(!self::userID()){
			return false;
		}
		switch($struc){
			case 'upload':
			case 'photo':
			case 'site':
			case 'animal':
				return $details['person_id'] == self::userID();
				break;

			case 'resourceset':
				return ResourceSet::canEdit($id);
				break;

			case 'resourcefile':
				return ResourceFile::canEdit($id);
				break;

			case 'sequence':
				return self::canEdit($details['upload_id'], 'upload');
				break;

			default:
				return false;
		}
	}

	public static function canCreate($struc, $fields){
		
		if($struc=="sequence" || $struc=="splitaudio"){
			return true;
		}
		if(!self::userID()){
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
			case 'origlargefile':
			case 'report':
			case 'resourceset':
			case 'resourcefile':
				return $fields->person_id == self::userID();   
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

	public static function canView ($struc, $fields) {
		
		switch($struc){
			case 'project':
				// If it's a public or protected project can view it.  If not must have access.  Only private projects cannot be viewed.
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
				$db = db();
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

	public static function canClassify ($struc, $fields) {
		
		switch($struc){
			case 'project':
				$person_id = self::userID();
				
				if (!$person_id ) {
					return false;
				}
				
				// If it's a public or protected project can view it.  If not must have access.
				// Check this project against "myProjects".
				$projects = Project::mySpottingProjects();
				
				if ( in_array($fields->project_id, array_keys($projects)) ) return true;
				else return false;
				break;
			default:
				return false;
		}
	}

	public static function canUpload ($struc, $fields) {
		
		switch($struc){
			case 'project':
				$person_id = self::userID();
				
				if (!$person_id ) {
					return false;
				}
				
				// If it's a public or protected project can view it.  If not must have access.
				// Check this project against "myProjects".
				$projects = myTrappingProjects();
				
				if ( in_array($fields->project_id, array_keys($projects)) ) return true;
				else return false;
				break;
			default:
				return false;
		}
	}

	public static function canRunScripts(){
	  
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

	public static function addMsg($type, $msg){
		$app = Factory::getApplication();
		$msgs = $app->getUserState("com_biodiv.msgs", self::noMsgs());

		$msgs[$type][] = $msg;
		$app->setUserState("com_biodiv.msgs", $msgs);
	}

	public static function noMsgs(){
		return array('success' => array(), 'warning' => array(), 'error' => array());
	}

	public static function getMsgs(){
		$app = Factory::getApplication();
		$msgs = $app->getUserState("com_biodiv.msgs", self::noMsgs());
		return $msgs;
	}

	public static function someMsgs($type){
		$msgs = self::getMsgs();
		return count($msgs[$type]);
	}

	public static function totalMsgs(){
		$msgs = self::getMsgs();
		return count($msgs['success']) + count($msgs['warning']) + count($msgs['error']);
	}

	public static function showMessages(){
	
		$app = Factory::getApplication();
		$msgs = $app->getUserState("com_biodiv.msgs", self::noMsgs());
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
		$app->setUserState("com_biodiv.msgs", self::noMsgs());
	}
	
	public static function clearMessages(){
	
		$app = Factory::getApplication();
		$app->setUserState("com_biodiv.msgs", self::noMsgs());
	}
	
	public static function showAlert ( $type, $msg ) {
		
		if($type == 'error'){
			$class = "alert-danger";
		}
		else{
			$class = "alert-$type";
		}
		print "<div class='alert-dismissable $class'><p class='$class'><b>" . ucfirst($type). "</b> $msg</p></div>\n";
				
	}
	
	public static function uploadRoot(){
	//  return JPATH_COMPONENT . "/uploads";
		//return "/var/www/html/biodivimages";
		return JPATH_SITE."/biodivimages";
	}

	public static function reportRoot(){
	//  return JPATH_COMPONENT . "/uploads";
		//return "/var/www/html/biodivimages";
		return JPATH_SITE."/biodivimages/reports";
	}

	// get dir of where images from a given site are stored
	public static function siteDir($site_id){
	  $site_id = (int)$site_id;
	  $site_id or die("Cannot find siteDir");

	  $details = codes_getDetails($site_id, "site");
	  $person_id = (int)$details['person_id'];
	  $person_id or die("Cannot find person for siteDir");
	  
	  return self::uploadRoot()."/person_{$person_id}/site_{$site_id}";
	}

	public static function siteURL($site_id){
	  $site_id = (int)$site_id;
	  $site_id or die("Cannot find siteDir");

	  $details = codes_getDetails($site_id, "site");
	  $person_id = (int)$details['person_id'];
	  $person_id or die("Cannot find person for siteDir");
	  
	  return Uri::root()."/biodivimages/person_{$person_id}/site_{$site_id}";
	}

	public static function biodivRoot() {
		return Uri::root();
	}
	
	public static function getOptionData ( $optionId, $dataType ) {
  
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("OD.value")->from("OptionData OD")
			->where("OD.data_type = '" . $dataType . "'"  )
			->where("OD.option_id = " . $optionId );
			
		$db->setQuery($query);
		$optionData = $db->loadColumn();
		
		return $optionData;

	}
	
	public static function getSupportedLanguages () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$query = $db->getQuery(true);
		$query->select("*")
			->from("Language")
			->where("tag != " . $db->quote('en-GB') );
		$db->setQuery($query);
		$langs = $db->loadObjectList("language_id");
		
		return $langs;
	}
	
	public static function getJSONDecodedArray ( $string ) {
		
		$decoded = null;
		if ( is_string ( $string ) ) {
			$newArray = json_decode($string, true);
			if ( is_array($newArray) && (json_last_error() == JSON_ERROR_NONE) ) {
				$decoded = $newArray;
			}
		}
		
		return $decoded;
	   
	}
	
	public static function checkHtml ( $htmlStr ) {
		
		$warning = null;
		
		// Just a basic check
		if ( strpos ( strtolower($htmlStr), "script") !== FALSE ) {
			$warning = "Text may contain a script";
		}
		
		return $warning;
	}

	public static function getAssociatedArticleId ( $article_id ) {
	
		$lang = self::langTag();
		
		$assoc_id = null;

		if ( $lang && ($article_id != null) )
		{
			$associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $article_id);
			
			//print_r($associations);
			if ( $associations ) {
				if ( array_key_exists( $lang, $associations ) ) {
					$assoc_id = $associations[$lang]->id;
				}
			}
		}
		
		// If no associated article (for this language) return the original one
		if ( $assoc_id == null ) $assoc_id = $article_id;
		
		return $assoc_id;
	}

  	// Get the article associated with this option, in the correct language if available.
	public static function getArticle ( $option_id ) {
		
		$option = codes_getDetails($option_id, "optiontran");
		
		//Set up the defaults
		$article_id = $option['article_id'];
		$title = $option['option_name'];
		$introtext = null;
		$fulltext = null;
		
		if( $article_id != 0 ) {
			
			$assoc_id = self::getAssociatedArticleId ( $article_id );
			
			$jarticle = Table::getInstance("content");

			$jarticle->load($assoc_id); 
		
			$title = $jarticle->title;
			$introtext = $jarticle->introtext;
			$fulltext = $jarticle->fulltext;
			
		}

		$article = new \stdClass();
		$article->id = $article_id;
		$article->title = $title;
		$article->introtext = $introtext;
		$article->fulltext = $fulltext;
		
		return $article;

	}

// Get the article associated with this option, in the correct language if available.
	public static function getArticleById ( $article_id ) {
		
		$title = null;
		$introtext = null;
		$fulltext = null;
		
		if( $article_id != 0 ) {
			
			$assoc_id = self::getAssociatedArticleId ( $article_id );
			
			$jarticle = Table::getInstance("content");

			$jarticle->load($assoc_id); 
		
			$title = $jarticle->title;
			$introtext = $jarticle->introtext;
			$fulltext = $jarticle->fulltext;
			
		}

		$article = new \stdClass();
		$article->id = $article_id;
		$article->title = $title;
		$article->introtext = $introtext;
		$article->fulltext = $fulltext;
		
		return $article;

	}

	public static function getLikes($max_num){
	
		$person_id = (int)self::userID();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("DISTINCT photo_id ")->from("Animal");
		$query->where("person_id = " . $person_id . " and species = 97");
		$query->order("rand()");
		$db->setQuery($query, 0, $max_num); // LIMIT $max_num
		$mylikes = $db->loadColumn();

		return $mylikes;
		
	}
	
	public static function checkLike($photoId = 0){
		
		$app = Factory::getApplication();
		
		if(!$photoId){
			$input = $app->getInput();
			$photoId = $input->getInt('photo_id');
		}
		$app->setUserState('com_biodiv.like_photo_id', $photoId);
	}

	public static function likePhoto($photoId = 0){
		
		$personId = self::userID();
		
		if ( $personId ) {
		
			if(!$photoId){
				$app = Factory::getApplication();
				$input = $app->getInput();
				$photoId = $input->getInt('photo_id');
			}
			self::unlikePhoto($photoId);

			$fields = new \StdClass();
			$fields->person_id = $personId;
			$fields->photo_id = $photoId;
			$fields->species = 97; // like
			$fields->gender = 0;
			$fields->age = 0;
			$fields->number = 0;
			$fields->notes = "";
			$animal_id = codes_insertObject($fields, 'animal');
		}
	}

	public static function unlikePhoto($photoId = 0){
		
		$personId = self::userID();
		
		if ( $personId ) {
		
			if(!$photoId){
				$app = Factory::getApplication();			
				$input = $app->getInput();
				$photoId = $input->getInt('photo_id');
			}

			$restrictions = array('person_id' => $personId,
					 'photo_id' => $photoId);

			foreach(self::likes($restrictions) as $animal_id => $details){
			  codes_deleteObject($animal_id, "animal");
			}
		}
	}
	
	public static function countLikes ( $photoId ) {
		
		$numLikes = 0;
		
		if ( $personId = self::userID() ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();


			$query = $db->getQuery(true);
			$query->select("count(*)")
				->from("Animal")
				->where("photo_id = ".$photoId)
				->where("species = 97" )
				->where("person_id = ".$personId);

			$db->setQuery($query);
			$numLikes = $db->loadResult();
		}
		return numLikes;
	}

	public static function addReportMedia ( $sequenceId, $notes ) {
			
		$fields = new \StdClass();
		$fields->person_id = Biodiv::userID();
		
		// Only do the work if user is logged in
		if ( $fields->person_id ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();

					
			$fields->sequence_id = $sequenceId;
			$fields->notes = $notes;
			
			$success = $db->insertObject("ReportedMedia", $fields);
			
			if(!$success){
				error_log ( "ReportedMedia insert failed" );
			}

			$updateFields = new \stdClass();
			$updateFields->status = 13;
			$updateFields->sequence_id = $sequenceId;
			
			$success = $db->updateObject("Photo", $updateFields, 'sequence_id');
			
			if(!$success){
				error_log ( "Photo status update failed" );
			}		
		}
	}
	
	public static function addAnimalSingleTag ( $fields, &$animals, &$allAnimals ) {
			
		$animalId = null;
		
		if ( $fields->person_id == self::userID() ) {
			
			$animalId = codes_insertObject($fields, 'animal');
			self::addAnimalId ($animalId, $animals, $allAnimals);
			
			// if human then update the photo to say so
			if($fields->species == 87){
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
				$db = db();

				$fields2 = new \StdClass();
				$fields2->photo_id = $fields->photo_id;
				$fields2->contains_human = 1;
				// we do note own this object so access db directly
				$db->updateObject('Photo', $fields2, 'photo_id');
				self::updateSequenceInUse ( null, $fields->photo_id, 0, false );
			}
			
			// If not nothing, remove any nothing classifications
			if ( $fields->species != 86 ) {
				$deletedId = self::deleteNothingClassification($fields->photo_id, $animals);
				if ( $deletedId ) self::removeAnimalId ($deletedId, $animals, $allAnimals);
			}
		}
		return $animalId;
	}
	
	public static function removeAnimalSingleTag ( $animalId, &$animals, &$allAnimals ) {
		
		$animal = codes_getDetails($animalId, "animal");
		$photoId = $animal['photo_id'];
		$personId = self::userID();
		if ( $animal['person_id'] == $personId ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();


			if ( $animal['species'] == 87 ) {
				$fields = new \stdClass();
				$fields->photo_id = $photoId;
				$fields->contains_human = 0;
				// we do note own this object so access db directly
				$db->updateObject('Photo', $fields, 'photo_id');
				self::updateSequenceInUse ( null, $photoId, 1, true );

			}
			
			$conditions = array('person_id = ' . $personId,
					 'photo_id = ' . $photoId,
					 'animal_id = ' . $animalId);

			$query = $db->getQuery(true);
			$query->delete($db->quoteName('Animal'));
			$query->where($conditions);
		 
			$db->setQuery($query);
		 
		 
			$result = $db->execute();
		}

		self::removeAnimalId ($animalId, $animals, $allAnimals);
		
	}
	
	public static function getAnimal ( $animalId ) {
		
		$animal = null;
		
		if ( $animalId ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();


			$query = $db->getQuery(true);
			$query->select("animal_id, species, gender, age, number")
			  ->from("Animal")
			  ->where("animal_id = ".$animalId);

			$db->setQuery($query);
			$animal = $db->loadObject();
			
		}
			
		return $animal;
	}

	public static function getOrdinal ( $num ) {
	
		$langObject = Factory::getLanguage();
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
	
	public static function helpVideoURL() {
		return Uri::root()."/images/video/HowTo.mp4";
	}

	public static function photoURL($photo_id){
	  $details = codes_getDetails($photo_id, 'photo');
	  if ( $details['s3_status'] == 1 ) {
		  // File has been transferred to s3 so get AWS S3 url
		  return AWS::s3URL($details);
	  }
	  else {
		  // debug
		  // echo siteURL($details['site_id']) . "/". $details['filename'];
		  // debug end
		  return self::siteURL($details['site_id']) . "/". $details['filename'];
	  }
	}
		
	public static function imageURL($option_id) {
	  
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("OD.value")->from("OptionData OD")
			->innerJoin("Options O on O.option_id = OD.option_id and O.option_id = " . $option_id )
			->where("OD.data_type = 'image'" );
			
		$db->setQuery($query);
		$image = $db->loadResult();

		return Uri::root().$image;
	}

	public static function isVideo($photo_id) {
		
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

	public static function isAudio($photo_id) {
		
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

	public static function getSiteLocation($site_id) {
	
		$siteDetails = codes_getDetails($site_id, 'site');	
		return new Location($siteDetails['latitude'], $siteDetails['longitude']);	

	}

	// Get project specific filters, either based on project_id or photo_id given
	public static function getProjectFilters ( $project_id, $photo_id = null ) {
		
		$lang = self::langTag();
		
		$projectFilters = array();
		// First get the ENglish version
		if ( $project_id ) {
			// Find filter for this project
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();

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
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();

			$query = $db->getQuery(true);
			$query->select("O.option_id, O.option_name")->from("Options O");
			$query->innerJoin("ProjectOptions PO on PO.option_id = O.option_id");
			$query->innerJoin("Photo P on P.photo_id = ". $photo_id);
			$query->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id");
			$query->where("P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is NULL or P.photo_id <= PSM.end_photo_id)" );
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
		
	public static function getSpecies ( $filterId, $onePage ) {
	
		$speciesList = array();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		
		$mammalArray = null;
		$birdArray = null;
		$invertArray = null;
		$notinlistArray = null;
		
		// Check the language tag and work differently if English
		$lang = self::langTag();
		if ( $lang == 'en-GB' ) {
			
			//error_log ( "Language is English so no join to OptionData" );
			$query = $db->getQuery(true);
			
			$query->select("O.option_id as id, O.option_name as name, O.struc as type")
				->from("Options O")
				->innerJoin("SpeciesList SL on O.option_id = SL.species_id")
				->where( "O.struc = 'mammal'" )
				->where( "SL.list_id = " . $filterId )
				->order("O.option_name");
				
			$db->setQuery($query);
			
			$mammalArray = $db->loadAssocList("id");
			
			//error_log ( "Got mammal names" );
			
			$query = $db->getQuery(true);
			
			$query->select("O.option_id as id, O.option_name as name, O.struc as type")
				->from("Options O")
				->innerJoin("SpeciesList SL on O.option_id = SL.species_id")
				->where( "O.struc = 'bird'" )
				->where( "SL.list_id = " . $filterId )
				->order("O.option_name");
				
			$db->setQuery($query);
			
			$birdArray = $db->loadAssocList("id");
			
			//error_log ( "Got bird names" );
			
			$query = $db->getQuery(true);
			
			$query->select("O.option_id as id, O.option_name as name, O.struc as type")
				->from("Options O")
				->innerJoin("SpeciesList SL on O.option_id = SL.species_id")
				->where( "O.struc = 'invertebrate'" )
				->where( "SL.list_id = " . $filterId )
				->order("O.option_name");
				
			$db->setQuery($query);
			
			$invertArray = $db->loadAssocList("id");
			
			
			
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
			
			$query = $db->getQuery(true);
			
			$query->select("OD.option_id as id, OD.value as name, O.struc as type")
				->from("OptionData OD")
				->innerJoin("SpeciesList SL on OD.option_id = SL.species_id")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'invertebrate'")
				->where("OD.data_type = " . $db->quote($lang) )
				->where( "SL.list_id = " . $filterId )
				->order("O.option_name");
				
			$db->setQuery($query);
			
			$invertArray = $db->loadAssocList("id");
			
		}
		
		if ( $mammalArray ) $speciesList['mammal'] = $mammalArray;
		if ( $birdArray ) $speciesList['bird'] = $birdArray;
		if ( $invertArray ) $speciesList['invertebrate'] = $invertArray;
		
		return $speciesList;
	}

	public static function prevPhoto ( $last_photo_id ){
		$photoDetails = codes_getDetails($last_photo_id, 'photo');
		$photo_id = $photoDetails['prev_photo'];
		return $photo_id;
	}

	public static function photoSequenceStart ( $last_photo_id ) {
		$photoDetails = codes_getDetails($last_photo_id, 'photo');
		$sequence_id = $photoDetails['sequence_id'];
		$sequenceDetails = codes_getDetails($sequence_id, 'sequence');
		$photo_id = $sequenceDetails['start_photo_id'];
		return $photo_id;
	}

	public static function getClassifyInputs () {
		
		//$translations = getTranslations("classify");
		$classifyInputs = array();
		
		foreach(array("gender", "age") as $struc){
			$tranStr = "COM_BIODIV_CLASSIFY_" . strtoupper(codes_getTitle($struc));
			$title_tran = Text::_($tranStr);
			$input = "<label for ='classify_$struc'>" . $title_tran . "</label><br />\n";
			//$input .= "<select id='classify_$struc' name='$struc'>\n";
			//$input .= codes_getOptions(1, $struc);
			// set default to be unknown:
			$features = array("gender"=>84, "age"=>85);
			$input .= codes_getRadioButtons($struc, $struc."tran", $features);
			//$input .= "\n</select>\n";
			$classifyInputs[] = $input;	    
		}
		$number = "<label for ='classify_number'>" . Text::_("COM_BIODIV_CLASSIFY_HOW_MANY") . "</label>\n";
		$number .= "<div id='classify_how_many'><button type='button' class='btn' id='classify_decrease'><span class='fa fa-minus'/></button>";
		$number .= "<input id='classify_number' type='number' min='1' max='99' value='1' name='number'/><button type='button' class='btn' id='classify_increase'><span class='fa fa-plus'/></button></div>\n";
		$classifyInputs[] = $number;
			
		//$number .= "<input id='classify_number' type='number' min='1' value='1' name='number'/>\n";
		//$classifyInputs[] = $number;
		
		$sure = "<label for ='classify_sure'>" . Text::_("COM_BIODIV_CLASSIFY_SURE") . "</label>\n";
		// See what happens if we don;t specify what to check - want it to default to first option
		$sure .= codes_getRadioButtons("sure", "suretran");
		$classifyInputs[] = $sure;
		
		$notes = "<label for ='classify_notes'>" . Text::_("COM_BIODIV_CLASSIFY_NOTES") . "</label>\n";
		$notes .= "<input id='classify_notes' type='text' maxlength='100' name='notes'/>\n";
		$classifyInputs[] = $notes;
			
		return $classifyInputs;
	}
	
	// public static function getClassifyNothingInputs () {
		
		// $classifyInputs = array();
		
		// foreach(array("nothing") as $struc){
			// $tranStr = "COM_BIODIV_CLASSIFY_" . strtoupper(codes_getTitle($struc));
			// $title_tran = Text::_($tranStr);
			// $input = "<label for ='classify_$struc'>" . $title_tran . "</label><br />\n";
			// // set default to be unknown:
			// //$features = array("gender"=>84, "age"=>85);
			// $input .= codes_getRadioButtons($struc, $struc."tran", $features);
			// //$input .= "\n</select>\n";
			// $classifyInputs[] = $input;	    
		// }
					
		// return $classifyInputs;
	// }
	
	public static function isFavourite($photo_id){
		
		if ( count(self::myLikes($photo_id)) > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}
		
	public static function myLikes($photo_id){
		
		$person_id = (int)self::userID();
		return self::likes(array('photo_id' => $photo_id,
				   'person_id' => $person_id));
	}

	private static function likes($restrictions){
		
		if(!is_array($restrictions)){
			return array();
		}
		$allowed = array('photo_id', 'person_id', 'species', 'gender', 'age', 'number');
		$restrictions = array_intersect_key($restrictions, array_flip($allowed));
		if(count($restrictions)<1){
			return array();
		}

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

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

	public static function getClassificationButton ( $id, $animalArray ) {
		
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

	// Return all the photo details for this sequence, in order.  Do one db call (well two) rather than one photo at a time.
	public static function getSequenceDetails($photo_id) {
	  
		$sequenceDetails = array();

		if ( $photo_id > 0 ) {
			// First get the sequence id
			$photoDetails = codes_getDetails($photo_id, 'photo');
			$sequenceId = $photoDetails['sequence_id'];

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();


			$query = $db->getQuery(true);
			$query->select("photo_id, site_id, dirname, filename, sequence_id, sequence_num, prev_photo, next_photo, person_id from Photo P")
				->where("P.sequence_id = " . $sequenceId )
				->where("P.status = 1")
				->order("P.sequence_num")
				->setLimit("20");
			$db->setQuery($query);

			$sequenceDetails = $db->loadAssocList();
		}
		return $sequenceDetails;
	}
	
	public static function addSite () {
	
		$app = Factory::getApplication();
		
		$personId = self::userID();

		// Get all the data
		$fields = new \StdClass();
		$fields->person_id = $personId;
		$fields->site_name = $app->input->getString('site_name');
		
		// Validate on person and sitename - don't add if already exists - add message
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("site_id")
			->from("Site")
			->where("person_id = " . $fields->person_id . " and site_name = " . $db->quote($fields->site_name) );
		
		$db->setQuery($query);
		$result = $db->loadRow();
		
		$site_id = null;
		
		if ( $result && count($result) > 0 ) {
			// Trying to insert site with same name as another site belonging to this person
			$app->enqueueMessage('Sorry, you already have a site with that name, could not add the site.');
		}
		else {
			$fields->latitude = $app->input->getString('latitude');
			$fields->longitude = $app->input->getString('longitude');
			$fields->grid_ref = $app->input->getString('grid_ref');
			$fields->habitat_id = $app->input->getInt('habitat_id');
			$fields->water_id = $app->input->getInt('water_id');
			$fields->purpose_id = $app->input->getInt('purpose_id');
			$fields->camera_id = $app->input->getInt('camera_id');
			$fields->camera_height = $app->input->getInt('camera_height');
			$fields->notes = $app->input->getString('notes');
			
			// Insert into the Site table
			$site_id = codes_insertObject($fields, 'site');
			
			//Update the ProjectSiteMap table with the projects this site is in
			$project_ids = $app->input->getVar('project_ids');  
			$fields2 = new \StdClass();
			$fields2->site_id = $site_id;
			$fields2->projects = implode(',', $project_ids);
			codes_updateSiteProjects($fields2, 'site');
			
			// Get any project specific data
			$projectsitedata = Project::getSiteDataStrucs($project_ids);
			$unique_strucs = array_unique(array_column($projectsitedata, 'struc'));
			foreach ( $unique_strucs as $struc ) {
				$fields = new \StdClass();
				$fields->person_id = $personId;
				$fields->site_id = $site_id;
				$fields->option_id = $app->input->getInt($struc."_id");
				// Insert into the SiteData table
				$sitedata_id = codes_insertObject($fields, 'sitedata');
			}
		}
		return $site_id;
	}

	public static function editSite ( $partialEdit = false ) {
	
		$app = Factory::getApplication();
		
		$personId = self::userID();
		
		$siteId = $app->input->getInt('site_id');
		
		// Validate on person and sitename - don't add if already exists - add message
		// and check no photos loaded as editing all details
		$db = db();

		$query = $db->getQuery(true);
		$query->select("S.site_id, S.person_id, (select count(*) from Photo where site_id = " . $db->quote($siteId) . " ) as photo_count")
			->from("Site S")
			->leftJoin("Photo P using (site_id)")
			->where("site_id = " . $db->quote($siteId) );
		
		$db->setQuery($query);
		$existingSite = $db->loadAssoc();
		
		if ( !$existingSite ) {
			$app->enqueueMessage('Sorry, that site does not yet exist, could not edit the site.');
		}
		else if ( $existingSite["person_id"] != $personId ) {
			$app->enqueueMessage('Sorry, you are not the owner of that site, could not edit the site.');
		}
		else {
			
			// Get all the data
			$fields = new \StdClass();
			$fields->person_id = $personId;
			$fields->site_id = $siteId;
			$fields->site_name = $app->input->getString('site_name');
			$fields->notes = $app->input->getString('notes');
			
			// Double check no photos yet and only update allowed fields if there are
			if ( !$partialEdit && $existingSite["photo_count"] == 0 ) {
			
				$fields->latitude = $app->input->getString('latitude');
				$fields->longitude = $app->input->getString('longitude');
				$fields->grid_ref = $app->input->getString('grid_ref');
				$fields->habitat_id = $app->input->getInt('habitat_id');
				$fields->water_id = $app->input->getInt('water_id');
				$fields->purpose_id = $app->input->getInt('purpose_id');
				$fields->camera_id = $app->input->getInt('camera_id');
				$fields->camera_height = $app->input->getInt('camera_height');	
			}
			
			// Insert into the Site table
			codes_updateObject($fields, 'site');
			
			//Update the ProjectSiteMap table with the projects this site is in
			$project_ids = $app->input->getVar('project_ids');  
			$fields2 = new \StdClass();
			$fields2->site_id = $siteId;
			$fields2->projects = implode(',', $project_ids);
			codes_updateSiteProjects($fields2, 'site');
			
			// Update any project specific data
			if ( !$partialEdit && $existingSite["photo_count"] == 0 ) {
			
				// delete any existing
				self::deleteSiteData ( $siteId );
				$projectsitedata = Project::getSiteDataStrucs ( $project_ids );
				$unique_strucs = array_unique(array_column($projectsitedata, 'struc'));
				foreach ( $unique_strucs as $struc ) {
					$fields = new \StdClass();
					$fields->person_id = $personId;
					$fields->site_id = $siteId;
					$fields->option_id = $app->input->getInt($struc."_id");
					// Insert into the SiteData table
					$sitedata_id = codes_insertObject($fields, 'sitedata');
				}
			}
		}
		return $siteId;
	}
	
	private static function deleteSiteData ( $siteId ) {
		
		$db = db();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName("SiteData"))
			->where("site_id = " .(int)$siteId)
			->where("person_id = " .(int)self::userID());
		$db->setQuery($query); 
		$returnValue = $db->execute();
		
		return $returnValue;
	}

	public static function getUploadDetails ( $siteId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("upload_id, deployment_date, collection_date, camera_tz, is_dst, timestamp")
			->from($db->quoteName("Upload"))
			->where("site_id = " .(int)$siteId)
			->where("person_id = " .(int)self::userID())
			//->order("collection_date DESC, upload_id DESC");
			->order("upload_id DESC");
		$db->setQuery($query, 0, 1); // LIMIT 1
		$uploadDetails = $db->loadAssoc();
		
		return $uploadDetails;
	}
	
	public static function getUploadCounts ( $uploadId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("count(*)")
			->from($db->quoteName("Photo"))
			->where("upload_id = " .(int)$uploadId);
		$db->setQuery($query); 
		$numUploaded = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("count(*)")
			->from($db->quoteName("OriginalFiles"))
			->where("upload_id = " .(int)$uploadId);
		$db->setQuery($query); 
		$numOriginal = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("count(*)")
			->from($db->quoteName("OriginalLargeFiles"))
			->where("upload_id = " .(int)$uploadId);
		$db->setQuery($query); 
		$numLarge = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("count(*)")
			->from($db->quoteName("UploadError"))
			->where("upload_id = " .(int)$uploadId);
		$db->setQuery($query); 
		$numFailed = $db->loadResult();
		
		$countObject = new \StdClass();
		$countObject->numUploaded = $numUploaded;
		$countObject->numOriginal = $numOriginal;
		$countObject->numLarge = $numLarge;
		$countObject->numFailed = $numFailed;
		return $countObject;
	}
	
	public static function verifyUpload ( $uploadId, $guid, $done ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();


		if($done){
			$query = $db->getQuery(true);
			$query->delete($db->quoteName("UploadVerify"));
			$query->where("upload_id = " . (int)$uploadId . " AND guid = '$guid'");
			$db->setQuery($query);
			$success = $db->execute();
		}
		else{
			$verify = new \StdClass();
			$verify->upload_id = $uploadId;
			$verify->guid = $guid;
			$success = $db->insertObject("UploadVerify", $verify);
		}
		
		return $success;
	
	}
	
	// When recording can get video/mp4 mime types so can require audio
	public static function uploadFile ( $upload_id, $site_id, $tmpName, $clientName, $fileSize, $fileType, $treatAsAudio = false ) {
	
		$problem = false;
		
		//error_log ( "uploadFile ( $upload_id, $site_id, $tmpName, $clientName, $fileSize, $fileType, $treatAsAudio ) called");

		if($fileSize > BIODIV_MAX_FILE_SIZE){
			self::addMsg("error",  "File " . $clientName ." too large: max " . BIODIV_MAX_FILE_SIZE);
			self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, "File too large: max " . BIODIV_MAX_FILE_SIZE );
			$problem = true;
		} 
		//	$tmpName = JFile::makeSafe($tmpName);
		// NB can get this error if PHP max dilsize and upload size are too small:
		else if(!is_uploaded_file($tmpName)){
			//error_log ( "Not an uploaded file $tmpName ( $clientName ) - check file size is below PHP limits, or there may be a network problem" );
			$msg = "$clientName could not be uploaded - file may be too large (max filesize is ". BIODIV_MAX_FILE_SIZE . ") or there may be a network problem";
			self::addMsg("error", $msg);
			self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, $msg );
			$problem = true;
		}
		else {

			$ext = strtolower(File::getExt($clientName));
			$newName = md5_file($tmpName) . "." . $ext;

			//error_log ( "Uploading file " . $clientName . ", extension is " . $ext );

			$dirName = self::siteDir($site_id);
			$newFullName = "$dirName/$newName";

			if(File::exists($newFullName)){
				self::addMsg("warning", "File already uploaded: $clientName");
				self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, "File already uploaded" );
				$problem = true;
			}
			else {
				
				// Create a BiodivFile object
				$biodivFile = new BiodivFile ( $tmpName, $clientName, $treatAsAudio );

				$taken = $biodivFile->takenDate();
				$exif = $biodivFile->exif();
				$is_audio = $biodivFile->isAudio();
				$toResize = $biodivFile->toResize();
				
				// If we have file splitting for audio, then write to different table.
				$splitAudio = self::getSetting("split_audio") == "yes";
				
				$struc = 'photo';
				$tbl = 'Photo';
				
				if ( $is_audio && $splitAudio ) {
					$struc = 'origfile';
					$tbl = 'OriginalFiles';
				}

				// For all avi files need to convert to MP4
				if ( $ext == 'avi' ) {
					$struc = 'origfile';
					$tbl = 'OriginalFiles';
				}
				
				if ( $toResize ) {
					$struc = 'origlargefile';		
					$tbl = 'OriginalLargeFiles';		
				}

											
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());	
				$db = db();

				$query = $db->getQuery(true)
					->select("count(*)")->from($tbl . " P")
					->where("P.site_id = " . $site_id)
					->where("P.filename = " . $db->quote($newName) );
				$db->setQuery($query);
				$numExisting = $db->loadResult();
				
				if ( $numExisting > 0 ) {
					
					self::addMsg("warning", "File already uploaded for this site: $clientName");
					self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, "File already uploaded for this site" );
					$problem = true;
				}
				else {

					
					if ( $taken == null ) {
						self::addMsg("error","File upload unsuccessful for $clientName, cannot get timestamp");
						self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, "Cannot get timestamp" );
						$problem = true;
					}
					else {

						$exists = is_file($tmpName);
						$success = File::upload($tmpName, $newFullName);
						if(!$success){
							self::addMsg("error","File upload unsuccessful for $clientName");
							self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, "Moving uploaded file failed" );
							$problem = true;
						}	
						else {
							
							$photoFields = new \StdClass();
							$photoFields->filename = $newName;
							$photoFields->upload_filename = $clientName;
							$photoFields->dirname = $dirName;
							$photoFields->site_id = $site_id;
							$photoFields->upload_id = $upload_id;
							$photoFields->person_id = self::userID();
							$photoFields->taken = $taken;
							$photoFields->size = $fileSize;
							$photoFields->exif = $exif;

							//error_log ( "split_audio setting = " . $splitAudio );
							//error_log ( "is_audio = " . $is_audio );
							
							//error_log ( "Inserting object for struc " . $struc );
							$newId = codes_insertObject($photoFields, $struc);
							if($newId){
								
								self::addMsg('success', "Uploaded $clientName");
			
								// Add the file to the Conservation AI queue
								if ( $struc == 'photo' ) {
									self::addToAIQueue('MEGA', $newId);
								}
							}
							else {
								// Remove files if the database insert failed
								File::delete($newFullName);
								self::writeUploadError ( $upload_id, $site_id, $tmpName, $clientName, "Error inserting into database" );
								$problem = true;
							}
						}
					}
				}
			}
		}
		
		return $problem == false;

	}
	
	
	private static function writeUploadError ( $uploadId, $siteId, $tmpName, $clientName, $reason ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();


		$errorFields = new \StdClass();
		$errorFields->upload_id = $uploadId;
		$errorFields->site_id = $siteId;
		$errorFields->upload_filename = $clientName;
		$errorFields->filename = $tmpName;
		$errorFields->reason = $reason;
		$success = $db->insertObject("UploadError", $errorFields);
		if(!$success){
			error_log ( "UploadError insert failed" );
		}
	}
	
	
	public static function getUploadErrors ( $uploadId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		
		$query = $db->getQuery(true);
		$query->select("upload_filename, reason")
			->from("UploadError UE")
			->innerJoin("Upload U on U.upload_id = UE.upload_id")
			->where("UE.upload_id = " .(int)$uploadId)
			->where("U.person_id = " .(int)self::userID());
		$db->setQuery($query); 
		$uploadErrors = $db->loadAssocList();
		
		//error_log("getUploadErrors select query created: " . $query->dump());
		
		return $uploadErrors;

	}
	
	
	public static function uploadMore ( $upload_id ) {
		
		//Get the upload details
		$db = db();
		$query = $db->getQuery(true);
		$query->select("site_id, person_id, camera_tz, is_dst, utc_offset, deployment_date, collection_date")
			->from($db->quoteName("Upload"))
			->where("upload_id = " .(int)$upload_id)
			->where("person_id = " .(int)self::userID());
		$db->setQuery($query); 
		$uploadDetails = $db->loadAssoc();

		Biodiv::canEdit($uploadDetails['site_id'], "site") or die ( );

		$newUploadId = null;
		
		if ( $uploadDetails && count($uploadDetails) > 0 ) {

			$fields = new \StdClass();

			$fields->site_id = $uploadDetails['site_id'];
			$fields->person_id = $uploadDetails['person_id'];
			$fields->camera_tz = $uploadDetails['camera_tz'];
			$fields->is_dst = $uploadDetails['is_dst'];
			$fields->utc_offset = $uploadDetails['utc_offset'];
			$fields->deployment_date = $uploadDetails['deployment_date'];
			$fields->collection_date = $uploadDetails['collection_date'];

			$newUploadId = codes_insertObject($fields, 'upload');

		}
		
		return $newUploadId;
	}
	
	
	public static function addToAIQueue($aiType, $photoId, $priority = 5) {
	
		if ( $aiType == 'CAI' && is_numeric($photoId) ) {
			
			$requireCAI = self::getSetting("conservation_ai") == "yes";
			
			if ( $requireCAI ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
				$db = db();


				$aiFields = new \StdClass();
				$aiFields->photo_id = $photoId;
				$aiFields->ai_type = 'CAI';
				$aiFields->priority = $priority;
				$success = $db->insertObject("AIQueue", $aiFields);
				if(!$success){
					error_log ( "AIQueue insert failed" );
				}
				
			}
		}
		else if ( $aiType == 'MEGA' && is_numeric($photoId) ) {
			
			$requireMEGA = self::getSetting("megadetector") == "yes";
			
			if ( $requireMEGA ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
				$db = db();


				$aiFields = new \StdClass();
				$aiFields->photo_id = $photoId;
				$aiFields->ai_type = 'MEGA';
				$aiFields->priority = $priority;
				$success = $db->insertObject("AIQueue", $aiFields);
				if(!$success){
					error_log ( "AIQueue insert failed" );
				}
				
			}
		}
		
	}
	
	// For multilingual
	public static function biodiv_label_icons($type, $str, $what=""){
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
			case "download":
				return "<i class='fa fa-download'></i> $str $what";
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
	
	public static function addUpload ( $isCamera, $site_id, $badge_id = null, $class_id = null ) {
	
		//error_log ( "addUpload ( $isCamera, $site_id ) called" );
		
		$app = Factory::getApplication();
		$input = $app->input;

		//error_log ( print_r ( $input, true ) );
		
		//$data  = $input->post->get('jform', array(), 'array');
		//$data  = $input->getArray('jform', array(), 'array');
		$data = $input->getArray();

		error_log ( print_r ( $data, true ) ); 

		self::canEdit($site_id, "site") or die("Cannot edit site_id $site_id");
		
		$fields = new \StdClass();
		
		$datetime = array();
		
		// For audio no need for deployment start and end times, camera only
		if ( $isCamera ) {
			// foreach(array("deployment", "collection") as $dt){
				// $date = $input->getString("{$dt}_date", 0);
				// if(!strlen($date)){
					// self::addMsg('error', "No $dt date specified");
				// }
				// $time = $input->getString("{$dt}_time", 0);
				// if(!strlen($time)){
					// self::addMsg('error', "No $dt time specified: $time ");
				// }
				// $datetime[$dt] = $date . " " . $time;
			// }
			
			$depDate = $data['deployment_date'];
			$collDate = $data['collection_date'];
		
			$fields->deployment_date = $depDate;
			$fields->collection_date = $collDate;
			
		}
		
		// These are the common fields
		//$camera_tz = $input->getString('timezone', 0);
		$camera_tz = $data['timezone'];
		$tz = 0;
		$is_dst = 0;
		$utc_offset = 0;
		if(!strlen($camera_tz)){
			self::addMsg('error', "No camera timezone specified");
		}
		// Get the offset
		$tz = \IntlTimeZone::createTimeZone($camera_tz);
		$utc_offset = $tz->getRawOffset()/60000;
		
		//$is_dst = $input->getInt('dst', 0);
		$is_dst = $data['dst'];
		if ($is_dst == 1 ) {
			// Adjust the offset from UTC for daylight saving time
			$utc_offset += $tz->getDSTSavings()/60000;
		}
		
		$upload_id = null;
			
		if(!self::someMsgs("error")){
			
			//error_log ( "addUpload no error messages so adding upload" );
			
			$fields->person_id = self::userID();
			$fields->site_id = $site_id;
			$fields->camera_tz = $camera_tz;
			$fields->is_dst = $is_dst;
			$fields->utc_offset = $utc_offset;
			$upload_id = codes_insertObject($fields, 'upload');
			$app->setUserState('com_biodiv.upload_id', $upload_id);
			
		}
		if ( $badge_id ) {
			$app->setUserState('com_biodiv.badge_id', $badge_id);
		}
		if ( $class_id ) {
			$app->setUserState('com_biodiv.class_id', $class_id);
		}
		
		return $upload_id;
	}

	// public static function addUploadMore ( $uploadId ) {
		
		// $newUploadId = null;
		// needs site id to call this $uploadDetails = self::getUploadDetails($uploadId);
		
		// if ( $uploadDetails && count($uploadDetails) > 0 ) {
			// if ( self::canEdit($uploadDetails['site_id'], "site") ) {

				// $fields = new \StdClass();

				// $fields->site_id = $uploadDetails['site_id'];
				// $fields->person_id = $uploadDetails['person_id'];
				// $fields->camera_tz = $uploadDetails['camera_tz'];
				// $fields->is_dst = $uploadDetails['is_dst'];
				// $fields->utc_offset = $uploadDetails['utc_offset'];
				// $fields->deployment_date = $uploadDetails['deployment_date'];
				// $fields->collection_date = $uploadDetails['collection_date'];

				// $newUploadId = codes_insertObject($fields, 'upload');

			// }
			// else {
				// self::addMsg('error', "Cannot edit site_id $site_id");
			// }	
		// }
		// else {
			// self::addMsg('error', "Can't use previous upload");
		// }
		// return $newUploadId;
		
	// }   


	public static function getUploadFailsForDownload ( $uploadId ) {
		
		// Check user is project admin for this project or if no project that users match
		$details = codes_getDetails($uploadId, 'upload');
		
		$personId = Biodiv::userID();
		
		$fails = null;
		
		if ( $details && ($details['person_id'] == $personId) ) {
			
			$fails = new \StdClass();
			
			$fails->filename = "Upload" . $uploadId . "Site" . $details['site_id'] . "Errors.csv";
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();

			$query = $db->getQuery(true);
			$query->select("upload_filename")
				->from($db->quoteName("UploadError"))
				->where("upload_id = " .(int)$uploadId);
			$db->setQuery($query);
			$fails->data = $db->loadColumn();
			
		}
		else {
			$fails = new \StdClass();
			$fails->data = "Sorry you do not have access to this file";
		}
		
		return $fails;
	}
	
	
		
	
	public static function getPrevPhotos ( $uploadId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("photo_id, taken, upload_filename")
			->from($db->quoteName("Photo"))
			->where("upload_id = " .(int)$uploadId)
			->where("person_id = " .(int)self::userID())
			->order("taken");
		$db->setQuery($query);
		$prevPhotos = $db->loadAssocList();
		
		return $prevPhotos;
	}
	
	public static function getSitePhotos ( $siteId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("upload_filename, taken")
			->from($db->quoteName("Photo"))
			->where("site_id = " .(int)$siteId)
			->where("person_id = " .self::userID())
			->order("upload_filename");
			
		$db->setQuery($query);
		$photos = $db->loadAssocList();
		return $photos;
	}
	
	public static function getPhotosToSplit ( $siteId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("upload_filename, taken")
			->from($db->quoteName("OriginalFiles"))
			->where("site_id = " .(int)$siteId)
			->where("person_id = " .self::userID())
			->order("upload_filename");

		$db->setQuery($query);
		$tosplit = $db->loadAssocList();
		
		return $tosplit;
	}
	
	public static function getPhotosToResize ( $siteId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("upload_filename, taken")
			->from($db->quoteName("OriginalLargeFiles"))
			->where("site_id = " .(int)$siteId)
			->where("person_id = " .self::userID())
			->order("upload_filename");

		$db->setQuery($query);
		$toresize = $db->loadAssocList();
		
		return $toresize;
	}
	
	public static function getLatestUploads ( $siteId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		
		$sub1 = "(select count(*) from OriginalLargeFiles OLF where OLF.upload_id = U.upload_id) as count_large";
        $sub2 = "(select count(*) from Photo P where P.upload_id = U.upload_id) as count_regular";
        $sub3 = "(select count(*) from UploadError UE where UE.upload_id = U.upload_id) as count_error";
        $query = $db->getQuery(true);
        $query->select("U.timestamp, U.deployment_date, U.collection_date, U.upload_id, " . $sub1 . ", " . $sub2 . ", " . $sub3)
            ->from($db->quoteName("Upload") . " U" )
            ->where("U.site_id = " .(int)$siteId)
            ->where("U.person_id = " .self::userID())
            ->group("U.upload_id")
            ->order("U.upload_id DESC");

        $db->setQuery($query, 0, 10);
        $latestUploads = $db->loadAssocList();

		return $latestUploads;
	}
	
	public static function getUnsequencedUploads () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

			
		$query = $db->getQuery(true);
		$query->select("distinct(upload_id)")
			->from($db->quoteName("Photo"))
			->where("sequence_id = 0");
		$db->setQuery($query);
		$uploads = $db->loadAssocList();
		
		return $uploads;
	}

	public static function getSiteDataStrucs ( $projectIds ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

			
		$project_ids = implode(',', $projectIds);
		$query = $db->getQuery(true);
		$query->select("DISTINCT PO.project_id, O.option_id, O.option_name as struc")
			->from("ProjectOptions PO")
			->innerJoin("Options O on O.option_id = PO.option_id and O.struc = 'sitedatastruc'")
			->where("PO.project_id in (" . $project_ids . ")");
		$db->setQuery($query);
		$sitedatastrucs = $db->loadAssocList();
		return $sitedatastrucs;
	}

	public static function getExistingClassifications ( $animalIds ) {
		
		$animals_csv = implode(",", explode("_", $animalIds));
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		$query = $db->getQuery(true);
		$query->select("animal_id, species, gender, age, number")
			->from("Animal")
			->where("animal_id in (".$animals_csv.")");

		$db->setQuery($query);
		$animals = $db->loadObjectList("animal_id");
		
		return $animals;

	}
	
	// New version of nextSequence which considers the classify priority mode of each project.
	// At the time of writing this could be:
	// Multiple (allow multiple classifications per photo, done by different users), Single (classify all photos once), Time ordered (classify the oldest sequence first)
	public static function nextSequence( $project_id = null){
	  
		//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
		$db = readDb();
		$app = Factory::getApplication();

		// Initialise photo_id and sequence_id to null
		$photo_id = null;
		$sequence_id = null;

		// First find out which classify button was pressed.
		$classify_project = $app->getUserState("com_biodiv.classify_only_project");
		$classify_own = $app->getUserState("com_biodiv.classify_self");
		$last_photo_id = (int)$app->getUserState('com_biodiv.prev_photo_id', 0);

		if ( $classify_project && $project_id == null ) {
		  $project_id = $app->getUserState("com_biodiv.project_id");
		}

		// Get a list of projects over which we are working and their priority mode (an assoc list keyed on project_id)
		// If we are doing a "classify all" then exclude some projects
		$exclude_flag = true;
		if ( $classify_project or $classify_own ) $exclude_flag = false;
		$project_options = Project::getProjectOptions ( $project_id, "priority", $exclude_flag );

		// Get all the priority option names indexed on project_id
		$priority_array = array_column ( $project_options, "option_name", "project_id" );

		$all_priorities = array("Single", "Multiple", "Single to multiple", "Site time ordered", "Repeat", "Single to repeat");

		// which priorities do we have projects for?
		$distinct_priorities = array_intersect ( $all_priorities, $priority_array );

		// Determine the order of priority types to try.  Take a weighted choice from each priority type that this user has 
		// access to.  
		$all_weightings = self::getPriorityWeightings ();
		
		$reqd_weightings = array_intersect_key ( $all_weightings, array_flip($priority_array ));
		
		$total_weighting = array_sum($reqd_weightings);

		$ordered_priorities = array();

		$num_iterations = count($reqd_weightings);

		for ( $i=0; $i< $num_iterations; $i++ ) {
			if ( count($reqd_weightings) == 1 ) {
				//print "<br>just one reqd weighting left <br>" ;

				$ordered_priorities[] = array_keys($reqd_weightings)[0];
				break;
			}
			// Choose a random integer between 0 and $total_weightings.
			$choice = rand ( 1, $total_weighting );

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
		
		foreach ( $ordered_priorities as $current_priority ) {
			if ( !$photo_id ) {
				switch ($current_priority) {
					case "Multiple":
						$project_ids = array_keys ( $priority_array, "Multiple" );
						$photo_id = self::chooseMultiple ( $project_ids, $classify_own );
						break;
					case "Single":
						$project_ids = array_keys ( $priority_array, "Single" );
						$photo_id = self::chooseSingle ( $project_ids, $classify_own );
						break;
					case "Single to multiple":
						$project_ids = array_keys ( $priority_array, "Single to multiple" );
						$photo_id = self::chooseSingle ( $project_ids, $classify_own );
						if ( !$photo_id ) {
							$photo_id = self::chooseMultiple ( $project_ids, $classify_own );
						}
						break;
					case "Site time ordered":
						$project_ids = array_keys ( $priority_array, "Site time ordered" );
						$photo_id = self::chooseSiteTimeOrdered ( $project_ids, $last_photo_id, $classify_own );
						break;
					case "Repeat":
						$project_ids = array_keys ( $priority_array, "Repeat" );
						$photo_id = self::chooseRepeat ( $project_ids, $classify_own );
						break;
					case "Single to repeat":
						$project_ids = array_keys ( $priority_array, "Single to repeat" );
						$photo_id = self::chooseSingle ( $project_ids, $classify_own );
						if ( !$photo_id ) {
							$photo_id = self::chooseRepeat ( $project_ids, $classify_own );
						}
						break;
					default:
						break;
				}
			}
		}

		return self::getSequenceDetails($photo_id);
	}
	
	// Photo id included for performance
	private static function getProjectSequenceThreshold ( $siteId, $photoId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
					 
		$query = $db->getQuery(true)
			->select("min(OD.value)")
			->from("ProjectOptions PO")
			->innerJoin("Options O on PO.option_id = O.option_id and O.struc = ". $db->quote("sequencethreshold"))
			->innerJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = ". $db->quote("seconds"))
			->innerJoin("ProjectSiteMap PSM on PSM.project_id = PO.project_id and PSM.site_id = " . $siteId)
			->where("PSM.start_photo_id < " . $photoId )
			->where("(PSM.end_photo_id is NULL or PSM.end_photo_id > " . $photoId . ")" );
			
		$db->setQuery($query);
		$numSeconds = $db->loadResult();
		
		// NB null if no values		
		return $numSeconds;

	}
	
	
	public static function sequencePhotos($upload_id, $sequenceThreshold = 10) {
		
		if(!$upload_id = (int)$upload_id){
			return false;
		}
		$uploadDetails = codes_getDetails ($upload_id, 'upload');
		$siteId = $uploadDetails['site_id'];
		$uploadPerson = $uploadDetails['person_id'];
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		$query = $db->getQuery(true);
		$query->select("photo_id, taken")
		->from("Photo")
		->where("upload_id = " . (int)$upload_id)
		->where("sequence_id = 0")
		->order("taken, upload_filename");

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
		
		$projectThreshold = null;
		if ( $num_results > 0 ) {
			$projectThreshold = self::getProjectSequenceThreshold ( $siteId, $res[0]["photo_id"] );
		}
		
		$threshold = $sequenceThreshold;
		if ( $projectThreshold !== null ) {
			$threshold = $projectThreshold;
		}

		foreach($res as $line){
			extract($line);
			$this_row++;
			print "<br/>sequencing photoid_id $photo_id<br/>";
			//print "<br/>Just read next row: seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
			$dateTime = new \DateTime($taken);
			//print "photo_id $photo_id ";

			$isAudio = self::isAudio($photo_id);
			$isVideo = self::isVideo($photo_id);

			// Images are not available for human classification until screened by AI, but video is not screened.
			$newPhotoStatus = 0;
			if ( $isVideo or $isAudio ) {
				$newPhotoStatus = 1;
			}

			if ($prev_dateTime !== null) {
				
				$diff = $dateTime->diff($prev_dateTime);
				print "photo_id $photo_id prev_photo_id $prev_photo_id diff ". $diff->s;
				//if((abs($diff->s) <10) && ($diff->i==0) && ($diff->h==0) && ($diff->d==0) & ($diff->m==0) & ($diff->y ==0)){ // less than 10 seconds between photos
				if(!$isAudio && !$isVideo && (abs($diff->s) < $threshold) && ($diff->i==0) && ($diff->h==0) && ($diff->d==0) & ($diff->m==0) & ($diff->y ==0)){ // less than 10 seconds between photos
					print "<br/> photos are close<br/>\n";
					if($sequence_id>0) {
						print "Existing sequence $sequence_id<br/>";	
						$seq_num++;
						$fields = new \StdClass();
						$fields->end_photo_id = $photo_id;
						$fields->sequence_length = $seq_num;
						$fields->sequence_id = $sequence_id;
						codes_updateObject($fields, 'sequence');
						//print "<br/>end of existing sequence seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
					}
					else { // start new sequence
						$fields = new \StdClass();
						$fields->start_photo_id = $prev_photo_id;
						$fields->upload_id = $upload_id;
						  $fields->site_id = $siteId;
						  $fields->person_id = $uploadPerson;
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

					self::canEdit( $prev_photo_id, 'photo', 1);
					self::canEdit( $photo_id, 'photo' , 1);
					//	print "canEdit photo $prev_photo_id ".canEdit('photo' , $prev_photo_id); 
					//	print "canEdit photo $photo_id ".canEdit('photo' , $photo_id); 
					$fields = new \StdClass();
					$fields->sequence_id = $sequence_id;
					$fields->prev_photo = $prev_photo_id;
					$fields->sequence_num = $seq_num;
					$fields->photo_id = $photo_id;
					//$fields->status = $newPhotoStatus; 
					print "updating ";
					print_r($fields);
					codes_updateObject($fields, 'photo');
					//print "<br/>after updating photo_id $photo_id seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";

					$fields = new \StdClass();
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

					// Check what file type the previous photo was as need to set status so that isn't screened by AI
					$prevIsAudio = self::isAudio($prev_photo_id);
					$prevIsVideo = self::isVideo($prev_photo_id);

					print "<br/><br/>Photos are not close so now on next sequence<br/>";
					$seq_num = 1; // as we're just starting a new sequence??
					// Previous one was a loner if it was first in a sequence and this is not close enough
					// to be in same sequence OR if it was the first one and this is not close enough..
					if ( $prev_seq_num == 1 or $this_row == 2 ){  
						print "<br/>Lone photo found: $prev_photo_id<br/>";
						$fields = new \StdClass();
						$fields->start_photo_id = $prev_photo_id;
						$fields->end_photo_id = $prev_photo_id;
						$fields->sequence_length = 1;
						$fields->upload_id = $upload_id;
						$fields->site_id = $siteId;
						$fields->person_id = $uploadPerson;
						$sequence_id = codes_insertObject($fields, 'sequence');
						print "new sequence $sequence_id<br/>";
						$seq_num = 1; // as we're just starting a new sequence
						//print "<br/>end of adding lone photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";

						self::canEdit( $prev_photo_id, 'photo', 1);
						//canEdit( $photo_id, 'photo' , 1);		
						$fields = new \StdClass();
						$fields->sequence_id = $sequence_id;
						$fields->sequence_num = 1;
						$fields->photo_id = $prev_photo_id;

						if ( $prevIsVideo or $prevIsAudio ) {
							$fields->status = 1;
						}

						print "updating ";
						print_r($fields);
						codes_updateObject($fields, 'photo');
						//print "<br/>end of updating lone photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";

						// Finally, if this is the last photo of the upload and the previous one was a loner, then this one must be too!
						if ($this_row == $num_results) {
							print "<br/>Final one in upload - lone photo found: $photo_id<br/>";
							$fields = new \StdClass();
							$fields->start_photo_id = $photo_id;
							$fields->end_photo_id = $photo_id;
							$fields->sequence_length = 1;
							$fields->upload_id = $upload_id;
							$fields->site_id = $siteId;
							$fields->person_id = $uploadPerson;
							$sequence_id = codes_insertObject($fields, 'sequence');
							print "new sequence $sequence_id<br/>";
							//$prev_seq_num = 1;

							self::canEdit( $photo_id, 'photo' , 1);

							$fields = new \StdClass();
							$fields->sequence_id = $sequence_id;
							$fields->sequence_num = 1;
							$fields->photo_id = $photo_id;

							if ( $isVideo or $isAudio ) {
								$fields->status = 1;
							}

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
				$fields = new \StdClass();
				$fields->start_photo_id = $prev_photo_id;
				$fields->end_photo_id = $prev_photo_id;
				$fields->sequence_length = 1;
				$fields->upload_id = $upload_id;
				$fields->site_id = $siteId;
				$fields->person_id = $uploadPerson;
				$sequence_id = codes_insertObject($fields, 'sequence');
				print "new sequence $sequence_id<br/>";
				$seq_num = 1; // as we're just starting a new sequence
				//print "<br/>end of adding sole photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";

				$prevIsAudio = self::isAudio($prev_photo_id);
				$prevIsVideo = self::isVideo($prev_photo_id);

				self::canEdit( $prev_photo_id, 'photo', 1);
				
				$fields = new \StdClass();
				$fields->sequence_id = $sequence_id;
				$fields->sequence_num = 1;
				$fields->photo_id = $prev_photo_id;

				if ( $prevIsVideo or $prevIsAudio ) {
					$fields->status = 1;
				}

				print "updating photo ";
				print_r($fields);
				codes_updateObject($fields, 'photo');
				//print "<br/>end of updating sole photo seq_num = $seq_num, prev_seq_num = $prev_seq_num<br/>";
				  
			}
		}
	}
	
	// This version is used by non-kiosk MammalWeb
	public static function printSpeciesListSearch ( $filterId, $speciesList, $useSeq=false, $dataToggle = true ) {
	
		// Should store this in the Options table as a system option. 
		$numPerPage = 36;
		
		$column0Class = "col-xs-12 col-sm-12 col-md-12";
		$dkOtherClass = "col-xs-12 col-sm-12 col-md-12";
		
		// Add pagination
		$numSpecies = 0;
		foreach ($speciesList as $type=>$all_this_type) {
			$numSpecies = $numSpecies + count($all_this_type);
		}
		
		$numPages = ceil($numSpecies/$numPerPage);
		
		if ( $numPages > 1 ) {
			print '<div class="row d-flex">';
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
			print '</div>';
		}
			
		$toggleExtras = "";
		$toggleExtrasNothing = "";

		if ( $dataToggle == true ) {
			$toggleExtras = " data-toggle='modal' data-target='#classify_modal'";
			$toggleExtrasNothing = " data-toggle='modal' data-target='#classify_nothing_modal'";
		}
		
		// Default
		//$btnClass = 'btn-warning';
		$btnClass = 'species-mammal-btn';
			
		print '<div class="row d-flex align-items-stretch">';
		
		// Now add the birds
		foreach ($speciesList as $type=>$all_this_type) {
			foreach($all_this_type as $species_id => $species){
				
				$name = $species['name'];
				$isLongSpeciesName = false;
				if ( strlen($name) > 20 ) $isLongSpeciesName = true;
				
				switch($species['type']){
				case 'mammal':  
					//$btnClass = 'btn-warning';
					$btnClass = 'species-mammal-btn';
					
					// For birds have a button to view the article and a song and call quick classify button
					$btnText = "<button type='button' id='species_select_{$filterId}_{$species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select species_select_name $column0Class'".$toggleExtras."  >$name</button>";
					
					print '<div id="species_group_'.$filterId.'_'.$species_id.'" class="col-xs-6 col-sm-6 col-md-6 btn-group species_group match" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
					
					break;

				case 'bird':  
				case 'invertebrate':
					//$btnClass = 'btn-info';
					$btnClass = 'species-bird-btn';
					
					// For birds have a button to view the article and a song and call quick classify button
					$btnText = "<button type='button' id='species_select_{$filterId}_{$species_id}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select species_select_name $column0Class'".$toggleExtras."  >$name</button>";
					
					print '<div id="species_group_'.$filterId.'_'.$species_id.'" class="col-xs-6 col-sm-6 col-md-6 btn-group species_group match" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
					
					break;

				default :
					// Do nothing, handle Don't know and Other separately, no mammals allowed in the bird species lists
					break;
				}
			}
		}
		
		// Add a padding disabled button for when the number of species is odd...
		$btnText = "<button type='button' id='species_select_blank_{$filterId}' class='btn $btnClass btn-sm btn-wrap-text species-btn $dkOtherClass' disabled".$toggleExtras." style='color:transparent;' >Blank</button>";
		print '<div id="species_group_blank_{$filterId}" class="col-xs-6 col-sm-6 col-md-6 btn-group species_group_blank" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		
		// Explicitly add the notinlist (Don't know and Other) buttons at the bottom
		//Get the option ids  
		//error_log("About to get dk and other ids" );
		$otherId = codes_getCode("Other",'species');
		//error_log("printBirdSpeciesList: otherId = " . $otherId );
		$dkId = codes_getCode("Don\'t Know",'species');
		//error_log("printBirdSpeciesList: dkId = " . $dkId );
		
		//$btnClass = 'btn-secondary';
		$btnClass = 'species-general-btn';
		
		$name = codes_getOptionTranslation($dkId);			
		$btnText = "<button type='button' id='species_select_{$filterId}_{$dkId}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select $dkOtherClass'".$toggleExtras."  >$name</button>";
		print '<div id="species_group_'.$filterId.'_'.$dkId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		$name = codes_getOptionTranslation($otherId);			
		$btnText = "<button type='button' id='species_select_{$filterId}_{$otherId}' class='btn $btnClass btn-sm btn-wrap-text species-btn species_select $dkOtherClass'".$toggleExtras."  >$name</button>";
		print '<div id="species_group_'.$filterId.'_'.$otherId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		// Add Nothing and Human 
		//error_log("About to get nothing and human ids" );
		$nothingId = codes_getCode("Nothing",'noanimal');
		//error_log("printBirdSpeciesList: nothingId = " . $nothingId );
		$humanId = codes_getCode("Human",'noanimal');
		//error_log("printBirdSpeciesList: humanId = " . $humanId );
		$vehicleId = codes_getCode("Vehicle",'noanimal');
		
		//$btnClass = 'btn-secondary';
		$btnClass = 'species-general-btn';
		
		// $name = codes_getOptionTranslation($nothingId);			
		// $btnText = "<button type='button' id='control_content_{$filterId}_{$nothingId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control nothing $dkOtherClass' ".$toggleExtrasNothing." >$name</button>";
		// print '<div id="species_group_'.$filterId.'_'.$nothingId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		$name = codes_getOptionTranslation($nothingId);			
		$btnText = "<button type='button' id='species_select_{$filterId}_{$nothingId}' class='btn $btnClass btn-sm btn-wrap-text species-btn nothing $dkOtherClass' ".$toggleExtrasNothing." >$name</button>";
		print '<div id="species_group_'.$filterId.'_'.$nothingId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		//print "<button type='button' class='btn btn-primary classify_control' id='control_content_$nothingId'>$name</button>";
		
		$name = codes_getOptionTranslation($vehicleId);			
		$btnText = "<button type='button' id='control_content_{$filterId}_{$vehicleId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control $dkOtherClass'  >$name</button>";
		print '<div id="species_group_'.$filterId.'_'.$vehicleId.'" class="col-xs-6 col-sm-6 col-md-6 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		$name = codes_getOptionTranslation($humanId);			
		$btnText = "<button type='button' id='control_content_{$filterId}_{$humanId}' class='btn $btnClass btn-sm btn-wrap-text species-btn classify_control $dkOtherClass'  >$name</button>";
		print '<div id="species_group_'.$filterId.'_'.$humanId.'" class="col-xs-12 col-sm-12 col-md-12 btn-group alwaysmatch" style="padding-left:0;padding-right:0;">'.$btnText.'</div>';
		
		print '</div>';
	}


	public static function allSpecies () {
			
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$speciesArray = null;
		
		// Check the language tag and work differently if English
		$lang = langTag();
		if ( $lang == 'en-GB' ) {
			
			//error_log ( "Language is English so no join to OptionData" );
			$query = $db->getQuery(true);
			
			$query->select("O.option_id as id, O.option_name as name")
				->from("Options O")
				->where( "O.struc in ( 'mammal', 'bird', 'invertebrate', 'notinlist' )" );
				
			$db->setQuery($query);
			
			$speciesArray = $db->loadRowList();
			
		}
		else {
			
			$query = $db->getQuery(true);
			
			$query->select("OD.option_id as id, OD.value as name")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc in ( 'mammal','bird','invertebrate','notinlist' )")
				->where("OD.data_type = " . $db->quote($lang) );
				
			$db->setQuery($query);
			
			$speciesArray = $db->loadRowList();
			
		}
		
		return $speciesArray;
	}


	public static function setRunning ( $processName, $isRunning ) {

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
        $table = 'Running';

        if ( !$isRunning ) {
                $query = $db->getQuery(true);

                $fields = array(
                        $db->quoteName('running') . ' = 0'
                );

                // Conditions for which records should be updated.
                $conditions = array(
                                $db->quoteName('process_name') . ' = ' . $db->quote($processName)
                );

                $query->update($db->quoteName($table))->set($fields)->where($conditions);

                $db->setQuery($query);
                $result = $db->execute();

                return true;

        }
        else {
			// If already running return false

			$query = $db->getQuery(true);
			$query->select("running")
					->from("Running")
					->where("process_name = " . $db->quote($processName) );
			$db->setQuery($query);

			$alreadyRunning = $db->loadResult();

			if ( $alreadyRunning ) {
					return false;
			}
			else {
					$query = $db->getQuery(true);

					$fields = array(
							$db->quoteName('running') . ' = 1'
					);

					// Conditions for which records should be updated.
					$conditions = array(
							$db->quoteName('process_name') . ' = ' . $db->quote($processName)
					);

					$query->update($db->quoteName($table))->set($fields)->where($conditions);

					$db->setQuery($query);

					$result = $db->execute();

					return true;
			}
        }
	}

	public static function addUserChoice () {
	
		//    JRequest::checkToken() or die( JText::_( 'Invalid Token' ) );
		$app = Factory::getApplication();
		$input = $app->input;
		
		$choice = $input->getString('choice', 0);
		$value = $input->getInt('value', 0);
			
		$fields = new \stdClass();
		$fields->person_id = self::userID();
		
		// Only do the work if user is logged in
		if ( $fields->person_id ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			 $db = db();
					
			$fields->choice = $choice;
			$fields->value = $value;
			
			$success = $db->insertObject("UserChoice", $fields);
			
			if(!$success){
				error_log ( "UserChoice insert failed" );
			}	
		}
	}

	public static function getNumSites () {
		
		$personId = self::userID();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$query = $db->getQuery(true);
		$query->select("count(*)" )
			->from("Site S")
			->where("person_id = " . $personId);
			
		$db->setQuery($query);
		
		$numSites = $db->loadResult();
		
		return $numSites;
	}
	
	public static function getSiteSelect () {
		
		$personId = self::userID();

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		// Filter by site
		$query = $db->getQuery(true);
		$query->select("distinct S.site_name, S.site_id" )
			->from("Site S")
			->innerJoin("Photo P on P.site_id = S.site_id and P.person_id = " . $personId)
			->innerJoin("Animal A on A.photo_id = P.photo_id")
			->where("A.species != 97")
			->order("S.site_name");
			
		$db->setQuery($query);
		
		//error_log("Site select query created: " . $query->dump());
				
		$siteSelect = $db->loadAssocList("site_id", "site_name");
		
		return $siteSelect;
	}
	
	public static function getNumAnimals () {
		
		$personId = self::userID();

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		// Does the user have any non-like classifications?
		$query = $db->getQuery(true);
		$query->select("count(*)" )
			->from("Animal A")
			->where("A.species != 97 and A.person_id = " . $personId);
			
		$db->setQuery($query);
		
		$numAnimals = $db->loadResult();
		
		return $numAnimals;
		
	}
	
	public static function getSeqsJson ( $projectId ) {
		
		// We will use three sequences or videos.  These will be set in the project options
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$query = $db->getQuery(true);
		$query->select("OD.value")->from("OptionData OD")
			->innerJoin("ProjectOptions PO on PO.option_id = OD.option_id")
			->innerJoin("Options O on PO.option_id = O.option_id and O.struc = 'kiosktutorial'")
			->where("OD.data_type = 'expertsequencejson' and PO.project_id = " . $projectId );
			
		
		$db->setQuery($query);
		$seqsJson = $db->loadResult();
		
		return $seqsJson;
	}
	
	public static function getSecondaryProject ( $projectId ) {
		
		// Get secondary project or default to MammalWeb Britain
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		$query = $db->getQuery(true)
				->select("OD.data_type as type, OD.value as value")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'secondaryproject'")
				->where("PO.project_id = " . $projectId);
		$db->setQuery($query); 
		
		$secondary = $db->loadObject();
		
		$secondaryProject = $secondary ? $secondary->value : 1;
		
		return $secondaryProject;
	
	}
	
	public static function getKioskAboutArticles ( $projectId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$query = $db->getQuery(true)
				->select("OD.value")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'aboutcategory'")
				->where("PO.project_id = " . $projectId);
		$db->setQuery($query); 
		
		$categoryId = $db->loadResult();
		
		
		$dbo = Factory::getContainer()->get('DatabaseDriver');
		$query = $dbo->getQuery(true)
			->select('*')
			->from('#__content')
			->where('catid = ' . $categoryId)
			->order('ordering');
		
		$dbo->setQuery((string)$query);
		$articles = $dbo->loadObjectList();
		return $articles;
	}
	
	public static function getKioskAllAnimals ( $allAnimalsArray ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();

		$query = $db->getQuery(true);
		$query->select("A.animal_id as id, A.species as species, A.gender as gender, A.age as age, A.number as number, O.struc as struc, O.option_name as name, OD.value as kiosk_image")
		  ->from("Animal A")
		  ->innerJoin("Options O on A.species = O.option_id")
		  ->leftJoin("OptionData OD on O.option_id = OD.option_id and OD.data_type='kioskimg'")
		  ->where("A.animal_id in (" . implode(",", $allAnimalsArray) . ")")
		  ->order("A.animal_id");

		$db->setQuery($query);
		$allAnimals = $db->loadObjectList();
		
		return $allAnimals;
		
	}
	
	public static function getPhotosToTransfer () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$query = $db->getQuery(true);
		$query->select("photo_id, person_id, site_id, dirname, filename")
				->from("Photo")
				->where("s3_status = 0");

		$max_num = 1000;
		$db->setQuery($query, 0, $max_num); // LIMIT $max_num
   
		$photos = $db->loadAssocList("photo_id");

		return $photos;
		
	}
	
	
	public static function getOriginalFilesToTransfer () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$query = $db->getQuery(true);
		$query->select("of_id, person_id, site_id, dirname, filename")
				->from("OriginalFiles")
				->where("s3_status = 0 and status = 1");

		$max_num = 1000;
		$db->setQuery($query, 0, $max_num); // LIMIT $max_num
   
		$originalFiles = $db->loadAssocList("of_id");

		return $originalFiles;
		
	}
	
	
	public static function getLargeFilesToTransfer () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		 $db = db();
		
		$query = $db->getQuery(true);
		$query->select("olf_id, person_id, site_id, dirname, filename")
                                ->from("OriginalLargeFiles")
                                ->where("s3_status = 0 and status = 0");

        $max_num = 1000;
		$db->setQuery($query, 0, $max_num); // LIMIT $max_num

		$largeFiles = $db->loadAssocList("olf_id");

		return $largeFiles;
		
	}

	
	private static function chooseMultiple ( $project_ids, $classify_own ) {
	
		//print "<br>chooseMultiple called, classify_own = " . $classify_own . " <br>";
		$photo_id = null;
		
		// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
		$own_string = "";
		if ( $classify_own ) {
			$own_string = " AND P.person_id = " . (int)self::userID();
		}
			
		if ( count($project_ids) > 0 ) {
		
			$project_id_str = implode(',', $project_ids);
		
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
			$q1 = $db->getQuery(true);
			
			$q1->select("count(P.photo_id)")
							->from("Photo P")
							->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
							->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
							->where("P.status = 1")
							->where("P.contains_human =0" . $own_string )
							->where("P.sequence_num = 1" )
							->where("P.photo_id >= PSM.start_photo_id")
				->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->where("P.photo_id not in (select photo_id from Animal where species != 97 and person_id = " . self::userID() . ")");

			
			$db->setQuery($q1); 
			$num_rows = $db->loadResult();
			
			// Get a random integer between 0 and $num_rows-1
			$row_num = rand(0, $num_rows - 1);
			
			$q2 = $db->getQuery(true);
			$q2->select("P.photo_id, P.sequence_id")
				->from("Photo P")
				->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
				->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
				->where("P.status = 1")
				->where("P.contains_human =0" . $own_string )
				->where("P.sequence_num = 1" )
				->where("P.photo_id >= PSM.start_photo_id")
				->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->where("P.photo_id not in (select photo_id from Animal where species != 97 and person_id = " . self::userID() . ")");
				
			$db->setQuery($q2, $row_num, 1); // LIMIT 1 sarting at the n = $row_num
			
			$photo = $db->loadObject();
			if ( $photo ) {
				
				$photo_id = $photo->photo_id;
			}	
			else {
				//error_log("chooseMultiple: no photo ");
			}
		}
		
		return $photo_id;

	}

	private static function chooseSingle ( $project_ids, $classify_own ) {
		
		$photo_id = null;
		
		// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
		$own_string = "";
		if ( $classify_own ) {
			$own_string = " AND P.person_id = " . (int)self::userID();
		}
		
		$project_id_str = implode(',', $project_ids);
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
		$db = readDb();

		$q1 = $db->getQuery(true);
		
		$q1->select("count(P.photo_id)")
        ->from("Photo P")
        ->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
        ->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
        ->where("P.status = 1")
		->where("P.contains_human =0" . $own_string )
	    ->where("P.sequence_num = 1" )
		->where("P.photo_id >= PSM.start_photo_id")
		->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
		->where("P.photo_id not in (select photo_id from Animal where species != 97)");
		
		$db->setQuery($q1); 
		$num_rows = $db->loadResult();
			
		// Get a random integer between 0 and $num_rows-1
		$row_num = rand(0, $num_rows - 1);
			
		$q2 = $db->getQuery(true);
		$q2->select("P.photo_id, P.sequence_id")
			->from("Photo P")
			->innerJoin("Project PROJ ON PROJ.project_id in (".$project_id_str.")")
			->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
			->where("P.status = 1")
			->where("P.contains_human = 0" . $own_string )
			->where("P.sequence_num = 1" )
			->where("P.photo_id >= PSM.start_photo_id")
			->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
			->where("P.photo_id not in (select photo_id from Animal where species != 97)");
	
		$db->setQuery($q2, $row_num, 1); // LIMIT 1 sarting at the n = $row_num
		
		$photo = $db->loadObject();
		if ( $photo ) {
			$photo_id = $photo->photo_id;
		}	
		else {
			//error_log("chooseSingle: no photo = ");
		}
		
		return $photo_id;
	}

	private static function chooseSiteTimeOrdered ( $project_ids, $last_photo_id, $classify_own ) {
		
		$photo_id = null;
		
		// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
		$own_string = "";
		if ( $classify_own ) {
			$own_string = " AND P.person_id = " . (int)self::userID();
		}
		
		$project_id_str = implode(',', $project_ids);
	  
		//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
		$db = readDb();
		
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

	private static function chooseRepeat ( $project_ids, $classify_own ) {
		
		//error_log ( "chooseRepeat called, classify_own = " . $classify_own );
		$photo_id = null;
		
		// If just classifying what this user has uploaded, add to the where string to reduce results to that set.
		$own_string = "";
		if ( $classify_own ) {
			$own_string = " AND P.person_id = " . (int)self::userID();
		}
			
		if ( count($project_ids) > 0 ) {
		
			$project_id_str = implode(',', $project_ids);
		
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
			$q1 = $db->getQuery(true);
			$q2 = $db->getQuery(true);
			
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
				
			// Get a random integer between 0 and $num_rows-1
			$row_num = rand(0, $num_rows - 1);
				
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

	private static function getPriorityWeightings () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
		$db = readDb();
		$app = Factory::getApplication();
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
	
	private static function deleteNothingClassification ( $photo_id, $animal_ids = 0 ) {
	
		$personId = self::userID();

		// Default to 0 means no matches if $animals_id set or meaningless if general delete done.
		$delete_id = 0;
		// If animal_ids, remove any existing classification of 'Nothing' which match the ids on the list
		if ( $animal_ids ) {		
			$animals_csv = implode (',', explode ( "_", $animal_ids ) );
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
			
			// Do any Nothings match?
			$query = $db->getQuery(true);
			$query->select($db->quoteName("animal_id"))
				->from("Animal")
				->where($db->quoteName("photo_id") . " = '$photo_id'" )
				->where($db->quoteName("person_id") . " = " . $personId)
				->where($db->quoteName("animal_id") . "in (".$animals_csv.")")
				->where($db->quoteName("species") . " = 86");
			$db->setQuery($query);
			$delete_id = $db->loadResult();
			
			if ( $delete_id ) {
				$query = $db->getQuery(true);
				$query->delete("Animal")
					->where($db->quoteName("photo_id") . " = '$photo_id'" )
					->where($db->quoteName("person_id") . " = " . $personId)
					->where($db->quoteName("animal_id") . " = '$delete_id'" )
					->where($db->quoteName("species") . " = 86");
				$db->setQuery($query);
				$success = $db->execute();
			}
		}		
		// If no animal_ids, remove any existing classification of 'Nothing'
		else {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
			$query = $db->getQuery(true);
			$query->delete("Animal")
				->where($db->quoteName("photo_id") . " = '$photo_id'" )
				->where($db->quoteName("person_id") . " = " . $personId)
				->where($db->quoteName("species") . " = 86");
			$db->setQuery($query);
			$success = $db->execute();
		}
		
		return $delete_id;
	}

	private static function addAnimalId ( $animalId, &$animalIds, &$allAnimalIds ) {
		
		if ( !$animalIds ) {
			$animalIds = $animalId;
		}
		else {
			$animalIds = $animalIds . "_" . $animalId;
		}
		
		if ( !$allAnimalIds ) {
			$allAnimalIds = $animalId;
		}
		else {
			$allAnimalIds = $allAnimalIds . "_" . $animalId;
		}
	}

	private static function removeAnimalId ( $animalId, &$animalIds, &$allAnimalIds ) {
		
		if ( $animalIds ) {
			$animals = explode("_", $animalIds);
			
			if (($key = array_search($animalId , $animals)) !== false) {
				unset($animals[$key]);
				$animals = array_values ( $animals );
				$animalIds = implode ("_" , $animals);
			}
		}
		if ( $allAnimalIds ) {
			$allAnimals = explode("_", $allAnimalIds);
			
			if (($key = array_search($animalId , $allAnimals)) !== false) {
				unset($allAnimals[$key]);
				$allAnimals = array_values ( $allAnimals );
				$allAnimalIds = implode ("_" , $allAnimals);
			}
		}
	}

	public static function updateSequenceInUse ( $sequenceId, $startPhotoId, $inUse, $checkForHuman ) {
		
		if ( !$sequenceId ) {
			if ( $startPhotoId ) {
				
				$photoDetails = codes_getDetails ( $startPhotoId, 'photo' );
				$sequenceId = $photoDetails['sequence_id'];
			}
			
		}
		if ( !$sequenceId ) {
			
			error_log ( "No sequence id in updateSequenceInUse" );
			
		}
		if ( $inUse == 1 && $checkForHuman ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
		
			$query = $db->getQuery(true);
			$query->select("count(*) from Photo P")
				->where("P.sequence_id = " . $sequenceId )
				->where("P.contains_human = 1");
			$db->setQuery($query);
			
			$numHumans = $db->loadResult();
			
			if ( $numHumans == 0 ) {
				
				$fields = new \StdClass ();
				$fields->sequence_id = $sequenceId;
				$fields->in_use = $inUse;
				   
				codes_updateObject($fields, 'sequence');
			}
		}
		else {
			
			$fields = new \StdClass ();
			$fields->sequence_id = $sequenceId;
			$fields->in_use = $inUse;
			   
			codes_updateObject($fields, 'sequence');
		}
	}
	
	public static function akrem($array,$key){
		$holding=array();
		foreach($array as $k => $v){
			if($key!=$k){
				$holding[$k]=$v;
			}
		}    
		return $holding;
	}


	public static function getFilesToConvert () {
		
		$db = db();

		// Limit to x number of files at a time...

		$query = $db->getQuery(true);
		$query->select("of_id, dirname, filename, upload_filename, taken")
			->from($db->quoteName("OriginalFiles"))
			->where("status = 0")
			->where("filename like '%avi'" );

		$db->setQuery($query, 0, 10);
		$files = $db->loadAssocList();
		
		// Mark all the files as being worked on - so set status to -1
		foreach ( $files as $origfile ) {
			$fields = new \stdClass();
			$fields->of_id = $origfile['of_id'];
			$fields->status = -1;
			$db->updateObject('OriginalFiles', $fields, 'of_id');
		}
	  
		return $files;
	
	}
	
	// Used to write details of a generated file (eg split or sonogram) to Photo table.
	public static function writeSplitFile ( $ofId, $newFile, $delay = 0 ) {
		
		// Note that the original exif will be stored in OriginalFilesExif.
		
		// Get the data from the original file
		$db = db();
		
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
			
		$photoFields = new \stdClass();
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
			//error_log("Success writing split file " . $newFile . ", photo_id = " . $photoId );
			
			self::addToAIQueue('MEGA', $photoId);
		}
		else {
			error_log("Failed writing split file " . $newFile );
		}
		
		return $photoId;
		
	}

	public static function setOriginalFileStatus ($tsId, $success) {
		
		$status = $success ? 1 : 2;
		
		$db = db();
		
		$fields = new \stdClass();
		$fields->of_id = $tsId;
		$fields->status = $status;
		$db->updateObject('OriginalFiles', $fields, 'of_id');
		
	}
	


}
	
	
?>
