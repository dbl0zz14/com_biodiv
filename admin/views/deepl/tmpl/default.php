<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

printAdminMenu("TRANSLATIONS");

print '<div id="j-main-container" class="span10 j-toggle-main">';

	
	print '<h2>Select which articles to download translations for</h2>';

	print '<form id="translateIniFile" action="'. BIODIV_ADMIN_ROOT . '&view=generatedeepl" method="post">';

	print '<p>';
	print '<label for="trLang">Choose a language (or select All):</label>';
	print '<select id="trLang" name="trLang">';
	foreach ( $this->targetLanguages as $targetLanguage ) {
		print '<option value="'.$targetLanguage->code.'">'.$targetLanguage->name.'</option>';
	}
	print '</select><br>';
	print '</p>';
		
		
	print '<button type="submit" class="btn js-stools-btn-clear" >Translate to selected language</button>';
	
	
	// $result = $this->translator->translateText('Hello, world!', null, 'fr');
	// print '<p>Translation of hello world: ' . $result->text . '</p>'; // Bonjour, le monde!
	
	// $sourceLanguages = $this->translator->getSourceLanguages();
	// foreach ($sourceLanguages as $sourceLanguage) {
		// print '<p>' . $sourceLanguage->name . ' (' . $sourceLanguage->code . ')</p>'; // Example: 'English (en)'
	// }

	// $targetLanguages = $this->translator->getTargetLanguages();
	// foreach ($targetLanguages as $targetLanguage) {
		// if ($targetLanguage->supportsFormality) {
			// print '<p>' . $targetLanguage->name . ' (' . $targetLanguage->code . ') supports formality</p>';
			// // Example: 'German (de) supports formality'
		// }
	// }


	print '</form>';


print '</div>';



JHTML::script("com_biodiv/admin.js", true, true);

?>
