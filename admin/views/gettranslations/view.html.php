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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewGetTranslations extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$input = JFactory::getApplication()->input;
		
		$this->articles = $input->get('article', array(), 'ARRAY');
		$this->language = $input->getString('trLang', 0);
		
		$this->languages = array();
		if ( $this->language == "All" ) {
			$allLangs = getSupportedLanguages();
			foreach ( $allLangs as $lang ) {
				$this->languages[] = $lang->tag;
			}
		}
		else {
			$this->languages[] = $this->language;
		}
		
		$this->statusResponses = array();
		
		$this->transifex = new BiodivTransifex ();
		
		foreach ( $this->languages as $lang ) {
			
			foreach ( $this->articles as $articleId ) {
				
				$this->statusResponses[$articleId][$lang] = $this->transifex->getTranslation ( $articleId, $lang );
			}
		}
				
		
		// Display the view
		parent::display($tpl);
    }
}



?>
