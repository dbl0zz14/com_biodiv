<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

include_once "local.php";


class BiodivSurvey {
	
	const TEXT = 1;
	const OPTION = 2;
	const SCALE10 = 3;
	const NUMBER = 4;
	
	private $surveyId;
	private $surveyDetails;
	private $introArticle;
	private $participantArticle;
	private $consentArticle;
	private $debriefArticle;
	private $sections;
	private $questions;
	private $responseOptions;
	private $languageTag;
	
	/*
	private $triggerView;
	private $triggerNumber;
	private $triggerType;
	*/
	
	
	function __construct( $surveyId )
	{
		$this->surveyId = $surveyId;
		
		$this->surveyDetails = codes_getDetails($surveyId, "survey");
		
		$this->isFollowUp = $this->surveyDetails['follow_up_to'] != null;
		
		$this->introArticle = null;
		$this->participantArticle = null;
		$this->consentArticle = null;
		$this->debriefArticle = null;
		
		$this->sections = null;
		$this->questions = null;
		$this->responseOptions = null;
	
		// Set language
		$langObject = JFactory::getLanguage();
		$this->languageTag = $langObject->getTag();
	}
	
	// Check whether a survey should be triggered for the given person and view. If yes, return the survey id.
	public static function triggerSurvey ( $personId, $view ) {
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Get all the surveys for this view
		$query = $db->getQuery(true);
		$query->select("survey_id, number, type")
			->from( "SurveyTiming" )
			->where( "view=".$db->quote($view) )
			->where( "number > -1" )
			->where( "survey_id not in (select survey_id from UserResponse where person_id = " . $personId . " ) " ) ;
		$db->setQuery($query);
		$surveys = $db->loadAssocList();
			
		// How many classifications has this user done?
		$query = $db->getQuery(true);
		$query->select("count(distinct photo_id)")
			->from( "Animal" )
			->where( "person_id=".$personId )
			->where( "species != 97" );
		$db->setQuery($query);
		$numAnimals = $db->loadResult();
		
		// What is the date of their first classification?
		$query = $db->getQuery(true);
		$query->select("min(timestamp)")
			->from( "Animal" )
			->where( "person_id=".$personId )
			->where( "species != 97" );
		$db->setQuery($query);
		$firstClassDate = $db->loadResult();
		
		// Which surveys does this user not want to take part in?
		$query = $db->getQuery(true);
		$query->select("survey_id")
			->from( "UserConsent" )
			->where( "person_id = ".$personId )
			->where( "consent_given = 0" );
		$db->setQuery($query);
		$dissentSurveys = $db->loadColumn();
		
		// Which surveys has this user consented to take part in?
		$query = $db->getQuery(true);
		$query->select("survey_id, num_animals, timestamp")
			->from( "UserConsent" )
			->where( "person_id = ".$personId )
			->where( "consent_given = 1" );
		$db->setQuery($query);
		$consentSurveys = $db->loadAssocList('survey_id');
		$consentIds = array_keys ( $consentSurveys );
		
		// Check each survey in turn
		foreach ( $surveys as $survey ) {
			
			$currSurveyId = $survey['survey_id'];
			
			$topLevelSurvey = BiodivSurvey::topLevelSurvey($currSurveyId);
			$isTopLevelSurvey = $currSurveyId == $topLevelSurvey;
			
			// First check we don't have dissent for this survey, top level or otherwise
			if ( !in_array( $currSurveyId, $dissentSurveys ) ) {
				
				// Only test trigger for top level surveys or those which have consent at top level, 
				if ( $isTopLevelSurvey || in_array($topLevelSurvey, $consentIds) ) {
			
			
					// If type is START, compare with how many classifications the user has made. This is for before users classify
					if ( $survey['type'] == 'START' && $numAnimals == 0 ) {
						return $currSurveyId;				
					}
					
					// If type is CLASSIFICATION, compare with how many classifications the user has made.
					else if ( $survey['type'] == 'CLASSIFICATION' ) {
						
						if ( $numAnimals >= $survey['number'] ) {
							
							// If this isn't the top level survey, ensure the user has done the number of classifications between original consent and now.
							// This makes sure users are not immediately presented with follow up surveys
							if ( $isTopLevelSurvey ) {
								return $currSurveyId;
							}
							else if ( $numAnimals >= $consentSurveys[$topLevelSurvey]['num_animals'] + $survey['number'] ) {
								return $currSurveyId;
							}
						}
						
					}			
					
					// If type is DAY get the original survey and see whether we are beyond that number of days.
					else if ( $survey['type'] == 'DAY' ) {
						
						
						$today = date_create("now");
						$firstDate = date_create($firstClassDate);
						
					   
						$interval = date_diff($today, $firstDate);
					   
						$diffDays =  $interval->format( '%a' );
						
						error_log ( "diff days = " . $diffDays );
						
						if ( $numAnimals > 0 && $diffDays >= $survey['number'] ) {
							// If this isn't the top level survey, ensure the same number of days has passed since consent was given.
							// This makes sure users are not immediately presented with follow up surveys
							if ( $isTopLevelSurvey ) {
								return $survey['survey_id'];
							}
							else {
								error_log ( "Checking days since consent was given" );
								$consentDate = date_create($consentSurveys[$topLevelSurvey]['timestamp']);
								$consentInterval = date_diff($today, $consentDate);
								$consentDiffDays = $consentInterval->format( '%a' );
						
								error_log ( "consent diff days = " . $consentDiffDays );
								
								if ( $consentDiffDays >= $survey['number'] ) {
									return $survey['survey_id'];
								}
							}
						}
						
					}	
				}
			}
		}
		
		return null;
	}
	
