<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class MessageList {
	
	private $personId;
	
	private $newMessageArray;
	
	
	
	function __construct()
	{
		$this->personId = userID();
		
	}
	
	public function newMessageCount () {
		
		if ( $this->personId ) {
			
			$options = dbOptions();
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("count(*) from Message M")
					->where("M.to_person = " . $this->personId)
					->where("M.read_flag = 0"); 
			
					
			$db->setQuery($query);
				
			//error_log("MessageList::newMessageCount select query created: " . $query->dump());
				
			$messageCount = $db->loadResult();
			
			return $messageCount;
				
		}
		else {
			return 0;
		}
		

	}
	
	
	public function newMessages () {
		
		if ( $this->personId ) {
			
			if ( !$this->newMessageArray ) {
			
		
				$options = dbOptions();
				$userDb = $options['userdb'];
				$prefix = $options['userdbprefix'];
				
				$db = \JDatabaseDriver::getInstance($options);
			
				$query = $db->getQuery(true)
						->select("U.username, M.*, A.image as avatar from Message M")
						->innerJoin($userDb . "." . $prefix ."users U on M.from_person = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = M.from_person")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("M.to_person = " . $this->personId)
						->where("M.read_flag = 0")
						->where("M.hidden = 0")
						->order("M.timestamp DESC"); 
				
						
				$db->setQuery($query);
					
				//error_log("MessageList::newMessages select query created: " . $query->dump());
					
				$this->newMessageArray = $db->loadObjectList("message_id");
				
			}
		}
			
		return $this->newMessageArray;

	}
	
	
	public function getMessages ( $numMessages = null ) {
		
		$messageArray = null;
		
		if ( $this->personId ) {
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("U.username, M.*, A.image as avatar from Message M")
					->innerJoin($userDb . "." . $prefix ."users U on M.from_person = U.id")
					->innerJoin("SchoolUsers SU on SU.person_id = M.from_person")
					->innerJoin("Avatar A on A.avatar_id = SU.avatar")
					->where("M.to_person = " . $this->personId )
					->where("M.hidden = 0")
					->order("M.timestamp DESC");
			
					
			$db->setQuery($query);
				
			//error_log("MessageList::newMessages select query created: " . $query->dump());
				
			$messageArray = $db->loadObjectList("message_id");
				
		}
			
		return $messageArray;

	}
	
	
	public function getSentMessages ( $numMessages = null ) {
		
		$messageArray = null;
		
		if ( $this->personId ) {
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = \JDatabaseDriver::getInstance($options);
		
			$query = $db->getQuery(true)
					->select("U.username, M.*, A.image as avatar from Message M")
					->innerJoin($userDb . "." . $prefix ."users U on M.to_person = U.id")
					->innerJoin("SchoolUsers SU on SU.person_id = M.to_person")
					->innerJoin("Avatar A on A.avatar_id = SU.avatar")
					->where("M.from_person = " . $this->personId)
					->where("M.hidden = 0")
					->order("M.timestamp DESC");
			
					
			$db->setQuery($query);
				
			//error_log("MessageList::newMessages select query created: " . $query->dump());
				
			$messageArray = $db->loadObjectList("message_id");
				
		}
			
		return $messageArray;

	}
	
	public function getRecipients () {
		
		return SchoolCommunity::getAdults();

	}
	
	public function messagesRead () {
		
		if ( $this->personId ) {
		
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
					
			$fields = array(
				$db->quoteName('read_flag') . ' = 1'
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('to_person') . ' = ' . $this->personId
			);

			$query->update("Message")->set($fields)->where($conditions);
			
			$db->setQuery($query);
			$result = $db->execute();
			
		}
	}	

	public function sendMessage ( $fromPerson, $toPerson, $messageText, $replyingTo = null ) {
		
		$personId = userID();
			
		if($personId and $personId == $fromPerson){
				
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			if ( !$replyingTo ) {
				$msgFields = (object) [
					'from_person' => $personId,
					'to_person' => $toPerson,
					'text' => $messageText
				];
				
				$messageId = $db->insertObject('Message', $msgFields);
			}
			else {
				$msgFields = (object) [
					'from_person' => $personId,
					'to_person' => $toPerson,
					'text' => $messageText
				];
				
				$messageId = $db->insertObject('Message', $msgFields);
				
				$query = $db->getQuery(true);
					
				
				$fields = array(
					$db->quoteName('reply_flag') . ' = 1',
					$db->quoteName('reply_id') . ' = ' . $messageId
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('message_id') . ' = ' . $replyingTo
				);

				$query->update("Message")->set($fields)->where($conditions);
				
				$db->setQuery($query);
				
				$result = $db->execute();
			}

		}
		else {
			return null;
		}
	}	
	
	public function reportMessage ( $fromPerson, $reportedMsgId, $reportText ) {
		
		$personId = userID();
			
		if($personId and $personId == $fromPerson){
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
			
			$fields = array(
				$db->quoteName('report_flag') . ' = 1',
				$db->quoteName('report_text') . ' = ' . $db->quote($reportText)
			);

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('message_id') . ' = ' . $reportedMsgId
			);
			
			$query->update("Message")->set($fields)->where($conditions);
			
			$db->setQuery($query);
			
			$result = $db->execute();
			
			$mailTo = \JText::_("COM_BIODIV_MESSAGELIST_REPORT_EMAIL");
			
			$mailer = \JFactory::getMailer();
			$config = \JFactory::getConfig();
			$sender = array( 
				$config->get( 'mailfrom' ),
				$config->get( 'fromname' ) 
			);
			
			

			$mailer->setSender($sender);
			
			// For more than one email address: $mailTo = array( 'person1@domain.com', 'person2@domain.com', 'person3@domain.com' );

			$mailer->addRecipient($mailTo);
			
			$body   = "A message has been reported, message id = " . $reportedMsgId . "\nReport text = " . $reportText;
			$mailer->setSubject('BES Encounters message reported');
			$mailer->setBody($body);
			
			$send = $mailer->Send();
			if ( $send !== true ) {
				error_log ( 'Error sending email reporting message id = ' . $reportedMsgId );
			} 
		}
	}
}



?>

