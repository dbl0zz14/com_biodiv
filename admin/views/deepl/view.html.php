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
class BioDivViewDeepL extends JViewLegacy
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
		//$input = JFactory::getApplication()->input;
		
		// $this->purpose = $input->getString('purpose', 0);
		// $this->page = $input->getString('page', 1);
		// $this->length = $input->getString('length', $this->defaultNumPerPage);
		
		// $articleObj = getAllArticlesForTranslation( $this->page, $this->length );
		
		// $this->totalNumArticles = $articleObj->total;
		// $this->articles = $articleObj->articles;
		
		// if ( $this->length > 0 ) {
			// $remainder = $this->totalNumArticles%$this->length;
			// if ( $remainder > 0 ) {
				// $this->totalNumPages = intval($this->totalNumArticles/$this->length) + 1;
			// }
			// else {
				// $this->totalNumPages = intval($this->totalNumArticles/$this->length);
			// }
		// }
		// else {
			// $this->totalNumPages = 1;
		// }
		
		$this->languages = getSupportedLanguages();
		
		$translateOpts = translateOptions();
		$authKey = $translateOpts['deepl']; 
		$this->translator = new \DeepL\Translator($authKey);

		$this->targetLanguages = $this->translator->getTargetLanguages();
	
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}