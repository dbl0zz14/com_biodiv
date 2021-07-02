<?php

namespace Biodiv;

// No direct access to this file
defined('_JEXEC') or die;

class BeginnerQuiz {
	
	private $topicId;
	private $numSequences;
	private $sequenceIds;
	private $sequences;
	private $speciesList;
	
	
	function __construct( $topicId, $numSequences  )
	{
		error_log ( "Biodiv\BeginnerQuiz constructor called, topic id = " . $topicId . ", numSequences = " . $numSequences );
		
		$this->topicId = $topicId;
		
		$this->numSequences = $numSequences;
		
		$this->sequenceIds = getTrainingSequences ( $this->topicId, $numSequences );
		
		$this->sequences = null;
		
		$this->setSpecies();
		
	}
	
	
	
	public function getSequenceIds() {
		return $this->sequenceIds;
	}
	
	
	// Get the sequence details including the correct species for all sequences
	public function getSequences() {
		
		error_log ("Biodiv\BeginnerQuiz getSequences called");
		
		if ( $this->sequences == null ) {
			
			foreach ( $this->sequenceIds as $seqId ) {
				
				error_log ( "Getting sequence details for seq " . $seqId );
		
				$this->sequences[] = getTrainingSequence ( $seqId, $this->topicId );
				
			}
		
		}
		
		//$errStr = print_r ( $this->sequences, true );
		//error_log ( "GetSequences, sequence details: " . $errStr );
		
		return $this->sequences;
	}
	
	// Get the sequence details including the correct species for all sequences
	public function getSequence ( $seqNum ) {
		
		error_log ("Biodiv\BeginnerQuiz getSequence called, seq num = ". $seqNum);
		
		$seqs = $this->getSequences();
		
		error_log ( "Got " . count($seqs) . " sequences" );
		
		if ( $seqNum < count($seqs) ) {
		
			return $seqs[$seqNum];
		}
		else {
			
			return null;
		}
	}
	
	// Generate the requested number of incorrect species options, taken from the species list for this topic
	public function getIncorrectSpecies( $seqNum, $numSpecies ) {
		
		error_log ("Biodiv\BeginnerQuiz getIncorrectSpecies called");
		
		$seq = $this->getSequence($seqNum);
		
		$errStr  = print_r ( $seq, true );
		error_log ( "seq = " . $errStr );
		
		$correctSpecies = $seq->getPrimarySpecies();
		
		error_log ( "Got correct species" );
		
		$correctId = $correctSpecies[0]->id;
		
		error_log ( "correct id = " . $correctId );
		
		$incorrect = array_diff ( $this->speciesList, array($correctId) );
		
		error_log ( "Got incorrect species" );
		
		shuffle($incorrect);
		
		error_log ( "Shuffled" );
		
		// NB assume here that the species list is longer than the number of species required
		return array_slice ( $incorrect, 0, $numSpecies );
		
	}
	
	
	private function setSpecies () {
		
		$topicFilters = getTopicFilters ( $this->topicId );
		
		$filterIds = array_keys($topicFilters);
		
		$errStr = print_r ( $filterIds, true );
		error_log ( "Biodiv\BeginnerQuiz setSpecies got filters: " . $errStr );
		
		$this->speciesList = array();
		
		foreach ( $filterIds as $listId ) {
			
			$species = getSpecies ( $listId, null );
			
			$mammalIds = array_keys($species['mammal']);
			
			$this->speciesList = array_merge ( $this->speciesList, $mammalIds);
			
			$birdIds = array_keys($species['bird']);
			
			$this->speciesList = array_merge ( $this->speciesList, $birdIds);
			
		}
	}
	
}



?>

