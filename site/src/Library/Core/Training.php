<?php

namespace MammalWeb\Component\Biodiv\Site\Library\Core;


// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\HTML\HTMLHelper;


// Class to hold generic site stuff including generating creation wizard
class Training {
	
	
	private $score;
	private $scorePercent;
	private $marks;
	private $correctPrimary;
	private $wrong;
	private $seqJson;
	private $animalsJson;
	private $written;
	private $sequences;
	private $topic_id;
	private $topicName;
	private $detail;
	private $classifications;
	private $primaryCorrect;
	private $totalSpecies;
	
	
	function __construct()
	{
		$this->written = false;
		$this->score = 0;
	}
	
	
	public static function getTrainingTopics () {
		
		$features = array();
		$features['restriction'] = "seq > 0";
		$topics = codes_getList( 'topictran', $features );
		return $topics;
	}
	
	
	public static function getTrainingTopicIds () {
		
		$topics = self::getTrainingTopics ();
		$topicIds = array_column($topics, 0);
		return $topicIds;
	}
	

	// Get project specific filters for the topic given
	public static function getTopicFilters ( $topicId ) {
		
		$lang = langTag();
		
		$topicFilters = array();
		
		// First get the English 
		if ( $topicId ) {
			// Find filter for this topic
			$db = db();
			$query = $db->getQuery(true);
			$query->select("O.option_id, O.option_name")->from("Options O")
				->innerJoin("OptionData OD on OD.value = O.option_id and OD.data_type = 'topicfilter'")
				->where("OD.option_id = " . $topicId )
				->order("O.seq");
			$db->setQuery($query);
			$topicFilters = $db->loadRowList();
		}
		
		// if we are not in English, use the translated filter names if they exist
		if ( $lang != "en-GB" ) {
			for ( $i = 0; $i < count($topicFilters); $i++ ) {
				list($id, $name) = $topicFilters[$i];
				$translatedName = codes_getName($id, 'projectfiltertran');
				$topicFilters[$i][1] = $translatedName;
			}
		}
		//print ( "<br> projectFilters: <br>" );
		//print_r ( $projectFilters );
			
		$returnFilters = array();
		
		foreach ($topicFilters as $filter) {
			$returnFilters[$filter[0]] = array('label'=>$filter[1]);
		}
		
		return $returnFilters;
	}


