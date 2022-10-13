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
class BioDivViewAddSchoolUser extends JViewLegacy
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
				
		$role = $input->getString('role', 0);
		$personId = $input->getInt('userId', 0);
		$schoolId = $input->getInt('schoolId', 0);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
			->select("role_id from Role where role_name = " . $db->quote($role) );
			
		$db->setQuery($query);
	
		$roleId = $db->loadResult();
		
		$userDetails = getUserDetails($personId);
		
		$success = false;
		
		error_log ( "AddSchoolUser about to insert" );
		
		if ( $roleId > 0 ) {
			
			try {
		
				// Create school user
				$fields = new \StdClass();
				$fields->person_id = $personId;
				$fields->role_id = $roleId;
				$fields->school_id = $schoolId;
				
				$success = $db->insertObject("SchoolUsers", $fields);
			
			}
			catch ( Exception $e ) {
				error_log ( "AddSchoolUser error inserting record " . $e->getMessage() );
				$success = false;
			}
			
		}
		
		error_log ( "AddSchoolUser success = " . $success );
		
		if(!$success){
			error_log ( "SchoolUsers insert failed" );
			$this->message = "Failed to add school user " . $userDetails->name . " ( " .$userDetails->email. " ) as " . $role;
		}
		else {
			$this->message = "Added school user " . $userDetails->name . " ( " .$userDetails->email. " )  as " . $role;
		}
		
		// Display the template
		parent::display($tpl);
	}
	
	// protected function addToolBar()
	// {
		// //JToolbarHelper::title(JText::_('COM_BIODIV_MANAGER_BIODIVS'));
		
	// }
	
	
}