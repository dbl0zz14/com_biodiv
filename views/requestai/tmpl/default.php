<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$allowedExtensions = array('jpg','jpeg','JPG','JPEG');

//$startTime = microtime(true);
//error_log ( "Calls to AI " . $this->aiType . " start time = " . $startTime );


for ( $i=0; $i < $this->repeat; $i++ ) {
	
	print "<h1>Processing AI queue to request classifications, request " . $i . "</h1>";
	
	$photos = null;
	$siteId = null;
	
	if ( array_key_exists($i, $this->photos) ) {
		$photos = $this->photos[$i];
	}
	if ( array_key_exists($i, $this->siteIds) ) {
		$siteId = $this->siteIds[$i];
	}
	
	if ( $photos == null ) {
		print "<h2>No photo list - could be an empty queue or no permission for this</h2>";
	}
	else if ( count($photos) == 0 ) {
		print "<h2>No s3 photos found in queue for AI</h2>";
	}
	else if ( !$siteId ) {
		print "<h2>No site id found in queue for AI</h2>";
	}
	else {
		
		print '<h2>Requesting '.$this->aiType.' classifications for ' . count($photos) . ' files from Photo table </h2>';

		$photoIds = array();
		$urls = array();
		$errorFound = false;
		
		foreach ( $photos as $photoId=>$photoDetails ) {
			
			$newSiteId = $photoDetails["site_id"];
			if ( $newSiteId == $siteId ) {
				
				$filename = $photoDetails['filename'];
				$ext =  JFile::getExt($filename);
				$sequenceNum = $photoDetails['sequence_num'];
				
				if ( !in_array($ext, $allowedExtensions ) ) {
					
					print ( "<br>Cannot add " . $this->aiType . " classification request for photo " . $photoId . ", file extension not allowed: ". $filename );
					$errorFound = true;
					
					// Add error status
					$this->setQueueStatusSingle ( $this->aiType, $photoId, BioDivViewRequestAI::FILETYPE_ERROR, "File extension not allowed (" . $ext . ")" );
				}
				else if ( $sequenceNum > 20 ) {
					
					$msg = "Long sequence (number " . $sequenceNum . " in sequence) so image not sent to ConservationAI";
					// Add long sequence status
					$this->setQueueStatusSingle ( $this->aiType, $photoId, BioDivViewRequestAI::LONG_SEQUENCE, $msg );
				}
				else {
					
					print ( "<br>Adding " . $this->aiType . " classification request for photo " . $photoId );
					
					$photoIds[] = $photoId;
					$url = s3URL ( $photoDetails );
					$urls[$photoId] = $url;
					
				}
				
			}
			else {
				print ( "<br>Skipping photo " . $photo_id . " site_ids don't match, site_id = " . $newSiteId . ", should be " . $siteId );
				$errorFound = true;
				
				// Add error status
				$this->setQueueStatusSingle ( $this->aiType, $photoId, BioDivViewRequestAI::PROCESSING_ERROR );
				
			}
		}
		
		if ( $this->aiType == 'CAI' ) {
			try {
				$cai = new BiodivConservationAI();
				$response = $cai->classify ( $siteId, $urls );
				
				// If response ok, update queue to say photos were sent.
				if ( $response === false ) {
					
					$errMsg = "Classify request failed, code: " . $cai->getLastCode() . ", message: " . $cai->getLastError();
					print ( "<br>" . $errMsg );
					error_log ( "Error calling BiodivConservationAI::classify: " . $errMsg );
					
					// Check for timeouts and requeue
					if (($cai->getLastCode() == 0) && (strpos($errMsg, 'Connection timed out') !== false)) {
						
						$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::TO_SEND, $errMsg );
					}
					else {
					
						$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::SEND_ERROR, $errMsg );
					}
					
				}
				else {
					
					$msg = $cai->getLastMessage();
					print ( "<br>" . $msg );
					
					$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::SEND_SUCCESS, $msg );
					
				}
				
			}
			catch(Exception $e) {
				
				$errMsg = $e->getMessage();
				print ( "<br>" . $errMsg );
				error_log ( "Error calling BiodivConservationAI::classify: " . $errMsg );
				
				// Add error status for all photos
				$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::SEND_ERROR, $errMsg );
			}
			
			if ( $errorFound ) {
				print ( "<br>" . "Error found when processing photos - some photos could not be sent for classification" );
			}
		}
		else if ( $this->aiType == 'MEGA' ) {
			try {
				$mega = new BiodivMegaDetector();
				$response = $mega->classify ( $siteId, $urls );
				
				// If response ok, update queue to say photos were sent.
				if ( $response === false ) {
					
					$errMsg = "Classify request failed, code: " . $mega->getLastCode() . ", message: " . $mega->getLastError();
					print ( "<br>" . $errMsg );
					error_log ( "Error calling BiodivMegaDetector::classify: " . $errMsg );
					
					// Check for timeouts and requeue
					if (($mega->getLastCode() == 0) && (strpos($errMsg, 'Connection timed out') !== false)) {
						
						$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::TO_SEND, $errMsg );
					}
					else {
					
						$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::SEND_ERROR, $errMsg );
					}
					
				}
				else {
					
					$msg = $mega->getLastMessage();
					print ( "<br>" . $msg );
					
					// Handle the individual responses..
					foreach ( $response as $photoId=>$photoResponse ) {
						$this->setQueueStatusSingle ( $this->aiType, 
													$photoId, 
													$photoResponse['status'], 
													$photoResponse['msg'] );
						
					}
				}
				
			}
			catch(Exception $e) {
				
				$errMsg = $e->getMessage();
				print ( "<br>" . $errMsg );
				error_log ( "Error calling BiodivConservationAI::classify: " . $errMsg );
				
				// Add error status for all photos
				$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::SEND_ERROR, $errMsg );
			}
			
			if ( $errorFound ) {
				print ( "<br>" . "Error found when processing photos - some photos could not be sent for classification" );
			}
		}
		else {
			
			print ("<br>AI type is " . $this->aiType );
		}
				
	}
	print '<h1>Request to '.$this->aiType.' complete, request ' . $i . '</h1>';
}

//$timeElapsedSecs = (microtime(true) - $startTime)*1000;
//error_log ( "AI call time elapsed = " . $timeElapsedSecs . " seconds" );



?>

