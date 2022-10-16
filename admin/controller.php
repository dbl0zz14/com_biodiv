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
 * General Controller of Biodiv component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 * @since       0.0.7
 */
class BioDivController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 12.2
	 */
	//protected $default_view = 'biodivs';
	protected $default_view = 'home';
	
	
	function createproject() {
		
		$app = JFactory::getApplication();

		$input = $app->input;
				
		$projectName = $input->getString('projectName', 0);
		$prettyName = $input->getString('prettyName', 0);
		$projectDescription = $input->getString('projectDescription', 0);
		$accessLevel = $input->getInt('accessLevel', 0);
		$parentProject = $input->getInt('parentProject', 0);
		$imageDir = $input->getString('imageDir', 'images/projects');
		$imageFile = $input->getString('imageFile', 0);
		$listingLevel = $input->getInt('listingLevel', 10000);
		$priority = $input->getInt('priority', 0);
		$displayOptions = $input->get('displayOptions', array(), 'ARRAY');
		$speciesLists = $input->get('speciesLists', array(), 'ARRAY');
		$projectAdmins = $input->get('projectAdmins', array(), 'ARRAY');
		$isSchoolProject = $input->getInt('isSchoolProject', 0);
		$existingSchoolId = $input->getInt('school', 0);
		$newSchoolName = $input->getString('newSchoolName', 0);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Create project
		$fields = new \StdClass();
		$fields->project_name = $projectName;
		$fields->project_prettyname = $prettyName;
		$fields->project_description = $projectDescription;
		$fields->access_level = $accessLevel;
		if ( $parentProject ) {
			$fields->parent_project_id = $parentProject;
		}
		$fields->dirname = $imageDir;
		$fields->image_file = $imageFile;
		$fields->listing_level = $listingLevel;
		
		$success = $db->insertObject("Project", $fields, 'project_id');
		if(!$success){
			error_log ( "Project insert failed" );
		}	
		
		$projectId = $fields->project_id;
		
		if ( $success ) {
			
			// Set classification priority
			$priorityFields = new \StdClass();
			$priorityFields->project_id = $projectId;
			$priorityFields->option_id = $priority;
			
			$success = $db->insertObject("ProjectOptions", $priorityFields);
			if(!$success){
				error_log ( "ProjectOptions insert classification priority failed" );
			}
			
			// Set displayOptions
			foreach ( $displayOptions as $displayId ) {
				$displayFields = new \StdClass();
				$displayFields->project_id = $projectId;
				$displayFields->option_id = $displayId;
				
				$success = $db->insertObject("ProjectOptions", $displayFields);
				if(!$success){
					error_log ( "ProjectOptions display option failed" );
				}
			}
			
			// Set displayOptions
			foreach ( $speciesLists as $listId ) {
				$speciesFields = new \StdClass();
				$speciesFields->project_id = $projectId;
				$speciesFields->option_id = $listId;
				
				$success = $db->insertObject("ProjectOptions", $speciesFields);
				if(!$success){
					error_log ( "ProjectOptions species list option failed" );
				}
			}
			
			// Set project admins
			foreach ( $projectAdmins as $projectAdmin ) {
				$adminFields = new \StdClass();
				$adminFields->project_id = $projectId;
				$adminFields->person_id = $projectAdmin;
				$adminFields->role_id = 1;
				
				$success = $db->insertObject("ProjectUserMap", $adminFields);
				if(!$success){
					error_log ( "ProjectUserMap species list option failed" );
				}
			}
			
			if ( $isSchoolProject == 1 ) {
				
				$schoolFields = new \StdClass();
				
				if ( $existingSchoolId > 0 ) {
					$schoolFields->school_id = $existingSchoolId;
					$schoolFields->project_id = $projectId;
				
					$success = $db->updateObject("School", $schoolFields, 'school_id');
					if(!$success){
						error_log ( "School project update failed" );
					}
				}
			}
			else if ( $isSchoolProject == 2 ) {
				
				$schoolFields = new \StdClass();
				
				if ( strlen ( $newSchoolName ) > 0 ) {
					$schoolFields->name = $newSchoolName;
					$schoolFields->project_id = $projectId;
					$schoolFields->image = $imageDir . '/' . $imageFile;
				
					$success = $db->insertObject("School", $schoolFields, 'school_id');
					if(!$success){
						error_log ( "School project insert failed" );
					}
				}
			}
		}

		
		$this->input->set('id', $projectId);
		$this->input->set('view', 'project');
		
		// $view = $this->getView( 'biodivs', 'html' );
		// $view->display();

		parent::display();
	}
	
	
	function editproject() {
		
		error_log ( "Edit project called" );
		
		$app = JFactory::getApplication();

		$input = $app->input;
		
		$projectId = $input->getInt('id', 0);
		$projectName = $input->getString('projectName', 0);
		$prettyName = $input->getString('prettyName', 0);
		$projectDescription = $input->getString('projectDescription', 0);
		$accessLevel = $input->getInt('accessLevel', 0);
		$parentProject = $input->getInt('parentProject', 0);
		$imageDir = $input->getString('imageDir', 'images/projects');
		$imageFile = $input->getString('imageFile', 0);
		$listingLevel = $input->getInt('listingLevel', 10000);
		$priority = $input->getInt('priority', 0);
		$displayOptions = $input->get('displayOptions', array(), 'ARRAY');
		$speciesLists = $input->get('speciesLists', array(), 'ARRAY');
		$projectAdmins = $input->get('projectAdmins', array(), 'ARRAY');
		$isSchoolProject = $input->getInt('isSchoolProject', 0);
		$existingSchoolId = $input->getInt('school', 0);
		$newSchoolName = $input->getString('newSchoolName', 0);
		
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Create project
		$fields = new \StdClass();
		$fields->project_id = $projectId;
		$fields->project_name = $projectName;
		$fields->project_prettyname = $prettyName;
		$fields->project_description = $projectDescription;
		$fields->access_level = $accessLevel;
		if ( $parentProject ) {
			$fields->parent_project_id = $parentProject;
		}
		$fields->dirname = $imageDir;
		$fields->image_file = $imageFile;
		$fields->listing_level = $listingLevel;
		
		$success = $db->updateObject("Project", $fields, 'project_id');
		if(!$success){
			error_log ( "Project update failed" );
		}	
		
		if ( $success ) {
			
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('ProjectOptions'))
				->where("project_id = " . $projectId );
			$db->setQuery($query);
			$result = $db->execute();
			
			
			// Set classification priority
			$priorityFields = new \StdClass();
			$priorityFields->project_id = $projectId;
			$priorityFields->option_id = $priority;
			
			$success = $db->insertObject("ProjectOptions", $priorityFields);
			if(!$success){
				error_log ( "ProjectOptions insert classification priority failed" );
			}
			
			// Set displayOptions
			foreach ( $displayOptions as $displayId ) {
				$displayFields = new \StdClass();
				$displayFields->project_id = $projectId;
				$displayFields->option_id = $displayId;
				
				$success = $db->insertObject("ProjectOptions", $displayFields);
				if(!$success){
					error_log ( "ProjectOptions display option failed" );
				}
			}
			
			// Set displayOptions
			foreach ( $speciesLists as $listId ) {
				$speciesFields = new \StdClass();
				$speciesFields->project_id = $projectId;
				$speciesFields->option_id = $listId;
				
				$success = $db->insertObject("ProjectOptions", $speciesFields);
				if(!$success){
					error_log ( "ProjectOptions species list option failed" );
				}
			}
			
			// Set project admins
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('ProjectUserMap'))
				->where("project_id = " . $projectId )
				->where("role_id = 1");
			$db->setQuery($query);
			$result = $db->execute();
			
			foreach ( $projectAdmins as $projectAdmin ) {
				$adminFields = new \StdClass();
				$adminFields->project_id = $projectId;
				$adminFields->person_id = $projectAdmin;
				$adminFields->role_id = 1;
				
				$success = $db->insertObject("ProjectUserMap", $adminFields);
				if(!$success){
					error_log ( "ProjectUserMap species list option failed" );
				}
			}
			
			if ( $isSchoolProject == 1 ) {
				
				$schoolFields = new \StdClass();
				
				if ( $existingSchoolId > 0 ) {
					$schoolFields->school_id = $existingSchoolId;
					$schoolFields->project_id = $projectId;
					
					$success = $db->updateObject("School", $schoolFields, 'school_id');
					if(!$success){
						error_log ( "School project update failed" );
					}
				}
			}
			else if ( $isSchoolProject == 2 ) {
				
				$schoolFields = new \StdClass();
				
				if ( strlen ( $newSchoolName ) > 0 ) {
					$schoolFields->name = $newSchoolName;
					$schoolFields->project_id = $projectId;
					$schoolFields->image = $imageDir . '/' . $imageFile;
				
					$success = $db->insertObject("School", $schoolFields, 'school_id');
					if(!$success){
						error_log ( "School project insert failed" );
					}
				}
			}
			
		}

		
		$this->input->set('id', $projectId);
		$this->input->set('view', 'project');
		
		// $view = $this->getView( 'biodivs', 'html' );
		// $view->display();

		parent::display();
	}
	
	
	function createschool() {
		
		$app = JFactory::getApplication();

		$input = $app->input;
				
		$schoolName = $input->getString('schoolName', 0);
		$image = $input->getString('image', 0);
		$projectId = $input->getInt('projectId', 0);
		
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Create school
		$fields = new \StdClass();
		$fields->name = $schoolName;
		$fields->image = $image;
		$fields->project_id = $projectId;
		
		$success = $db->insertObject("School", $fields, 'school_id');
		if(!$success){
			error_log ( "School insert failed" );
		}	
		
		$schoolId = $fields->school_id;
		
				
		$this->input->set('id', $schoolId);
		$this->input->set('view', 'school');
		
		parent::display();
	}
	
	
	function editschool() {
		
		$app = JFactory::getApplication();

		$input = $app->input;
		
		$schoolId = $input->getInt('id', 0);
		$schoolName = $input->getString('schoolName', 0);
		$image = $input->getString('image', 0);
		$projectId = $input->getInt('projectId', 0);
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		// Update school
		$fields = new \StdClass();
		$fields->school_id = $schoolId;
		$fields->name = $schoolName;
		$fields->image = $image;
		$fields->project_id = $projectId;
		
		
		$success = $db->updateObject("School", $fields, 'school_id');
		if(!$success){
			error_log ( "School update failed" );
		}	
		
				
		$this->input->set('id', $schoolId);
		$this->input->set('view', 'school');
		
		parent::display();
	}
	
	
	
	// function deletefiles() {
		
		// error_log ( "deletefiles called" );
		
		// $filePath = JPATH_SITE."/biodivimages/reports/school";
		
		// $files = glob($filePath . '/*.csv');

		// //Loop through the file list.
		// foreach($files as $file){
			// //Make sure that this is a file and not a directory.
			// if(is_file($file)){
				// error_log ( "Removing " . $file );
				// //Use the unlink function to delete the file.
				// unlink($file);
			// }
		// }
		
		// $this->input->set('deleted', '1');
		// $this->input->set('view', 'biodivs');

		// parent::display();
		
	// }
  
  
}