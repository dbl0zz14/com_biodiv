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
* Kiosk Classify top level
*
* @since 0.0.1
*/
class BioDivViewWildSpace extends JViewLegacy
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

		$this->personId = userID();
		
		$this->allSpecies = array();
		
		if ( $this->personId ) {
			
			$this->helpOption = codes_getCode ( "wildspace", "beshelp" );
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();

			$this->allSpecies = Biodiv\SchoolSpecies::getUnlockedSpecies();
		}
		
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>