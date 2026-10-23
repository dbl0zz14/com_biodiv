<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\Biodivs;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the Biodivs view (batch create users)
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$this->deleted = $input->getInt('deleted', 0);

		$this->model = $this->getModel();
		
		$this->projects = $this->model->getAllProjects();
		
		$this->schools = $this->model->getAllSchools();
		
		$this->userGroups = $this->model->getSchoolUserGroups();


		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}