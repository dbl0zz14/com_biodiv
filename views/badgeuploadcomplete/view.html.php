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
* Kiosk Standard Quiz results 
*
* @since 0.0.1
*/
class BioDivViewBadgeUploadComplete extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();
		
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		if ( $this->schoolUser ) {
			$this->personId = $this->schoolUser->person_id;
		}
		else {
			$this->personId = (int)userID();
		}
		
		if ( $this->schoolUser ) {

			// Get the parameters from the input data
			$input = $app->input;
			
			$this->uploadId = $input->getInt('upload_id', 0);
			$this->badge = $input->getInt('badge', 0);
			$this->classId = $input->getInt('class_id', 0);
			
			if ( $this->badge > 0 ) {
				
				$this->badgeResult = Biodiv\Badge::checkJustCompleted ( $this->schoolUser, $this->classId, $this->badge );
				
			}
		}
		else {
			$this->badgeResult = null;
		}
	
		
		// Display the view
		parent::display($tpl);
    }
}



?>