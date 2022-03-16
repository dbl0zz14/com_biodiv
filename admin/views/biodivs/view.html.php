<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Biodivs View
 *
 * @since  0.0.1
 */
class BioDivViewBiodivs extends JViewLegacy
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
		
		$app = JFactory::getApplication();

		$input = $app->input;
				
		$this->deleted = $input->getInt('deleted', 0);
		
		
		$db = \JDatabaseDriver::getInstance(dbOptions());


		$query = $db->getQuery(true)
			->select("P.project_id, P.project_prettyname as name from Project P")
			->order("name");
			
		$db->setQuery($query);
		
		//error_log("SchoolCommunity constructor select query created: " . $query->dump());
		
		$this->projects = $db->loadAssocList("project_id", "name");


		$query = $db->getQuery(true)
			->select("S.school_id, S.name from School S")
			->order("S.name");
			
		$db->setQuery($query);
		
		//error_log("SchoolCommunity constructor select query created: " . $query->dump());
		
		$this->schools = $db->loadAssocList("school_id", "name");
		
		
		$joomlaDb = JFactory::getDbo();
        $joomlaDb->setQuery( 'SELECT id, title' .
                        ' FROM `#__usergroups`' .
						' WHERE title like "School%"' );
                        
        
        $this->userGroups = $joomlaDb->loadAssocList ("id", "title");


		// Display the template
		parent::display($tpl);
	}
	
	protected function addToolBar()
	{
		//JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	}
	
	
}