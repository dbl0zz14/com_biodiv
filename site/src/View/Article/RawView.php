<?php

namespace MammalWeb\Component\Biodiv\Site\View\Article;
  
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for like
 */
class RawView extends BaseHtmlView {

	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		$biodivModel = $this->getModel();
		
		$this->personId = $biodivModel->userID();
		
		if ( $this->personId ) {
			
			//Check whether set id 
			$input = Factory::getApplication()->input;
			
			$optionId = $input->getInt('option_id', 0);
			$this->displayTitle = $input->getInt('title', 0);
			
			$this->article = $biodivModel->getArticle ( $optionId );
			
			//$joomlaArticle = getArticleById ( $this->article->id );
			$this->title = $this->article->title;
			$this->introtext = $this->article->introtext;
			
		}
	  
		parent::display($tpl);
		
    }
}



?>