	public static function getTrainingSequences( $topic_id, $max_number=10 ) {
		
		$seq_ids = array();
		
		if ( $topic_id ) {
			
			$db = db();
						
			// Get a list of distinct species for this topic so we can ensure we have a range 
			// Check they have a primary species otherwise leave out
			$query = $db->getQuery(true);
			$query->select("ES.species_id, COUNT(*) as num_seqs, GROUP_CONCAT(ES.sequence_id order by rand()) as seqs")->from("ExpertSequences ES")
				->innerJoin("OptionData OD on OD.value = ES.es_id")
				->where("OD.option_id = " . $topic_id . " and OD.data_type = 'expertsequence'" )
				->where("ES.is_primary = 1" )
				->group("species_id");
			$db->setQuery($query);
			$sequences_by_species = $db->loadAssocList('species_id');
			
			//$err_str = print_r($sequences_by_species, true);
			//error_log("sequences by species: " . $err_str);

			$used_ids = array();
			
			$num_seqs = 0;
			foreach ( $sequences_by_species as $sp_id=>$sp ) {
				
				$sp_count = $sp['num_seqs'];
				
				// At this point make sure each sequence_id only appears once.  If there is more than one species in a sequence it will be on more than one row
				$ids = explode ( ',', $sp['seqs'] );
				
				$already_used = array_intersect($used_ids, $ids);
				
				if ( count($already_used) > 0 ) {
					//error_log ("Got already used sequence(s) for species " . $sp_id );
					foreach ( $already_used as $used_id ) {
						$id_key = array_search($used_id, $ids);
						unset($ids[$id_key]);
						$sp_count -= 1;
					}
				}
				
				// If no sequences left, remove this species from the list.
				if ( $sp_count <= 0 ) {
					//error_log ("Got no sequences left for species " . $sp_id );
					unset($sequences_by_species[$sp_id]);
				}
				else {
					// Only change if necessary
					if ( $sp_count != $sp['num_seqs'] ) {
						//error_log ("Removed sequence(s) so resetting list for species " . $sp_id );
					
						$sp['num_seqs'] = $sp_count;
						$sp['seqs'] = implode ( ',', $ids );
					}
					$used_ids = array_merge ( $used_ids, $ids );
					$num_seqs += $sp_count;
				}
				
			}
			
			//$err_str = print_r($sequences_by_species, true);
			//error_log("sequences by species: " . $err_str);

			// Need at least one species and at least max_number of sequences
			$num_species = count($sequences_by_species);
			if ( $num_species > 0 && $num_seqs >= $max_number ) {
			
				$species = array_keys ( $sequences_by_species );
				
				shuffle( $species );
				
				//$err_str = print_r($species, true);
				//error_log("species: " . $err_str);
				
				$species_to_use = null;
				
				if ( $num_species >= $max_number ) {
					$species_to_use = array_slice ( $species, 0, $max_number );
				}
				else {
					$species_to_use = array_merge ( $species  );
					
					// Now we need to pad the array with repeating species.
					// However we have to make sure there are enough sequences in the padding species.
					while ( count($species_to_use) < $max_number ) {
						$try = array_rand ( $species );
						$try_key = $species[$try];
						
						$num_already = array_count_values ( $species_to_use )[$try_key];
						
						$num_seqs_for_species = $sequences_by_species[$try_key]['num_seqs'];
						
						if ( $num_already < $num_seqs_for_species ) {
							$species_to_use[] = $try_key;
						}
					}
				}
				
				// Set up an assoc array with species and sequence count required
				$species_with_count = array_count_values($species_to_use);

				
				foreach ( $species_with_count as $species_id=>$seq_count ) {
					$seqs = $sequences_by_species[$species_id];
					$ids = explode ( ',', $seqs['seqs'] );
					
					// randomise the ids
					shuffle($ids);
					
					// and take the first one(s)
					$seq_ids = array_merge ( $seq_ids, array_slice($ids, 0, $seq_count) );
					
				}
			}
		}
		
		shuffle($seq_ids);
		
		return $seq_ids;
	}


