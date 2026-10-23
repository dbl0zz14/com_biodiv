<?php

namespace MammalWeb\Component\Biodiv\Administrator\View\UpdateTranslation;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View for creating batch users, used in admin batch users (biodivs)
 */
class RawView extends BaseHtmlView 
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$app = Factory::getApplication();

		$input = Factory::getApplication()->input;
		
		$this->articleId = $input->getInt('article', 0);
		$this->language = $input->getString('trLang', 0);
		$this->title = $input->getString('title', 0);
		$this->text = $input->getString('text', 0);
		
		$this->titleResult = null;
		$this->textResult = null;
		
		$this->result = new \StdClass();
		$this->result->articleId = $this->articleId;
		$this->result->language = $this->language;
		$this->result->messages = array();
		
		$this->model = $this->getModel();
		
		if ( $this->text ) {
			
			$decodedTitle = base64_decode ( $this->title );
			$decodedText = base64_decode ( $this->text );
			
			$this->textResult = $this->model->updateArticle ( $this->articleId, $this->language, $decodedTitle, $decodedText );
			$this->result->message = $this->textResult;
			
		}

		
		// Display the view
		parent::display($tpl);
    }
}



?>
