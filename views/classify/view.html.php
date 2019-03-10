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
      // Assign data to the view
	  //($person_id = (int)userID()) or die("No person_id");
	  $app = JFactory::getApplication();
	  $this->photo_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id', 0);

	  $this->self = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_self', 'classify_self', 0);
	  
	  $this->classify_project = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_project', 'classify_project', 0);
		
	  //echo "BioDivViewClassify, this->classify_project = ", $this->classify_project;
	  
	  $this->classify_only_project = 
	    (int)$app->getUserStateFromRequest('com_biodiv.classify_only_project', 'classify_only_project', 0);
		
	  //echo "BioDivViewClassify, this->classify_only_project = ", $this->classify_only_project;
	  
	  $this->my_project = 
	    $app->getUserStateFromRequest('com_biodiv.my_project', 'my_project', 0);
		
	  // Check the user has access as this view can be loaded from project pages as well as Spotter status page
	  
	  if ( !userID() ) {
		$app = JFactory::getApplication();
		$message = "Please log in before classifying.";
		$url = "".BIODIV_ROOT."&view=classify";
		if ( $this->classify_only_project ) $url .= "&classify_only_project=" . $this->classify_only_project;
		if ( $this->my_project ) $url .= "&my_project=" . $this->my_project;
		if ( $this->self ) $url .= "&classify_self=" . $this->self;
		$url = urlencode(base64_encode($url));
		$url = JRoute::_('index.php?option=com_users&view=login&return=' . $url );
		$app->redirect($url, $message);
	  }

	  
	  // Check the user can access this project, if a project is specified.
	  if ( $this->my_project ) {
		  $project_id = codes_getCode($this->my_project, "project" );
		  $fields = new StdClass();
		  $fields->project_id = $project_id;
		  if ( !canClassify("project", $fields) ) {
			$app = JFactory::getApplication();
			$message = "Sorry you do not have access to classify on this project.";
			$url = "".BIODIV_ROOT."&view=projecthome";
			$url .= "&project_id=" . $project_id;
			$app->redirect($url, $message);
			
		  }
	  }
	  
	  
	  $this->sequence = null;
	  // Need to do a check here so that refresh doesn't load next sequence......
	  // If there is a photo_id then get the sequence for that photo id.  If not, get a new sequence
	  if(!$this->photo_id){
		$this->sequence = nextSequence();
	  }
	  else {
		$this->sequence = getSequenceDetails($this->photo_id);
	  }
	
	  if (count($this->sequence) > 0) {
		$this->firstPhoto = $this->sequence[0];
		$this->photo_id = $this->firstPhoto["photo_id"];
	  }
	  $app->setUserState('com_biodiv.photo_id', $this->photo_id);
	  
	  
	  //print "<br>Got the following sequence:<br>";
	  //print_r($this->sequence);
	  
	  /* now sequence contains the details
	  $this->sequence_details = array();
	  foreach ($this->sequence as $next_photo_id) {
		  //print "<br>Adding photo " . $next_photo_id->photo_id . "<br>";
		  $this->sequence_details[] = codes_getDetails($next_photo_id, 'photo');
	  }
	  //print "<br>Got the following ". count($this->sequence_details) . " sequence details:<br>";
	  //print_r($this->sequence_details);
	  */
	  $this->photoDetails = null;
	  // There might be nothing to classify...
	  if ( count($this->sequence) > 0 ) {
		 $this->photoDetails = $this->sequence[0];
		
		 // Check for video
		 $this->isVideo = false;
		 $filename = $this->photoDetails['filename'];
		 if ( strpos(strtolower($filename), '.mp4') !== false ) {
			 $this->isVideo = true;
		 }
	  
	  
		// Get the filter ids and filter labels for this photo. 
		$project_id = codes_getCode($this->my_project, 'project');
	  
		$this->filters = getFilters ();
	  
		foreach ( $this->filters as $filterId=>$filter ) {
		  $isCommon = $filter['label'] == 'Common';
		  $this->filters[$filterId]['species'] = getSpecies ( $filterId, $isCommon );
		}
	  
		$this->projectFilters = getProjectFilters ( $project_id, $this->photo_id );
	  
		foreach ( $this->projectFilters as $filterId=>$filter ) {
		  $this->projectFilters[$filterId]['species'] = getSpecies ( $filterId, false );
		}
	  
	  
		$this->allSpecies = array();
		$this->allSpecies = codes_getList ( "species" );
	  
	 

		$this->lcontrols = array();
		$this->rcontrols = array();
		foreach(codes_getList("noanimal") as $stuff){
			list($id, $name) = $stuff;
			$this->lcontrols["control_content_" . $id] = $name;
		}

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
	  
		$this->nextseq = "Next sequence <span class='fa fa-arrow-circle-right'/>";

		$this->classifyInputs = array();
		foreach(array("gender", "age") as $struc){
			$input = "<label for ='classify_$struc'>" . codes_getTitle($struc) . "</label><br />\n";
			//$input .= "<select id='classify_$struc' name='$struc'>\n";
			//$input .= codes_getOptions(1, $struc);
			// set default to be unknown:
			$features = array("gender"=>84, "age"=>85);
			$input .= codes_getRadioButtons($struc, $struc, $features);
			//$input .= "\n</select>\n";
			$this->classifyInputs[] = $input;	    
		}
		$number = "<label for ='classify_number'>How many?</label>\n";
		$number .= "<input id='classify_number' type='number' min='1' value='1' name='number'/>\n";
		$this->classifyInputs[] = $number;
	  
	  }

	  // Display the view
	  parent::display($tpl);
        }
}



?>