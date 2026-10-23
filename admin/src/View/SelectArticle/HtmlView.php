<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\SelectArticle;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the SelectArticle view, used in translations
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$this->defaultNumPerPage = 20;
		
		$input = Factory::getApplication()->input;
		
		$this->purpose = $input->getString('purpose', 0);
		$this->page = $input->getString('page', 1);
		$this->length = $input->getString('length', $this->defaultNumPerPage);
		
		$searchInput = $input->getString('search', 0);
			
		$this->searchStr = null;
		
		if ( $searchInput ) {
			$this->searchStr = filter_var(
									$searchInput,
									FILTER_UNSAFE_RAW,
									FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK
									);
			//error_log ( "Sanitized search str = " . $this->searchStr );
		}
		
		$this->model = $this->getModel();
		
		$articleObj = $this->model->getAllArticlesForTranslation( $this->page, $this->length, $this->searchStr );
		
		$this->totalNumArticles = $articleObj->total;
		$this->articles = $articleObj->articles;
		
		if ( $this->length > 0 ) {
			$remainder = $this->totalNumArticles%$this->length;
			if ( $remainder > 0 ) {
				$this->totalNumPages = intval($this->totalNumArticles/$this->length) + 1;
			}
			else {
				$this->totalNumPages = intval($this->totalNumArticles/$this->length);
			}
		}
		else {
			$this->totalNumPages = 1;
		}
		
		if ( $this->purpose == "gettranslations" ) {
			$this->languages = $this->model->getSupportedLanguages();
		}
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}