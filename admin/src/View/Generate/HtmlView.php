<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\Generate;

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
		
		$app = Factory::getApplication();

		$input = $app->input;
			
		$this->species = $input->getInt('species', 0);
		$this->options = $input->getInt('opt', 0);
		
		$this->listTranId = $input->getInt('tranid', 0);
		
		$this->model = $this->getModel();
		
		
		if ( $this->species == 1 ) {
			
			$this->title = "Species file for translation";
			$this->reportURL = $this->model->generateSpeciesOptionsFile ( $this->listTranId );
			
		}
		else if ( $this->options == 1 ) {
			
			$this->title = "Non-species Options file for translation";
			$this->reportURL = $this->model->generateNonSpeciesOptionsFile ();

		}
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>
