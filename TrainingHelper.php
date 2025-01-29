<?php

// No direct access to this file
defined('_JEXEC') or die;

// Class to hold generic site stuff including generating creation wizard
class TrainingHelper {
	
	
	private $score;
	private $scorePercent;
	private $marks;
	private $correctPrimary;
	private $wrong;
	private $seqJson;
	private $animalsJson;
	private $written;
	private $sequences;
	private $topicName;
	
	function __construct()
	{
		$this->written = false;
		$this->score = 0;
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
		
		$app = JFactory::getApplication();
		
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
			$this->sequences[] = getTrainingSequence($sequence_id, $this->topic_id);
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
		$app = JFactory::getApplication();
		
		$written = $app->getUserState('com_biodiv.written');
		if ( !$this->written ) {
			
			// Write the results to the database
			writeTestResults ( $this->topic_id, $this->seqJson, $this->animalsJson, $this->scorePercent );
			
			// Ensure we don't rewrite this training session
			$app->setUserState('com_biodiv.written', '1');
			
		}
	}
	
	
	
	
	
	public function generateTrainingResults( $inPage = false ) {
		
		$num_sequences = count($this->sequences);

		print "<h4 class='modal-title' id='resultsTitle'>".$this->topicName . " - " . JText::_("COM_BIODIV_TRAINING_YOU_SCORED")." " . $this->score . "/" . $this->totalSpecies . " </h4>";
		print "<p></p>";
		
		print "<div class='row'>";	
		
		for ( $i = 0; $i < $num_sequences; $i++ ) {
			$seq = $this->sequences[$i];
			$mediafile = array_values($seq->getMediaFiles())[0];
			$correctPrimary = $seq->getPrimarySpecies();
			$correctSecondary = $seq->getSecondarySpecies();
			
			$useranimals = array();
			if ( count($this->classifications) > $i) {
				$useranimals = $this->classifications[$i];
			}
			
			$smile = "<span class='fa fa-smile-o fa-lg text-danger'></span>";
			$frown = "<span class='fa fa-frown-o fa-lg text-danger'></span>";
			$meh = "<span class='fa fa-meh-o fa-lg text-danger'></span>";
			
					
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
				$correct_names_sec[] = $animal_name . " " . JText::_("COM_BIODIV_TRAINING_SEC");
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
			
			print "<div class='col-md-4'>";
			print "<div class='well'>";
			
			
			print "<h4>". $yn . "</h4>";
			if ( $seq->getMedia() == "photo" ) {
				print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
			}
			else if ( $seq->getMedia() == "video" ) {
				print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
			}
			else if ( $seq->getMedia() == "audio" ) {
				print "<button class='media-btn' data-seq_id='".$seq->getId()."'><i class='fa fa-play'></i> " . JText::_("COM_BIODIV_TRAINING_REVIEW") . "<audio src = '" . $mediafile . "' width='100%'></audio></button>";
			}
			
			print "<div class='row'>";
			
			print "<div class='col-md-6'>";
			
			print "<h4>" . "  " . JText::_("COM_BIODIV_TRAINING_YOU_SEL") . "</h4>";
			print "<p id='user_" . $seq->getId() . "'>" . implode(', <br>', $user_names) . "</p>";
			
			print "</div>"; // col 6
			
			print "<div class='col-md-6'>";
			
			print "<h4>" . "  " . JText::_("COM_BIODIV_TRAINING_EXP_SEL") . "</h4>";
			print "<p id='expert_" . $seq->getId() . "'>" . implode(', <br>', $correct_names) . "</p>";
			print "<p id='expert_sec_" . $seq->getId() . "'>" . implode(', <br>', $correct_names_sec) . "</p>";
			
			print "</div>"; // col 6
			
			print "</div>"; // row
			
			if ( !$allCorrect ) {
				print "<hr>";
				print "<button class='btn btn-primary challenge-btn' id='challengeBtn_" . $seq->getId() . "' data-seq_id='".$seq->getId()."'>" . JText::_("COM_BIODIV_TRAINING_CHALLENGE") . "</button>";
				print "<div id='challengeDone_" . $seq->getId() . "'></div>";
			}
			
			print "</div>"; // well
			print "</div>"; // col-3
			
			// Every third sequence start a new row
			if ($i%3 == 2 ) {
				print "<div class='row'>";	
				print "</div>";
			}
			
		}
		print "</div>"; // results row
		
			
		print "<div class='row'>";	// Finish div
		print "<p></p>";
		
		if ( $inPage ) {
			print "<div class='col-md-3'>";
			print "  <button class='btn btn-success btn-lg reloadPage' >".
						JText::_("COM_BIODIV_TRAINING_TRY_AGAIN")."</button>";
			print "</div>"; // col-3
			
		}
		else {
			print "<div class='col-md-3'>";
			print "	      <form action = '".BIODIV_ROOT."' method = 'GET'>";
			print "		  <input type='hidden' name='view' value='training'/>";
			print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
			print "		  <button class='btn btn-success btn-lg' type='submit' >".
						  JText::_("COM_BIODIV_TRAINING_FINISH")."</button>";
			print "		  </form>";
			print "</div>"; // col-3
			
			print "<div class='col-md-3'>";
			print "	      <form action = '".BIODIV_ROOT."' method = 'GET'>";
			print "		  <input type='hidden' name='view' value='status'/>";
			print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
			print "		  <button class='btn btn-success btn-lg' type='submit' >".
						  JText::_("COM_BIODIV_TRAINING_SPOT")."</button>";
			print "		  </form>";
			print "</div>"; // col-3
		}

		print "</div>"; // finish



	
		print '<div id="carousel_modal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog "  style="width: 60%;">';

		print '	<!-- Modal content-->';
		print '	<div class="modal-content">';
		print '	  <div class="modal-header">';
		print '		<button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '		<h4 class="modal-title"> <?php print JText::_("COM_BIODIV_TRAINING_REVIEW"); ?> </h4>';
		print '	  </div>';
		print '	  <div class="modal-body">';
		print '		<div id="media_carousel" ></div>';
		print '	  </div>';
		print '	  <div class="modal-footer">';
		print '		<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
		print '	  </div>';
				  
		print '	</div>';

		print '  </div>';
		print '</div>';

		print '<div id="challenge_modal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog "  style="width: 60%;">';

		print '	<!-- Modal content-->';
		print '	<div class="modal-content">';
		print '	  <div class="modal-header">';
		print '		<button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '		<h4 class="modal-title"> <?php print JText::_("COM_BIODIV_TRAINING_CHALLENGE"); ?> </h4>';
		print '	  </div>';
		print '	  <div class="modal-body">';
				
		print '		<form id="challengeForm" role="form">';
				
		print '		<input id="currSequenceId" type="hidden" name="sequence_id" value=""/>';
				
		print '		<div class="form-group">';
		print '		<label for="challenge_expert">'.JText::_("COM_BIODIV_TRAINING_EXPERT").'</label>';
		print '		<textarea class="form-control" id="challenge_expert" name="expert_species" rows="2" maxlength="200" readonly></textarea>';
		print '		</div>';
				
		print '		<div class="form-group">';
		print '		<label for="challenge_suggestion">'.JText::_("COM_BIODIV_TRAINING_SUGGEST").'</label>';
		print '		<textarea class="form-control" id="challenge_suggestion" name="user_species" rows="2" maxlength="200" ></textarea>';
		print '		</div>';
				
		print '		<div class="form-group">';
		print '		<label for="challenge_notes">'.JText::_("COM_BIODIV_TRAINING_NOTES").'</label>';
		print '		<textarea class="form-control" id="challenge_notes" name="notes" rows="2" maxlength="200" ></textarea>';
		print '		</div>';
				
		print '	  </div>';
		print '	  <div class="modal-footer">';
		print '		<button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_TRAINING_CANCEL").'</button>';
		print '		<button type="button" class="btn btn-success" data-dismiss="modal" id="challenge-save">'.JText::_("COM_BIODIV_TRAINING_SUBMIT").'</button>';
		print '		</form>';
		print '	  </div>';
				  
		print '	</div>';

		print '  </div>';
		print '</div>';

		print '<div id="map_modal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog modal-sm">';

		print '	<!-- Modal content-->';
		print '	<div class="modal-content">';
		print '	  <div class="modal-header">';
		print '		<button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '		<h4 class="modal-title"> '.JText::_("COM_BIODIV_TRAINING_REVIEW").' </h4>';
		print '	  </div>';
		print '	  <div class="modal-body">';
		print '		<div id="no_map"><h5> '.JText::_("COM_BIODIV_TRAINING_NO_MAP").' </h5></div>';
		print '		<div id="map_canvas" style="width:500px;height:500px;"></div>';
		print '	  </div>';
		print '	  <div class="modal-footer">';
		print '		<button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_TRAINING_CLOSE").'</button>';
		print '	  </div>';
				  
		print '	</div>';

		print '  </div>';
		print '</div>';

	}
	
}

?>

