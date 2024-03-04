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
* HTML View class for the MammalWeb Component
*
*/
class BioDivViewRequestAI extends JViewLegacy
{
	
	const TO_SEND			= 0;
	const SEND_SUCCESS		= 1;
	const PROCESSING		= 2;
	const PROCESSING_ERROR	= 3;
	const SEND_ERROR		= 4;
	const FILETYPE_ERROR	= 5;
	const LONG_SEQUENCE		= 6;
	
	const MAX_NUM = 50;
	const NUM_REQUESTS = 5;
	
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		$app = JFactory::getApplication();
		
		$input = $app->input;
			
		$this->repeat = $input->getInt('repeat', 0);
		
		if ( !$this->repeat ) {
			$this->repeat = self::NUM_REQUESTS;
		}
		
		$this->number = $input->getInt('number', 0);
		
		if ( !$this->number ) {
			$this->number = self::MAX_NUM;
		}
		
		$this->photos = array();
		$this->siteIds = array();
		
		for ( $i=0; $i < $this->repeat; $i++ ) {
			
			if ( canRunScripts() ) {
				
				// Check the site_id we're using
				$db = JDatabase::getInstance(dbOptions());
				$query = $db->getQuery(true);
				$query->select("P.site_id")
					->from("AIQueue AIQ")
					->innerJoin("Photo P on P.photo_id = AIQ.photo_id")
					->where("ai_type = " . $db->quote('CAI') )
					->where("AIQ.status = " . self::TO_SEND)
					->where("P.s3_status = 1")
					->where("P.sequence_id > 0")
					->order("AIQ.priority, AIQ.aiq_id");

				$db->setQuery($query, 0, 1); 
				
				$this->siteIds[$i] = $db->loadResult();
				$siteId = $this->siteIds[$i];
				
				if ( $siteId ) {
					
					// Create a list of photos to be sent to Conservation AI for classification.
					$db = JDatabase::getInstance(dbOptions());
					$query = $db->getQuery(true);
					$query->select("AIQ.photo_id, P.person_id, P.site_id, P.dirname, P.filename, P.sequence_num")
						->from("AIQueue AIQ")
						->innerJoin("Photo P on P.photo_id = AIQ.photo_id")
						->where("ai_type = " . $db->quote('CAI') )
						->where("AIQ.status = " . self::TO_SEND)
						->where("P.site_id = " . $siteId )
						->where("P.s3_status = 1")
						->where("P.sequence_id > 0")
						->order("AIQ.priority, AIQ.aiq_id");
						
					$db->setQuery($query, 0, self::MAX_NUM); // LIMIT $max_num
					
					//error_log("RequestAI select query created: " . $query->dump());
			
					$this->photos[$i] = $db->loadAssocList("photo_id");
					
					$theseIds = array_keys($this->photos[$i]);
					
					$this->setQueueStatusMultiple ( $theseIds, BioDivViewRequestAI::PROCESSING );
				
				}
				
			}
		}
		
		parent::display($tpl);
	}
	
	
	
	
	protected function setQueueStatusSingle ( $photoId, $status, $msg = null ) {
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		$table = "AIQueue";
			
		$fields = array(
			$db->quoteName('status') . ' = ' . $status,
			$db->quoteName('timestamp') . ' = CURRENT_TIMESTAMP' 
		);

		if ( $msg ) {
			$fields[] = $db->quoteName('msg') . ' = ' . $db->quote($msg);
		}
		
		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('photo_id') . ' = ' . $photoId 
		);

		$query->update($db->quoteName($table))->set($fields)->where($conditions);
		
		//error_log("setQueueStatusSingle update query created: " . $query->dump());
		
		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;
		
	}
	
	
	protected function setQueueStatusMultiple ( $photoIdArray, $status, $msg = null ) {
		
		$result = true;
		if ( count($photoIdArray) > 0 ) {
			
			$db = JDatabase::getInstance(dbOptions());
			$query = $db->getQuery(true);
			$table = "AIQueue";
			$photoStr = implode(',', $photoIdArray);
				
			$fields = array(
				$db->quoteName('status') . ' = ' . $status,
				$db->quoteName('timestamp') . ' = CURRENT_TIMESTAMP' 
			);

			if ( $msg ) {
				$fields[] = $db->quoteName('msg') . ' = ' . $db->quote($msg);
			}
			
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('photo_id') . ' IN (' . $photoStr . ') '
			);

			$query->update($db->quoteName($table))->set($fields)->where($conditions);
			
			//error_log("setQueueStatusMultiple update query created: " . $query->dump());
			
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		return $result;
	}
}



?>
