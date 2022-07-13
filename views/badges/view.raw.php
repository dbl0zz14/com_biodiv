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
* Return badge data as JSON eg for display on student dashboard
*
* @since 0.0.1
*/
class BioDivViewBadges extends JViewLegacy
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
		$this->translations = getTranslations("badges");
	
		$this->personId = (int)userID();
		
		$this->data = array();

		if ( !$this->personId ) {
			
			error_log("Badges view: no person id" );
			
		}
		else {
			
			$this->onOneLine = false;
			
			$this->statusIcons = array ( "fa-lock", "fa-unlock", "fa-clock-o", "fa-check", "fa-check" );
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			$this->moduleId = $input->getInt('module', 0);
			
			
			// Get badge group (pillar), if no group get all
			$this->singleBadgeGroup = false;
			$badgeGroupId = $input->getInt('group', 0);
			if ( $badgeGroupId ) {
				$this->singleBadgeGroup = true;
			}
			
			$this->completeOnly = $input->getInt('complete', 0);
			if ( $this->completeOnly ) {
				
				$this->onOneLine = true;
				
			}
			
			$this->unlockedOnly = $input->getInt('unlocked', 0);
			if ( $this->unlockedOnly ) {
				
				$this->onOneLine = true;
				
			}
			
			
			$this->numPerPage = 3;
			if ( Biodiv\SchoolCommunity::isStudent() ) {
				
				$this->numPerPage = 4;
			}
		
			
			// Is the user asking for suggested badges
			$this->suggest = $input->getInt('suggest', 0);
			
			$this->displayBadges = false;
			$this->displayBadges = $input->getInt('display', 0) == 1;
			
			$this->numToCollect = 0;
			if ( $this->displayBadges ) {
				$this->numToCollect = Biodiv\Task::countStudentTasks( Biodiv\Badge::COMPLETE );
				
			}
			
			// Are we displaying badges for collection
			$this->collect = $input->getInt('collect', 0);
			if ( $this->collect ) {
				
				$this->onOneLine = true;
				$this->numToCollect = Biodiv\Task::countStudentTasks( Biodiv\Badge::COMPLETE );
				if ( $this->numToCollect == 0 ) {
					$this->displayBadges = true;
				}
				
			}
			
			// Is it a teacher or ecologist wanting a view of student badges?
			$this->viewOnly = $input->getInt('viewonly', 0);			
			$this->teacher = $input->getInt('teacher', 0);			
			
			$this->badgeGroups = array();
			
			if ( !$badgeGroupId ) {
				$badgeGroups = codes_getList ( "badgegroup" );
				foreach ( $badgeGroups as $bg ) {
					$this->badgeGroups[$bg[0]] = $bg[1];
				}
			}
			else {
				$badgeGroupName = codes_getName ( $badgeGroupId, "badgegroup" );
				$this->badgeGroups[$badgeGroupId] = $badgeGroupName;
			}
			
			$this->badgeGroupIcons = Biodiv\BadgeGroup::getGroupIcons();
			
			$this->badgeGroupColour = array();
			$this->badgeIcons = array();
				
			foreach ( $this->badgeGroups as $groupId=>$badgeGroupName ) {
				
				$badgeColorArray = getOptionData ( $groupId, "color" ); 

				if ( count($badgeColorArray) > 0 ) {
					$this->badgeGroupColour[$groupId] = $badgeColorArray[0];
				}
				
				$iconArray = getOptionData ( $groupId, "icon" ); 

				$icon = "";
			
				if ( count($iconArray) > 0 ) {
					$icon = $iconArray[0];
				}
				
				$this->badgeIcons[$groupId] = $icon;
			}
				
			if ( $this->suggest ) {
				
				$this->onOneLine = true;
				
				// Form the json required from the suggested tasks.
				
				$this->badgeGroupData = array();
				$groups = array();
				
				$maxTasks = 3;
				$suggestedTasks = Biodiv\Task::getSuggestedTasks ( $maxTasks, $this->moduleId );
				
				foreach ( $suggestedTasks as $task ) {
					
					$badgeGroup = $task->group_id;
					if ( !array_key_exists ( $badgeGroup, $groups ) ) {
						$groups[$badgeGroup] = new StdClass ();
						$groups[$badgeGroup]->badges = array();
					}
					$badge = new StdClass();
					$badge->badge_id = $task->badge_id;
					$badge->badge_name = $task->badge_name;
					$badge->badge_image = $task->badge_image;
					$badge->unlocked_image = $task->unlocked_image;
					$badge->locked_image = $task->locked_image;
					$badge->icon = $task->icon;
					$badge->module_icon = $task->module_icon;
					$badge->tasks = array ( $task );
					
					$groups[$badgeGroup]->badges[$task->badge_id] = $badge;
				}
				foreach ( $groups as $id=>$group ) {
					$this->badgeGroupData[$id] = json_encode($group->badges);
				}
				
			}
			else if ( $this->viewOnly ) {
				
				$this->badgeGroupData = array();
				$groups = array();
				$allTasks = array();
				
				if ( $badgeGroupId ) {
					if ( $this->teacher ) {
						$allTasks = Biodiv\Task::getAllTeacherTasksToView ( $badgeGroupId, $this->moduleId );
					}
					else {
						$allTasks = Biodiv\Task::getAllStudentTasksToView ( $badgeGroupId, $this->moduleId );
					}
				}
				
				foreach ( $allTasks as $task ) {
					
					$badgeGroup = $task->group_id;
					if ( !array_key_exists ( $badgeGroup, $groups ) ) {
						$groups[$badgeGroup] = new StdClass ();
						$groups[$badgeGroup]->badges = array();
					}
					$badge = new StdClass();
					$badge->badge_id = $task->badge_id;
					$badge->badge_name = $task->badge_name;
					$badge->badge_image = $task->badge_image;
					$badge->unlocked_image = $task->unlocked_image;
					$badge->locked_image = $task->locked_image;
					$badge->icon = $task->icon;
					$badge->tasks = array ( $task );
					
					$groups[$badgeGroup]->badges[$task->badge_id] = $badge;
				}
				foreach ( $groups as $id=>$group ) {
					$this->badgeGroupData[$id] = json_encode($group->badges);
				}
			}
			else {
			
				// Get the current status for each badge group.
				$this->badgeGroupData = array();
				
				foreach ( $this->badgeGroups as $groupId=>$badgeGroupName ) {
					
					$badgeGroup = new Biodiv\BadgeGroup ( $groupId, $this->moduleId );
					
					// If complete only, only include badge groups with completed tasks
					if ( $this->completeOnly ) {
						if ( $badgeGroup->getSummary()["numTasks"] == 0 ) {
							continue;
						}
					}
					
					$this->badgeGroupData[$groupId] = $badgeGroup->getAllBadgesJSON();
					
					//$this->badgeGroupData[$groupId] = $badgeGroup->getAllBadges();
					
					//error_log ( "Got badges from BadgeGroup class" );
				}
			}			
		}

		// Display the view
		parent::display($tpl);
    }
}



?>