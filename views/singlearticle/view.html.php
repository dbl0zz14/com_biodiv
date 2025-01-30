<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

 
/**
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewSingleArticle extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
  
	public function display($tpl = null) 
	{
    
		$personId = (int)userID();
    
		if ( !$personId ) {
		
			$app = JFactory::getApplication();
		
			$currentUri = Uri::getInstance();
		
			$loginParam = $app->input->getString('login', 0);
			
			$defaultLoginPage = 'index.php?option=com_users&view=login';
			
			if ( $loginParam ) {
				
				// assume login page has specific routing?
				$url = JRoute::_($loginParam);
				
			}
			else {
				$url = JRoute::_($defaultLoginPage.'&return='.base64_encode($currentUri));
			}
			
			$message = JText::_("COM_BIODIV_SINGLEARTICLE_LOGIN_MSG");
			$app->redirect($url, $message);

		}


		$app = JFactory::getApplication();
		
		$articleId = $app->input->getInt('id', 0);
		
		$this->article = getArticleById ( $articleId );
		
		$this->title = $this->article->title;
		$this->introtext = $this->article->introtext;
		$this->fulltext = $this->article->fulltext;
		
		// Display the view
		parent::display($tpl);
	}
}



?>