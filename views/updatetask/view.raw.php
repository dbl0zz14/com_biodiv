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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewUpdateTask extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "UpdateTask display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("updatetask");
		
		$this->personId = userID();
		
		$this->resourceId = null;
		
		$this->message = $this->translations['default_msg']['translation_text'];
		
		if ( $this->personId ) {
	
			$input = JFactory::getApplication()->input;
			
			$taskId = $input->getInt('id', 0);
			
			$collect = $input->getInt('collect', 0);
			
			$unlock = $input->getInt('unlock', 0);
			
			$done = $input->getInt('done', 0);
			
			$approve = $input->getInt('approve', 0);
			
			$reject = $input->getInt('reject', 0);
			
			$taskSetId = $input->getInt('uploaded', 0);
			
			$schoolTaskSetId = $input->getInt('schooluploaded', 0);
			
			
			
			$this->avatar = null;
			$this->feedback = null;
			$this->message = null;
			$this->nextStep = null;
			$this->findActivityButton = null;
			$this->reviewWorkButton = null;
			$this->reloadButton = null;
			
			if ( $unlock ) {
				
				$task = new Biodiv\Task ( $taskId );
				$task->unlock();
				$this->message =  $this->translations['unlock_msg']['translation_text'] . ': ' . $task->getSpecies();
		
			}
			else if ( $collect ) {
				
				$task = new Biodiv\Task ( $taskId );
				$task->collect();
				$this->message =  $this->translations['collect_msg']['translation_text'] . ': ' . $task->getTaskName();
		
			}
			else if ( $done ) {
				
				$task = new Biodiv\Task ( $taskId );
				$task->done();
				
				// Get my avatar
				$this->avatar = Biodiv\SchoolCommunity::getAvatar();
				
				$this->feedback = $this->translations['well_done']['translation_text'];
				
				$this->message = $this->translations['done_msg']['translation_text'] . ' ' . $task->getTaskName() . ' ' . 
						$this->translations['activity']['translation_text'];
				
				if ( Biodiv\SchoolCommunity::isStudent() ) {
					$this->nextStep = $this->translations['once_approved']['translation_text'];
				}	
				
				$this->reloadButton = true;
				
			}
			else if ( $approve ) {
				
				$task = Biodiv\Task::createFromStudentTaskId ( $taskId );
				$task->approve();
				$studentId = $task->getTaskPerson();
				error_log ( "Task person id = " . $studentId );
				$schoolUser = Biodiv\SchoolCommunity::getSchoolUser( $studentId );
				$this->message = $this->translations['approve_msg']['translation_text'] . ': ' . $task->getTaskName() . ' ' .
						$this->translations['for']['translation_text'] . ' ' . $schoolUser->username;
			
			}
			else if ( $reject ) {
				
				$task = Biodiv\Task::createFromStudentTaskId ( $taskId );
				$task->reject();
				$studentId = $task->getTaskPerson();
				$schoolUser = Biodiv\SchoolCommunity::getSchoolUser( $studentId );
				$this->message = $this->translations['reject_msg']['translation_text'] . ': ' . $task->getTaskName() . ' ' .
						$this->translations['for']['translation_text'] . ' ' . $schoolUser->username;
			
			}
			else if ( $taskSetId ) {
				
				$task = Biodiv\Task::createFromResourceSet ( $taskSetId );
				
				// Get my avatar
				$this->avatar = Biodiv\SchoolCommunity::getAvatar();
				
				$this->feedback = $this->translations['well_done']['translation_text'];
				
				$this->message = $this->translations['done_msg']['translation_text'] . ' ' . $task->getTaskName() . ' ' . 
						$this->translations['activity']['translation_text'];
				
				if ( Biodiv\SchoolCommunity::isStudent() ) {
					$this->nextStep = $this->translations['once_approved']['translation_text'];
				}	
				
				$this->reloadButton = true;
				
				if ( Biodiv\SchoolCommunity::isStudent() ) {
					$this->reviewWorkButton = true;
				}
			}
			else if ( $schoolTaskSetId ) {
				
				$task = Biodiv\Task::createFromSchoolResourceSet ( $schoolTaskSetId );
				
				if ( $task ) {
					// Get my avatar
					$this->avatar = Biodiv\SchoolCommunity::getAvatar();
					
					$this->feedback = $this->translations['thank_you']['translation_text'];
					
					$this->message = $this->translations['done_msg']['translation_text'] . ' ' . $task->getTaskName() . ' ' . 
						$this->translations['activity']['translation_text'];
					
					$this->reloadButton = true;
				}
				else {
					$this->message = $this->translations['problem']['translation_text'];
				}
				
			}	
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>