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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewProcessAI extends JViewLegacy
{
	
	const STATUS_UNAVAILABLE = 0;
	const STATUS_AVAILABLE = 1;
	const STATUS_UNCLASSIFIED = 9; // Unclassified by Conservation AI
	const STATUS_CALIBRATION_POLE = 10;
	const STATUS_NOTHING = 14; // Nothing found by Megadetector (or below threshold)
	
	const AI_SEND_SUCCESS = 1;
	
	
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$app = JFactory::getApplication();
		
		$input = $app->input;
			
		$this->processAll = $input->getInt('all', 0);
		$this->aiType = $input->getString('ai', 'CAI');
		
		if ( $this->aiType == 'CAI' ) {
		
			// Do Unclassified first.  Then get lasted classify_id.  Then screen out based on last classify_id.  Then update to available where received a classification or unclassified and not screened out and classify_id <=max
			
			// Handle images where no classification is received
				
			$unclassified = "Unclassified";
			
			$noClassId = codes_getCode ( $unclassified, "aiclass" );
			
			// Update Classify table to add an Unclassified row where no classification was received for whole sequence.  Consider all successfully 
			// sent images where the timestamp was more than 6 hours ago.  Then set the status on Photo table so they are not shown to the user
			
			$db = JDatabase::getInstance(dbOptions());

			$query = $db->getQuery(true);
			
			$query->select($db->quoteName(array('P.photo_id', 'P.sequence_id', 'P.site_id', 'P.filename')))
				->from($db->quoteName('AIQueue') . ' A')
				->innerJoin($db->quoteName('Photo') . ' P on P.photo_id = A.photo_id and A.status = ' . self::AI_SEND_SUCCESS)
				->where($db->quoteName('P.sequence_id') . ' NOT IN (select sequence_id from Classify where origin = '.$db->quote($this->aiType).')' );
				
			if ( $this->processAll ) {
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
			
			$this->modelVersion = null;
			if ( $numPhotos > 0 ) {
				if ( $this->aiType == 'CAI' ) {
					$this->caiOptions = caiOptions();
					if ( $this->caiOptions ) {
						$this->modelVersion = $this->caiOptions['modelversion'];
					}
				}
			}
			
			foreach ( $photos as $photo ) {
				
				$imageId = $photo->photo_id;
				$sequenceId = $photo->sequence_id;
				$siteId = $photo->site_id;
				$filename = $photo->filename;
				
				print "<br>Processing unclassified sequence " . $sequenceId . ", photoId = " . $imageId;
				
				// Write to database
				$classifyId = $helper->classify( $sequenceId, 
													$imageId,
													$this->aiType, 
													$this->modelVersion, 
													null, 
													$siteId, 
													$filename, 
													$this->aiType, 
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
				
				$fields = array(
					$db->quoteName('status') . ' = ' . self::STATUS_AVAILABLE 
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('photo_id') . ' = ' . $imageId, 
					$db->quoteName('status') . ' = ' . self::STATUS_UNAVAILABLE
				);

				$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

				$db->setQuery($query);

				$result = $db->execute();
				
				if ( !$result ) {
					error_log ("Process AI error - failed to update status for Photo " . $imageId );
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
				$db->quoteName('sequence_id') . ' in (select sequence_id from Classify where origin = '.$db->quote($this->aiType).
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
				$db->quoteName('status') . ' = ' . self::STATUS_CALIBRATION_POLE 
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('sequence_id') . ' IN (select sequence_id from Classify where origin = '.$db->quote($this->aiType).
								' and species_id = ' . $poleId . ')', 
				$db->quoteName('status') . ' = ' . self::STATUS_UNAVAILABLE
			);

			$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);
			
			$db->setQuery($query);

			$result = $db->execute();
				
			
			// Set sequences which have passed screening to available
				
			print "<h2>Updating images which have passed screening </h2>";
			
			// Update Photo table status = 1.
			$query = $db->getQuery(true);
			
			$fields = array(
				$db->quoteName('status') . ' = ' . self::STATUS_AVAILABLE 
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('sequence_id') . ' IN (select sequence_id from Classify where origin = '.$db->quote($this->aiType).
								' and classify_id <= ' . $maxClassifyId . ')', 
				$db->quoteName('status') . ' = ' . self::STATUS_UNAVAILABLE,
				$db->quoteName('contains_human') . ' = 0' 
			);

			$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);
			
			$db->setQuery($query);

			$result = $db->execute();
				
				
		}
		else if ( $this->aiType == 'MEGA' ) {
			
			// Identify sequences which have been sent through Megadetector and all
			// images have either no detections or probability < threshold.
			// These will be unavailable and not sent to CAI so set the status for all Photos in sequence.
			$nothing = 'Nothing';
			$nothingId = codes_getCode ( $nothing, 'noanimal' );
			
			$this->megaOptions = megaOptions();
			if ( $this->megaOptions ) {
				$megadetectorThreshold = $this->megaOptions['threshold'];
			}
			
			$db = JDatabase::getInstance(dbOptions());

			$query = $db->getQuery(true);
			
			$query->select('distinct ' . $db->quoteName('C.sequence_id'))
				->from($db->quoteName('Classify') . ' C')
				->innerJoin($db->quoteName('Photo') . ' P on P.photo_id = C.photo_id and P.sequence_num = 1 and P.status = 0')
				->where($db->quoteName('C.origin') . ' = '.$db->quote('MEGA') )
				->where($db->quoteName('P.photo_id') . 
						' NOT IN (select photo_id from AIQueue where ai_type = '.$db->quote('CAI').')' );
				
			//error_log("ProcessAI view select sequence ids (post Megadetector) query created: " . $query->dump());	
			
			$db->setQuery($query, 0, 40); // Limit to 50 at a time

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
					->leftJoin($db->quoteName('Classify') . ' C on P.photo_id = C.photo_id and C.origin = '. $db->quote('MEGA'))
					->where($db->quoteName('P.sequence_id') . ' = ' . $sequenceId );
				
				$db->setQuery($query);

				//error_log("ProcessAI view select photo ids query created: " . $query->dump());	
			
				$photos = $db->loadObjectList();	
				
				
				$onCAIQueue = 0;
				$hasCAIProblem = 0;
				$hasNothingDetected = array();
				$hasDetectionBelowThreshold = array();
				$hasDetectionAboveThreshold = array();
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
						
						// No further processing of this sequence
						break;
					}
					if ( property_exists($photo, 'species_id') ) {
						if ( $photo->species_id == $nothingId ) {
							$hasNothingDetected[$photo->photo_id] = true;
						}
						else if ( $photo->prob < $megadetectorThreshold ) {
							$hasDetectionBelowThreshold[$photo->photo_id] = true;
						}
						else {
							$hasDetectionAboveThreshold[$photo->photo_id] = true;
						}
					}
				}
				if ( $onCAIQueue ) {
					continue;
				}
				
				$query = $db->getQuery(true);
			
				$query->select($db->quoteName(array('P.photo_id')))
					->from($db->quoteName('Photo') . ' P')
					->where($db->quoteName('P.sequence_id') . ' = ' . $sequenceId );
				
				$db->setQuery($query);

				//error_log("ProcessAI view MEGA: select sequence photo ids query created: " . $query->dump());	
			
				$sequencePhotoIds = $db->loadColumn();	
				
				
				//error_log ( "Got hasNothingDetected " . print_r ( $hasNothingDetected, true ) );
				//print ( "<br>Got hasNothingDetected " . print_r ( $hasNothingDetected, true ) );
				//error_log ( "Got hasDetectionBelowThreshold " . print_r ( $hasDetectionBelowThreshold, true ) );
				//print ( "<br>Got hasDetectionBelowThreshold " . print_r ( $hasDetectionBelowThreshold, true ) );
				//error_log ( "Got hasDetectionAboveThreshold " . print_r ( $hasDetectionAboveThreshold, true ) );
				//print ( "<br>Got hasDetectionAboveThreshold " . print_r ( $hasDetectionAboveThreshold, true ) );
				
				$photosInSequence = count($sequencePhotoIds);
				$photosProcessed = count($uniquePhotoIds);
				//print ( "<br>Sequence has " . $photosInSequence . " photos" );
				//print ( "<br>Processed " . $photosProcessed . " photos" );
				
				if ( $photosInSequence != $photosProcessed ) {
					print ( "<br>Incomplete sequence (".$sequenceId."), leave for next run" );
				}
				else if ( count($hasDetectionAboveThreshold) > 0 ) {
					
					print ( "<br>Some photos in sequence " . $sequenceId . " have detections so adding to AIQueue" );
					//error_log ( "Some photos in sequence " . $sequenceId . " have detections so adding to AIQueue" );
					
					// Add to CAI queue
					foreach ( $uniquePhotoIds as $newId ) {
						addToAIQueue('CAI', $newId);
					}
				}
				else {
					
					//error_log ( "All photos in sequence " . $sequenceId . " have nothing detected" );
					// Screen out as nothing in sequence
					// Update the status of the image
					$query = $db->getQuery(true);
					
					$fields = array(
						$db->quoteName('status') . ' = ' . self::STATUS_NOTHING 
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('sequence_id') . ' = ' . $sequenceId, 
						$db->quoteName('status') . ' = ' . self::STATUS_UNAVAILABLE
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

		
			
		//}
		
		// if ( $this->processHuman ) {
			
			// print "<h2>Updating images where human found to have contains_human = 1</h2>";
			
			// $humanId = codes_getCode ( "Human", "content" );
			
			// // Update Photo table contains_human column to 1 if not already set.
			// $db = JDatabase::getInstance(dbOptions());

			// $query = $db->getQuery(true);
			
			// $fields = array(
				// $db->quoteName('contains_human') . ' = 1' 
			// );

			// // Conditions for which records should be updated.
			// $conditions = array(
				// $db->quoteName('sequence_id') . ' in (select sequence_id from Classify where origin = '.$db->quote($this->aiType).
								// ' and species_id = ' . $humanId . ')', 
				// $db->quoteName('contains_human') . ' = 0' 
			// );

			// $query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

			// error_log("ProcessAI view update human query created: " . $query->dump());	

			// $db->setQuery($query);

			// $result = $db->execute();
			
		// }
		// if ( $this->processPole ) {
			
			// $poleId = codes_getCode ( "Calibration pole", "aiclass" );
			
			// print "<h2>Updating images where calibration pole found, id = " . $poleId . "</h2>";
			
			// // Update Photo table status = 9.
			// $db = JDatabase::getInstance(dbOptions());

			// $query = $db->getQuery(true);
			
			// $fields = array(
				// $db->quoteName('status') . ' = ' . self::STATUS_CALIBRATION_POLE 
			// );

			// // Conditions for which records should be updated.
			// $conditions = array(
				// $db->quoteName('sequence_id') . ' IN (select sequence_id from Classify where origin = '.$db->quote($this->aiType).
								// ' and species_id = ' . $poleId . ')', 
				// $db->quoteName('status') . ' = ' . self::STATUS_UNAVAILABLE
			// );

			// $query->update($db->quoteName('Photo'))->set($fields)->where($conditions);
			
			// $db->setQuery($query);

			// $result = $db->execute();
			
		// }
		// if ( $this->processNone ) {
			
			// $unclassified = "Unclassified";
			
			// $noClassId = codes_getCode ( $unclassified, "aiclass" );
			
			// // Update Classify table to add an Unclassified row where no classification was received for whole sequence.  Consider all successfully 
			// // sent images where the timestamp was more than 6 hours ago.  Then set the status on Photo table so they are not shown to the user
			
			// $db = JDatabase::getInstance(dbOptions());

			// $query = $db->getQuery(true);
			
			// $query->select($db->quoteName(array('P.photo_id', 'P.sequence_id', 'P.site_id', 'P.filename')))
				// ->from($db->quoteName('AIQueue') . ' A')
				// ->innerJoin($db->quoteName('Photo') . ' P on P.photo_id = A.photo_id and A.status = ' . self::AI_SEND_SUCCESS)
				// ->where($db->quoteName('P.sequence_id') . ' NOT IN (select sequence_id from Classify where origin = '.$db->quote($this->aiType).')' );
				
			// if ( $this->processAll ) {
				// $query->where('A.timestamp < SUBTIME(NOW(), "6:0:0")'  );
			// }
			// else {
				// $query->where('A.timestamp BETWEEN SUBTIME(NOW(), "18:1:0") AND SUBTIME(NOW(), "6:0:0")'  );
			// }
				
			// //error_log("ProcessAI view select unclassified query created: " . $query->dump());	
			
			// $db->setQuery($query);

			// $photos = $db->loadObjectList();
			
			// $helper = new BiodivHelper();
			
			// $numPhotos = count($photos);
			
			// print "<h2>Found " . $numPhotos . " unclassified images</h2>";
			
			// $this->modelVersion = null;
			// if ( $numPhotos > 0 ) {
				// if ( $this->aiType == 'CAI' ) {
					// $this->caiOptions = caiOptions();
					// if ( $this->caiOptions ) {
						// $this->modelVersion = $this->caiOptions['modelversion'];
					// }
				// }
			// }
			
			// foreach ( $photos as $photo ) {
				
				// $imageId = $photo->photo_id;
				// $sequenceId = $photo->sequence_id;
				// $siteId = $photo->site_id;
				// $filename = $photo->filename;
				
				// print "<br>Processing unclassified sequence " . $sequenceId . ", photoId = " . $imageId;
				
				// // Write to database
				// $classifyId = $helper->classify( $sequenceId, 
													// $imageId,
													// $this->aiType, 
													// $this->modelVersion, 
													// null, 
													// $siteId, 
													// $filename, 
													// $this->aiType, 
													// $unclassified, 
													// $noClassId, 
													// 1 );
				
				// // Return response
				// if ( !$classifyId ) {
					
					// $err_msg = "Failed to write to database for imageId " . $imageId;
					// error_log ("Process AI error - " . $err_msg );
				
				// }
				
				// // Update the status of the image
				// $db = JDatabase::getInstance(dbOptions());

				// $query = $db->getQuery(true);
				
				// $fields = array(
					// $db->quoteName('status') . ' = ' . self::STATUS_UNCLASSIFIED 
				// );

				// // Conditions for which records should be updated.
				// $conditions = array(
					// $db->quoteName('photo_id') . ' = ' . $imageId, 
					// $db->quoteName('status') . ' = ' . self::STATUS_AVAILABLE
				// );

				// $query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

				// $db->setQuery($query);

				// $result = $db->execute();
				
				// if ( !$result ) {
					// error_log ("Process AI error - failed to update status for Photo " . $imageId );
				// }
			// }
			
		// }
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>
