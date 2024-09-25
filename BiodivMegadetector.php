<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains functions to request classifications from the Conservation AI API.

// for Joomla 4 namespace MammalWeb\Component\Biodiv\Site\Library\Core;

// No direct access to this file
defined('_JEXEC') or die;

//use Riverline\MultiPartParser\StreamedPart;

class BiodivMegaDetector {
	
	private $megaOptions;
	private $endpoint;
	private $authkey;
	private $modelVersion;
	private $tempDir;
	private $lastError;
	private $helper;
	private $speciesIds;
	private $allowedMimeTypes;
	
	
	function __construct()
	{
		$this->lastCode = null;
		$this->lastError = null;
		$this->lastMessage = null;
		$this->megaOptions = megaOptions();
		if ( $this->megaOptions ) {
			$this->endpoint = $this->megaOptions['endpoint'];
			$this->authkey = $this->megaOptions['key']; 
			$this->modelVersion = $this->megaOptions['modelversion'];
			$this->tempDir = $this->megaOptions['tempdir'];

			if ( !is_dir ( $this->tempDir ) ) {
				print ( "<br>Making temp dir " . $this->tempDir );
				$success = mkdir($this->tempDir);
			}
		}
		
		$this->helper = new BiodivHelper();
		
		// Megadetector returns nothing, animal(1), person(2) or vehicle(3)
		$this->speciesIds = array();
		$this->speciesIds[0] = codes_getCode ( 'Nothing', 'content' );
		$this->speciesIds[1] = codes_getCode ( 'Animal', 'aiclass' );
		$this->speciesIds[2] = codes_getCode ( 'Human', 'content' );
		$this->speciesIds[3] = codes_getCode ( 'Car', 'aiclass' );

		$this->allowedMimeTypes = ['image/jpeg', 'image/png'];
	}
	
	
	public function getLastCode () {
		
		return $this->lastCode;
	}
	
	
	public function getLastError () {
		
		return $this->lastError;
	}
	
	
	public function getLastMessage () {
		
		return $this->lastMessage;
	}
	
	
	// urls should be an array of objects with properties id (photoId) and url.
	public function classify ( $siteId, $urls ) {
		
		if ( !$this->megaOptions ) {
			return false;
		}
		else {
			
			$this->lastError = "";
			
			$imgArray = array();
			
			$responseArray = array();
			
			$idLookup = array();

			$imagesToCleanUp = array();
			
			$dir = $this->tempDir . '/site_' . $siteId;
					
			if ( is_array($urls) ) {
				
				$numImages = 0;
				foreach ( $urls as $photoId=>$url ) {
					
					$success = true;
					$fileName = basename($url);

					$idLookup[$fileName] = $photoId;
					
					if ( !is_dir ( $dir ) ) {
						print ( "<br>Making temp dir " . $dir );
						$success = mkdir($dir);
					}
					if ( $success ) {
						$fullFileName = $dir . '/' . $fileName;
						if ( file_put_contents($fullFileName, file_get_contents($url)) ) {
							$imagesToCleanUp[] = $fullFileName;
							$numImages += 1;
							$mimeType = mime_content_type($fullFileName);
							print ("<br>Mime type of " . $fullFileName . " is " . mime_content_type($fullFileName ) );

							if ( in_array($mimeType, $this->allowedMimeTypes) ) {
								$imgArray['image'.$numImages] = new CURLFile($fullFileName, $mimeType, $fileName);
							}
							else {
								$errMsg = "Mime type " .$mimeType. " not in allowed list";
								error_log ( $errMsg );
								print ("<br>" . $errMsg );
							}
						}
						else {
							error_log ( "BiodivMegadetector local save failed for image " . $fullFileName );
							print ( "<br>BiodivMegadetector local save failed for image " . $fullFileName );
							$responseArray[$photoId] = array('photo_id'=>$photoId,
											'status'=>BioDivViewRequestAI::PROCESSING_ERROR,
											'msg'=>'Local save failed');
						}
					}
					else {
						error_log ( "BiodivMegadetector could not mkdir " . $dir );
						print ( "<br>BiodivMegadetector could not mkdir " . $dir );
						$responseArray[$photoId] = array('photo_id'=>$photoId,
															'status'=>BioDivViewRequestAI::PROCESSING_ERROR,
															'msg'=>'Could not mkdir');
					}
				}
			}
			else {
				error_log ( "BiodivMegadetector error urls not an array" );
				print ( "<br>BiodivMegadetector error urls not an array" );
				return false;
			}
			
			
			$this->lastError = null;
			$this->lastMessage = null;
			
			$curl = curl_init();
			
			curl_setopt_array($curl, [
				CURLOPT_URL => $this->endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 360,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $imgArray,
				CURLOPT_HTTPHEADER => [
				"key: ".$this->authkey
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			$this->lastCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			//error_log ( "response = " . $response );
			
			//$responseDecoded = json_decode($response);
			
			curl_close($curl);
			
			if ($err) {
				error_log ( "cURL Error #:" . $err );
				$this->lastError = "cURL Error #:" . $err;
				$this->cleanUpFiles($imagesToCleanUp);
				return false;
			}
			else {
				//$errMsg = print_r ( $response, true );
				//error_log ( "Response " . $this->lastCode . " code received from Megadetector API call: " . $errMsg );
				
				$start = strpos( $response, '{' );
				$end = strrpos( $response, '}' );
				$length = $end - $start + 1;
				$jsonResponse = substr($response, $start, $length );

				$responseDecoded = json_decode($jsonResponse);

				//$errMsg = print_r ( $responseDecoded, true );
				//print ( "<br>Megadetector response decoded = " . $errMsg );
				//

				if ( property_exists($responseDecoded, 'error') ) {

					error_log ( "Megadetector error = " . $responseDecoded->error );
					print ( "<br>Megadetector response = " . $responseDecoded->error );
					$this->cleanUpFiles($imagesToCleanUp);
					return false;
				}
				else {
					print  "<br>Processing detections";	

					foreach ( $responseDecoded as $imageFilename=>$detectionArray ) {
					
						$photoId = $idLookup[$imageFilename];

						$imageFullName = $dir . '/' . $imageFilename;
					
						$responseSuccess = $this->handleResponse ( $photoId, $imageFullName, $detectionArray );
						
						if ( $responseSuccess ) {
							$responseArray[$photoId] = array('photo_id'=>$photoId,
												'status'=>BioDivViewRequestAI::SEND_SUCCESS,
												'msg'=>null);
						}
						else {
							$responseArray[$photoId] = array('photo_id'=>$photoId,
												'status'=>BioDivViewRequestAI::PROCESSING_ERROR,
												'msg'=>'Error writing detections');
						}
					}
	
					if ( !array_key_exists($photoId, $responseArray) ) {
						$responseArray[$photoId] = array('photo_id'=>$photoId,
												'status'=>BioDivViewRequestAI::PROCESSING_ERROR,
												'msg'=>'Error: no response received, possible timeout ');
					}
					$this->cleanUpFiles($imagesToCleanUp);
					return $responseArray;
				}
			}
		}
		
		return $responseArray;
	}



	private function cleanUpFiles ( $fullPathArray ) {

		foreach ( $fullPathArray as $fullFileName ) {

			unlink ( $fullFileName );
		}
	}

	
	
	private function handleResponse ( $photoId, $imageFullName, $detections ) {

		$responseSuccess = true;
		
		$photoDetails = codes_getDetails($photoId, 'photo');

		// If no detections, write a nothing row into the Classify table
		if (  count($detections) == 0 ) {
			$classifyId = $this->helper->classify( $photoDetails['sequence_id'], 
												$photoId,
												'MEGA', 
												$this->modelVersion, 
												null, 
												$photoDetails['site_id'], 
												$photoDetails['filename'], 
												'MEGA', 
												'Nothing', 
												$this->speciesIds[0] 
												 );
			
			// Return response
			if ( !$classifyId ) {
				
				$err_msg = "Failed to write nothing detected to database for imageId " . $imageId;
				error_log ("Process AI error - " . $err_msg );
				$responseSuccess = false;
			
			}
			
			// Note that all images in a sequence need to be blank for a sequence to be blank
			// so this is handled in the processai step.
		}
		else {
			
			// Get image width and height in pixels.
			$imageSizeArray = getimagesize($imageFullName);

			$imageWidth = $imageSizeArray[0];
			$imageHeight = $imageSizeArray[1];

			$detectionNum = 1;
			foreach ( $detections as $detection ) {
				
				$xmin = $detection[0];				
				$ymin = $detection[1];				
				$xmax = $detection[2];				
				$ymax = $detection[3];			
				$prob = $detection[4];
				$megaSpecies = $detection[5];
		
				// Write each detection into the Classify table
				$classifyId = $this->helper->classify( $photoDetails['sequence_id'], 
													$photoId,
													'MEGA', 
													$this->modelVersion, 
													null, 
													$photoDetails['site_id'], 
													$photoDetails['filename'], 
													'MEGA', 
													$megaSpecies, 
													$this->speciesIds[$megaSpecies], 
													$prob,
													$xmin*$imageWidth,
													$ymin*$imageHeight,
													$xmax*$imageWidth,
													$ymax*$imageHeight);
				
				// Return response
				if ( !$classifyId ) {
					
					$err_msg = "Failed to write to database for imageId " . $imageId . ", detection " . $detectionNum;
					error_log ("Process AI error - " . $err_msg );
					$responseSuccess = false;
				
				}
				
				$detectionNum += 1;
			}
		}
		
		return $responseSuccess;
	}

}

?>
