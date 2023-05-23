<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Biodivs View
 *
 * @since  0.0.1
 */
class BioDivViewGenerateDeepL extends JViewLegacy
{
	/**
	 * Display the Biodivs view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$this->language = $input->getString('trLang', 0);
		$this->options = $input->getInt('opt', 0);
		
		error_log ( "Got language: " . $this->language );
		
		if ( $this->options ) {
			$this->title = "Download translated non-species options file";
		}
		else {
			$this->title = "Download translated ini file";
		}

		$translateOpts = translateOptions();
		$authKey = $translateOpts['deepl']; 
		$this->translator = new \DeepL\Translator($authKey);
		
		if ( $this->options ) {
			
			// get the options rows
			$this->optionsRows = getNonSpeciesOptionsForTranslation();
			
			// $optionsStr = print_r ( $this->optionsRows, true );
			// error_log ( 'Got options: ' . $optionsStr );
			
			$optionsCol = array();
			foreach ( $this->optionsRows as $row ) {
				$optionsCol[] = $row->option_name;
			}
			
			
			
			$translatedStrings = null;
			try {
				
				$translatedStrings = $this->translator->translateText(
					$optionsCol,
					'en',
					$this->language,
				);
			} catch (Exception $error) {
				echo 'Error occurred while translating strings: ' . ($error->getMessage() ?? 'unknown error');
				
			}
			
			// Now update the rows for the required language
			$numRows = count($this->optionsRows);
			if ( $numRows == count($translatedStrings) ) {
				
				$errMsg = print_r ( $translatedStrings, true );
				error_log ("Got correct num translations: " . $errMsg);
				
				$languages = getSupportedLanguages();
				
				// Find the Transifex lang code
				$trCode = "no_lang";
				foreach ( $languages as $lang ) {
					if ( $lang->deepl_code == $this->language ) {
						$trLang = $lang->transifex_code;
					}
				}
				
				$optionProp = strtolower($trLang);
				
				$i = 0;
				foreach ( $this->optionsRows as $optionRow ) {
					$translation = $translatedStrings[$i];
					$optionRow->$optionProp = $translation->text;
					$i++;
				}
				
				// $optionsStr = print_r ( $this->optionsRows, true );
				// error_log ( 'Options plus translations: ' . $optionsStr );
				
				
				$headings = getOptionsHeadings();
				
				$rows = array();
				foreach ( $this->optionsRows as $optionId=>$option ) {
				
					$optionsArray = array($optionId, 
										str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->option_name)),
										$option->struc );
										
										
					foreach ($languages as $id=>$lang) {
						$col = strtolower($lang->transifex_code);
						$prop = str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->$col));
						$optionsArray[] = $prop;
					}
										
					array_push($rows, $optionsArray);
				}
				
				$this->reportURL = createReportFile ( "OptionsForTranslation", $headings, $rows );
			
			}
			else {
				echo "Error: Num translated strings not equal to option strings";
			}
		}
		else {
			// Open the ini file for reading
			$iniFilename = JPATH_SITE . "/language/en-GB/en-GB.com_biodiv.ini";
			
			$t=time();
			$dateStr = date("Ymd_His",$t);
		
			$reportRoot = JPATH_SITE."/biodivimages/reports";
			$filePath = $reportRoot."/admin/";
			$snippetsFilename = $filePath . "snippets" . $dateStr . ".txt";
			
			$iniFile = fopen($iniFilename, "r") or die("Unable to open file!");
			$snippetsFile = fopen($snippetsFilename, "w") or die("Unable to open file!");
			while(!feof($iniFile)) {
				$nextRow = fgets($iniFile);
				$firstQuotePos = strpos ( $nextRow, '"' );
				$lastQuotePos = strrpos ( $nextRow, '"' );
				$snippetLen = $lastQuotePos - $firstQuotePos -1;
				$firstPart = substr ( $nextRow, 0, $firstQuotePos + 1 );
				$toTranslate = substr ( $nextRow, $firstQuotePos + 1, $snippetLen );
				fputs ( $snippetsFile, $toTranslate . "\n" );
			}
			fclose($iniFile);
			fclose($snippetsFile);
			
			$translatedSnippets = $filePath . $this->language ."translatedSnippets" . $dateStr . ".txt";
			
			try {
				$this->translator->translateDocument(
					$snippetsFilename,
					$translatedSnippets,
					'en',
					$this->language,
				);
			} catch (\DeepL\DocumentTranslationException $error) {
				// If the error occurs after the document was already uploaded,
				// documentHandle will contain the document ID and key
				echo 'Error occurred while translating document: ' . ($error->getMessage() ?? 'unknown error');
				if ($error->documentHandle) {
					$handle = $error->documentHandle;
					echo "Document ID: {$handle->documentId}, document key: {$handle->documentKey}";
				} else {
					echo 'Unknown document handle';
				}
			}
			
			// Now create new file for download
			$rows = array();
				
			$iniFile = fopen($iniFilename, "r") or die("Unable to open file!");
			$trFile = fopen($translatedSnippets, "r") or die("Unable to open file!");
			while(!feof($iniFile) and !feof($trFile)) {
				$nextRow = fgets($iniFile);
				$firstQuotePos = strpos ( $nextRow, '"' );
				$lastQuotePos = strrpos ( $nextRow, '"' );
				$snippetLen = $lastQuotePos - $firstQuotePos -1;
				$firstPart = substr ( $nextRow, 0, $firstQuotePos + 1 );
				
				$translatedSnippet = fgets ( $trFile );
				
				$translatedRow = $firstPart . trim($translatedSnippet) . '"';
				array_push($rows, $translatedRow);
			}
			fclose($iniFile);
			fclose($trFile);
			
			$newFilename = $this->language . "com_biodiv";
			$this->reportURL = createReportFileTxt ( $newFilename, $rows );
		}
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}