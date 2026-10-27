<?php

namespace MammalWeb\Component\Biodiv\Site\View\KioskSpecies;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View used as kiosk intro page
 */
class RawView extends BaseHtmlView {

	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		$app = Factory::getApplication();
		
		$input = $app->getInput();

		$optionId = $input->getInt("option_id");
		
		$kioskModel = $this->getModel();
		
		$this->personId = $kioskModel->userID();
		
		$article = $kioskModel->getArticle ( $optionId );
		
		$this->imageSrc = null;
		$this->scientificName = null;
		$this->appearance = null;
		$this->photoAttribution = null;
		$this->title = "";
		
		if ( $article ) {

			$this->title = $article->title;

			$this->introtext = $article->introtext;

			$maxArticleLength = 300;

			//error_log ( "Getting dom document" );
			
			if ( $this->introtext ) {
			
				$dom = new \DomDocument();
				@ $dom->loadHTML(mb_convert_encoding($this->introtext, 'HTML-ENTITIES', 'UTF-8'));

				$imageNodes = $dom->getElementsByTagName('img');
				
				//$this->imageSrc = null;
				
				//error_log ("About to get first image node" );
				if ( $imageNodes->length > 0 ) {
					$this->imageSrc = $imageNodes->item(0)->getAttribute('src');
				}
				
				// $this->scientificName = null;
				// $this->appearance = null;
				// $this->photoAttribution = null;
				
				
				$ps = $dom->getElementsByTagName('p');
				foreach( $ps as $p ) {
					
					$text = (string)$p->textContent;
					//$text = (string)$p->nodeValue;
					
					//error_log ("Next para: " . $text);
					
					$sciName = Text::_("COM_BIODIV_KIOSKSPECIES_SCI_NAME");
					
					if ( stripos ( $text, $sciName ) !== false ) {
						//error_log ("Found scientific name");
						$this->scientificName = substr( $text, strlen($sciName) );
					}
					
					$appearText = Text::_("COM_BIODIV_KIOSKSPECIES_APPEARANCE");
					
					if ( stripos ( $text, $appearText ) !== false ) {
						//error_log ("Found appearance");
						$this->appearance = substr( $text, strlen($appearText), $maxArticleLength );
						
						if ( strlen($text) > $maxArticleLength ) {
							$lastFullStopPos = strrpos ( $this->appearance, "." );
							
							//error_log ( "Pos of last full stop = " . $lastFullStopPos );
							
							if ( $lastFullStopPos !== false ) {
								$this->appearance = substr( $this->appearance, 0, $lastFullStopPos+1);
							}
						}
					}
					
					// NB we just get the first image and the first attribution
					if ( !$this->photoAttribution ) {
						
						$attribText = Text::_("COM_BIODIV_KIOSKSPECIES_ATTRIB");
						
						//error_log ("attrib text = " . $attribText );
						
						if ( stripos ( quotemeta($text), quotemeta($attribText) ) !== false ) {
							//error_log ("Found attribution");
							$this->photoAttribution = $text;
						}
						else {
							// Try sentence starting with Image
							$attribText = Text::_("COM_BIODIV_KIOSKSPECIES_IMAGE");
						
							if ( stripos ( $text, $attribText ) !== false ) {
								//error_log ("Found attribution");
								$this->photoAttribution = substr( $text, strlen($attribText)+1 );
							}
						}
					}
				}
			}
		}
		else {
		}

		parent::display($tpl);
    }
}



?>