<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;


 
/**
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewClassify extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
	  $app = JFactory::getApplication();

	  $input = $app->input;
	  
	   $clearPhoto = $input->getInt('clear', 0);
        if ( $clearPhoto ) {
                $this->photo_id = 0;
        }
        else {
                $this->photo_id =
                (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id', 0);
        }

	  $this->self = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_self', 'classify_self', 0);
	  
	  $this->classify_project = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_project', 'classify_project', 0);
		
	  $this->classify_only_project = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_only_project', 'classify_only_project', 0);
		
	  (int)$this->project_id = 
	    $app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
	  $this->animal_ids = 
	    $app->getUserStateFromRequest('com_biodiv.animal_ids', 'animal_ids', 0);
	  
	  // all_animal_ids not used in original classify mode so set to 0 here.
	  $app->setUserState('com_biodiv.all_animal_ids', 0);
	  
	  
	  // Check the user has access as this view can be loaded from project pages as well as Spotter status page
	  
	  if ( !userID() ) {
		  
		$app = JFactory::getApplication();
		
		$currentUri = Uri::getInstance();
		
		$loginParam = $app->input->getString('login', 0);
		
		$defaultLoginPage = 'index.php?option=com_users&view=login';
		
		if ( $loginParam ) {			
			// assume any passed in login page has specific routing
			$url = JRoute::_($loginParam);			
		}
		else {
			$url = JRoute::_($defaultLoginPage.'&return='.base64_encode($currentUri));
		}
		
		$message = JText::_("COM_BIODIV_CLASSIFY_LOG_CLASS");
		$app->redirect($url, $message);
		
	  }

	  
	  // Check the user can access this project, if a project is specified.
	  if ( $this->project_id ) {
		  $fields = new StdClass();
		  $fields->project_id = $this->project_id;
		  if ( !canClassify("project", $fields) ) {
			$app = JFactory::getApplication();
			$message = JText::_("COM_BIODIV_CLASSIFY_NO_ACCESS");  //"Sorry you do not have access to classify on this project.";
			$url = "".BIODIV_ROOT."&view=projecthome";
			$url .= "&project_id=" . $this->project_id;
			$app->redirect($url, $message);
			
		  }
	  }
	  
	  $this->projectPage = null;
	  if ( $this->project_id ) {
		  $projectPages = getSingleProjectOptions ( $this->project_id, "projectpage");
		  
		  if ( count($projectPages) > 0 ) {
			  $this->projectPage = $projectPages[0]["option_name"];
		  }
	  }
	  
	  $this->sequence = null;
	  // Need to do a check here so that refresh doesn't load next sequence......
	  // If there is a photo_id then get the sequence for that photo id.  If not, get a new sequence
	  if(!$this->photo_id){
        //error_log("BioDivViewClassify.display calling nextSequence ");
		$this->sequence = nextSequence();
	  }
	  else {
		//error_log("BioDivViewClassify.display cusing existing sequence ");
		$this->sequence = getSequenceDetails($this->photo_id);
		
		// Only check for existing classifications if we are refreshing...
		if ( $this->animal_ids ) {
			//error_log("classify view, animal ids found: " . $this->animal_ids );
			$animals_csv = implode(",", explode("_", $this->animal_ids));
			$db = JDatabase::getInstance(dbOptions());
			$query = $db->getQuery(true);
			$query->select("animal_id, species, gender, age, number")
			->from("Animal")
			->where("animal_id in (".$animals_csv.")");

			$db->setQuery($query);
			$this->animals = $db->loadObjectList("animal_id");
		}
	  }
	
	  if (count($this->sequence) > 0) {
		$this->firstPhoto = $this->sequence[0];
		$this->photo_id = $this->firstPhoto["photo_id"];
	  }
	  
	  //error_log("BioDivViewClassify.display photo_id now = " . $this->photo_id . ", setting user state" );
	  
	  
	  $app->setUserState('com_biodiv.photo_id', $this->photo_id);
	  $app->setUserState('com_biodiv.prev_photo_id', $this->photo_id);
	  
	  $this->photoDetails = null;
	  // There might be nothing to classify...
	  if ( count($this->sequence) > 0 ) {
		 $this->photoDetails = $this->sequence[0];
		
		 // Check for video
		 $this->isVideo = isVideo($this->photo_id);
		 $this->isAudio = isAudio($this->photo_id);
		 
		 $this->maxClassifications = 10;
		 if ( $this->isAudio ) $this->maxClassifications = 20;
		 
		// Get the general location of the site to help spotters
		$site_id = $this->photoDetails['site_id'];
		$this->location = getSiteLocation($site_id);
		
		// Get the filter ids and filter labels for this photo. 
		//$project_id = codes_getCode($this->my_project, 'project');
	  
		$this->projectFilters = getProjectFilters ( $this->project_id, $this->photo_id );
	  
		foreach ( $this->projectFilters as $filterId=>$filter ) {
		  $this->projectFilters[$filterId]['species'] = getSpecies ( $filterId, false );
		}
	  
		$this->allSpecies = array();
		$this->allSpecies = codes_getList ( "speciestran" );

/*
		$this->lcontrols = array();
		$this->rcontrols = array();
		foreach(codes_getList("noanimaltran") as $stuff){
			list($id, $name) = $stuff;
			// Handle special cases
			if ( $id == 86 )
				$this->lcontrols["control_content_" . $id] = biodiv_label_icons( "nothing", $name );
			else if ( $id == 87 )
				$this->lcontrols["control_content_" . $id] = biodiv_label_icons( "human", $name );
			else
				$this->lcontrols["control_content_" . $id] = $name;
		}
*/
		$this->sequence_id = $this->photoDetails['sequence_id'];
		$this->sequenceDetails = codes_getDetails($this->sequence_id, "sequence");
		$this->sequenceLength = $this->sequenceDetails['sequence_length'];
		$this->sequencePosition = $this->photoDetails['sequence_num'];
		$this->sequenceStartPhoto = photoSequenceStart($this->photo_id);
		if($this->sequenceLength>0){
			$this->sequenceProgress = round($this->sequencePosition*100.0/$this->sequenceLength);
		}
		else{
			$this->sequenceProgress = 0;
		}
	  
		$this->invertimage = JText::_("COM_BIODIV_CLASSIFY_INVERT_IMAGE") . " <span class='fa fa-adjust'/>";
		$this->sequenceinfo = " <span class='fa fa-info'/>";
		$this->showmap = JText::_("COM_BIODIV_CLASSIFY_SHOW_MAP") . " <span class='fa fa-map-marker'/>";
		$this->nextseq = JText::_("COM_BIODIV_CLASSIFY_NEXT_SEQ") . " <span class='fa fa-arrow-circle-right'/>";

		$this->classifyInputs = getClassifyInputs();
		
		$this->eggsId = codes_getCode('Eggs', 'species');
		$this->nestId = codes_getCode('Nest', 'species');
	  
	  }

	  // Display the view
	  parent::display($tpl);
        }
}



?>
