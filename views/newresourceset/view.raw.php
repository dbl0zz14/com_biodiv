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
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		$this->personId = $this->schoolUser->person_id;
		
		if ( !$this->personId ) {
			
			error_log("NewResourceSet view: no person id" );
			
		}
		else {
		
			// Get the form data and create a new ResourceSet
			// Check whether site id is in form
			$app = JFactory::getApplication();
			$input = $app->input;
			
			$this->loaded = false;
			$this->isBadge = false;
			$this->isPost = false;
				
			$setName = $input->getString('uploadName', 0);
			
			error_log ( "Set name = " . $setName );
			
			if ( $setName ) {
				
				$resourceType = $input->getInt('resourceType', 0);
				$setText = $input->getString('uploadDescription', 0);
				$school = $input->getInt('school', 0);
				$this->badgeId = $input->getInt('badge', 0);
				$classId = $input->getInt('classId', 0);
				$students = $input->getInt('studentCheckBox', 0);
				$isSchoolTask = $input->getInt('schoolTask', 0);
				$source = $input->getString('source', 0);
				$externalText = $input->getString('externalText', 0);
				$shareLevel = $input->getInt('shareLevel', 0);
				$this->toPost = $input->getInt('post', 0);
				
				$tags = $input->get('tag', array(), 'ARRAY');
				
				$uploadParams = new StdClass();
				$uploadParams->source = $source;
				$uploadParams->externalText = $externalText;
				$uploadParams->shareLevel = $shareLevel;
				
				// if ( ($badgeId > 0) and (count($tags) == 0) ) {
					// $badge = Biodiv\Badge::createFromId($badgeId);
					// $tags[] = $task->getModuleTagId();
				// }
				$uploadParams->tags = $tags;
				
				$this->resourceSet = Biodiv\ResourceSet::createResourceSet ( $school, $resourceType, $setName, $setText, json_encode($uploadParams));
				
				$errMsg = print_r ( $this->resourceSet, true );
				error_log ( "New resource set: " . $errMsg );
				
				$this->newSetId = $this->resourceSet->getSetId();
				
				if ( $this->toPost ) {
					
					$this->resourceSet->postSet();
				}
				
				//error_log ("New set id = " . $this->newSetId);
				
				// If there's a badge, associate this set id with the badge
				if ( $this->badgeId > 0 ) {
					
					$this->isBadge = true;
					
					if ( $isSchoolTask ) {
						
						// foreach ( $students as $studentId ) {
							
							// if ( Biodiv\SchoolCommunity::isMyStudent ( $studentId ) ) {
								
								// // A new student needs tasks copying over to the StudentTasks table
								// if ( Biodiv\SchoolCommunity::isNewUser ( $studentId ) ) {
									// Biodiv\Badge::unlockBadges ( $studentId );
								// }
					
								// $task = new Biodiv\Task ( $taskId, $studentId );
								// $task->linkResourceSet ( $this->newSetId, true, true );
																
							// }
						// }
						// Biodiv\SchoolCommunity::logEvent ( true, Biodiv\SchoolCommunity::SCHOOL, "completed the " . $task->getBadgeName() . " badge" );
					}
					else {
						
						$badge = Biodiv\Badge::createFromId($this->schoolUser, $classId, $this->badgeId);
						
						$badge->linkResourceSet ( $this->newSetId, $setText );
						
					}
					
				}
				else {
					$postTypeId = codes_getCode ( "Post", "resource" );
					
					if ( $postTypeId == $resourceType ) {
						$this->isPost = true;
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