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
class BioDivViewSelectArticle extends JViewLegacy
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
		$this->defaultNumPerPage = 20;
		
		$input = JFactory::getApplication()->input;
		
		$this->purpose = $input->getString('purpose', 0);
		$this->page = $input->getString('page', 1);
		$this->length = $input->getString('length', $this->defaultNumPerPage);
		
		$searchInput = $input->getString('search', 0);
			
		$this->searchStr = null;
		
		if ( $searchInput ) {
			$this->searchStr = filter_var($searchInput, FILTER_SANITIZE_STRING);
			error_log ( "Sanitized search str = " . $this->searchStr );
		}
		
		$articleObj = getAllArticlesForTranslation( $this->page, $this->length, $this->searchStr );
		
		$this->totalNumArticles = $articleObj->total;
		$this->articles = $articleObj->articles;
		
		if ( $this->length > 0 ) {
			$remainder = $this->totalNumArticles%$this->length;
			if ( $remainder > 0 ) {
				$this->totalNumPages = intval($this->totalNumArticles/$this->length) + 1;
			}
			else {
				$this->totalNumPages = intval($this->totalNumArticles/$this->length);
			}
		}
		else {
			$this->totalNumPages = 1;
		}
		
		if ( $this->purpose == "gettranslations" ) {
			$this->languages = getSupportedLanguages();
		}
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}