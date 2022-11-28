<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCELIST_LOGIN").'</div>';
}

else {
	
	print '<div class="row">';

	print '<div class="col-md-12">';
	
	$currSetId = 0;
	
	foreach ( $this->doneTasksNoFiles as $task ) {
		print '<div class="row">';
		print '<div class="col-md-2 col-sm-2 col-xs-12">';
		print '<div>'.$task->username.'</div>';
		print '</div>'; // col-2
		print '<div class="col-md-8 col-sm-6 col-xs-12">';
		print '<div>'.$task->badge_group . ' ' . JText::_("COM_BIODIV_RESOURCELIST_BADGE") . '. ' . $task->badge_name . ' - ' . $task->name.'</div>';
		print '</div>'; // col-8
		print '<div class="col-md-2 col-sm-4 col-xs-12 text-right">';
		if ( $this->getApprove ) {
			print '<div id="approveTask_'.$task->st_id.'" class="btn approveTask"><i class="fa fa-check"></i></div>';
			print '<div id="rejectTask_'.$task->st_id.'" class="btn rejectTask"><i class="fa fa-times"></i></div>';
		}
		print '</div>';
		print '</div>'; 
	}
	
	if ( count($this->resourceFiles) == 0 ) {
		if ( $this->student ) {
			print '<div class="h3">'.JText::_("COM_BIODIV_RESOURCELIST_NO_STUDENT_FILES").'</div>';
		}
	}
	
	foreach ( $this->resourceFiles as $resourceFile ) {
		
		if ( $this->includeSet ) {
			$newSetId = $resourceFile["set_id"];
		
			$studentTaskId = $resourceFile["st_id"];
			
			if ( $newSetId != $currSetId ) {
				print '<div class="row">';
				if ( array_key_exists ( "username", $resourceFile ) ) {
					print '<div class="col-md-2 col-sm-2 col-xs-3">';
					print '<div>'.$resourceFile["username"].'</div>';
					print '</div>'; // col-2
				}
				print '<div class="col-md-8 col-sm-6 col-xs-6">';
				print '<div class="resourceSet">'.$resourceFile["set_name"].'</div>';
				print '</div>'; // col-8
				print '<div class="col-md-2 col-sm-4 col-xs-3 text-right">';
				if ( $this->getApprove ) {
					print '<div id="approveTask_'.$studentTaskId.'" class="btn approveTask"><i class="fa fa-check"></i></div>';
					print '<div id="rejectTask_'.$studentTaskId.'" class="btn rejectTask"><i class="fa fa-times"></i></div>';
				}
				print '</div>';
				$currSetId = $newSetId;
				print '</div>';
			}
		}
		
		$resourceId = $resourceFile["resource_id"];
		
		$resourceFile = new Biodiv\ResourceFile ( $resourceId, 
												$resourceFile["resource_type"],
												$resourceFile["person_id"],
												$resourceFile["school_id"],
												$resourceFile["access_level"],
												$resourceFile["set_id"],
												$resourceFile["upload_filename"],
												$resourceFile["title"],
												$resourceFile["description"],
												$resourceFile["source"],
												$resourceFile["external_text"],
												$resourceFile["filetype"],
												$resourceFile["is_pin"],
												$resourceFile["is_fav"],
												$resourceFile["is_like"],
												$resourceFile["num_likes"],
												$resourceFile["num_in_set"],
												$resourceFile["s3_status"],
												$resourceFile["url"]);
		
		if ( $this->student ) {
			$resourceFile->printStudentHtml();
		}
		else {
			$resourceFile->printHtml();
		}
		
	}

	print '</div>'; // col-12

	print '</div>'; // row

}

?>