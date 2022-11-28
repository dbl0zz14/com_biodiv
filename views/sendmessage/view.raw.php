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
* Capture resource set data
*
* @since 0.0.1
*/
class BioDivViewSendMessage extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->person_id = (int)userID();

		if ( !$this->person_id ) {
			
			$resp = ['error' => JText::_("COM_BIODIV_SENDMESSAGE_LOGIN") ];
			$this->messageResponse = (object) $resp;
			error_log("SendMessage view: no person id" );
			
		}
		else {
		
			// Get the form data and create a new SendMessage
			// Check whether site id is in form
			$app = JFactory::getApplication();
			$input = $app->input;
			
			//$this->loaded = false;
			
			$sender = $input->getString('sender', 0);
			
			error_log("BioDivViewSendMessage sender = " . $sender);
			
			if ( $sender ) {
				
				$isReport = $input->getInt ( 'reportMessage', 0 );
				
				if ( $isReport ) {
					
					error_log ( "Reporting a message" );
					
					$reportedMsgId = $input->getInt('reportedMsgId', 0);
					$reportText = $input->getString('reportText', 0);
					
					$messageManager = new Biodiv\MessageList();
					
					try {
						$messageManager->reportMessage ( $sender, $reportedMsgId, $reportText );
						
						$resp = ['message' => JText::_("COM_BIODIV_SENDMESSAGE_REPORT_SENT") ];
						$this->messageResponse = (object) $resp;
					}
					catch ( Throwable $th ) {
						$resp = ['error' => JText::_("COM_BIODIV_SENDMESSAGE_REPORT_FAILED") ];
						$this->messageResponse = (object) $resp;
					}
					
					
				}
				else {
				
					$recipient = $input->getInt('recipientSelect', 0);
					
					if ( !$recipient ) {
						$recipient = $input->getInt('replyRecipient', 0);
					}
					
					$messageText = $input->getString('messageText', 0);
					
					if ( !$messageText ) {
						$messageText = $input->getString('replyText', 0);
					}
					
					$replyToId = $input->getInt('replyTo', 0);
					
					$messageManager = new Biodiv\MessageList();
					
					$messageManager->sendMessage ( $sender, $recipient, $messageText, $replyToId );
					
					error_log ( "SendMessage - about to set up response" );
					
					//$resp = ['message' => 'Message sent' ];
					$resp = ['message' => JText::_("COM_BIODIV_SENDMESSAGE_SUCCESS"), 'replyTo' => $replyToId, 'replyIcon' => '<i class="fa fa-reply"></i>' ];
					$this->messageResponse = (object) $resp;
				}
			}
			else {
				
				$resp = ['error' => "Sender not found" ];
				$this->messageResponse = (object) $resp;
				
			}
			
		}

		// Display the view
		parent::display($tpl);
    }
}



?>