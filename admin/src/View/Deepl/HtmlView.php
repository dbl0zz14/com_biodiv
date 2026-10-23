<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\Deepl;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the Biodivs view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		//$input = JFactory::getApplication()->input;
		
		// $this->purpose = $input->getString('purpose', 0);
		// $this->page = $input->getString('page', 1);
		// $this->length = $input->getString('length', $this->defaultNumPerPage);
		
		// $articleObj = getAllArticlesForTranslation( $this->page, $this->length );
		
		// $this->totalNumArticles = $articleObj->total;
		// $this->articles = $articleObj->articles;
		
		// if ( $this->length > 0 ) {
			// $remainder = $this->totalNumArticles%$this->length;
			// if ( $remainder > 0 ) {
				// $this->totalNumPages = intval($this->totalNumArticles/$this->length) + 1;
			// }
			// else {
				// $this->totalNumPages = intval($this->totalNumArticles/$this->length);
			// }
		// }
		// else {
			// $this->totalNumPages = 1;
		// }
		
		$this->model = $this->getModel();
		
		$this->languages = $this->model->getSupportedLanguages();
		
		$translateOpts = translateOptions();
		$authKey = $translateOpts['deepl']; 
		$this->translator = new \DeepL\Translator($authKey);

		$this->targetLanguages = $this->translator->getTargetLanguages();
	
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}