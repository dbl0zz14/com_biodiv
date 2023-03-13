<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains functions to check user recaptcha was successful.

// No direct access to this file
defined('_JEXEC') or die;


class BiodivRecaptcha {
	
	private $recaptchaOptions;
	private $sitekey;
	private $secretkey;
	private $lastError;
	
	
	function __construct()
	{
		$this->lastError = null;
		$this->recaptchaOptions = recaptchaOptions();
		if ( $this->recaptchaOptions ) {
			$this->sitekey = $this->recaptchaOptions['sitekey'];
			$this->secretkey = $this->recaptchaOptions['secretkey'];
		}
	}
	
	
	public static function checkUserRecaptcha ( $clientResponse ) {
		
		$recapChecker = new self();
		
		return $recapChecker->checkRecaptcha ( $clientResponse );
	
	}
	
	
	private function checkRecaptcha ( $clientResponse ) {
		
		if ( !$this->recaptchaOptions ) {
			return true;
		}
		else {
			
			// set post fields
			$post = [
				'secret' => $this->secretkey,
				'response' => $clientResponse,
			];

			$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

			// execute!
			$apiResponse = curl_exec($ch);
			$err = curl_error($ch);

			// close the connection, release resources used
			curl_close($ch);

			
			if ($err) {
				error_log ( "cURL Error #:" . $err );
				$this->lastError = "cURL Error #:" . $err;
				return false;
			}
			
			$responseDecoded = json_decode($apiResponse);
			if ( property_exists($responseDecoded, "success") ) {
				return $responseDecoded->success === true;
			}
			else {
				return false;
			}
		}
	}

}

?>