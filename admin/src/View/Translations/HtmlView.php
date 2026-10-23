<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\Translations;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$this->model = $this->getModel();
		
		$this->speciesTrans = $this->model->getSpeciesTranslationLists();
		
		// Display the view
		parent::display($tpl);
		
    }
}



?>
