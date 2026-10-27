<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskQuizResults;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View used as kiosk quiz results page
 */
class RawView extends BaseHtmlView {

    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
		$kioskModel = $this->getModel();

		$this->personId = $kioskModel->userID();
		
		$this->topicId = $input->getInt('topic', 0);
		$qs = $input->getString('questions', 0);
		$as = $input->getString('answers', 0);
		$this->badge = $input->getInt('badge', 0);
		
		//error_log ( "Project: " . $this->projectId );
		//error_log ( "Topic: " . $this->topicId );
		//error_log ( "Questions: " . $qs );
		//error_log ( "Answers: " . $as );
		
		$this->questions = json_decode($qs);
		$this->answers = json_decode($as);
		
		$this->errorMsg = null;

		if ( !$this->personId ) $errorMsg = Text::_("COM_BIODIV_KIOSKQUIZRESULTS_NO_PERSON");
		if ( !$this->projectId ) $errorMsg = Text::_("COM_BIODIV_KIOSKQUIZRESULTS_NO_PROJECT");
		if ( !$this->topicId ) $errorMsg = Text::_("COM_BIODIV_KIOSKQUIZRESULTS_NO_TOPIC");
		if ( !$this->questions ) $errorMsg = Text::_("COM_BIODIV_KIOSKQUIZRESULTS_NO_QUESTIONS");
		if ( !$this->answers ) $errorMsg = Text::_("COM_BIODIV_KIOSKQUIZRESULTS_NO_ANSWERS");
		
		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		$this->numQuestions = count($this->questions);
		//error_log ("Got " . $this->numQuestions . " questions" );
		
		$this->score = 0;
		
		$this->sequences = array();
		$this->results = array();
		
		for ( $i = 0; $i < $this->numQuestions; $i++ ) {
			
			$seqId = $this->questions[$i];
			//error_log ("Checking sequence " . $seqId );
			
			$answer = $this->answers[$i];
			//error_log ("User answer is " . $answer->speciesId);
			
			$seq = $kioskModel->getTrainingSequence ( $seqId, $this->topicId );
			
			$this->sequences[] = $seq;
			
			$correctAnswer = $seq->getPrimarySpecies()[0];
			
			//$errStr = print_r ( $correctAnswer, true );
			//error_log ("Correct answer is " . $errStr);
			
			$isCorrect = false;
			$correctId = $correctAnswer->id;
			$userId = (int)$answer->speciesId;
			if ( $correctId === $userId ) {
				$isCorrect = true;
				$this->score += 1;
			}
			
			$this->results[] = ["correctId"=>$correctId, 
									"userId"=> $userId, 
									"isCorrect"=> $isCorrect];
									
			//error_log ( "Added result" );
			
		}
		
		$scorePercent = 100 * $this->score / $this->numQuestions;
		
		// Write results to database
		$written = $app->getUserState('com_biodiv.written');
		if ( !$written ) {
			
			// Write the results to the database
			$kioskModel->writeTestResults ( $this->topicId, $qs, $as, $scorePercent );
			
			// Ensure we don't rewrite this training session
			$app->setUserState('com_biodiv.written', '1');
			
		}
		
		// // No sign of this 
		// if ( $this->badge > 0 ) {
			// $badgeResult = $kioskModel->checkBadge ( $this->badge );
		// }
	
		// Display the view
		parent::display($tpl);
    }
}



?>