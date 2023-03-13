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
class BioDivViewBadgeComplete extends JViewLegacy
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
			error_log("TaskUpload view: no person id" );
		}
		else {
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			$this->badgeId = $input->getInt('id', 0);
			$this->classId = $input->getInt('class_id', 0);
			
			$this->badge = Biodiv\Badge::createFromId ( $this->schoolUser, $this->classId, $this->badgeId );
			
			$this->badgeName = $this->badge->getBadgeName();
			
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>