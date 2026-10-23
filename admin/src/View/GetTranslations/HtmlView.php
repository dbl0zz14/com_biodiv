<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\GetTranslations;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the GetTranslations admin view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$input = Factory::getApplication()->input;
		
		$articles = $input->get('article', array(), 'ARRAY');
		$language = $input->getString('trLang', 0);
		
		$this->model = $this->getModel();
		
		$this->statusResponses = $this->model->getTranslations ( $articles, $language );
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>