	// Check whether given person has given consent for the given survey. 
	public static function topLevelSurvey ( $surveyId ) {
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		$query->select("distinct S.survey_id, S.follow_up_to")
			->from("Survey S");
		$db->setQuery($query);
		$links = $db->loadAssocList('survey_id');
		
		$topSurveyId = null;
		$prevSurveyId = null;
		$nextSurveyId = $surveyId;
		while ( $topSurveyId == null ) {
			$prevSurveyId = $nextSurveyId;
			$nextSurveyId = $links[$prevSurveyId]['follow_up_to'];
			if ( $nextSurveyId == null ) {
				$topSurveyId = $prevSurveyId;
			}
		}
		
		error_log ( "Top level survey id: " . $topSurveyId );
		
		return $topSurveyId;
	}
	
	// Check whether given person has given consent for the given survey. 
	public static function haveConsent ( $personId, $surveyId ) {
		
		$consentGiven = 0;
		
		$db = JDatabase::getInstance(dbOptions());
		
		$topSurveyId = BiodivSurvey::topLevelSurvey($surveyId);
		
		$query = $db->getQuery(true);
		$query->select("distinct UC.consent_given")
			->from("UserConsent UC")
			->where("UC.survey_id = " . $topSurveyId )
			->where("UC.person_id = " . $personId );
		$db->setQuery($query);
		$consentGiven = $db->loadResult();
		
		return $consentGiven == 1;
	}
	
	
	public static function getResponseTypes ( $surveyId ) {
		
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
				
		// Direct sql
		$query->select("distinct SQ.sq_id, SQ.response_type")
			->from("SurveyQuestions SQ")
			->where("SQ.survey_id = " . $surveyId );
		$db->setQuery($query);
		$results = $db->loadAssocList();
		
		return array_column ( $results, 'response_type', 'sq_id');
	}
	
	public static function getResponseTranslations () {
		
		$trns = codes_getList ( 'responsetran' );
		return array_column ( $trns, 1, 0);
	}
			
	public static function isFollowUp ( $surveyId ) {
		
		$surveyDetails = codes_getDetails($surveyId, "survey");
		
		return $surveyDetails['follow_up_to'] != null;
		
	}
			
	
	public function getHook() {
		if ( $this->introArticle == null ) {
			$this->introArticle = getArticleById ( $this->surveyDetails['intro_article'] );
		}
		return $this->introArticle->title;
	}
	
	public function getIntroText() {
		if ( $this->introArticle == null ) {
			$this->introArticle = getArticleById ( $this->surveyDetails['intro_article'] );
		}
		return $this->introArticle->introtext;
	}
	
	public function getParticipantInfo() {
		if ( $this->participantArticle == null ) {
			$this->participantArticle = getArticleById ( $this->surveyDetails['participant_article'] );
		}
		return $this->participantArticle->introtext;
	}
	
	public function getConsentHeading() {
		if ( $this->consentArticle == null ) {
			$this->consentArticle = getArticleById ( $this->surveyDetails['consent_article'] );
		}
		return $this->consentArticle->title;
	}
	
	public function getConsentInstructions() {
		if ( $this->consentArticle == null ) {
			$this->consentArticle = getArticleById ( $this->surveyDetails['consent_article'] );
		}
		return $this->consentArticle->introtext;
	}
	
	public function getConsentText() {
		if ( $this->consentArticle == null ) {
			$this->consentArticle = getArticleById ( $this->surveyDetails['consent_article'] );
		}
		return $this->consentArticle->fulltext;
	}
	
	public function getDebriefArticle() {
		if ( $this->debriefArticle == null ) {
			$this->debriefArticle = getArticleById ( $this->surveyDetails['debrief_article'] );
		}
		return $this->debriefArticle->introtext;
	}
	
