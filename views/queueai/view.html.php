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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewQueueAI extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		// Live $this->minPhotoId = 21100;
		$this->minPhotoId = 500; // Dev

		$app = JFactory::getApplication();
		
		$input = $app->input;
			
		$this->uploadId = $input->getInt('upload_id', 0);
		$this->projectId = $input->getInt('project_id', 0);
		$this->excludeSubs = $input->getInt('exclude_subs', 0);
		$this->aiType = $input->getString('ai_type', 0);
		$this->priority = $input->getInt('priority', 5);
		$this->latest = $input->getInt('latest', 0);
		$this->test = $input->getInt('test', 0);
		$this->testTag = $input->getString('tag', 0);
		
		
		if ( !$this->aiType ) {
			
			$this->aiType = 'CAI';
			
		}
		
		$this->photoIds = null;
		
		if ( $this->test ) {
			
			// Find the latest (or earliest) upload id not yet added to queue
			$db = JDatabase::getInstance(dbOptions());

			$query = $db->getQuery(true);
				
			$query->select("distinct photo_id")
				->from("AITest")
				->where("tag = " . $db->quote($this->testTag) );
				
			$db->setQuery($query);
			$this->photoIds = $db->loadColumn();
				
		}
		else if ( !$this->uploadId ) {
			
			if ( $this->projectId ) {
				
				if ( !$this->excludeSubs ) {
					
					$thisAndSubs = getSubProjectsById ( $this->projectId, true );
			
					$idString = implode(",", array_keys($thisAndSubs));
	
				}
				else {
					
					$idString = $this->projectId;
				}
				
				// Find the latest (or earliest) upload id not yet added to queue
				$db = JDatabase::getInstance(dbOptions());

				$query = $db->getQuery(true);
				
				if ( $this->latest ) {
					
					$query->select("MAX(P.upload_id)")
						->from("Photo P")
						->innerJoin("Upload U")
						->innerJoin("Project PROJ ON PROJ.project_id in (".$idString.")")
						->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
						->where("P.upload_id = U.upload_id")
						->where("P.photo_id > " . $this->minPhotoId)
						->where("P.s3_status = 1")
						->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($this->aiType).") ")
						->where("P.photo_id >= PSM.start_photo_id")
						->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)");
				}
				else {
					
					$query->select("MIN(P.upload_id)")
						->from("Photo P")
						->innerJoin("Upload U")
						->innerJoin("Project PROJ ON PROJ.project_id in (".$idString.")")
						->innerJoin("ProjectSiteMap PSM ON PSM.site_id = P.site_id AND PSM.project_id = PROJ.project_id")
						->where("P.upload_id = U.upload_id")
						->where("P.photo_id > " . $this->minPhotoId)
						->where("P.s3_status = 1")
						->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($this->aiType).") ")
						->where("P.photo_id >= PSM.start_photo_id")
						->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)");
				}
				
				//error_log("QueueAI view select query created: " . $query->dump());

				$db->setQuery($query);
				$this->uploadId = $db->loadResult();
				
			}
			else {
			
				// Find the latest (or earliest) upload id not yet added to queue
				$db = JDatabase::getInstance(dbOptions());

				$query = $db->getQuery(true);
				
				if ( $this->latest ) {
					
					$query->select("MAX(P.upload_id)")
						->from("Photo P")
						->innerJoin("Upload U")
						->where("P.upload_id = U.upload_id")
						->where("P.photo_id > " . $this->minPhotoId)
						->where("P.s3_status = 1")
						->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($this->aiType).") ");
				}
				else {
					
					$query->select("MIN(P.upload_id)")
						->from("Photo P")
						->innerJoin("Upload U")
						->where("P.upload_id = U.upload_id")
						->where("P.photo_id > " . $this->minPhotoId)
						->where("P.s3_status = 1")
						->where("P.photo_id NOT IN (select photo_id from AIQueue where ai_type = ".$db->quote($this->aiType).") ");
				}
				
				//error_log("QueueAI view select query created: " . $query->dump());

				$db->setQuery($query);
				$this->uploadId = $db->loadResult();
			
			}
			
		}
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>
