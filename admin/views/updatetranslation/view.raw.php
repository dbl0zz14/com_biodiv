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
class BioDivViewUpdateTranslation extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$app = JFactory::getApplication();

		$input = JFactory::getApplication()->input;
		
		$this->articleId = $input->getInt('article', 0);
		$this->language = $input->getString('trLang', 0);
		$this->title = $input->getString('title', 0);
		$this->text = $input->getString('text', 0);
		
		$this->titleResult = null;
		$this->textResult = null;
		
		$this->result = new StdClass();
		$this->result->articleId = $this->articleId;
		$this->result->language = $this->language;
		$this->result->messages = array();
		
		if ( $this->text ) {
			
			$decodedTitle = base64_decode ( $this->title );
			$decodedText = base64_decode ( $this->text );
			
			$this->textResult = updateArticle ( $this->articleId, $this->language, $decodedTitle, $decodedText );
			$this->result->message = $this->textResult;
			
		}

		
		// Display the view
		parent::display($tpl);
    }
}



?>
