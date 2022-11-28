<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains common functions used to work with the Transifex API to send files for translation and get translated files.

// No direct access to this file
defined('_JEXEC') or die;


class BiodivTransifex {
	
	private $key;
	private $org;
	private $project;
	private $lastError;
	private static $languageMap = null;
	
	
	function __construct()
	{
		$trOptions = translateOptions();
		$this->key = $trOptions['key'];
		$this->org = $trOptions['organisation'];
		$this->project = $trOptions['project'];
		$this->lastError = "";
		
	}
	
	
	public function getLastError () {
		return $this->lastError;
	}
	
	
	public static function getLanguageMap () {
		if ( !self::$languageMap ) {
			self::$languageMap = array();
			$allLangs = getSupportedLanguages();
			foreach ( $allLangs as $lang ) {
				self::$languageMap[$lang->tag] = $lang->transifex_code;
			}
		}
		return self::$languageMap;
	}
	
	
	public function articleExists ( $articleId ) {
		
		$this->lastError = "";
		
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://rest.api.transifex.com/resources/o:".$this->org.":p:".$this->project.":r:article-" . $articleId,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
			"accept: application/vnd.api+json",
			"authorization: Bearer ".$this->key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);
		
		if ( $err ) {
			error_log ( "cURL Error #:" . $err );
			$this->lastError = "cURL Error #:" . $err;
			return null;
		}
		