	public static function getTrainingSequence( $sequence_id, $topic_id = null ) {
		
		$db = db();
		
		$seq = new Sequence ( $sequence_id );
		
		$esObjects = null;
		
		// If no topic, then get for all topics
		if ( !$topic_id ) {
			$query = $db->getQuery(true);
			$query->select("distinct ES.species_id as id, ES.gender, ES.age, sum(ES.number) as number, is_primary")->from("ExpertSequences ES")
				->where("ES.sequence_id = ".$sequence_id )
				->group("ES.sequence_id, ES.species_id, ES.gender, ES.age, ES.is_primary");
			$db->setQuery($query);
			$esObjects = $db->loadObjectList();
		}
		else {
			$query = $db->getQuery(true);
			$query->select("ES.species_id as id, ES.gender, ES.age, sum(ES.number) as number, is_primary")->from("ExpertSequences ES")
				->innerJoin("OptionData OD on OD.value = ES.es_id")
				->where("OD.option_id = " . $topic_id . " and OD.data_type = 'expertsequence'" )
				->where("ES.sequence_id = ".$sequence_id )
				->group("ES.sequence_id, ES.species_id, ES.gender, ES.age, ES.is_primary");
			$db->setQuery($query);
			$esObjects = $db->loadObjectList();
		}
		
		foreach ( $esObjects as $es ) {
			if ( $es->is_primary == 1 ) {
				$seq->addPrimarySpecies ( $es );
			}
			else {
				$seq->addSecondarySpecies ( $es );
			}
		
		}
			
		return $seq;
	}

	
	public static function getBeginnerQuiz ( $projectId ) {
		
		$topicId = self::getTopic ( $projectId, 'beginner' );
		
		$quiz = new BeginnerQuiz($topicId, 5);
		
		return $quiz;
		
	}
	
	
	public static function getTopic ( $projectId, $level ) {
		
		$db = db();
				
		// Get the topic for this quiz level
		$query = $db->getQuery(true)
				->select("OD.option_id")
				->from("OptionData OD")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'topiclevel' and OD.value = '" . $level . "'" )
				->where("PO.project_id = " . $projectId);
		$db->setQuery($query); 
		
		$topicId = $db->loadResult();
		
		return $topicId;
	}
	
	
	// Return the expertise details for a single user id as a list of objects
	public static function userExpertise( $personId ) {
		
		$db = db();

		// Use language directly to get translated option names for speed.
		$query = $db->getQuery(true);
		$query->select("O.option_id as topic_id, O.option_name as topic, UE.score as level")->from("UserExpertise UE")
			->innerJoin("Options O on O.option_id = UE.topic_id")
			->where("person_id = " . $db->quote($personId) );
		$db->setQuery($query);
		$userDetails = $db->loadAssocList();
		
		if ( $userDetails == null ) return array();

		return $userDetails;
	}

    
	public static function addChallenge ( $sequenceId, $expertSpecies,
									$userSpecies, $notes ) {
																		
					
		$fields = new \StdClass();
		$fields->person_id = Biodiv::userID();
		
		// Only do the work if user is logged in
		if ( $fields->person_id ) {
			
			$db = db();
					
			$fields->sequence_id = $sequenceId;
			$fields->expert_species = $expertSpecies;
			$fields->user_species = $userSpecies;
			$fields->notes = $notes;
			
			$success = $db->insertObject("Challenge", $fields);
			
			if(!$success){
				error_log ( "Challenge insert failed" );
			}
		}
	}
	
	
	public static function writeTestResults ( $topicId, $sequencesJson, $answersJson, $scorePercent ) {
	
		//error_log ( "writeTestResults ( " . $topicId . ", " . $sequencesJson . ", " . $answersJson . ", " . $scorePercent . ")" );
		
		$personId = Biodiv::userID();
		
		// Write the results to the database
		$db = db();
		$fields = new \StdClass();
		$fields->person_id = $personId;
		$fields->topic_id = $topicId;
		$fields->sequences = $sequencesJson;
		$fields->answers = $answersJson;
		$fields->score = $scorePercent;
		$success = $db->insertObject("UserTest", $fields);
		$userTestId = $db->insertid();
		if(!$success){
			error_log ( "UserTest insert failed" );
		}
		else {
			// Calculate the moving average and write it to the database
			$query = $db->getQuery(true);
			$query->select("sequences, score")->from("UserTest UE")
				->where("person_id = " . $db->quote($personId) )
				->where("topic_id = " . $topicId)
				->order("timestamp DESC")
				->setLimit("3");
			$db->setQuery($query);
			$rows = $db->loadAssocList();
			
			$scores = array_column($rows, "score");
			
			$seqs = array_column($rows, "sequences");
			
			$num_seqs = 0;
			foreach ( $seqs as $s ) {
				$s_list = json_decode($s);
				$num_seqs += count($s_list);
			}
			
			$moving_avg = array_sum($scores) / count($scores);
			
			// Is there already an average row for this topic? If so, update it, otherwise insert.
			$query = $db->getQuery(true);
			$query->select("ue_id")->from("UserExpertise UE")
				->where("person_id = " . $db->quote($personId) )
				->where("topic_id = " . $topicId);
			$db->setQuery($query);
			$ue_id = $db->loadResult();
			
			if ( $ue_id ) {
				//error_log("expertise exists - updating ue_id " . $ue_id);
				$avfields = new \StdClass();
				$avfields->ue_id = $ue_id;
				$avfields->num_sequences = $num_seqs;
				$avfields->score = $moving_avg;
				$avfields->timestamp = date("Y-m-d H:i:s");
				$success = $db->updateObject("UserExpertise", $avfields, "ue_id");
				if(!$success){
					error_log ( "UserExpertise update failed" );
				}
			}
			else {
				//error_log("enew expertise - inserting");
				$avfields = new \StdClass();
				$avfields->person_id = $personId;
				$avfields->topic_id = $topicId;
				$avfields->num_sequences = $num_seqs;
				$avfields->score = $moving_avg;
				$success = $db->insertObject("UserExpertise", $avfields);
				if(!$success){
					error_log ( "UserExpertise insert failed" );
				}
			}
		}
		
		return $userTestId;
		
	}
	
	
	// This function just compares the distinct species ids in the classifications.
	private function compareSpecies ( $correctSet, $userSet ) {
	  
		$correctIds = array_unique (array_map ( function($x) { return $x->id; } , $correctSet ) );
		$userIds = array_unique (array_map ( function($x) { return $x->id; } , $userSet ) );
		  
		// Compare the sets of species.
		sort($correctIds);
		sort($userIds);

		if ( json_encode($userIds) === json_encode($correctIds) ) {
			return true;
		}
		else {
			return false;
		}
	}

