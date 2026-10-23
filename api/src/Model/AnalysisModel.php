<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MammalWeb\Component\Biodiv\Api\Model;



\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

use MammalWeb\Component\Biodiv\Site\Library\Wrapper\Auth;
use MammalWeb\Component\Biodiv\Site\Library\Core\BiodivHelper;



/**
 * Biodiv Component Analysis API Model
 *
 * @since  1.6
 */
class AnalysisModel extends BiodivItemModel
{
	public function getItem ( $pk = null ) {
		
		$result = new \StdClass();
		
		
		$auth = new Auth();
		$helper = new BiodivHelper();
		$scopestem = $helper->getScopeStem();
		
		//error_log ("Method = GET" );
	
		$isok = false;
		
		try {
			// Require create here as this is only used when creating, no route to get.
			$isok = $auth->checkToken( $scopestem."/create_analysis");
		}
		catch ( \Exception $e ) {
			error_log("Exception caught out of BiodivAuth");
			error_log("Exception message: " . $e->getMessage() );
			
			throw new \Joomla\CMS\Access\Exception\NotAllowed($e->getMessage(), 11001, $e);

			
		}
		
		error_log ("Auth check returns " . $isok );
	
		if ( $isok ) {
			
			$analysisId = $this->state->get('analysis.id', 0);
			
			if ( !$analysisId ) {
				
				throw new \Tobscure\JsonApi\Exception\InvalidParameterException("No analysis id supplied", 10000);
				
			}
			else {
				
				// Extend this if needed in future to get details
				$result = new \stdClass;
				$result->id = $analysisId;
				return $result;
			}
		}
		else {
			error_log ("Auth failed" );
			
			throw new \Joomla\CMS\Access\Exception\NotAllowed( "Auth failed" );
		}
	}
	
	
	public function saveItem ( $data )
	{
		$auth = new Auth();
		$helper = new BiodivHelper();
		$scopestem = $helper->getScopeStem();
		
		$isok = false;
		
		try {
			$isok = $auth->checkToken( $scopestem."/create_analysis");
		}
		catch (Exception $e ) {
			error_log("Exception caught out of BiodivAuth");
			
			throw new \Joomla\CMS\Access\Exception\NotAllowed($e->getMessage(), 11001);
						
		}	
		
		if ( $isok ) {
			
			$app = Factory::getApplication();
		
			$input = $app->input;
			
			// Get the details from the request.
			$inArray = $input->json->getArray();
			
			// Assumes first parameter is type
			//$analysisType = $input->getString('type', null);
			
			//$analysisType = $this->state->get('analysis.type', 0);
			$analysisType = $input->post->getString('type', null);
			
			if ( $analysisType == 'ruleofthumb' ) {
		
				// data = {
					  // ‘origin: ‘MammalWeb’,
					  // ‘ai_type: ‘CAI’,
					  // ‘ai_version’: ‘UK Mammals V7’,
					  // ‘analysis_version’: ‘Rule of thumb 1’, 
					  // ‘sequence_id’: 1234567,
					  // ‘species’:[34,20,20]
				// }

				
				// Do some validation
				$isValid = true;
				$err_msg = null;
				$err_code = 100000;
				if (!array_key_exists("origin", $inArray)) {
					$isValid = false;
					$err_msg = "No origin system supplied";
					$err_code = 100005;
				}
				else if (!array_key_exists("ai_type", $inArray)) {
					$isValid = false;
					$err_msg = "No AI type supplied";
					$err_code = 100017;
				}
				else if (!array_key_exists("ai_version", $inArray)) {
					$isValid = false;
					$err_msg = "No AI version supplied";
					$err_code = 100018;
				}
				else if (!array_key_exists("analysis_version", $inArray)) {
					$isValid = false;
					$err_msg = "No analysis version supplied";
					$err_code = 100019;
				}
				else if (!array_key_exists("sequence_id", $inArray)) {
					$isValid = false;
					$err_msg = "No sequence_id supplied";
					$err_code = 100020;
				}
				else if (!array_key_exists("species", $inArray)) {
					$isValid = false;
					$err_msg = "No species supplied";
					$err_code = 100012;
				}
				
				$species = null;
				
				if ( $isValid ) {
					$species = $inArray["species"];
					if ( ! is_array($species) ) {
						$isValid = false;
						$err_msg = "Species should be array";
						$err_code = 100021;
					}
				}
				
				if (!$isValid) {
					error_log ("Incorrect parameters - " . $err_msg );
					
					throw new \Tobscure\JsonApi\Exception\InvalidParameterException($err_msg, $err_code);
				
				}
				else {
					
					$origin = $inArray["origin"];
					$aiType = $inArray["ai_type"];
					$aiVersion = $inArray["ai_version"];
					$analysisVersion = $inArray["analysis_version"];
					$sequenceId = $inArray["sequence_id"];
					
					
					// Write to database
					$analysisIds = $helper->ruleOfThumb( $origin, 
														$aiType,
														$aiVersion, 
														$analysisVersion, 
														$sequenceId, 
														$species );
					
					// Return response
					if ( !$analysisIds ) {
						
						$isValid = false;
						$err_msg = "Failed to write to database for sequence " . $sequenceId;
						$err_code = 100016;
				
						error_log ("Classify API error - " . $err_msg );
						
						throw new \Joomla\CMS\MVC\Controller\Exception\Save($err_msg, $err_code);
					
					}
					else {
						
						if ( is_array($analysisIds) ) {
							return implode('_', $analysisIds);
						}
						else {
							$isValid = false;
							$err_msg = "Failed to write to database - non-array returned - for sequence " . $sequenceId;
							$err_code = 100016;
					
							error_log ("Classify API error - " . $err_msg );
							
							throw new \Joomla\CMS\MVC\Controller\Exception\Save($err_msg, $err_code);
						}
						
						// header('Access-Control-Allow-Credentials: true');
						// header('Access-Control-Allow-Origin: *');

						// $result = new \stdClass;
						// $result->id = $analysisId;
						
						// $this->plugin->setResponse( $result );
						
					}
				}
			}
		}
		else {
			// For now respond Forbidden but could be not authorized and should distinguish between the two.
			$err_msg = "Exception raised as auth check returned false";
			error_log ( $err_msg );
			
			throw new \Joomla\CMS\Access\Exception\NotAllowed($err_msg, 11001);
			
		}
		
	}
	
	
}
