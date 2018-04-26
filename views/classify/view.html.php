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
	  ($person_id = (int)userID()) or die("No person_id");
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
		
	  //echo "BioDivViewClassify, this->my_project = ", $this->my_project;
	  /*
	  if(!$this->photo_id){
	    
	    $this->photo_id = nextPhoto(0);
	    $app->setUserState('com_biodiv.photo_id', $this->photo_id);
	  }
	  
	  $this->photoDetails = codes_getDetails($this->photo_id, 'photo');
	  */
	  $this->sequence = null;
	  // Need to do a check here so that refresh doesn't load next sequence......
	  // If there is a photo_id then get the sequence for that photo id.  If not, get a new sequence
	  if(!$this->photo_id){
	    $this->sequence = nextSequence();
	  }
	  else {
		$this->sequence = getSequence($this->photo_id);
	  }
	
	  if ($this->sequence) {
		$this->photo_id = $this->sequence[0];
	  }
	  $app->setUserState('com_biodiv.photo_id', $this->photo_id);
	  
	  
	  //print "<br>Got the following sequence:<br>";
	  //print_r($this->sequence);
	  
	  $this->sequence_details = array();
	  foreach ($this->sequence as $next_photo_id) {
		  //print "<br>Adding photo " . $next_photo_id->photo_id . "<br>";
		  $this->sequence_details[] = codes_getDetails($next_photo_id, 'photo');
	  }
	  //print "<br>Got the following ". count($this->sequence_details) . " sequence details:<br>";
	  //print_r($this->sequence_details);
	  
	  $this->photoDetails = $this->sequence_details[0];
	  

	  
	  $this->species = array();
	  foreach(codes_getList("species") as $stuff){
	    list($id, $name) = $stuff;
	    $details = codes_getDetails($id, 'species');
	    $this->species[$id] = array("name" => $name,
					"type" => $details['struc'], // mammal or bird or notinlist
					"page" => $details['seq']);;
	  }

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
	  /*
	  if($this->photo_id == $this->sequenceStartPhoto){
	    $this->rcontrols["control_startseq"] = "<span class='fa fa-arrow-circle-left disabled'></span> Start";
	  }
	  else{
	    $this->rcontrols["control_startseq"] = "<span class='fa fa-arrow-circle-left'></span> Start";
	  }
	  */
/*
	  if($this->photoDetails['prev_photo']>0){
	    $this->rcontrols["control_prev"] = "<span class='fa fa-chevron-circle-left'></span> Previous";
	  }
	  else{
	    $this->rcontrols["control_prev"] = "<span class='fa fa-chevron-circle-left disabled'></span> Previous";
	  }
*/
/*
	  if($this->photoDetails['next_photo']>0){
	    $this->rcontrols["control_next"] = "Next <span class='fa fa-chevron-circle-right'/>";
	  }
	  else{
	    $this->rcontrols["control_next"] = "Next <span class='fa fa-chevron-circle-right disabled'/>";
	  }
	  
	  $this->rcontrols["control_nextseq"] = "Next sequence <span class='fa fa-arrow-circle-right'/>";
	  */
	  
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

	  // Display the view
	  parent::display($tpl);
        }
}



?>