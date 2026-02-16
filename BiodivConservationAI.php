<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains functions to request classifications from the Conservation AI API.

// No direct access to this file
defined('_JEXEC') or die;


class BiodivConservationAI {
	
	private $caiOptions;
	private $endpoint;
	private $email;
	private $model;
	private $project;
	private $authkey;
	private $lastError;
	
	
	function __construct()
	{
		$this->lastCode = null;
		$this->lastError = null;
		$this->lastMessage = null;
		$this->caiOptions = caiOptions();
		if ( $this->caiOptions ) {
			$this->endpoint = $this->caiOptions['endpoint'];
			$this->email = $this->caiOptions['email'];
			$this->model = $this->caiOptions['model'];
			$this->modelVersion = $this->caiOptions['modelversion'];
			$this->project = $this->caiOptions['project'];
			$this->authkey = $this->caiOptions['key'];
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
	
	
	// urls should be an array of objects with properties id (photoId) and url.
	public function classify ( $siteId, $urls ) {
		
		if ( !$this->caiOptions ) {
			return false;
		}
		else {
			
			$this->lastError = "";
			
			$uploadArray = array();
			$uploadArray[] = $this->email;
			$uploadArray[] = $this->model;
			$uploadArray[] = $this->project;
			$uploadArray[] = $siteId;
			$uploadArray[] = 'Yes';
			$uploadArray[] = $this->authkey;
			
			if ( is_array($urls) ) {
				$urlArray = $urls;
			}
			else {
				$urlArray = array();
			}
			
			$data = new stdClass;
			$data->uploaddata = $uploadArray;
			$data->urls = array_values($urlArray);
			
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
				CURLOPT_TIMEOUT => 300,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				//CURLOPT_POSTFIELDS => "{\"uploaddata\":[".$this->email.",".$this->model.",".$this->project.",".$this->siteId.",\"Yes\",".$this->authkey.",".$urls."]}",
				CURLOPT_POSTFIELDS => $dataJson,
				CURLOPT_HTTPHEADER => [
				"accept: application/vnd.api+json",
				"authorization: Bearer ".$this->authkey,
				"content-type: application/vnd.api+json"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);
			$this->lastCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			//error_log ( "response = " . $response );
			
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
				error_log ( "Non-200 error code (" .$this->lastCode. ") received from Conservation AI API call: " . $errMsg );
				$this->lastError = "Non-200 response code (" .$this->lastCode. ") received from Conservation AI API call, response: " . $errMsg;
				return false;
			  
			}
			else {
				// $errMsg = print_r ( $response, true );
				// error_log ( "200 error code received from Conservation AI API call: " . $errMsg );
				if ( property_exists($responseDecoded, "message") ) {
					$this->lastMessage = $responseDecoded->message;
				}
				return true;
			}
		}
	}

}

?>
