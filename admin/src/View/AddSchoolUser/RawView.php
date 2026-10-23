<?php

namespace MammalWeb\Component\Biodiv\Administrator\View\AddSchoolUser;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
 

/**
 * View for add school user, used in admin edit school
 */
class RawView extends BaseHtmlView 
{
	/**
	 * Display the AddSchoolUser view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$role = $input->getString('role', 0);
		$personId = $input->getInt('userId', 0);
		$schoolId = $input->getInt('schoolId', 0);
		
		$this->model = $this->getModel();
		
		$success = $this->model->addSchoolUser ( $role, $personId, $schoolId );
		
		if(!$success){
			$this->message = "Failed to add school user " . $personId . " as " . $role;
		}
		else {
			$this->message = "Added school user " . $personId . " as " . $role;
		}
		
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}