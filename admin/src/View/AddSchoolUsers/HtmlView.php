<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\AddSchoolUsers;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView 
{
	/**
	 * Display the Add School Users admin view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$this->schoolId = $input->getInt('id', 0);
		
		$this->model = $this->getModel();
		
		$this->school = $this->model->getSchool ( $this->schoolId );
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}