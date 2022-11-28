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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewTrainingResults extends JViewLegacy
{
 
   
  // This function just compares the distinct species ids in the classifications.
  public function compareSpecies ( $correctSet, $userSet ) {
	  
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
  public function countCorrectSpecies ( $correctSet, $userSet ) {
	  
	$correctIds = array_unique (array_map ( function($x) { return $x->id; } , $correctSet ) );
	$userIds = array_unique (array_map ( function($x) { return $x->id; } , $userSet ) );
	  
	// Compare the sets of species.
	$userCorrect = array_intersect ( $correctIds, $userIds );
	
	return count($userCorrect);
	
  }
   
  // This function compares the detail in the classifications, ie gender, age and number.
  public function compareSpeciesDetail ( $correctSet, $userSet ) {
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
  
   /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  public function display($tpl = null) 
  {
    $person_id = (int)userID();
    
	$person_id or die("No person_id");

    $app = JFactory::getApplication();
	
	$this->topic_id = 
	    (int)$app->getUserStateFromRequest('com_biodiv.topic_id', 'topic_id', 0);
		
	$this->topicName = codes_getName($this->topic_id, 'topictran');
	
	$this->detail = 
	    (int)$app->getUserStateFromRequest('com_biodiv.detail', 'detail', 0);
	
	// Get a set of sequences and correct answers for this topic.
	$seq_json = $app->getUserStateFromRequest('com_biodiv.sequences', 'sequences', 0);
	
	
	$sequence_ids = json_decode($seq_json);
	
	$this->sequences = array();
	foreach ( $sequence_ids as $sequence_id ) {
		$this->sequences[] = getTrainingSequence($sequence_id, $this->topic_id);
	}
		
	$ani_json = $app->getUserStateFromRequest('com_biodiv.animals', 'animals', 0);
	
	$this->classifications = json_decode($ani_json);
	
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
				  
				error_log("userIds:");
				$str = print_r($userIds, true);
				error_log($str);
				
				// Compare the sets of species.
				$userCorrectPrimary = array_intersect ( $userIds, $correctPrimaryIds );
				
				error_log("userCorrectPrimary:");
				$str = print_r($userCorrectPrimary, true);
				error_log($str);
				
				$numCorrectPrimary = count($userCorrectPrimary);
				
				$userSecondary = array_diff ( $userIds, $userCorrectPrimary );
				
				error_log("userSecondary:");
				$str = print_r($userSecondary, true);
				error_log($str);
				
				$userCorrectSecondary = array_intersect ( $correctSecondaryIds, $userSecondary );
				$numCorrectSecondary = count($userCorrectSecondary);
				
				error_log("userCorrectSecondary:");
				$str = print_r($userCorrectSecondary, true);
				error_log($str);
				
				$userRemaining = array_diff ( $userSecondary, $userCorrectSecondary );
				
				error_log("userRemaining:");
				$str = print_r($userRemaining, true);
				error_log($str);
				
				$numWrong = count($userRemaining);
				
				$this->score += $numCorrectPrimary;
				
				// Only add secondary to total if the user has got it
				if ( $numCorrectSecondary > 0 ) {
					$this->score += $numCorrectSecondary;
					$this->totalSpecies += $numCorrectSecondary;
				}
				
				$this->wrong[] = $numWrong;
				
				error_log("Num wrong for " . $seq->getId() . " is " . $numWrong);
				
				$marks = $numCorrectPrimary + $numCorrectSecondary;
				$this->marks[] = $marks;
				$this->correctPrimary[] = $numCorrectPrimary;
				
				error_log("Marks for " . $seq->getId() . " is " . $marks);
				
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
	
	$score_percent = 100*$this->score/$this->totalSpecies;
	
	// Check there are sequences - if not we have already written
	
	$written = $app->getUserState('com_biodiv.written');
	if ( !$written ) {
		
		// Write the results to the database
		writeTestResults ( $this->topic_id, $seq_json, $ani_json, $score_percent );
		
		// Ensure we don't rewrite this training session
		$app->setUserState('com_biodiv.written', '1');
		
	}
	
    // Display the view
    parent::display($tpl);
  }
}



?>