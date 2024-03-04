<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains functions to request classifications from the Conservation AI API.

// No direct access to this file
defined('_JEXEC') or die;


class BiodivOctopus {
	
	private $octopusOptions;
	private $endpoint;
	private $listid;
	private $authkey;
	private $lastError;
	
	
	function __construct()
	{
		$this->lastCode = null;
		$this->lastError = null;
		$this->octopusOptions = octopusOptions();
		if ( $this->octopusOptions ) {
			$this->endpoint = $this->octopusOptions['endpoint'];
			$this->listid = $this->octopusOptions['listid'];
			$this->authkey = $this->octopusOptions['key'];
		}
	}
	
	
	public function getLastCode () {
		
		return $this->lastCode;
	}
	
	
	public function getLastError () {
		
		return $this->lastError;
	}
	
	
	public function subscribe ( $email, $name ) {
		
		$ch = curl_init();
		
		$url = $this->endpoint . '/lists/' . $this->listid . '/contacts';

		curl_setopt($ch, CURLOPT_URL, $url );
		// 'https://emailoctopus.com/api/1.6/lists/00000000-0000-0000-0000-000000000000/contacts');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		
		$fields = '"fields":{}';
		if ( $name ) {
			$fields = '"fields":{"FirstName":"'.$name.'"}';
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, '{"api_key":"'.$this->authkey.'","email_address":"'.$email.'",'.$fields.'}');

		$headers = array();
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		$retValue = true;
		if (curl_errno($ch)) {
			$err = curl_error($ch);
			echo 'Error:' . $err;
			$this->lastError = "cURL Error #:" . $err;
			$retValue = false;
		}
		curl_close($ch);
		
		return $retValue;
	}

}

?>