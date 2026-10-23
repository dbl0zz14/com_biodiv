<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\Project;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the Project view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$this->projectId = $input->getInt('id', 0);
		
		$this->model = $this->getModel();
		
		$this->project = $this->model->getProject($this->projectId);
		
		$this->speciesLists = $this->project->speciesLists;
		$this->displayOptions = $this->project->displayOptions;
		$this->projectAdmins = $this->project->projectAdmins;
		
		$this->accessLevels = $this->model->getAccessLevels();
		
		$this->allProjectAdmins = $this->model->getAllProjectAdmins();
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}