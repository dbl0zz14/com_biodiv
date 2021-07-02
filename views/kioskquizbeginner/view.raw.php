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
* Kiosk Classify top level
*
* @since 0.0.1
*/
class BioDivViewKioskQuizBeginner extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "BioDivViewKioskQuizBeginner::display called" );
		
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		error_log ( "Project id = " . $this->projectId );

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);
		
		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		// Get the text snippets - enables multilingual
		$this->translations = getTranslations("kioskquizbeginner");

		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);
		
		// Get the topic for this project beginner quiz
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
				->select("OD.option_id")
				->from("OptionData OD")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'topiclevel' and OD.value = 'beginner'")
				->where("PO.project_id = " . $this->projectId);
		$db->setQuery($query); 
		
		$this->topicId = $db->loadResult();
		
		// use the BeginnerQuiz class to get the sequences and options
		
		$this->quiz = new Biodiv\BeginnerQuiz($this->topicId, 5);
		
		$this->sequenceIds = $this->quiz->getSequenceIds();
		$this->sequences = $this->quiz->getSequences();
		
		
		//$firstCorrect = $this->sequences[0]->getPrimarySpecies();
		//$this->firstCorrectSpecies = $firstCorrect[0]->id;
		
		// Determine whether (initial) photos or video
		/* do this sequence by sequence
		$photoIds = array_keys ( $this->sequences[0]->getMediaFiles() );
		$this->isVideo = isVideo($photoIds[0]);
		*/
		
		/*
		error_log ( "Correct species = " . $this->firstCorrectSpecies );
		
		$this->options = $this->quiz->getIncorrectSpecies(0, 3);
		
		$this->options[] = $this->firstCorrectSpecies;
		
		shuffle ( $this->options );
		*/

		$this->mediaCarousel = new MediaCarousel();
		
		error_log ( "About to call template" );
		// Display the view
		parent::display($tpl);
    }
}



?>