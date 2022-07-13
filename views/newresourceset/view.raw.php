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
class BioDivViewNewResourceSet extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("newresourceset");
	
		$this->person_id = (int)userID();

		if ( !$this->person_id ) {
			
			error_log("NewResourceSet view: no person id" );
			
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
				$source = $input->getString('source', 0);
				$externalText = $input->getString('externalText', 0);
				$shareLevel = $input->getInt('shareLevel', 0);
				
				$tags = $input->get('tag', array(), 'ARRAY');
				
				$uploadParams = new StdClass();
				$uploadParams->source = $source;
				$uploadParams->externalText = $externalText;
				$uploadParams->shareLevel = $shareLevel;
				
				if ( ($taskId > 0) and (count($tags) == 0) ) {
					$task = new Biodiv\Task($taskId);
					$tags[] = $task->getModuleTagId();
				}
				$uploadParams->tags = $tags;
					
				
				$this->resourceSet = Biodiv\ResourceSet::createResourceSet ( $school, $resourceType, $setName, $setText, json_encode($uploadParams));
				
				$this->newSetId = $this->resourceSet->getSetId();
				
				// If there's a task, associate this set id with the task
				if ( $taskId > 0 ) {
					
					if ( $isSchoolTask ) {
						
						foreach ( $students as $studentId ) {
							
							if ( Biodiv\SchoolCommunity::isMyStudent ( $studentId ) ) {
								
								// A new student needs tasks copying over to the StudentTasks table
								if ( Biodiv\SchoolCommunity::isNewUser ( $studentId ) ) {
									Biodiv\Badge::unlockBadges ( $studentId );
								}
					
								$task = new Biodiv\Task ( $taskId, $studentId );
								$task->linkResourceSet ( $this->newSetId, true, true );
																
							}
						}
						Biodiv\SchoolCommunity::logEvent ( true, Biodiv\SchoolCommunity::SCHOOL, "completed the " . $task->getBadgeName() . " badge" );
					}
					else {
						$task = new Biodiv\Task ( $taskId );
						// Only students linking their own tasks need to be approved.
						if ( Biodiv\SchoolCommunity::isStudent() ) {
							$task->linkResourceSet ( $this->newSetId, false, false );
						}
						else {
							$task->linkResourceSet ( $this->newSetId, false, true );
						}
						
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