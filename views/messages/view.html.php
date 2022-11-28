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
class BioDivViewMessages extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->personId = userID();
		
		if ( $this->personId ) {
			
			$this->helpOption = codes_getCode ( "messages", "beshelp" );
		
			// Get user and school details
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
			$this->messageList = new Biodiv\MessageList();
	
			$this->recipients = $this->messageList->getRecipients();
			
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>