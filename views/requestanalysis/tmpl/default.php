<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

	print "<h1>Analysis view called</h1>";
	
	if ( $this->test && $this->type!='ruleofthumb' ) {		

		//error_log ("Non rule of thumb test requested" );
		
		$errorFound = false;
		
		try {
			$analysis = new BiodivAnalysis();
			$response = $analysis->testAnalysis ();
			
			// If response ok, update queue to say photos were sent.
			if ( $response === false ) {
				
				$errMsg = "BiodivAnalysis::test request failed, code: " . $analysis->getLastCode() . ", message: " . $analysis->getLastError();
				print ( "<p>" . $errMsg . "</p>" );
				error_log ( "Error calling BiodivAnalysis::test: " . $errMsg );
				
				$errorFound = true;
				
			}
			else {
				
				$msg = $analysis->getLastMessage();
				print ( "<p>" . $msg . "</p>" );
				
				print ( "<p> response: " . $response . "</p>" );
				
				
			}
			
		}
		catch(Exception $e) {
			
			$errMsg = $e->getMessage();
			print ( "<p>" . $errMsg . "</p>" );
			error_log ( "Error calling BiodivAnalysis::test: " . $errMsg );
			
		}
		
		if ( $errorFound ) {
			print ( "<p>Error found when testing analysis API</p>" );
		}
	}
	else if ( $this->test && $this->type == 'ruleofthumb' ) {		
		
		//error_log ("Rule of thumb test requested" );

		$errorFound = false;
		
		try {
			$analysis = new BiodivAnalysis();
			$response = $analysis->testRuleOfThumb ();
			
			// If response ok, update queue to say photos were sent.
			if ( $response === false ) {
				
				$errMsg = "BiodivAnalysis::test request failed, code: " . $analysis->getLastCode() . ", message: " . $analysis->getLastError();
				print ( "<p>" . $errMsg . "</p>" );
				error_log ( "Error calling BiodivAnalysis::testRuleOfThumb: " . $errMsg );
				
				$errorFound = true;
				
			}
			else {
				
				$msg = $analysis->getLastMessage();
				print ( "<p>" . $msg . "</p>" );
				
				print ( "<p> response: " . $response . "</p>" );
				
				
			}
			
		}
		catch(Exception $e) {
			
			$errMsg = $e->getMessage();
			print ( "<p>" . $errMsg . "</p>" );
			error_log ( "Error calling BiodivAnalysis::testRuleOfThumb: " . $errMsg );
			
		}
		
		if ( $errorFound ) {
			print ( "<p>Error found when testing analysis API</p>" );
		}
	}
	else if ( $this->type == 'ruleofthumb' ) {		
		
		//error_log ("Rule of thumb requested - this is not a test" );

		$analysis = new BiodivAnalysis();
		
		$sentSequences = array();
		
		foreach ( $this->sequences as $sequence ) {
			
			$errorFound = false;
			$sequenceId = intval($sequence->sequence_id);
			if ( !in_array($sequenceId, $sentSequences) ) {
				
				$sentSequences[] = $sequenceId;
				
				// error_log ( "Got sequence id " . $sequenceId );
				
				// $errMsg = print_r ( $sentSequences, true );
				// error_log ("sent sequences: " . $errMsg );
				
				$aiType = $sequence->origin;
				$aiVersion = $sequence->model;
				// $humanSpecies = explode('|', $sequence->human_species);
				// $aiSpecies = explode('|', $sequence->ai_species);
				
				
				$humanSpecies = json_decode($sequence->human_species); // already json
				$aiSpecies = json_decode($sequence->ai_species); // already json
				
				try {
					
					$response = $analysis->ruleOfThumb ( $aiType, 
															$aiVersion, 
															$sequenceId,
															$humanSpecies,
															$aiSpecies );
					
					// If response ok, update queue to say photos were sent.
					if ( $response === false ) {
						
						$errMsg = "BiodivAnalysis::ruleOfThumb request failed, code: " . $analysis->getLastCode() . ", message: " . $analysis->getLastError();
						print ( "<p>" . $errMsg . "</p>" );
						error_log ( "Error calling BiodivAnalysis::ruleOfThumb: " . $errMsg );
						
						$errorFound = true;
						
					}
					else {
						
						$msg = $analysis->getLastMessage();
						print ( "<p>" . $msg . "</p>" );
						print ( "<p> response: " . $response . "</p>" );
						
					}
					
				}
				catch(Exception $e) {
					
					$errMsg = $e->getMessage();
					print ( "<p>" . $errMsg . "</p>" );
					error_log ( "Error calling BiodivAnalysis::ruleOfThumb: " . $errMsg );
					
				}
				if ( $errorFound ) {
					print ( "<p>Error found when calling rule of thumb API for sequence " . $sequenceId . "</p>" );
				}
			}
		}
	}
				
	
	print '<h1>Request to Analysis API complete</h1>';




?>

