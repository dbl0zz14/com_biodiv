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
class BioDivViewSendTranslations extends JViewLegacy
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
		
		$this->articles = $input->get('article', array(), 'ARRAY');
		
		$this->transifex = new BiodivTransifex ();
		
		$this->responses = array();
		
		foreach ( $this->articles as $articleId ) {
			
			$article = getArticle( $articleId );
					
			$articleContent = $article->introtext;
			$title = $article->title;
			
			//articleBase64 = base64_encode ( $articleContent );
			
			$articleExists = $this->transifex->articleExists( $articleId );
			if ( $articleExists === true ) {
				$response = $this->transifex->updateArticle ( $articleId, $title, $articleContent );
				if ( $response === false ) {
					$error = $this->transifex->getLastError();
					$this->responses[] = "Transifex article " . $articleId . " failed to update: " . $error;
				}
				else {
					$this->responses[] = "Transifex article " . $articleId . " updated";
				}
			}
			else if ( $articleExists === false ){
				$response = $this->transifex->newArticle ( $articleId, $title, $articleContent );
				if ( $response === false ) {
					$error = $this->transifex->getLastError();
					$this->responses[] = "Transifex article " . $articleId . " failed to create: " . $error;
				}
				else {
					$this->responses[] = "Transifex article " . $articleId . " created";
				}
			}
			else {
				$error = $this->transifex->getLastError();
				error_log ("Error checking whether Transifex article exists: " . $error);
				$this->responses[] = "Error checking whether Transifex article " . $articleId . " exists: " . $error;
			}
			
		}
				
		// Display the view
		parent::display($tpl);
    }
}



?>
