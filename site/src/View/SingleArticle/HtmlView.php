<?php
namespace MammalWeb\Component\Biodiv\Site\View\SingleArticle;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

 /**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for a single article display within this component  
 */
class HtmlView extends BaseHtmlView {

	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
  
	public function display($tpl = null) 
	{
    
		$biodivModel = $this->getModel();

		$personId = $biodivModel->userID();

		$app = Factory::getApplication();
		
		if ( !$personId ) {
		
			$currentUri = Uri::getInstance();
		
			$loginParam = $app->input->getString('login', 0);
			
			$defaultLoginPage = 'index.php?option=com_users&view=login';
			
			if ( $loginParam ) {
				
				// assume login page has specific routing?
				$url = Route::_($loginParam);
				
			}
			else {
				$url = Route::_($defaultLoginPage.'&return='.base64_encode($currentUri));
			}
			
			$message = Text::_("COM_BIODIV_SINGLEARTICLE_LOGIN_MSG");
			$app->redirect($url);

		}


		$articleId = $app->input->getInt('id', 0);
		
		$this->article = $biodivModel->getArticleById ( $articleId );
		
		$this->title = $this->article->title;
		$this->introtext = $this->article->introtext;
		$this->fulltext = $this->article->fulltext;
		
		// Display the view
		parent::display($tpl);
	}
}



?>