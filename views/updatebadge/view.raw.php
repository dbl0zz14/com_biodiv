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
class BioDivViewUpdateBadge extends JViewLegacy
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
		
		$this->message = JText::_("COM_BIODIV_UPDATEBADGE_DEFAULT_MSG");
		
		if ( $this->personId ) {
	
			$input = JFactory::getApplication()->input;
			
			$badgeId = $input->getInt('id', 0);
			
			$completeText = $input->getString('done_text', 0);
			
			$this->collect = $input->getInt('collect', 0);
			
			$this->done = $input->getInt('done', 0);
			
			$this->classId = $input->getInt('class_id', 0);
			
			if ( $this->done ) {
				
				$success = Biodiv\Badge::updateStatus ( $this->schoolUser, $this->classId, $badgeId, Biodiv\Badge::COMPLETE, $completeText );
								
			}
			else if ( $this->collect ) {
				
				$this->newBadge = Biodiv\Badge::createFromId ( $this->schoolUser, $this->classId, $badgeId );
				$this->newBadge->collect();
				$this->message =  JText::_("COM_BIODIV_UPDATEBADGE_COLLECT_MSG") . ': ' . $this->newBadge->getBadgeName();
				
				$this->className = null;
				if ( $this->classId ) {
					$classDetails = Biodiv\SchoolCommunity::getClassDetails ( $this->schoolUser, $this->classId );
					$this->className = $classDetails->name;
				}
		
			}
			
				
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>