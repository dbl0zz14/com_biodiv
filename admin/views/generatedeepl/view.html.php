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
		
		$this->title = "Download translated ini file";

		$translateOpts = translateOptions();
		$authKey = $translateOpts['deepl']; 
		$this->translator = new \DeepL\Translator($authKey);
		
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
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}