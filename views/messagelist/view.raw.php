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
class BioDivViewMessageList extends JViewLegacy
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
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->sent = $input->getInt('sent', 0);
			
			$this->messageList = new Biodiv\MessageList();
	
			if ( $this->sent ) {
				$this->messages = $this->messageList->getSentMessages();
			}
			else {
				$this->messages = $this->messageList->getMessages();
			}
			
			$this->recipients = $this->messageList->getRecipients();
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>