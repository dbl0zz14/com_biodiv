<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

namespace MammalWeb\Component\Biodiv\Site\Library\Core;

// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\File;


use MammalWeb\Component\Biodiv\Site\Library\Wrapper\ConservationAI;
use MammalWeb\Component\Biodiv\Site\Library\Wrapper\Megadetector;
use MammalWeb\Component\Biodiv\Site\Library\Wrapper\AWS;



class AI {
	
	const MIN_PHOTO_ID = 21100; // Live 
	//const MIN_PHOTO_ID = 3; // Dev
	
	const TO_SEND			= 0;
	const SEND_SUCCESS		= 1;
	const PROCESSING		= 2;
	const PROCESSING_ERROR	= 3;
	const SEND_ERROR		= 4;
	const FILETYPE_ERROR	= 5;
	const LONG_SEQUENCE		= 6;
	const MANUAL_UNQUEUE	= 7;
	
	const ALLOWEDEXTENSIONS = array('jpg','jpeg','JPG','JPEG');
	
	public static function getTestPhotoIds ( $testTag ) {
		
		// Find the latest (or earliest) upload id not yet added to queue
		$db = db();

		$query = $db->getQuery(true);
			
		$query->select("distinct photo_id")
			->from("AITest")
			->where("tag = " . $db->quote($testTag) );
			
		$db->setQuery($query);
		$photoIds = $db->loadColumn();
		
		return $photoIds;
	}
	
	public static function getUploadId ( $aiType, $projectId, $excludeSubs, $latest ) {
		
		if ( $projectId ) {
				
			if ( !$excludeSubs ) {
				
				$thisAndSubs = Project::getSubProjectsById ( $projectId, true );
		
				$idString = implode(",", array_keys($thisAndSubs));

			}
			else {
				
				$idString = $projectId;
			}
			
			// Find the latest (or earliest) upload id not yet added to queue
			$db = db();

			$query = $db->getQuery(true);
			
			if ( $latest ) {
				
				$query->select("MAX(P.upload_id)")
					->from("Photo P")
					->innerJoin("Upload U")
					->innerJoin("Project PROJ ON PROJ.project_id in (".$idString.")")
					->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
					->where("P.upload_id = U.upload_id")
					->where("P.photo_id > " . self::MIN_PHOTO_ID)
					->where("P.s3_status = 1")
					->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($aiType).") ")
					->where("P.photo_id >= PSM.start_photo_id")
					->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
					->where("P.status not in (4,5,7)");
			}
			else {
				
				$query->select("MIN(P.upload_id)")
					->from("Photo P")
					->innerJoin("Upload U")
					->innerJoin("Project PROJ ON PROJ.project_id in (".$idString.")")
					->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
					->where("P.upload_id = U.upload_id")
					->where("P.photo_id > " . self::MIN_PHOTO_ID)
					->where("P.s3_status = 1")
					->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($aiType).") ")
					->where("P.photo_id >= PSM.start_photo_id")
					->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
					->where("P.status not in (4,5,7)");
			}
			
			error_log("QueueAI view select query created: " . $query->dump());

