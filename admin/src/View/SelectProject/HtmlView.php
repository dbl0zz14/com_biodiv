<?php
namespace MammalWeb\Component\Biodiv\Administrator\View\SelectProject;

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
		
		// $app = JFactory::getApplication();

		// $input = $app->input;
				
		// $this->deleted = $input->getInt('deleted', 0);
		
		$this->model = $this->getModel();
		
		$this->projects = $this->model->getAllProjects();
		
		$this->accessLevels = $this->model->getAccessLevels();
		
		
		// $db = \JDatabaseDriver::getInstance(dbOptions());


		// $query = $db->getQuery(true)
			// ->select("P.project_id, P.project_name as name, P.project_prettyname as prettyname, P.access_level as access from Project P")
			// ->order("name");
			
		// $db->setQuery($query);
		
		// error_log("ProjectSetup project select query created: " . $query->dump());
		
		// $this->projects = $db->loadObjectList("project_id");


		// $query = $db->getQuery(true)
			// ->select("S.school_id, S.name from School S")
			// ->order("S.name");
			
		// $db->setQuery($query);
		
		// //error_log("SchoolCommunity constructor select query created: " . $query->dump());
		
		// $this->schools = $db->loadAssocList("school_id", "name");
		
		
		// $joomlaDb = JFactory::getDbo();
        // $joomlaDb->setQuery( 'SELECT id, title' .
                        // ' FROM `#__usergroups`' .
						// ' WHERE title like "School%"' );
                        
        
        // $this->userGroups = $joomlaDb->loadAssocList ("id", "title");


		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}