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
* Kiosk Standard Quiz results 
*
* @since 0.0.1
*/
class BioDivViewKioskQuizResultsMulti extends JViewLegacy
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
		
		$this->personId = (int)userID();
		
		
		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		//error_log ( "Project id = " . $this->projectId );

		// Get the parameters from the input data
		$input = JFactory::getApplication()->input;
		
		$this->topicId = $input->getInt('topic', 0);
		$qs = $input->getString('questions', 0);
		$as = $input->getString('answers', 0);
		
		error_log ( "Project: " . $this->projectId );
		error_log ( "Topic: " . $this->topicId );
		error_log ( "Questions: " . $qs );
		error_log ( "Answers: " . $as );
		
		$this->questions = json_decode($qs);
		$this->answers = json_decode($as);
		
		$this->errorMsg = null;

		if ( !$this->personId ) $errorMsg = JText::_("COM_BIODIV_KIOSKQUIZRESULTSMULTI_NO_PERSON");
		if ( !$this->projectId ) $errorMsg = JText::_("COM_BIODIV_KIOSKQUIZRESULTSMULTI_NO_PROJECT");
		if ( !$this->topicId ) $errorMsg = JText::_("COM_BIODIV_KIOSKQUIZRESULTSMULTI_NO_TOPIC");
		if ( !$this->questions ) $errorMsg = JText::_("COM_BIODIV_KIOSKQUIZRESULTSMULTI_NO_QUESTIONS");
		if ( !$this->answers ) $errorMsg = JText::_("COM_BIODIV_KIOSKQUIZRESULTSMULTI_NO_ANSWERS");
		

		
		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		$this->numQuestions = count($this->questions);
		//error_log ("Got " . $this->numQuestions . " questions" );
		
		$this->score = 0;
		$this->totalSpecies = 0;
		
		$this->sequences = array();
		$this->results = array();
		
		for ( $i = 0; $i < $this->numQuestions; $i++ ) {
			
			$seqId = $this->questions[$i];
			//error_log ("Checking sequence " . $seqId );
			
			$answers = $this->answers[$i];
			
			$errStr = print_r ( $answers->speciesId, true );
			error_log ("User answers are " . $errStr);
			
			// Need the sequence details for media files
			$seq = getTrainingSequence ( $seqId, $this->topicId );			
			$this->sequences[] = $seq;
			
			$seqScore = calculateTestScore ( $this->topicId, $seqId, $answers->speciesId );
			
			$this->score += $seqScore["correct"];
			$this->totalSpecies += $seqScore["total"];

			$this->results[] = $seqScore;
			//error_log ( "Added result" );
			
		}
		
		$scorePercent = 100 * $this->score / $this->totalSpecies;
		
		
		// Write results to database
		$written = $app->getUserState('com_biodiv.written');
		if ( !$written ) {
			
			// Write the results to the database
			writeTestResults ( $this->topicId, $qs, $as, $scorePercent );
			
			// Ensure we don't rewrite this training session
			$app->setUserState('com_biodiv.written', '1');
			
		}
	
		
		// Display the view
		parent::display($tpl);
    }
}



?>