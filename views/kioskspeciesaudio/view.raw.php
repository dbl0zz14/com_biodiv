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
* HTML View class for the Biodiversity Monitoring component
*
* @since 0.0.1
*/
class BioDivViewKioskSpeciesAudio extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		
		$this->person_id = (int)userID();

		$option_id = JRequest::getInt("option_id");
		
		$article = getArticle ( $option_id );

		$this->title = $article->title;

		$this->introtext = $article->introtext;

		// Get the text snippets - enables multilingual
		$this->translations = getTranslations("kioskspeciesaudio");
		
		// Check whether we have a locally stored sonogram
		$this->sonogram = null;
		$this->audioAttribution = null;
		
		$sonoJSON = getOptionData ( $option_id, 'sonogram' );
		//$errStr = print_r ( $sonoJSON, true );
		//error_log ( "Sonogram JSON: " . $errStr ); 
			
		if ( count($sonoJSON) > 0 ) {
			$sonoObj = json_decode ( $sonoJSON[0] );
			
			$errStr = print_r ( $sonoObj, true );
			//error_log ( "Sonogram Object: " . $errStr ); 
		
			
			$this->sonogram = $sonoObj->video;
			$this->audioAttribution = $sonoObj->attribution;
		}
		
		$maxArticleLength = 300;

		//error_log ( "Getting dom document" );
		
		$dom = new DomDocument();
		@ $dom->loadHTML(mb_convert_encoding($this->introtext, 'HTML-ENTITIES', 'UTF-8'));

		$imageNodes = $dom->getElementsByTagName('img');
		
		$this->imageSrc = null;
		
		//error_log ("About to get first image node" );
		if ( $imageNodes->length > 0 ) {
			$this->imageSrc = $imageNodes->item(0)->getAttribute('src');
		}
		
		
		$this->iframe = null;
		if ( !$this->sonogram ) {
			
			$iframeNodes = $dom->getElementsByTagName('iframe');
			
			
			
			//error_log ("About to get first iframe node" );
			if ( $iframeNodes->length > 0 ) {
				//error_log ( "iframe found" );
				$this->iframe = $iframeNodes->item(0)->getAttribute('src');
				
				//error_log ( "this->iframe:" );
				//error_log ( $this->iframe );
			}
		}
		
		
		$this->scientificName = null;
		$this->song = null;
		$this->whenHeard = null;
		
		$this->photoAttribution = null;
		
		
		$ps = $dom->getElementsByTagName('p');
		foreach( $ps as $p ) {
			
			$text = $p->textContent;
			
			//error_log ("Next para: " . $text);
			
			$sciName = $this->translations['sci_name']['translation_text'];
			
			if ( stripos ( $text, $sciName ) !== false ) {
				//error_log ("Found scientific name");
				$this->scientificName = substr( $text, strlen($sciName) );
			}
			
			$songText = $this->translations['song']['translation_text'];
			
			if ( stripos ( $text, $songText ) !== false ) {
				//error_log ("Found song");
				$this->song = substr( $text, strlen($songText), $maxArticleLength );
				
				if ( strlen($text) > $maxArticleLength ) {
					$lastFullStopPos = strrpos ( $this->song, "." );
					
					//error_log ( "Pos of last full stop = " . $lastFullStopPos );
					
					if ( $lastFullStopPos !== false ) {
						$this->song = substr( $this->song, 0, $lastFullStopPos+1);
					}
				}
			}
			
			$whenHeardText = $this->translations['when_heard']['translation_text'];
			
			if ( stripos ( $text, $whenHeardText ) !== false ) {
				//error_log ("Found whenHeard");
				$this->whenHeard = substr( $text, strlen($whenHeardText), $maxArticleLength );
				
				if ( strlen($text) > $maxArticleLength ) {
					$lastFullStopPos = strrpos ( $this->whenHeard, "." );
					
					//error_log ( "Pos of last full stop = " . $lastFullStopPos );
					
					if ( $lastFullStopPos !== false ) {
						$this->whenHeard = substr( $this->whenHeard, 0, $lastFullStopPos+1);
					}
				}
			}
			
			/*
			$iframeText = $this->translations['iframe']['translation_text'];
			
			if ( stripos ( $text, $iframeText ) !== false ) {
				error_log ("Found iframe");
				$this->iframe = $text;
			}
			*/
			
			// NB we just get the first image and the first attribution
			if ( !$this->photoAttribution ) {
				
				$attribText = $this->translations['attrib']['translation_text'];
				
				//error_log ("attrib text = " . $attribText );
				
				if ( stripos ( quotemeta($text), quotemeta($attribText) ) !== false ) {
					//error_log ("Found attribution");
					$this->photoAttribution = $text;
				}
				else {
					// Try sentence starting with Image
					$attribText = $this->translations['image']['translation_text'];
				
					if ( stripos ( $text, $attribText ) !== false ) {
						//error_log ("Found attribution");
						$this->photoAttribution = substr( $text, strlen($attribText) );
					}
				}
			}
		}

		
		//error_log ("Number of images = " . $imageNodes->length );
		
		//error_log ("Image src = " . $this->imageSrc );



		

		parent::display($tpl);
    }
}



?>