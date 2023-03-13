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
* Browse tasks by badge group
*
* @since 0.0.1
*/
class BioDivViewCommunityPosts extends JViewLegacy
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
		if ( $this->schoolUser ) {
			$this->personId = $this->schoolUser->person_id;
		}
		else {
			$this->personId = (int)userID();
		}
		
		if ( !$this->personId ) {
			
			error_log("CommunityPosts view: no person id" );
			
		}
		else {
			
			$app = JFactory::getApplication();
			$input = $app->input;

			$this->badge = $input->getInt('badge', 0);
			$this->classId = $input->getInt('class_id', 0);
			$this->page = $input->getInt('page', 1);
			$this->school = $input->getInt('school', 0);
			
			$this->newBadge = false;
			if ( $this->badge > 0 ) {
				
				$this->badgeResult = Biodiv\Badge::checkJustCompleted ( $this->schoolUser, $this->classId, $this->badge );
				
				if ( $this->badgeResult && $this->badgeResult->isComplete ) {
					
					$this->newBadge = true;
				}
				
			}
			
			$postObj = Biodiv\Post::getPosts ( $this->schoolUser, $this->school, $this->page );
			
			$this->numPosts = $postObj->total;
			$this->posts = $postObj->posts;
			
			$numPerPage = Biodiv\Post::NUM_PER_PAGE;
			$this->numPages = ceil($this->numPosts/$numPerPage);
				
		}

		// Display the view
		parent::display($tpl);
    }
}



?>