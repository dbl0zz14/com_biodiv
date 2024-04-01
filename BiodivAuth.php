<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

require_once __DIR__ . '/libraries/vendor/autoload.php';


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;


class BiodivAuth {
	
	// this is where known keys can be found https://cognito-idp.eu-west-1.amazonaws.com/eu-west-1_n1w24hxK0/.well-known/jwks.json
	
	function checkToken ( $scope ) {
		
		$isAuthorised = false;
		
		$token =  getallheaders ();
		
		if ( !array_key_exists("Authorization", $token) ) {
			error_log("No Authorization header");
			return false;
		}
		
		$auth_header = $token["Authorization"];
		
		// Split up the string...
		$auth_array = explode(' ', $auth_header);
		
		$jwt = $auth_array[1];
		
		// Get the first part of the jwt, the header, decode it and get the kid
		$jwtArray = explode('.', $jwt);
		$jwtHeader = JWT::urlsafeB64Decode($jwtArray[0]);
		
		$decodedHeader = json_decode($jwtHeader);
		
		$kid = $decodedHeader->kid;
		
		// Pick up some environment options:
		$apiOpts = apiOptions();
		
		$region = $apiOpts['region'];
		$userPoolId = $apiOpts['userpool'];
        
		
		$jwksUrl = sprintf('https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json', $region, $userPoolId);
		
		
        $ch = curl_init($jwksUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
        ]);
        $jwks = curl_exec($ch);
			
        if ($jwks) {
			$json = json_decode($jwks, false);
			if ($json && isset($json->keys) && is_array($json->keys)) {
                foreach ($json->keys as $jwk) {
					if ($jwk->kid === $kid) {
						if (isset($jwk->e) && isset($jwk->n)) {
							$rsa = new RSA();
							$rsa->loadKey([
								'e' => new BigInteger(JWT::urlsafeB64Decode($jwk->e), 256),
								'n' => new BigInteger(JWT::urlsafeB64Decode($jwk->n),  256)
							]);
							$key = $rsa->getPublicKey();
						}
                    }
					
                }
            }
			
        }
		
		$decoded = JWT::decode($jwt, new Key($key, 'RS256'));

		
		// Check aud
		
		// Check iss
		
		// Check token_use
		
		// Check scope includes requested scope
		if ( in_array($scope, explode(' ', $decoded->scope) ) ) {
			//error_log("its in scope string");
			$isAuthorised = true;
		}
				
		return $isAuthorised;
	}
	
	function testAuth () {

		$key = "example_key";
		$payload = array(
			"iss" => "http://example.org",
			"aud" => "http://example.com",
			"iat" => 1356999524,
			"nbf" => 1357000000
		);

		/**
		 * IMPORTANT:
		 * You must specify supported algorithms for your application. See
		 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
		 * for a list of spec-compliant algorithms.
		 */
		$jwt = JWT::encode($payload, $key);
		$decoded = JWT::decode($jwt, $key, array('HS256'));
		
		// print($jwt);

		// print_r($decoded);

		/*
		 NOTE: This will now be an object instead of an associative array. To get
		 an associative array, you will need to cast it as such:
		*/

		$decoded_array = (array) $decoded;

		/**
		 * You can add a leeway to account for when there is a clock skew times between
		 * the signing and verifying servers. It is recommended that this leeway should
		 * not be bigger than a few minutes.
		 *
		 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
		 */
		JWT::$leeway = 60; // $leeway in seconds
		$decoded = JWT::decode($jwt, $key, array('HS256'));
			
	}
	
}

?>