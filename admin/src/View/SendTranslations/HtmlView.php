<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\SendTranslations;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the SendTranslations admin view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$app = Factory::getApplication();

		$input = Factory::getApplication()->input;
		
		$articles = $input->get('article', array(), 'ARRAY');
		
		$this->model = $this->getModel();
		
		$this->responses = $this->model->sendTranslations ( $articles );
				
		// Display the view
		parent::display($tpl);
    }
}



?>
