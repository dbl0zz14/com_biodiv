<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* Capture resource set data
*
* @since 0.0.1
*/
class BioDivViewResourceSet extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "ResourceSet display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("resourceset");
	
		$this->person_id = (int)userID();

		if ( !$this->person_id ) {
			
			error_log("ResourceSet view: no person id" );
			
		}
		else {
		
			// Get the form data and create a new ResourceSet
			// Check whether site id is in form
			$app = JFactory::getApplication();
			$input = $app->input;
			
			$this->loaded = false;
			
			$setName = $input->getString('uploadName', 0);
			
			if ( $setName ) {
				
				$resourceType = $input->getInt('resourceType', 0);
				$setText = $input->getString('uploadDescription', 0);
				$school = $input->getInt('school', 0);
				$taskId = $input->getInt('task', 0);
				$students = $input->getInt('studentCheckBox', 0);
				$isSchoolTask = $input->getInt('schoolTask', 0);
				
				
				
				$errMsg = print_r ( $students, true );
				error_log ( "BioDivViewResourceSet students = " . $errMsg );
				
				$this->resourceSet = Biodiv\ResourceSet::createResourceSet ( $school, $resourceType, $setName, $setText );
				
				$this->newSetId = $this->resourceSet->getSetId();
				
				// If there's a task, associate this set id with the task
				if ( $taskId > 0 ) {
					
					if ( $isSchoolTask ) {
						
						error_log ( "School task found" );
						
						foreach ( $students as $studentId ) {
							
							if ( Biodiv\SchoolCommunity::isMyStudent ( $studentId ) ) {
								
								// A new student needs tasks copying over to the StudentTasks table
								if ( Biodiv\SchoolCommunity::isNewUser ( $studentId ) ) {
									Biodiv\Badge::unlockBadges ( $studentId );
								}
					
								$task = new Biodiv\Task ( $taskId, $studentId );
								 error_log ( "Created task for student " . $studentId );
								 error_log ( "Linking task " . $taskId . " with set " . $this->newSetId );
								$task->linkResourceSet ( $this->newSetId, true, true );
								error_log ( "Linked task " . $taskId . " with set " . $this->newSetId );
								
							}
						}
						Biodiv\SchoolCommunity::logEvent ( true, Biodiv\SchoolCommunity::SCHOOL, "completed the " . $task->getBadgeName() . " badge" );
					}
					else {
						$task = new Biodiv\Task ( $taskId );
						// error_log ( "Created task" );
						// error_log ( "Linking task " . $taskId . " with set " . $this->newSetId );
						// Only students linking their own tasks need to be approved.
						if ( Biodiv\SchoolCommunity::isStudent() ) {
							$task->linkResourceSet ( $this->newSetId, false, false );
						}
						else {
							$task->linkResourceSet ( $this->newSetId, false, true );
						}
						//error_log ( "Linked task " . $taskId . " with set " . $this->newSetId );
					}
					
				}
				
				$app->setUserState('com_biodiv.resource_set_id', $this->newSetId);
			}
			
		}

		// Display the view
		parent::display($tpl);
    }
}



?>