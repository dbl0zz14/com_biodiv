<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains functions to request classifications from the Conservation AI API.

// No direct access to this file
defined('_JEXEC') or die;


class BiodivAnalysis {
	
	private $analysisOptions;
	private $endpoint;
	private $authkey;
	private $lastCode;
	private $lastError;
	private $lastMessage;
	
	
	function __construct()
	{
		$this->lastCode = null;
		$this->lastError = null;
		$this->lastMessage = null;
		$this->analysisOptions = analysisOptions();
		if ( $this->analysisOptions ) {
			$this->endpoint = $this->analysisOptions['endpoint'];
			//$this->authkey = $this->analysisOptions['key'];
		}
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
	
	
	public function testAnalysis () {
		
		error_log ( "BiodivAnalysis::testAnalysis called" );

		if ( !$this->analysisOptions ) {
			return false;
		}
		else {
			
			$this->lastError = "";
			
			
			$data = new stdClass;
			$data->message = "Hello from MammalWeb";
			
			$dataJson = json_encode ( $data );
			
			//error_log ( "About to post with data: " . $dataJson );
			$this->lastError = null;
			$this->lastMessage = null;
				
			
			$curl = curl_init();
			
			curl_setopt_array($curl, [
				CURLOPT_URL => $this->endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				//CURLOPT_POSTFIELDS => "{\"uploaddata\":[".$this->email.",".$this->model.",".$this->project.",".$this->siteId.",\"Yes\",".$this->authkey.",".$urls."]}",
				CURLOPT_POSTFIELDS => $dataJson,
				CURLOPT_HTTPHEADER => [
				"accept: application/vnd.api+json",
				//"authorization: Bearer ".$this->authkey,
				"content-type: application/vnd.api+json"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			$this->lastCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			error_log ( "response = " . $response );
			
			$responseDecoded = json_decode($response);
			
			// if ( property_exists($responseDecoded, "success") ) {
				// return $responseDecoded->success === true;
			// }

			curl_close($curl);
			
			if ($err) {
				error_log ( "cURL Error #:" . $err );
				$this->lastError = "cURL Error #:" . $err;
				return false;
			}
			else if ( $this->lastCode != 200 ) {
				
				$errMsg = print_r ( $response, true );
				error_log ( "Non-200 error code (" .$this->lastCode. ") received from Analysis API call: " . $errMsg );
				$this->lastError = "Non-200 response code (" .$this->lastCode. ") received from Analysis API call, response: " . $errMsg;
				return false;
			  
			}
			else {
				// $errMsg = print_r ( $response, true );
				// error_log ( "200 error code received from Analysis API call: " . $errMsg );
				if ( property_exists($responseDecoded, "message") ) {
					$this->lastMessage = $responseDecoded->message;
				}
				return $response;
			}
		}
	}
	
	public function testRuleOfThumb () {
		
		error_log ( "BiodivAnalysis::testRuleOfThumb called" );

		if ( !$this->analysisOptions ) {
			return false;
		}
		else {
			
			$this->lastError = "";
			
			
			$data = new stdClass;
			$data->ai_type = "testType";
			$data->ai_version = "testVersion";
			$data->sequence_id = 350;
			$data->human_species = [34,20,20,20];
			$data->ai_species = [20,20];
			
			$dataJson = json_encode ( $data );
			
			//error_log ( "About to post with data: " . $dataJson );
			$this->lastError = null;
			$this->lastMessage = null;
				
			
			$curl = curl_init();
			
			curl_setopt_array($curl, [
				CURLOPT_URL => $this->endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				//CURLOPT_POSTFIELDS => "{\"uploaddata\":[".$this->email.",".$this->model.",".$this->project.",".$this->siteId.",\"Yes\",".$this->authkey.",".$urls."]}",
				CURLOPT_POSTFIELDS => $dataJson,
				CURLOPT_HTTPHEADER => [
				"accept: application/vnd.api+json",
				//"authorization: Bearer ".$this->authkey,
				"content-type: application/vnd.api+json"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			$this->lastCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			error_log ( "response = " . $response );
			
			$responseDecoded = json_decode($response);
			
			// if ( property_exists($responseDecoded, "success") ) {
				// return $responseDecoded->success === true;
			// }

			curl_close($curl);
			
			if ($err) {
				error_log ( "cURL Error #:" . $err );
				$this->lastError = "cURL Error #:" . $err;
				return false;
			}
			else if ( $this->lastCode != 200 ) {
				
				$errMsg = print_r ( $response, true );
				error_log ( "Non-200 error code (" .$this->lastCode. ") received from Analysis API call: " . $errMsg );
				$this->lastError = "Non-200 response code (" .$this->lastCode. ") received from Analysis API call, response: " . $errMsg;
				return false;
			  
			}
			else {
				// $errMsg = print_r ( $response, true );
				// error_log ( "200 error code received from Analysis API call: " . $errMsg );
				if ( property_exists($responseDecoded, "message") ) {
					$this->lastMessage = $responseDecoded->message;
				}
				return $response;
			}
		}
	}
	
	public function ruleOfThumb ( $aiType, $aiVersion, $sequenceId, $humanSpecies, $aiSpecies ) {
		
		error_log ( "BiodivAnalysis::ruleOfThumb called" );

		if ( !$this->analysisOptions ) {
			return false;
		}
		else {
			
			$this->lastError = "";
			$foundError = false;
			
			$data = new stdClass;
			$data->ai_type = $aiType;
			$data->ai_version = $aiVersion;
			$data->sequence_id = $sequenceId;
			if ( is_array($humanSpecies) ) {
				$data->human_species = $humanSpecies;
			}
			else {
				$foundError = true;
			}
			if ( !$foundError && is_array($aiSpecies) ) {
				$data->ai_species = $aiSpecies;
			}
			else {
				$foundError = true;
			}
			
			if ( $foundError ) {
				$this->lastError = "Found species error - human or ai species not array";
				return false;
			}
			
			$dataJson = json_encode ( $data );
			
			error_log ( "About to post with data: " . $dataJson );
			
			print ( "About to post with data: " . $dataJson );
			$this->lastError = null;
			$this->lastMessage = null;
				
			
			$curl = curl_init();
			
			curl_setopt_array($curl, [
				CURLOPT_URL => $this->endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				//CURLOPT_POSTFIELDS => "{\"uploaddata\":[".$this->email.",".$this->model.",".$this->project.",".$this->siteId.",\"Yes\",".$this->authkey.",".$urls."]}",
				CURLOPT_POSTFIELDS => $dataJson,
				CURLOPT_HTTPHEADER => [
				"accept: application/json",
				//"authorization: Bearer ".$this->authkey,
				"content-type: application/json"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			$this->lastCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			error_log ( "response = " . $response );
			
			$responseDecoded = json_decode($response);
			
			// if ( property_exists($responseDecoded, "success") ) {
				// return $responseDecoded->success === true;
			// }

			curl_close($curl);
			
			if ($err) {
				error_log ( "cURL Error #:" . $err );
				$this->lastError = "cURL Error #:" . $err;
				return false;
			}
			else if ( $this->lastCode != 200 ) {
				
				$errMsg = print_r ( $response, true );
				error_log ( "Non-200 error code (" .$this->lastCode. ") received from Analysis API call: " . $errMsg );
				$this->lastError = "Non-200 response code (" .$this->lastCode. ") received from Analysis API call, response: " . $errMsg;
				return false;
			  
			}
			else {
				// $errMsg = print_r ( $response, true );
				// error_log ( "200 error code received from Analysis API call: " . $errMsg );
				if ( property_exists($responseDecoded, "message") ) {
					$this->lastMessage = $responseDecoded->message;
				}
				return $response;
			}
		}
	}

}

?>
