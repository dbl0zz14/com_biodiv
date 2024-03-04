<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$allowedExtensions = array('jpg','jpeg','JPG','JPEG');

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
		
		print '<h2>Requesting Conservation AI classifications for ' . count($photos) . ' files from Photo table </h2>';

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
					
					print ( "<br>Cannot add Conservation AI classification request for photo " . $photoId . ", file extension not allowed: ". $filename );
					$errorFound = true;
					
					// Add error status
					$this->setQueueStatusSingle ( $photoId, BioDivViewRequestAI::FILETYPE_ERROR, "File extension not allowed (" . $ext . ")" );
				}
				else if ( $sequenceNum > 20 ) {
					
					$msg = "Long sequence (number " . $sequenceNum . " in sequence) so image not sent to ConservationAI";
					// Add long sequence status
					$this->setQueueStatusSingle ( $photoId, BioDivViewRequestAI::LONG_SEQUENCE, $msg );
				}
				else {
					
					print ( "<br>Adding Conservation AI classification request for photo " . $photoId );
					
					$photoIds[] = $photoId;
					$url = s3URL ( $photoDetails );
					$urls[] = $url;
					
				}
				
			}
			else {
				print ( "<br>Skipping photo " . $photo_id . " site_ids don't match, site_id = " . $newSiteId . ", should be " . $siteId );
				$errorFound = true;
				
				// Add error status
				$this->setQueueStatusSingle ( $photoId, BioDivViewRequestAI::PROCESSING_ERROR );
				
			}
		}
		
		
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
					
					$this->setQueueStatusMultiple ( $photoIds, BioDivViewRequestAI::TO_SEND, $errMsg );
				}
				else {
				
					$this->setQueueStatusMultiple ( $photoIds, BioDivViewRequestAI::SEND_ERROR, $errMsg );
				}
				
			}
			else {
				
				$msg = $cai->getLastMessage();
				print ( "<br>" . $msg );
				
				$this->setQueueStatusMultiple ( $photoIds, BioDivViewRequestAI::SEND_SUCCESS, $msg );
				
			}
			
		}
		catch(Exception $e) {
			
			$errMsg = $e->getMessage();
			print ( "<br>" . $errMsg );
			error_log ( "Error calling BiodivConservationAI::classify: " . $errMsg );
			
			// Add error status for all photos
			$this->setQueueStatusMultiple ( $photoIds, BioDivViewRequestAI::SEND_ERROR, $errMsg );
		}
		
		if ( $errorFound ) {
			print ( "<br>" . "Error found when processing photos - some photos could not be sent for classification" );
		}
				
	}
	print '<h1>Request to Conservation AI complete, request ' . $i . '</h1>';
}



?>

