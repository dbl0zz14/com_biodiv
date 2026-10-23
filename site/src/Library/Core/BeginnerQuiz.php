<?php

namespace MammalWeb\Component\Biodiv\Site\Library\Core;

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

		$this->topicId = $topicId;
		
		$this->numSequences = $numSequences;
		
		$this->sequenceIds = Training::getTrainingSequences ( $this->topicId, $numSequences );
		
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
		
				$this->sequences[] = Training::getTrainingSequence ( $seqId, $this->topicId );
				
			}
		
		}
		
		//$errStr = print_r ( $this->sequences, true );
		//error_log ( "GetSequences, sequence details: " . $errStr );
		
		return $this->sequences;
	}
	
	// Get the sequence details including the correct species for all sequences
	public function getSequence ( $seqNum ) {
		
		$seqs = $this->getSequences();
		
		if ( $seqNum < count($seqs) ) {
		
			return $seqs[$seqNum];
		}
		else {
			
			return null;
		}
	}
	
	// Generate the requested number of incorrect species options, taken from the species list for this topic
	public function getIncorrectSpecies( $seqNum, $numSpecies ) {
		
		$seq = $this->getSequence($seqNum);
		
		$correctSpecies = $seq->getPrimarySpecies();
		
		$correctId = $correctSpecies[0]->id;
		
		$incorrect = array_diff ( $this->speciesList, array($correctId) );
		
		shuffle($incorrect);
		
		// NB assume here that the species list is longer than the number of species required
		return array_slice ( $incorrect, 0, $numSpecies );
		
	}
	
	
	private function setSpecies () {
		
		$topicFilters = Training::getTopicFilters ( $this->topicId );
		
		$filterIds = array_keys($topicFilters);
		
		$this->speciesList = array();
		
		foreach ( $filterIds as $listId ) {
			
			$species = Biodiv::getSpecies ( $listId, null );
			
			if ( array_key_exists ( 'mammal', $species ) ) {
				$mammalIds = array_keys($species['mammal']);
				$this->speciesList = array_merge ( $this->speciesList, $mammalIds);
			}
			
			if ( array_key_exists ( 'bird', $species ) ) {
				$birdIds = array_keys($species['bird']);
				$this->speciesList = array_merge ( $this->speciesList, $birdIds);
			}
			
		}
	}
	
}



?>