	// Follow up surveys do not require additional consent, they rely on the original survey consent
	public function requireConsent() {
		
		return $this->surveyDetails['follow_up_to'] == null;
	}
	
	public function getSections () {
		if ( $this->sections == null ) {
			
			$db = JDatabase::getInstance(dbOptions());
			
			// Check language
			if ( $this->languageTag == "en-GB" ) {
				$query = $db->getQuery(true);
				
				// Direct sql
				$query->select("distinct S.section_id, S.text as section_text")
					->from("Section S")
					->innerJoin ("SurveyQuestions SQ on SQ.section_id = S.section_id")
					->where("SQ.survey_id = " . $this->surveyId )
					->order("S.seq");
				$db->setQuery($query);
				$this->sections = $db->loadAssocList();
			}
			else {
			
				$query = $db->getQuery(true);
				
				// Direct sql
				$query->select("distinct S.section_id, ST.text as section_text ")
					->from("Section S")
					->innerJoin ("SurveyQuestions SQ on SQ.section_id = S.section_id")
					->innerJoin ( "SectionTrn ST on S.section_id = ST.section_id")
					->where("SQ.survey_id = " . $this->surveyId )
					->where("ST.lang_tag = " . $db->quote($this->languageTag) )
					->order("S.seq");
				$db->setQuery($query);
				$this->sections = $db->loadAssocList();
			}
			
		}
		return $this->sections;
	}
	
	public function getResponseOptions() {
		if ( $this->responseOptions == null ) {
			
			$responseTrns = BiodivSurvey::getResponseTranslations();
			$err_str = print_r ( $responseTrns, true );
			error_log ( "Response translations: " . $err_str );
			
			$db = JDatabase::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
				
			// Direct sql
			$query->select("distinct SQ.sq_id, SO.response_id")
				->from("SurveyOptions SO")
				->innerJoin ("SurveyQuestions SQ on SQ.sq_id = SO.sq_id")
				->innerJoin ("Options O on SO.response_id = O.option_id")
				->where("SQ.survey_id = " . $this->surveyId )
				->order("O.seq");
			$db->setQuery($query);
			$options = $db->loadAssocList();
			
			$this->responseOptions = array();
			foreach ($options as $option ) {
				$sq_id = $option['sq_id'];
				$response_id = $option['response_id'];
				$this->responseOptions[$sq_id][$response_id] = $responseTrns[$response_id] ;
			}
			
			$err_str = print_r ( $this->responseOptions, true );
			error_log ( "Response options: " . $err_str );
			
		}
		return $this->responseOptions;
	}
	
	public function getQuestions ( $sectionId = null ) {
		
		if ( $this->questions == null ) {
			
			$allQuestions = null;
		
			$db = JDatabase::getInstance(dbOptions());
			
			// Check language
			if ( $this->languageTag == "en-GB" ) {
				$query = $db->getQuery(true);
				
				// Direct sql
				$query->select("SQ.sq_id, SQ.section_id, SQ.question_id, Q.text, SQ.response_type")
					->from("Question Q")
					->innerJoin ("SurveyQuestions SQ on SQ.question_id = Q.question_id")
					->where("SQ.survey_id = " . $this->surveyId )
					->order("SQ.section_id, SQ.question_seq");
				$db->setQuery($query);
				$allQuestions = $db->loadAssocList();
			}
			else {
			
				$query = $db->getQuery(true);
				
				// Direct sql
				$query->select("SQ.sq_id, SQ.section_id, SQ.question_id, QT.text, SQ.response_type")
					->from("Question Q")
					->innerJoin ("SurveyQuestions SQ on SQ.question_id = Q.question_id")
					->innerJoin ( "QuestionTrn QT on Q.question_id = QT.question_id")
					->where("SQ.survey_id = " . $this->surveyId )
					->where("QT.lang_tag = " . $db->quote($this->languageTag) )
					->order("SQ.question_id, SQ.question_seq");
					
				$db->setQuery($query);
				$allQuestions = $db->loadAssocList();
			}
			
			$sections = $this->getSections();
			foreach ( $sections as $section ) {
				$this->questions[$section['section_id']] = array();
			}
			
			$responseOptions = $this->getResponseOptions();
			
			
			foreach ( $allQuestions as $question ) {
				$qSection = $question['section_id'];
				$qSqId = $question['sq_id'];
				
				// Add response options if relevant
				if ( $question['response_type'] == self::OPTION ) {
					$question['options'] = $responseOptions[$qSqId];
				}
				$this->questions[$qSection][] = $question;
				
			}
			
			$err_str = print_r ( $this->questions, true );
			error_log ( "Survey questions by section:" . $err_str );
			
		}
		
		return $this->questions;
	}
}

?>