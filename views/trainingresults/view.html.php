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
	
	$this->detail = 
	    (int)$app->getUserStateFromRequest('com_biodiv.detail', 'detail', 0);
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("training");
	
	// Get a set of sequences and correct answers for this topic.
	$seq_json = $app->getUserStateFromRequest('com_biodiv.sequences', 'sequences', 0);
	$sequence_ids = json_decode($seq_json);
	
	$this->sequences = array();
	foreach ( $sequence_ids as $sequence_id ) {
		$this->sequences[] = getTrainingSequence($sequence_id);
	}
		
	$ani_json = $app->getUserStateFromRequest('com_biodiv.animals', 'animals', 0);
	$this->classifications = json_decode($ani_json);
	
	// Calculate the score
	$this->score = 0;
	$this->marks = array();
	$num_sequences = count($this->sequences);
	for ( $i = 0; $i < $num_sequences; $i++ ) {
		
		$seq = $this->sequences[$i];
		$correct = $seq->getSpecies();
		
		if ( count($this->classifications) > $i ) {
			$useranimals = $this->classifications[$i];
			
			if ( $this->detail == 1 ) {
				if ( $this->compareSpeciesDetail ( $correct, $useranimals ) ) {
					$this->score += 1;
					$this->marks[] = 1;
				}
				else {
					$this->marks[] = 0;
				}
			}
			else {
				if ( $this->compareSpecies ( $correct, $useranimals ) ) {
					$this->score += 1;
					$this->marks[] = 1;
				}
				else {
					$this->marks[] = 0;
				}
			}
		}
		else {
			$this->marks[] = 0;
		}
	}
	
	$score_percent = 100*$this->score/$num_sequences;
	
	// Check there are sequences - if not we have already written
	
	$written = $app->getUserState('com_biodiv.written');
	if ( !$written ) {
		// Write the results to the database
		$db = JDatabase::getInstance(dbOptions());
		$fields = new StdClass();
		$fields->person_id = $person_id;
		$fields->topic_id = $this->topic_id;
		$fields->sequences = $seq_json;
		$fields->answers = $ani_json;
		$fields->score = $score_percent;
		$success = $db->insertObject("UserExpertise", $fields);
		if(!$success){
			error_log ( "UserExpertise insert failed" );
		}
		
		// Remove the sequences so we don't rewrite this training session
		$app->setUserState('com_biodiv.written', '1');
		
	}
	
    // Display the view
    parent::display($tpl);
  }
}



?>