		if ( $code == 200) {
			return true;
		}
		else if ( $code == 404) {
			return false;
		}
		else {
			$this->lastError = "articleExists unexpected code returned: " . $code;
			return null;
		}

	}
	
	public function updateArticle ( $articleId, $title, $articleHtml ) {
		
		$this->lastError = "";
		
		$articleText = "<h2>".$title."</h2>" . $articleHtml;
		$articleBase64 = base64_encode ( $articleText );
		
		$curl = curl_init();
					
		curl_setopt_array($curl, [
			CURLOPT_URL => "https://rest.api.transifex.com/resource_strings_async_uploads",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\"data\":{\"attributes\":{\"callback_url\":null,\"replace_edited_strings\":false,\"content\":\"".$articleBase64."\",\"content_encoding\":\"base64\"},\"relationships\":{\"resource\":{\"data\":{\"type\":\"resources\",\"id\":\"o:".$this->org.":p:".$this->project.":r:article-".$articleId."\"}}},\"type\":\"resource_strings_async_uploads\"}}",
			CURLOPT_HTTPHEADER => [
			"accept: application/vnd.api+json",
			"authorization: Bearer ".$this->key,
			"content-type: application/vnd.api+json"
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			error_log ( "cURL Error #:" . $err );
			$this->lastError = "cURL Error #:" . $err;
			return false;
		}
		
		return $response;
	}

	
	public function newArticle ( $articleId, $title, $articleHtml ) {
		
		$this->lastError = "";
		
		$articleText = "<h2>".$title."</h2>" . $articleHtml;
		$articleBase64 = base64_encode ( $articleText );
		
		$curl = curl_init();
				
		curl_setopt_array($curl, [
			CURLOPT_URL => "https://rest.api.transifex.com/resources",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\"data\":{\"attributes\":{\"accept_translations\":true,\"slug\":\"article-".$articleId."\",\"name\":\"".$title."\"},\"relationships\":{\"i18n_format\":{\"data\":{\"type\":\"i18n_formats\",\"id\":\"HTML_FRAGMENT\"}},\"project\":{\"data\":{\"type\":\"projects\",\"id\":\"o:".$this->org.":p:".$this->project."\"}}},\"type\":\"resources\"}}",
			CURLOPT_HTTPHEADER => [
			"accept: application/vnd.api+json",
			"authorization: Bearer ".$this->key,
			"content-type: application/vnd.api+json"
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		if ($err) {
			error_log ( "cURL Error #:" . $err );
			$this->lastError = "cURL Error #:" . $err;
			return false;
		}
		else {
			
			$curl = curl_init();
			
			curl_setopt_array($curl, [
				CURLOPT_URL => "https://rest.api.transifex.com/resource_strings_async_uploads",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "{\"data\":{\"attributes\":{\"callback_url\":null,\"replace_edited_strings\":false,\"content\":\"".$articleBase64."\",\"content_encoding\":\"base64\"},\"relationships\":{\"resource\":{\"data\":{\"type\":\"resources\",\"id\":\"o:".$this->org.":p:".$this->project.":r:article-".$articleId."\"}}},\"type\":\"resource_strings_async_uploads\"}}",
				CURLOPT_HTTPHEADER => [
				"accept: application/vnd.api+json",
				"authorization: Bearer ".$this->key,
				"content-type: application/vnd.api+json"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				error_log ( "cURL Error #:" . $err );
				$this->lastError = "cURL Error #:" . $err;
				return false;
			}
		  
		}
		
		return $response;
		
	}
	
	public function getTranslation ( $articleId, $lang ) {
		
		$this->lastError = "";
		
		$articleStatus = new StdClass();
		$articleStatus->id = $articleId;
		$articleStatus->language = $lang;
									
		$languages = self::getLanguageMap();
		
		$transifexCode = $languages[$lang];
		
		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => "https://rest.api.transifex.com/resource_translations_async_downloads",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\"data\":{\"attributes\":{\"callback_url\":null,\"content_encoding\":\"text\",\"file_type\":\"default\",\"mode\":\"default\",\"pseudo\":false},\"relationships\":{\"language\":{\"data\":{\"type\":\"languages\",\"id\":\"l:".$transifexCode."\"}},\"resource\":{\"data\":{\"type\":\"resources\",\"id\":\"o:".$this->org.":p:".$this->project.":r:article-".$articleId."\"}}},\"type\":\"resource_translations_async_downloads\"}}",
			CURLOPT_HTTPHEADER => [
			"accept: application/vnd.api+json",
			"authorization: Bearer ".$this->key,
			"content-type: application/vnd.api+json"
			],
		]);


		$response = curl_exec($curl);
		$err = curl_error($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($err) {
			$articleStatus->error = "getTranslation cURL Error #:" . $err;
			$this->lastError = $articleStatus->error;
		} 
		else {
			
			$responseObject = json_decode($response);
				
			$errMsg = print_r ( $responseObject, true );
			error_log ( "Response: " . $errMsg );
				
			if ( property_exists ( $responseObject, "errors" ) ) {
				
				error_log ( "Found errors for article " . $articleId );
				//$articleStatus = new StdClass();
				// $articleStatus->id = $articleId;
				// $articleStatus->language = $lang;
				$articleStatus->error = "Article " . $articleId . " for language " . $lang . " not found in Transifex - not yet uploaded for translation";
				//$this->statusResponses[$articleId][$lang] = $articleStatus;
				$this->lastError = $articleStatus->error;
				
			}
			else {
				
				$uuid = $responseObject->data->id;
				
				//error_log ( "Uuid = " . $uuid );
				
				$code = 200;
				$complete = false;
				$maxTries = 20;
				$numTries = 0;
				while ( $code == 200 and !$complete and ($numTries < $maxTries) ) {
					
					$curl = curl_init();

					curl_setopt_array($curl, [
					  CURLOPT_URL => "https://rest.api.transifex.com/resource_translations_async_downloads/".$uuid,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 30,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "GET",
					  CURLOPT_HTTPHEADER => [
						"accept: application/vnd.api+json",
						"authorization: Bearer ".$this->key
					  ],
					]);
					
					//error_log ( "About to call curl_exec" );

					$statusResponse = curl_exec($curl);
					
					//error_log ( "curl_exec completed" );
					
					$err = curl_error($curl);
					$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					
					
					//error_log ( "curl_error and getinfo completed, code = " . $code );

					curl_close($curl);

					if ($err) {
						//echo "cURL Error #:" . $err;
						$complete = true;
					} 
					else {
						
						if ( $code == 200 ) {
							
							//$articleStatus = new StdClass();
							//$articleStatus->id = $articleId;
							//$articleStatus->language = $lang;
							
							$decodedArray = getJSONDecodedArray ( $statusResponse );
							if ( $decodedArray ) {
								$errors = $decodedArray['data']['attributes']['errors'];
								if ( count ($errors) > 0 ) {
									$complete = true;
									//$articleStatus = new StdClass();
									// $articleStatus->id = $articleId;
									// $articleStatus->language = $lang;
									$articleStatus->error = "Article " . $articleId . " not available for download, errors: " . implode ( ',', $errors);
									$this->lastError = $articleStatus->error;
									//$this->statusResponses[$articleId][$lang] = $articleStatus;
								}
								else if ( $decodedArray['data']['attributes']['status'] == 'processing' ) {
									$complete = false;
									error_log ("Still processing, try " . strval($numTries + 1) . ", try again" );
								}
								else {
									$complete = true;
									//$articleStatus = new StdClass();
									// $articleStatus->id = $articleId;
									// $articleStatus->language = $lang;
									$articleStatus->error = "Article " . $articleId . " not available for download, unknown problem, possibly slow connection";
									$this->lastError = $articleStatus->error;
									//$this->statusResponses[$articleId][$lang] = $articleStatus;
								}
							}
							else {
								if ( $issue = checkHtml ( $statusResponse ) ) {
									$articleStatus->warning = $issue;
								}
								$endOfTitleTag = stripos($statusResponse, "</h2>");
								if ( $endOfTitleTag !== false ) {
									$trTitle = substr($statusResponse, 4, $endOfTitleTag - 4);
									$trText = substr($statusResponse, $endOfTitleTag + 5);
								}
								$articleStatus->title = $trTitle;
								$articleStatus->text = $trText;
								$articleStatus->escapedText = htmlspecialchars($statusResponse, ENT_QUOTES);
								//$this->statusResponses[$articleId][$lang] = $articleStatus;
								$complete = true;
							}

						}
						else {
							//$articleStatus = new StdClass();
							// $articleStatus->id = $articleId;
							// $articleStatus->language = $lang;
							$articleStatus->error = "Article " . $articleId . " not available for download";
							$this->lastError = $articleStatus->error;
							//$this->statusResponses[$articleId][$lang] = $articleStatus;
						}
					}
					$numTries += 1;
				}
			}
			
			return $articleStatus;
		}
		
		
	}
}

?>