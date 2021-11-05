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
* Kiosk media carousel for LHS of kiosk classify page
*
* @since 0.0.1
*/
class BioDivViewKioskMediaCarousel extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		//error_log ( "BioDivViewKioskMediaCarousel::display called" );
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();
		$input = $app->input;

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		//error_log ( "Project id = " . $this->projectId );

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);
		
		// Set the user state so classifies just for this project
		$app->setUserState('com_biodiv.classify_only_project', 1);

		// Flag to say whether or not we're classifying the second project.
		$classifySecond = 
		$app->getUserStateFromRequest('com_biodiv.classify_second_project', 'classify_second_project', 0);

		$this->secondProject = null;
		if ( $classifySecond == 1 ) {
			// Use MammalWeb for now
			$this->secondProject = 1;
		}
		
		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		// Get the text snippets - enables multilingual
		$this->translations = getTranslations("kioskclassifyproject");

		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);

		$this->mediaCarousel = new MediaCarousel();
		
		// Is a specific sequence requested?
		//error_log ( "getting sequence" );
		
		$this->sequenceId = null;
		$this->sequence = null;
		
		$this->sequenceId = $input->getInt('sequence_id', null);
		
		//error_log ( "sequence id parameter = " . $this->sequenceId );
		
		if ( $this->sequenceId == null ) {
			//error_log ( "sequence id parameter is null" );
			
			// May have asked for a second project, deafults to primary (from user request) if not
			$sequenceDetails = nextSequence($this->secondProject);
			
			if ( count($sequenceDetails) > 0 ) {
				//error_log ("got details");
				$this->sequenceId = $sequenceDetails[0]['sequence_id'];
				$this->sequence = new Sequence ( $this->sequenceId );
			}
		}
		else {
			//error_log ( "sequence id parameter is set" );
			
			$this->sequence = new Sequence ( $this->sequenceId );
		}
		
		
		

		/*
		// Get a sequence - if none is specified
		if ( $this->sequenceId == null ) {
			$sequenceDetails = nextSequence();
			if ( count($sequenceDetails) > 0 ) {
				$this->sequenceId = $sequenceDetails[0]['sequence_id'];
				$this->sequence = new Sequence ( $this->sequenceId );
			}
		}
		else {
			this->sequence = new Sequence ( $this->sequenceId );
		}
		*/
		
		/*
		$sequenceDetails = nextSequence();
		if ( count($sequenceDetails) > 0 ) {
			$this->sequenceId = $sequenceDetails[0]['sequence_id'];
			$this->sequence = new Sequence ( $this->sequenceId );
		}
		*/
		
		//error_log ( "about to call display" );
		
		// Display the view
		parent::display($tpl);
    }
}



?>