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
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		if ( $this->schoolUser ) {
			$this->personId = $this->schoolUser->person_id;
		}
		else {
			$this->personId = (int)userID();
		}
		
		if ( !$this->personId ) {
			
			error_log("Badges view: no person id" );
			
		}
		else {
			
			$this->newAward = null;
			$this->certificateData = null;
			
			$input = JFactory::getApplication()->input;
			
			$this->classId = $input->getInt('class_id', 0);
			$this->help = $input->getInt('help', 0);
			
			$this->helpOption = codes_getCode ( "badges", "beshelp" );
			
			$this->chooseClass = false;
			if ( ($this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE)  and !$this->classId ) {
				
				$this->chooseClass = true;
				
				$this->classes = Biodiv\SchoolCommunity::getClasses ( $this->schoolUser );
			}
			$this->notMyClass = false;
			if ( $this->classId ) {
				$myClass = Biodiv\SchoolCommunity::checkMyClass ( $this->schoolUser, $this->classId );
				
				if ( !$myClass ) {
					$this->notMyClass = true;
				}
			}
			if ( !$this->chooseClass and !$this->notMyClass ) {
			
				$this->newUser = Biodiv\Badge::unlockBadges( $this->schoolUser, $this->classId );
				
				if ( $this->newUser ) {
					
					$this->help = 1;
				}
				
				$this->newBadgeId = Biodiv\Badge::getNewBadgeId ( $this->schoolUser, $this->classId );
				
				$this->awards = Biodiv\Award::getAwards ( $this->schoolUser, $this->classId );
				
				$this->newAwardId = null;
				foreach ( $this->awards as $award ) {
					if ( !$this->newAwardId && $award->isNew() ) {
						$this->newAward = $award;
						$this->newAwardId = $award->getAwardId();
					}
				}
				
				$this->certificateData = Biodiv\SchoolCommunity::getCertificateData ( $this->schoolUser, $this->classId );
				
				$this->toCollect = false;
				if ( $this->newBadgeId or $this->newAwardId ) {
					
					$this->toCollect = true;
				}
				
				if ( !$this->toCollect ) {
					$this->allModules = Biodiv\Module::getModules();
					
					// Get the pillars: Quizzer etc
					$this->badgeGroups = Biodiv\BadgeGroup::getBadgeGroups();
					
					$this->badges = Biodiv\Badge::getBadges ( $this->schoolUser, $this->classId );
					
					$allIcons = json_decode(getSetting("school_icons"));
					$this->allBadgesImg = $allIcons->all_badges;
					$this->allBadgesActiveImg = $allIcons->all_badges_inv;
					
					$this->blankAwards = Biodiv\Award::getBlankAwards( $this->schoolUser );
				}
			}
			
		}

		// Display the view
		parent::display($tpl);
    }
}



?>