	// Count the correct species and return that number
	private function countCorrectSpecies ( $correctSet, $userSet ) {
	  
		$correctIds = array_unique (array_map ( function($x) { return $x->id; } , $correctSet ) );
		$userIds = array_unique (array_map ( function($x) { return $x->id; } , $userSet ) );
		  
		// Compare the sets of species.
		$userCorrect = array_intersect ( $correctIds, $userIds );

		return count($userCorrect);

	}

	// This function compares the detail in the classifications, ie gender, age and number.
	private function compareSpeciesDetail ( $correctSet, $userSet ) {
		// Copy each set.
		$correctCopy = json_decode(json_encode($correctSet));
		$userCopy = json_decode(json_encode($userSet));

		foreach ( $correctCopy as $ca ) $ca->found = false;
		foreach ( $userCopy as $ua ) $ua->found = false;

		// Reduce both sets each time find a user match.
		foreach ( $correctCopy as $ca ) {
			foreach ( $$userCopy as $ua ) {
			  
				// Top line here checks age and gender are correct
				if ( !$ca->found && !$ua->found && $ua->id == $ca->id && $ua->age == $ca->age && $ua->gender == $ca->gender ) {
					//print("<br>Key matches");
					// Does the number match.
					$diff = $ua->number - $ca->number;
					if ( $diff == 0 ) {
						$ua->found = true;
						$ca->found = true;
					}
					else if ( $diff < 0 ) {
						$ca->number = $ca->number - $ua->number;
						$ua->found = true;
					}
					else if ( $diff > 0 ) {
						$ua->number = $ua->number - $ca->number;
						$ca->found = true;
					}
				}
			}	  
		}

		// If anything is not found they don't match.
		foreach ( $correctCopy as $ca ) {
			if ( !$ca->found ) return false;
		}
		foreach ( $userCopy as $ua ) {
			if ( !$ua->found ) return false;
		}

		return true;
	}
	
	
	public function calculateScores () {
		
		$app = Factory::getApplication();
		
		$this->topic_id = 
			(int)$app->getUserStateFromRequest('com_biodiv.topic_id', 'topic_id', 0);
			
		$this->topicName = codes_getName($this->topic_id, 'topictran');
		
		$this->detail = 
			(int)$app->getUserStateFromRequest('com_biodiv.detail', 'detail', 0);
		
		// Get a set of sequences and correct answers for this topic.
		$this->seqJson = $app->getUserStateFromRequest('com_biodiv.sequences', 'sequences', 0);
		
		$sequence_ids = json_decode($this->seqJson);
		
		$this->sequences = array();
		foreach ( $sequence_ids as $sequence_id ) {
			$this->sequences[] = self::getTrainingSequence($sequence_id, $this->topic_id);
		}
			
		$this->animalsJson = $app->getUserStateFromRequest('com_biodiv.animals', 'animals', 0);
		
		$this->classifications = json_decode($this->animalsJson);
		
		// Calculate the score
		$this->score = 0;
		$this->marks = array();
		$this->primaryCorrect = array();
		$this->wrong = array();
		$num_sequences = count($this->sequences);
		$this->totalSpecies = 0;
		for ( $i = 0; $i < $num_sequences; $i++ ) {
			
			$seq = $this->sequences[$i];
			
			//NB should be using biodiv.php function calculateTestScore for each sequence...
			
			$correctPrimary = $seq->getPrimarySpecies();
			$correctSecondary = $seq->getSecondarySpecies();
			$numExpertPrimary = count($correctPrimary);
			$numExpertSecondary = count($correctSecondary);
			$this->totalSpecies += $numExpertPrimary;
			$numExpertSpecies = $numExpertPrimary + $numExpertSecondary;
			
			if ( count($this->classifications) > $i ) {
				$useranimals = $this->classifications[$i];
				
				if ( $this->detail == 1 ) {
					// NB not tested as never used
					if ( $this->compareSpeciesDetail ( $correct, $useranimals ) ) {
						$this->score += 1;
						$this->marks[] = 1;
					}
					else {
						$this->marks[] = 0;
					}
				}
				else {
					// Note need to keep hold of all the detail so do here instead of calling function
					//$numCorrect = $this->countCorrectSpecies ( $correct, $useranimals );
					//$this->score += $numCorrect;
					
					$correctPrimaryIds = array_unique (array_map ( function($x) { return $x->id; } , $correctPrimary ) );
					$correctSecondaryIds = array_unique (array_map ( function($x) { return $x->id; } , $correctSecondary ) );
					
					$userIds = array_unique (array_map ( function($x) { return $x->id; } , $useranimals ) );
					  
					// Compare the sets of species.
					$userCorrectPrimary = array_intersect ( $userIds, $correctPrimaryIds );
					
					$numCorrectPrimary = count($userCorrectPrimary);
					
					$userSecondary = array_diff ( $userIds, $userCorrectPrimary );
					
					$userCorrectSecondary = array_intersect ( $correctSecondaryIds, $userSecondary );
					$numCorrectSecondary = count($userCorrectSecondary);
					
					
					$userRemaining = array_diff ( $userSecondary, $userCorrectSecondary );
					
					$numWrong = count($userRemaining);
					
					$this->score += $numCorrectPrimary;
					
					// Only add secondary to total if the user has got it
					if ( $numCorrectSecondary > 0 ) {
						$this->score += $numCorrectSecondary;
						$this->totalSpecies += $numCorrectSecondary;
					}
					
					$this->wrong[] = $numWrong;
					
					
					$marks = $numCorrectPrimary + $numCorrectSecondary;
					$this->marks[] = $marks;
					$this->correctPrimary[] = $numCorrectPrimary;
					
					
					$numUserAnimals = count($useranimals);
					
					$extraAnimals = $numUserAnimals - $numExpertSpecies;
					
					if ( $extraAnimals > 0 ) {
						// Increase denominator if user has added extra species.
						$this->totalSpecies += $extraAnimals;
					}
				}
			}
			else {
				$this->marks[] = 0;
				$this->correctPrimary[] = 0;
				$this->wrong[] = 0;
			}
		}
		
		$this->scorePercent = 100*$this->score/$this->totalSpecies;
		
		
	}
	
	
	public function writeScores () {
		
		// Check there are sequences - if not we have already written
		$app = Factory::getApplication();
		
		$written = $app->getUserState('com_biodiv.written');
		if ( !$this->written ) {
			
			// Write the results to the database
			$this->writeTestResults ( $this->topic_id, $this->seqJson, $this->animalsJson, $this->scorePercent );
			
			// Ensure we don't rewrite this training session
			$app->setUserState('com_biodiv.written', '1');
			
		}
	}
	
	
	public function generateTrainingResults( $inPage = false ) {
		
		$num_sequences = count($this->sequences);

		print "<h4 class='modal-title' id='resultsTitle'>".$this->topicName . " - " . Text::_("COM_BIODIV_TRAINING_YOU_SCORED")." " . $this->score . "/" . $this->totalSpecies . " </h4>";
		print "<p></p>";
		
		print "<div class='row row-cols-1 row-cols-md-3 g-4'>";	
		
		$smile = "<span class='fa fa-smile-o fa-lg text-danger'></span>";
		$frown = "<span class='fa fa-frown-o fa-lg text-danger'></span>";
		$meh = "<span class='fa fa-meh-o fa-lg text-danger'></span>";
			
		for ( $i = 0; $i < $num_sequences; $i++ ) {
			$seq = $this->sequences[$i];
			$mediafile = array_values($seq->getMediaFiles())[0];
			$correctPrimary = $seq->getPrimarySpecies();
			$correctSecondary = $seq->getSecondarySpecies();
			
			$useranimals = array();
			if ( count($this->classifications) > $i) {
				$useranimals = $this->classifications[$i];
			}
					
			$yn = $frown;
			$isCorrect = $this->marks[$i] >= 1;
			if ( $isCorrect ) {
				$yn = $meh;
			}
			
			$allCorrect = ($this->wrong[$i] == 0) && ($this->correctPrimary[$i] >= count($correctPrimary));
			if ( $allCorrect ) {
				$yn = $smile;
			}
			
			$correct_names = array();
			$correct_names_sec = array();
			
			foreach ($correctPrimary as $animal) {
				$animal_id = $animal->id;
				$animal_name = codes_getOptionTranslation($animal_id);
				if ( $this->detail && $animal_id != 86 && $animal_id != 87 ) {
					$animal_name .= " (" . $animal->number;
					$animal_age = $animal->age;
					if ( $animal_age != 85 ) $animal_name .= " " . codes_getOptionTranslation($animal_age);
					
					$animal_gender = $animal->gender;
					if ( $animal_gender != 84 ) $animal_name .= " " . codes_getOptionTranslation($animal_gender);
					$animal_name .= ")";
				}
				$correct_names[] = $animal_name;
			}
			foreach ($correctSecondary as $animal) {
				$animal_id = $animal->id;
				$animal_name = codes_getOptionTranslation($animal_id);
				if ( $this->detail && $animal_id != 86 && $animal_id != 87 ) {
					$animal_name .= " (" . $animal->number;
					$animal_age = $animal->age;
					if ( $animal_age != 85 ) $animal_name .= " " . codes_getOptionTranslation($animal_age);
					
					$animal_gender = $animal->gender;
					if ( $animal_gender != 84 ) $animal_name .= " " . codes_getOptionTranslation($animal_gender);
					$animal_name .= ")";
				}
				$correct_names_sec[] = $animal_name . " " . Text::_("COM_BIODIV_TRAINING_SEC");
			}
			
			$user_names = array();
			foreach ($useranimals as $animal) {
				$animal_id = $animal->id;
				$user_name = codes_getOptionTranslation($animal_id);
				if ( $this->detail && $animal_id != 86 && $animal_id != 87 ) {
					$user_name .= " (" . $animal->number;
					$animal_age = $animal->age;
					if ( $animal_age != 85 ) $user_name .= " " . codes_getOptionTranslation($animal_age);
					
					$animal_gender = $animal->gender;
					if ( $animal_gender != 84 ) $user_name .= " " . codes_getOptionTranslation($animal_gender);
					$user_name .= ")";
				}
				
				$user_names[] = $user_name;
			}
			
			// Sort the species
			sort($user_names);
			sort($correct_names);
			sort($correct_names_sec);
			
			print "<div class='col'>";
			print "<div class='card h-100'>";
			
			print '<div class="card-body">';
			print "<h4>". $yn . "</h4>";
			if ( $seq->getMedia() == "photo" ) {
				print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
			}
			else if ( $seq->getMedia() == "video" ) {
				print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
			}
			else if ( $seq->getMedia() == "audio" ) {
				print "<button class='media-btn' data-seq_id='".$seq->getId()."'><i class='fa fa-play'></i> " . Text::_("COM_BIODIV_TRAINING_REVIEW") . "<audio src = '" . $mediafile . "' width='100%'></audio></button>";
			}
			
			print "<div class='row mt-2'>";
			
			print "<div class='col-md-6'>";
			
			print "<h4>" . "  " . Text::_("COM_BIODIV_TRAINING_YOU_SEL") . "</h4>";
			print "<p id='user_" . $seq->getId() . "'>" . implode(', <br>', $user_names) . "</p>";
			
			print "</div>"; // col 6
			
			print "<div class='col-md-6'>";
			
			print "<h4>" . "  " . Text::_("COM_BIODIV_TRAINING_EXP_SEL") . "</h4>";
			print "<p id='expert_" . $seq->getId() . "'>" . implode(', <br>', $correct_names) . "</p>";
			print "<p id='expert_sec_" . $seq->getId() . "'>" . implode(', <br>', $correct_names_sec) . "</p>";
			
			print "</div>"; // col 6
			
			print "</div>"; // row
			
			print '</div>'; // col-body
			
			if ( !$allCorrect ) {
				print '<div class="card-footer">';
				print "<button class='btn btn-outline-secondary challenge-btn m-1' id='challengeBtn_" . $seq->getId() . "' data-seq_id='".$seq->getId()."' data-bs-toggle='modal' data-bs-target='#challenge_modal' >" . Text::_("COM_BIODIV_TRAINING_CHALLENGE") . "</button>";
				print "<div id='challengeDone_" . $seq->getId() . "'></div>";
				print "</div>"; // card-footer
			}
			
			print "</div>"; // card
			print "</div>"; // col
			
		}
		print "</div>"; // results row
		
			
		if ( $inPage ) {
			print "  <button class='btn btn-primary btn-lg reloadPage mt-5' >".
						Text::_("COM_BIODIV_TRAINING_TRY_AGAIN")."</button>";
			
		}
		else {
			print '<div class="d-inline-flex mt-5 me-3">';
			print "	      <form action = '".BIODIV_ROOT."' method = 'GET' style='display: inline;'>";
			print "		  <input type='hidden' name='view' value='training'/>";
			print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
			print "		  <button class='btn btn-secondary btn-lg' type='submit' >".
						  Text::_("COM_BIODIV_TRAINING_FINISH")."</button>";
			print "		  </form>";
			print "</div>"; // d-inline-flex
			
			print '<div class="d-inline-flex mt-5 ">';
			print "	      <form action = '".BIODIV_ROOT."' method = 'GET' style='display: inline;'>";
			print "		  <input type='hidden' name='view' value='status'/>";
			print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
			print "		  <button class='btn btn-primary btn-lg' type='submit' >".
						  Text::_("COM_BIODIV_TRAINING_SPOT")."</button>";
			print "		  </form>";
			print "</div>"; // d-inline-flex
		}

	
		print '<div id="carousel_modal" class="modal fade" tabindex="-1" aria-labelledby="carouselModalLabel" >';
		print '  <div class="modal-dialog modal-dialog-sm"  >';
		print '	<div class="modal-content">';
		print '	  <div class="modal-header">';
		print '		<button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '		<h4 class="modal-title" id=" "> '.Text::_("COM_BIODIV_TRAINING_REVIEW").' </h4>';
		print '	  </div>';
		print '	  <div class="modal-body">';
		print '		<div id="media_carousel" ></div>';
		print '	  </div>';
		print '	  <div class="modal-footer">';
		print '		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		print '	  </div>';				  
		print '	</div>';
		print '  </div>';
		print '</div>';


		print '<div id="challenge_modal" class="modal fade" tabindex="-1" aria-labelledby="challengeModalLabel" >';
		print '<div class="modal-dialog modal-dialog-sm">';

		print '<!-- Modal content-->';
		print '<div class="modal-content">';
		print '	  <div class="modal-header">';
		print '		<button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '		<h4 class="modal-title" id="challengeModalLabel">'.Text::_("COM_BIODIV_TRAINING_CHALLENGE").'</h4>';
		print '	  </div>';
		print '	  <div class="modal-body">';
				
		print '		<form id="challengeForm" role="form">';
		
		print HTMLHelper::_('form.token');
				
		print '		<input id="currSequenceId" type="hidden" name="sequence_id" value=""/>';
				
		print '		<div class="form-group m-4">';
		print '		<label for="challenge_expert">'.Text::_("COM_BIODIV_TRAINING_EXPERT").'</label>';
		print '		<textarea class="form-control mt-2" id="challenge_expert" name="expert_species" rows="2" maxlength="200" readonly></textarea>';
		print '		</div>';
				
		print '		<div class="form-group m-4">';
		print '		<label for="challenge_suggestion">'.Text::_("COM_BIODIV_TRAINING_SUGGEST").'</label>';
		print '		<textarea class="form-control mt-2" id="challenge_suggestion" name="user_species" rows="2" maxlength="200" ></textarea>';
		print '		</div>';
				
		print '		<div class="form-group m-4">';
		print '		<label for="challenge_notes">'.Text::_("COM_BIODIV_TRAINING_NOTES").'</label>';
		print '		<textarea class="form-control mt-2" id="challenge_notes" name="notes" rows="2" maxlength="200" ></textarea>';
		print '		</div>';
		
		print '     <hr/>';
		print '     <div class="d-flex justify-content-end">';
		print '     <button type="button" class="btn btn-secondary m-1 mb-3" data-dismiss="modal">'.Text::_("COM_BIODIV_TRAINING_CANCEL").'</button>';
		print '     <button type="button" class="btn btn-primary m-1 mb-3 me-4" id="challenge-save" data-dismiss="modal">'.Text::_("COM_BIODIV_TRAINING_SUBMIT").'</button>';
		print '	    </div>';

		print '		</form>';
		
		print '	  </div>';
		print '</div>';
		print '</div>';
		print '</div>';


		print '<div id="map_modal" class="modal fade" tabindex="-1" aria-labelledby="mapModalLabel" >';
		print '  <div class="modal-dialog modal-sm">';

		print '	<!-- Modal content-->';
		print '	<div class="modal-content">';
		print '	  <div class="modal-header">';
		print '		<button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '		<h4 class="modal-title" id="mapModalLabel"> '.Text::_("COM_BIODIV_TRAINING_REVIEW").' </h4>';
		print '	  </div>';
		print '	  <div class="modal-body">';
		print '		<div id="no_map"><h5> '.Text::_("COM_BIODIV_TRAINING_NO_MAP").' </h5></div>';
		print '		<div id="map_canvas" style="width:500px;height:500px;"></div>';
		print '	  </div>';
		print '	  <div class="modal-footer">';
		print '		<button type="button" class="btn btn-primary" data-dismiss="modal">'.Text::_("COM_BIODIV_TRAINING_CLOSE").'</button>';
		print '	  </div>';
				  
		print '	</div>';

		print '  </div>';
		print '</div>';

	}
	
}

?>

