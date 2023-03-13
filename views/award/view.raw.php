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
* Return school summary data for display in charts 
*
* @since 0.0.1
*/
class BioDivViewAward extends JViewLegacy
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
			$this->personId = userID();
		}
		
		if ( !$this->personId ) {
			
			error_log("Award view: no person id" );
			
		}
		else {
			
			$input = JFactory::getApplication()->input;
			
			$awardId = $input->getInt('id', 0);
			
			$this->collect = $input->getInt('collect', 0);
			$this->classId = $input->getInt('class_id', 0);
			
			if ( $this->collect ) {
				
				$this->award = Biodiv\Award::createFromId ( $this->schoolUser, $this->classId, $awardId );
				
				if ( $this->award ) {
					$this->award->collect();
				}
			}
			
			
				
		}

		// Display the view
		parent::display($tpl);
    }
}



?>