			$db->setQuery($query);
			$uploadId = $db->loadResult();
			
		}
		else {
		
			// Find the latest (or earliest) upload id not yet added to queue
			$db = db();

			$query = $db->getQuery(true);
			
			if ( $latest ) {
				
				$query->select("MAX(P.upload_id)")
					->from("Photo P")
					->innerJoin("Upload U")
					->where("P.upload_id = U.upload_id")
					->where("P.photo_id > " . self::MIN_PHOTO_ID)
					->where("P.s3_status = 1")
					->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($aiType).") ")
					->where("P.status not in (4,5,7)");
			}
			else {
				
				$query->select("MIN(P.upload_id)")
					->from("Photo P")
					->innerJoin("Upload U")
					->where("P.upload_id = U.upload_id")
					->where("P.photo_id > " . self::MIN_PHOTO_ID)
					->where("P.s3_status = 1")
					->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($aiType).") ")
					->where("P.status not in (4,5,7)");
			}
			
			error_log("QueueAI view select query created: " . $query->dump());

			$db->setQuery($query);
			$uploadId = $db->loadResult();
		
		}
		
		return $uploadId;
	}
	
	
	public static function addToAIQueue($aiType, $photoId, $priority = 5) {
	
		if ( $aiType == 'CAI' && is_numeric($photoId) ) {
			
			$requireCAI = Biodiv::getSetting("conservation_ai") == "yes";
			
			if ( $requireCAI ) {
				
				$db = db();

				$aiFields = new \StdClass();
				$aiFields->photo_id = $photoId;
				$aiFields->ai_type = 'CAI';
				$aiFields->priority = $priority;
				$success = $db->insertObject("AIQueue", $aiFields);
				if(!$success){
					error_log ( "AIQueue insert failed" );
				}
				
			}
		}
		else if ( $aiType == 'MEGA' && is_numeric($photoId) ) {
			
			$requireMEGA = Biodiv::getSetting("megadetector") == "yes";
			
			if ( $requireMEGA ) {
				
				$db = db();

				$aiFields = new \StdClass();
				$aiFields->photo_id = $photoId;
				$aiFields->ai_type = 'MEGA';
				$aiFields->priority = $priority;
				$success = $db->insertObject("AIQueue", $aiFields);
				if(!$success){
					error_log ( "AIQueue insert failed" );
				}
				
			}
		}
		
	}


	public static function addUploadToAIQueue($aiType, $uploadId, $priority = 5) {
		
		if (  (strCmp($aiType, "CAI") == 0) && is_numeric($uploadId) ) {
			
			$requireCAI = Biodiv::getSetting("conservation_ai") == "yes";
			
			if ( $requireCAI ) {
				
				$db = db();
				
				$query = $db->getQuery(true);
				$query->select("photo_id")->from("Photo P")
					->where("upload_id = " . $uploadId )
					->where("status not in (4,5,7)")
					->where("photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($aiType).") ");
				$db->setQuery($query);
				$photoIds = $db->loadColumn();
				
				foreach ( $photoIds as $photoId ) {
					
					self::addToAIQueue($aiType, $photoId, $priority);
					
				}
			}
		}
		else if (  (strCmp($aiType, "MEGA") == 0) && is_numeric($uploadId) ) {
			
			$requireMEGA = Biodiv::getSetting("megadetector") == "yes";
			
			if ( $requireMEGA ) {
				
				$db = db();
				
				$query = $db->getQuery(true);
				$query->select("photo_id")->from("Photo P")
					->where("upload_id = " . $uploadId )
					->where("status not in (4,5,7)")
					->where("photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($aiType).") ");
				$db->setQuery($query);
				$photoIds = $db->loadColumn();
				
				foreach ( $photoIds as $photoId ) {
					
					self::addToAIQueue($aiType, $photoId, $priority);
					
				}
			}
		}
	}
	
	public static function getNextSiteId ( $aiType ) {
		
		
		// Iterate through possible priorities for performance reasons
		$siteId = null;
		
		$priority = 0;
		
		$db = db();
		for ( $priority=1; $priority <= 10; $priority++ ) {
			
			// Check the site_id we're using
			$query = $db->getQuery(true);
			$query->select("P.site_id")
				->from("AIQueue AIQ")
				->innerJoin("Photo P on P.photo_id = AIQ.photo_id")
				->where("ai_type = " . $db->quote($aiType) )
				->where("AIQ.status = " . self::TO_SEND)
				->where("AIQ.priority = " . $priority)
				->where("P.s3_status = 1")
				->where("P.sequence_id > 0");

			$db->setQuery($query, 0, 1); 
			
			$siteId = $db->loadResult();
			
			error_log ( "AI::getNextSiteId, priority = " . $priority . ", siteId = " . $siteId );
			
			if ( $siteId ) break;
		}			
		
		return array ( $siteId, $priority );
					
	}
	
	public static function getPhotoDetails ( $aiType, $siteId, $number, $lowestPriority ) {
		
		// Get a list of photos to be sent to Conservation AI for classification.
		$db = db();
		
		$photos = array();
		for ( $priority=$lowestPriority; $priority <= 10; $priority++ ) {
			
			// Number still to get
			$numMore = $number - count($photos);
		
			$query = $db->getQuery(true);
			$query->select("AIQ.photo_id, P.person_id, P.site_id, P.dirname, P.filename, P.sequence_num")
				->from("AIQueue AIQ")
				->innerJoin("Photo P on P.photo_id = AIQ.photo_id")
				->where("ai_type = " . $db->quote($aiType) )
				->where("AIQ.status = " . self::TO_SEND)
				->where("P.site_id = " . $siteId )
				->where("P.s3_status = 1")
				->where("P.sequence_id > 0")
				->where("AIQ.priority = " . $priority);
				
			$db->setQuery($query, 0, $numMore); // LIMIT $this->max_num
			
			//error_log("RequestAI select query created: " . $query->dump());

			$nextPhotos = $db->loadAssocList("photo_id");
			
			$photos = array_replace($photos, $nextPhotos);
			
			if ( count($photos) >= $number ) break;
		}
		
		return $photos;
	}
	
	
	public static function setQueueStatusSingle ( $aiType, $photoId, $status, $msg = null ) {
		
		$db = db();
		
		$query = $db->getQuery(true);
		$table = "AIQueue";
			
		$fields = array(
			$db->quoteName('status') . ' = ' . $status,
			$db->quoteName('timestamp') . ' = CURRENT_TIMESTAMP' 
		);

		if ( $msg ) {
			$fields[] = $db->quoteName('msg') . ' = ' . $db->quote($msg);
		}
		
		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('ai_type') . ' = ' . $db->quote($aiType),
			$db->quoteName('photo_id') . ' = ' . $photoId 
		);

		$query->update($db->quoteName($table))->set($fields)->where($conditions);
		
		//error_log("setQueueStatusSingle update query created: " . $query->dump());
		
		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;
		
	}
	
	
	public static function setQueueStatusMultiple ( $aiType, $photoIdArray, $status, $msg = null ) {
		
		$result = true;
		if ( count($photoIdArray) > 0 ) {
			
			$db = db();
			$query = $db->getQuery(true);
			$table = "AIQueue";
			$photoStr = implode(',', $photoIdArray);
				
			$fields = array(
				$db->quoteName('status') . ' = ' . $status,
				$db->quoteName('timestamp') . ' = CURRENT_TIMESTAMP' 
			);

			if ( $msg ) {
				$fields[] = $db->quoteName('msg') . ' = ' . $db->quote($msg);
			}
			
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('ai_type') . ' = ' . $db->quote($aiType),
			$db->quoteName('photo_id') . ' IN (' . $photoStr . ') '
			);

			$query->update($db->quoteName($table))->set($fields)->where($conditions);
			
			//error_log("setQueueStatusMultiple update query created: " . $query->dump());
			
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		return $result;
	}
	
	public static function classify ( $aiType, $siteId, $photos ) {
		
		$photoIds = array();
		$urls = array();
		$errorFound = false;
		
		foreach ( $photos as $photoId=>$photoDetails ) {
			
			$newSiteId = $photoDetails["site_id"];
			if ( $newSiteId == $siteId ) {
				
				$filename = $photoDetails['filename'];
				$ext =  File::getExt($filename);
				$sequenceNum = $photoDetails['sequence_num'];
				
				if ( !in_array($ext, self::ALLOWEDEXTENSIONS ) ) {
					
					print ( "<br>Cannot add " . $aiType . " classification request for photo " . $photoId . ", file extension not allowed: ". $filename );
					$errorFound = true;
					
					// Add error status
					self::setQueueStatusSingle ( $aiType, $photoId, self::FILETYPE_ERROR, "File extension not allowed (" . $ext . ")" );
				}
				else if ( $sequenceNum > 20 ) {
					
					$msg = "Long sequence (number " . $sequenceNum . " in sequence) so image not sent to ConservationAI";
					// Add long sequence status
					self::setQueueStatusSingle ( $aiType, $photoId, self::LONG_SEQUENCE, $msg );
				}
				else {
					
					print ( "<br>Adding " . $aiType . " classification request for photo " . $photoId );
					
					$photoIds[] = $photoId;
					$url = AWS::s3URL ( $photoDetails );
					$urls[$photoId] = $url;
					
				}
				
			}
			else {
				print ( "<br>Skipping photo " . $photo_id . " site_ids don't match, site_id = " . $newSiteId . ", should be " . $siteId );
				$errorFound = true;
				
				// Add error status
				self::setQueueStatusSingle ( $aiType, $photoId, self::PROCESSING_ERROR );
				
			}
		}
		
		if ( $aiType == 'CAI' ) {
			
			self::classifyCAI ( $siteId, $photoIds, $urls );
			
			if ( $errorFound ) {
				print ( "<br>" . "Error found when AI classifying photos - some photos could not be sent for classification" );
			}
			
		}
		else if ( $aiType == 'MEGA' ) {
			
			self::classifyMega ( $siteId, $photoIds, $urls );
			
			if ( $errorFound ) {
				print ( "<br>" . "Error found when AI classifying photos - some photos could not be sent for classification" );
			}
		}
		else {
			
			print ("<br>AI type is " . $aiType );
		}		 
	}
	
	
	public static function process ( $aiType, $processAll = false ) {
		
		if ( $aiType == 'CAI' ) {
			
			$result = self::processCAI ( $processAll );
		}
		else if ( $aiType == 'MEGA' ) {
			
			$result = self::processMega ();
		}
		
		return $result;
	}
	
	
	
	private static function classifyCAI ( $siteId, $photoIds, $urls ) {
		
		try {
			$cai = new BiodivConservationAI();
			$response = $cai->classify ( $siteId, $urls );
			
			// If response ok, update queue to say photos were sent.
			if ( $response === false ) {
				
				$errMsg = "Classify request failed, code: " . $cai->getLastCode() . ", message: " . $cai->getLastError();
				print ( "<br>" . $errMsg );
				error_log ( "Error calling ConservationAI::classify: " . $errMsg );
				
				// Check for timeouts and requeue
				if (($cai->getLastCode() == 0) && (strpos($errMsg, 'timed out') !== false)) {
					
					//$this->setQueueStatusMultiple ( $this->aiType, $photoIds, BioDivViewRequestAI::TO_SEND, $errMsg );
					
					//Avoid requeueing repeatedly - temporarily
					self::setQueueStatusMultiple ( $aiType, $photoIds, self::SEND_ERROR, $errMsg );

				}
				else {
				
					self::setQueueStatusMultiple ( $aiType, $photoIds, self::SEND_ERROR, $errMsg );
				}
				
			}
			else {
				
				$msg = $cai->getLastMessage();
				print ( "<br>" . $msg );
				
				self::setQueueStatusMultiple ( $aiType, $photoIds, self::SEND_SUCCESS, $msg );
				
			}
			
		}
		catch(Exception $e) {
			
			$errMsg = $e->getMessage();
			print ( "<br>" . $errMsg );
			error_log ( "Error calling ConservationAI::classify: " . $errMsg );
			
			// Add error status for all photos
			self::setQueueStatusMultiple ( $aiType, $photoIds, self::SEND_ERROR, $errMsg );
		}
		
	}
	
	
	private static function classifyMega ( $siteId, $photoIds, $urls ) {
		
		try {
			$mega = new MegaDetector();
			$response = $mega->classify ( $siteId, $urls );
			
			// If response ok, update queue to say photos were sent.
			if ( $response === false ) {
				
				$errMsg = "Classify request failed, code: " . $mega->getLastCode() . ", message: " . $mega->getLastError();
				print ( "<br>" . $errMsg );
				error_log ( "Error calling MegaDetector::classify: " . $errMsg );
				
				// Check for timeouts and requeue
				if (($mega->getLastCode() != 200) && (strpos($errMsg, 'timed out') !== false)) {
					
					self::setQueueStatusMultiple ( 'MEGA', $photoIds, self::TO_SEND, $errMsg );
				}
				else {
				
					self::setQueueStatusMultiple ( 'MEGA', $photoIds, self::SEND_ERROR, $errMsg );
				}
				
			}
			else {
				
				$msg = $mega->getLastMessage();
				print ( "<br>" . $msg );
				
				// Handle the individual responses..
				foreach ( $response as $photoId=>$photoResponse ) {
					self::setQueueStatusSingle ( 'MEGA', 
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
			self::setQueueStatusMultiple ( 'MEGA', $photoIds, self::SEND_ERROR, $errMsg );
		}
	}
	
	private static function processCAI ( $processAll ) {
		
		$aiType = 'CAI';
		
		// Do Unclassified first.  Then get lasted classify_id.  Then screen out based on last classify_id.  Then update to available where received a classification or unclassified and not screened out and classify_id <=max
			
		// Handle images where no classification is received
			
		$unclassified = "Unclassified";
		
		$noClassId = codes_getCode ( $unclassified, "aiclass" );
		
		// Update Classify table to add an Unclassified row where no classification was received for whole sequence.  Consider all successfully 
		// sent images where the timestamp was more than 6 hours ago.  Then set the status on Photo table so they are not shown to the user
		
		$db = db();

		$query = $db->getQuery(true);
		
		$query->select($db->quoteName(array('P.photo_id', 'P.sequence_id', 'P.sequence_num', 'P.site_id', 'P.filename', 'A.aiq_id')))
			->from($db->quoteName('AIQueue') . ' A')
			->innerJoin($db->quoteName('Photo') . ' P on P.photo_id = A.photo_id and A.ai_type = '.$db->quote($aiType).' and A.status = ' . self::SEND_SUCCESS)
			->where($db->quoteName('P.sequence_id') . ' NOT IN (select sequence_id from Classify where origin = '.$db->quote($aiType).')' );
			
		if ( $processAll ) {
			$query->where('A.timestamp < SUBTIME(NOW(), "6:0:0")'  );
		}
		else {
			$query->where('A.timestamp BETWEEN SUBTIME(NOW(), "18:1:0") AND SUBTIME(NOW(), "6:0:0")'  );
		}
			
		//error_log("ProcessAI view select unclassified query created: " . $query->dump());	
		
		$db->setQuery($query);

		$photos = $db->loadObjectList();
		
		$helper = new BiodivHelper();
		
		$numPhotos = count($photos);
		
		print "<h2>Found " . $numPhotos . " unclassified images</h2>";
		
		$modelVersion = null;
		if ( $numPhotos > 0 ) {
			$caiOptions = caiOptions();
			if ( $caiOptions ) {
				$modelVersion = $caiOptions['modelversion'];
			}
		}
		
		foreach ( $photos as $photo ) {
			
			$imageId = $photo->photo_id;
			$sequenceId = $photo->sequence_id;
			$sequenceNum = $photo->sequence_num;
			$siteId = $photo->site_id;
			$filename = $photo->filename;
			$aiqId = $photo->aiq_id;
			
			print "<br>Processing unclassified sequence " . $sequenceId . ", photoId = " . $imageId;
			
			// Write to database
			$classifyId = $helper->classify( $sequenceId, 
												$imageId,
												$aiType, 
												$modelVersion, 
												null, 
												$siteId, 
												$filename, 
												$aiType, 
												$unclassified, 
												$noClassId, 
												1 );
			
			// Return response
			if ( !$classifyId ) {
				
				$err_msg = "Failed to write to database for imageId " . $imageId;
				error_log ("Process AI error - " . $err_msg );
			
			}
			
			// Update the status of the image
			// These images are now made available as Megadetector used for screening.
			$query = $db->getQuery(true);
			
			if ( $aiqId < 7176072 ) {
									$fields = array(
											$db->quoteName('status') . ' = ' . Sequence::STATUS_UNCLASSIFIED
									);
							}
							else {
									$fields = array(
											$db->quoteName('status') . ' = ' . Sequence::STATUS_AVAILABLE
									);
							}


			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('photo_id') . ' = ' . $imageId, 
				$db->quoteName('status') . ' = ' . Sequence::STATUS_UNAVAILABLE
			);

			$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();
			
			if ( !$result ) {
				error_log ("Process AI error - failed to update status for Photo " . $imageId );
			}

			if ( $sequenceNum == 1 ) {
				Biodiv::updateSequenceInUse ( $sequenceId, null, 1, false );	
			}
		}

		$query = $db->getQuery(true);
		
		$query->select('max(classify_id)')
			->from($db->quoteName('Classify') );
			
		$db->setQuery($query);

		$maxClassifyId = $db->loadResult();
		
		
		
		// Screen out any images with humans
			
		print "<h2>Updating images where human found to have contains_human = 1</h2>";
		
		$humanId = codes_getCode ( "Human", "content" );
		
		// Update Photo table contains_human column to 1 if not already set.
		$query = $db->getQuery(true);
		
		$fields = array(
			$db->quoteName('contains_human') . ' = 1' 
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('sequence_id') . ' in (select sequence_id from Classify where origin = '.$db->quote($aiType).
							' and species_id = ' . $humanId . ')', 
			$db->quoteName('contains_human') . ' = 0' 
		);

		$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

		//error_log("ProcessAI view update human query created: " . $query->dump());	

		$db->setQuery($query);

		$result = $db->execute();
			
			
			
			
		// Screen out any images with Calibration poles
			
		$poleId = codes_getCode ( "Calibration pole", "aiclass" );
		
		print "<h2>Updating images where calibration pole found, id = " . $poleId . "</h2>";
		
		// Update Photo table status = 9.
		$query = $db->getQuery(true);
		
		$fields = array(
			$db->quoteName('status') . ' = ' . Sequence::STATUS_CALIBRATION_POLE 
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('sequence_id') . ' IN (select sequence_id from Classify where origin = '.$db->quote($aiType).
							' and species_id = ' . $poleId . ')', 
			$db->quoteName('status') . ' = ' . Sequence::STATUS_UNAVAILABLE
		);

		$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);
		
		$db->setQuery($query);

		$result = $db->execute();
			
		
		// Set sequences which have passed screening to available
			
		print "<h2>Updating images which have passed screening </h2>";
		
		// Update Photo table status = 1 and PhotoSequence table in_use = 1.
		
		$query = $db->getQuery(true);
		
		$query->select('distinct sequence_id')
			->from($db->quoteName('Photo') )
			->where('status = ' . Sequence::STATUS_UNAVAILABLE)
			->where('contains_human = 0')
			->where('sequence_id IN (select sequence_id from Classify where origin = '.$db->quote($aiType).
							' and classify_id <= ' . $maxClassifyId . ')');
			
		$db->setQuery($query);

		$sequenceIds = $db->loadColumn();
		
		
		$query = $db->getQuery(true);
		
		$fields = array(
			$db->quoteName('status') . ' = ' . Sequence::STATUS_AVAILABLE 
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('sequence_id') . ' IN (select sequence_id from Classify where origin = '.$db->quote($aiType).
							' and classify_id <= ' . $maxClassifyId . ')', 
			$db->quoteName('status') . ' = ' . Sequence::STATUS_UNAVAILABLE,
			$db->quoteName('contains_human') . ' = 0' 
		);

		$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);
		
		$db->setQuery($query);

		$result = $db->execute();
		
		foreach ( $sequenceIds as $seqId ) {
			
			Biodiv::updateSequenceInUse ( $seqId, null, 1, false );	
		}
			
		// Update status of queue where request failed (usually timed out) but classifications received.  

		print "<h2>Updating queue status where request timed out but classifications received</h2>";

		$query = $db->getQuery(true);
		
		$fields = array(
			$db->quoteName('status') . ' = ' . self::SEND_SUCCESS ,
			$db->quoteName('timestamp') . ' = NOW()'
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('photo_id') . ' IN (select photo_id from Classify where origin = '.$db->quote($aiType).
							' and species_id != ' . $noClassId . ')', 
			$db->quoteName('status') . ' = ' . self::SEND_ERROR,
			$db->quoteName('ai_type') . ' = ' . $db->quote($aiType),
			$db->quoteName('timestamp') . ' < SUBTIME(NOW(), "6:0:0")' 
		);

		$query->update($db->quoteName('AIQueue'))->set($fields)->where($conditions);
		
		$db->setQuery($query);

		$result = $db->execute();
		
		
		
		// Requeue timed out requests where no classification received.  Limit numbers so don't flood system.

		print "<h2>Re-queueing images where request timed out and no classifications received</h2>";

		$query = $db->getQuery(true);
		
		$fields = array(
			$db->quoteName('status') . ' = ' . self::TO_SEND ,
			$db->quoteName('msg') . ' = ' . $db->quote('Requeued'),
			$db->quoteName('timestamp') . ' = NOW()'
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('photo_id') . ' NOT IN (select photo_id from Classify where origin = '.$db->quote($aiType). ')', 
			$db->quoteName('status') . ' = ' . self::SEND_ERROR,
			$db->quoteName('ai_type') . ' = ' . $db->quote($aiType),
			$db->quoteName('msg') . ' like "%timed out%"' ,
			$db->quoteName('timestamp') . ' < SUBTIME(NOW(), "6:0:0")'
		);

		$query->update($db->quoteName('AIQueue'))->set($fields)->where($conditions);
		
		$db->setQuery($query, 0, 20);

		$result = $db->execute();
	}
	
	
	private static function processMega () {
		
		$aiType = 'MEGA';
	
		// Identify sequences which have been sent through Megadetector and all
		// images have either no detections or probability < threshold.
		// These will be unavailable and not sent to CAI so set the status for all Photos in sequence.
		$nothingId = codes_getCode ( 'Nothing', 'noanimal' );
		$humanId = codes_getCode ( 'Human', 'content' );
		$vehicleId = codes_getCode ( 'Vehicle', 'content' );
		$animalId = codes_getCode ( 'Animal', 'aiclass' );
		
		$megadetectorHumanThreshold = 0.01;
		$megadetectorAnimalThreshold = 0.01;
		$megadetectorVehicleThreshold = 0.01;
		
		$megaOptions = megaOptions();
		if ( $megaOptions ) {
			$megadetectorHumanThreshold = $megaOptions['humanthreshold'];
			$megadetectorAnimalThreshold = $megaOptions['animalthreshold'];
			$megadetectorVehicleThreshold = $megaOptions['vehiclethreshold'];
		}
		
		$db = db();

		$query = $db->getQuery(true);
		
		$query->select('distinct ' . $db->quoteName('C.sequence_id'))
			->from($db->quoteName('Classify') . ' C')
			->innerJoin($db->quoteName('Photo') . ' P on P.photo_id = C.photo_id and P.sequence_num = 1 and P.status = 0 and P.contains_human = 0')
			->where($db->quoteName('C.origin') . ' = '.$db->quote('MEGA') )
			->where($db->quoteName('P.photo_id') . 
					' NOT IN (select photo_id from AIQueue where ai_type = '.$db->quote('CAI').')' );
			
		//error_log("ProcessAI view select sequence ids (post Megadetector) query created: " . $query->dump());	
		
		$db->setQuery($query, 0, 250); // Limit to 50 at a time

		$sequences = $db->loadColumn();
		
		// Identify sequences which have received Megadetector
		// detections > prob threshold.
		// Add to CAI queue.
		foreach ( $sequences as $sequenceId ) {
			
			print ( "<br>Processing sequence " . $sequenceId );


			$query = $db->getQuery(true);
		
			$query->select($db->quoteName(array('P.photo_id', 'P.status', 'P.contains_human', 
											'AIQ.aiq_id', 'AIQ.status', 
											'C.species_id', 'C.prob'),
											array('photo_id', 'status', 'contains_human',
											'aiq_id', 'cai_status', 'species_id', 'prob' )))
				->from($db->quoteName('Photo') . ' P')
				->leftJoin($db->quoteName('AIQueue') . ' AIQ on P.photo_id = AIQ.photo_id and AIQ.ai_type = '. $db->quote('CAI'))
				->innerJoin($db->quoteName('Classify') . ' C on P.photo_id = C.photo_id and C.origin = '. $db->quote('MEGA'))
				->where($db->quoteName('P.sequence_id') . ' = ' . $sequenceId )
				->order("P.sequence_num");
			
			$db->setQuery($query);

			//error_log("ProcessAI view select photo ids query created: " . $query->dump());	
		
			$photos = $db->loadObjectList();	
			
			
			$onCAIQueue = 0;
			$hasCAIProblem = 0;
			$hasHumanDetected = 0;
			$hasNothingDetected = array();
			$hasDetectionBelowThreshold = array();
			$hasDetectionAboveThreshold = array();
			$hasVehicleBelowThreshold = array();
			$hasVehicleAboveThreshold = array();
			$uniquePhotoIds = array();

			foreach ( $photos as $photo ) {
				
				//error_log ( "Got photo " . print_r ( $photo, true ) );
				//print ( "<br>Got photo " . print_r ( $photo, true ) );
				
				$photoId = $photo->photo_id;
				
				if ( !in_array($photoId, $uniquePhotoIds) ) {
					$uniquePhotoIds[] = $photoId;
				}
				
				if ( property_exists($photo, 'aiq_id') && $photo->aiq_id > 0) {
					
					$onCAIQueue += 1;
					
					if ( $photo->cai_status > 2 ) {
						$hasCAIProblem += 1;
						error_log ( "Photo " . $photo->photo_id . " has CAI problem in AIQueue" );
					}
					
					// No further processing of this sequence here - see section after photo by photo checks
					break;
				}
				if ( property_exists($photo, 'species_id') ) {
					if ( $photo->species_id == $humanId && $photo->prob > $megadetectorHumanThreshold ) {
						$hasHumanDetected += 1;
						break;
					}
					else if ( $photo->species_id == $nothingId ) {
						$hasNothingDetected[$photo->photo_id] = true;
					}
					else if ( $photo->species_id == $animalId && $photo->prob < $megadetectorAnimalThreshold ) {
							$hasDetectionBelowThreshold[$photo->photo_id] = true;
					}
					else if ( $photo->species_id == $vehicleId && $photo->prob < $megadetectorVehicleThreshold ) {
						$hasVehicleBelowThreshold[$photo->photo_id] = true;
					}
					else if ( $photo->species_id == $vehicleId && $photo->prob > $megadetectorVehicleThreshold ) {
						$hasVehicleAboveThreshold[$photo->photo_id] = true;
					}
					else {
						$hasDetectionAboveThreshold[$photo->photo_id] = true;
					}
				}
			}
			if ( $onCAIQueue ) {
				print ( "<br>Got CAI entries so checking for case where only part of sequence was processed");
				print ( "<br>Delete any CAI entries if unsent so works next time");

				$query = $db->getQuery(true);

				$query->select("count(*)")
										->from($db->quoteName('Photo') . ' P')
										->where($db->quoteName('P.photo_id') . ' in ( select photo_id from Photo where sequence_id = ' . $sequenceId  . ')' );
				$db->setQuery($query);
				$numInSequence = $db->loadResult();

				$timestamp = time() - (60 * 60);
				$dateStr = date('Y-m-d H:i:s', $timestamp);

				$query = $db->getQuery(true);
				$query->select("aiq_id")
										->from($db->quoteName('AIQueue') . ' AIQC')
					->where($db->quoteName('AIQC.ai_type') . ' = ' . $db->quote('CAI'))
					->where($db->quoteName('AIQC.status') . ' IN ( 0, 2)' )
					->where($db->quoteName('AIQC.timestamp') . ' < ' . $db->quote($dateStr) )
										->where($db->quoteName('AIQC.photo_id') . ' in ( select photo_id from Photo where sequence_id = ' . $sequenceId  . ')' );
				error_log("ProcessAI view select cai unsent: " . $query->dump());

				$db->setQuery($query);
				$caiZeros = $db->loadColumn();

				$numZeros = count($caiZeros);
				if ( $numZeros > 0 and $numZeros < $numInSequence ) {
					print ( "<br>Deleting " . count($caiZeros) . " part processed CAI entries from AIQueue"); 

					$query = $db->getQuery(true);
								$query->delete("AIQueue")
										->where($db->quoteName("aiq_id") . " in ( " . implode(',', $caiZeros) . ")" );
								$db->setQuery($query);
								$success = $db->execute();

					if ( !$success ) {
						print ( "<br>Problem deleting  part processed entries" );
					}
				}
				else {
					print ( "<br>num zeros >= num photos in sequence so not deleting");
				}

				continue;
			}
			
			$query = $db->getQuery(true);
		
			$query->select($db->quoteName(array('P.photo_id')))
				->from($db->quoteName('Photo') . ' P')
				->where($db->quoteName('P.sequence_id') . ' = ' . $sequenceId )
				->order("P.sequence_num");
			
			$db->setQuery($query, 0, 20);

			//error_log("ProcessAI view MEGA: select sequence photo ids query created: " . $query->dump());	
		
			$sequencePhotoIds = $db->loadColumn();	
			
			
			//error_log ( "Got hasNothingDetected " . print_r ( $hasNothingDetected, true ) );
			print ( "<br>Got hasNothingDetected " . print_r ( $hasNothingDetected, true ) );
			//error_log ( "Got hasDetectionBelowThreshold " . print_r ( $hasDetectionBelowThreshold, true ) );
			print ( "<br>Got hasDetectionBelowThreshold " . print_r ( $hasDetectionBelowThreshold, true ) );
			//error_log ( "Got hasDetectionAboveThreshold " . print_r ( $hasDetectionAboveThreshold, true ) );
			print ( "<br>Got hasDetectionAboveThreshold " . print_r ( $hasDetectionAboveThreshold, true ) );
			
			$photosInSequence = count($sequencePhotoIds);
			$photosProcessed = count($uniquePhotoIds);
			print ( "<br>Sequence has " . $photosInSequence . " photos" );
			print ( "<br>Processed " . $photosProcessed . " photos" );
			
			if ( $hasHumanDetected > 0 ) {
				
				print ( "<br>Some photos in sequence " . $sequenceId . " have human detections so flagging before adding to AIQueue" );
				//error_log ( "Some photos in sequence " . $sequenceId . " have human detections so flagging before adding to AIQueue" );
				
				// Update Photo table contains_human column to 1 if not already set.
				$query = $db->getQuery(true);
		
				$fields = array(
					$db->quoteName('contains_human') . ' = 1' 
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('sequence_id') . ' = ' . $sequenceId, 
					$db->quoteName('contains_human') . ' = 0' 
				);

				$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

				//error_log("ProcessAI view update human query created: " . $query->dump());	

				$db->setQuery($query);

				$result = $db->execute();
		
				// Add to CAI queue
				foreach ( $sequencePhotoIds as $newId ) {
					self::addToAIQueue('CAI', $newId);
				}
			}
			else if ( $photosInSequence != $photosProcessed ) {
			//else if ( $photosInSequence > $photosProcessed ) {
				print ( "<br>Incomplete sequence (".$sequenceId."), leave for next run" );
			}
			else if ( count($hasDetectionAboveThreshold) > 0 ) {
				
				print ( "<br>Some photos in sequence " . $sequenceId . " have detections so adding to AIQueue" );
				//error_log ( "Some photos in sequence " . $sequenceId . " have detections so adding to AIQueue" );
				
				// Add to CAI queue
				foreach ( $uniquePhotoIds as $newId ) {
					self::addToAIQueue('CAI', $newId);

					// Make available by setting status
					$query = $db->getQuery(true);

					$fields = array(
						$db->quoteName('status') . ' = ' . Sequence::STATUS_AVAILABLE
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('photo_id') . ' = ' . $newId,
						$db->quoteName('status') . ' = ' . Sequence::STATUS_UNAVAILABLE
					);

					$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$result = $db->execute();

					if ( !$result ) {
						error_log ("Process AI error - failed to update status to available for photo " . $newId );
					}
				}


			}
			else if ( count($hasVehicleAboveThreshold) > 0 ) {

				print ( "<br>Some photos in sequence " . $sequenceId . " have only vehicle detections so adding to AIQueue" );
				//error_log ( "Some photos in sequence " . $sequenceId . " have only vehicle detections so adding to AIQueue" );

				// Add to CAI queue
				foreach ( $uniquePhotoIds as $newId ) {
					self::addToAIQueue('CAI', $newId);

					// Make available by setting status
					$query = $db->getQuery(true);

					$fields = array(
						$db->quoteName('status') . ' = ' . Sequence::STATUS_MEGADETECTOR_VEHICLE
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('photo_id') . ' = ' . $newId,
						$db->quoteName('status') . ' = ' . Sequence::STATUS_UNAVAILABLE
					);

					$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$result = $db->execute();

					if ( !$result ) {
						error_log ("Process AI error - failed to update status to vehicle for photo " . $newId );
					}
				}
			}
			else {
				
				//error_log ( "All photos in sequence " . $sequenceId . " have nothing detected" );
				// Screen out as nothing in sequence
				// Update the status of the image
				$query = $db->getQuery(true);
				
				$fields = array(
					$db->quoteName('status') . ' = ' . Sequence::STATUS_NOTHING 
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('sequence_id') . ' = ' . $sequenceId, 
					$db->quoteName('status') . ' = ' . Sequence::STATUS_UNAVAILABLE
				);

				$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

				$db->setQuery($query);

				$result = $db->execute();
				
				if ( !$result ) {
					error_log ("Process AI error - failed to update status for sequence " . $sequenceId );
				}
			}					
		}	
	
	}
	
}

