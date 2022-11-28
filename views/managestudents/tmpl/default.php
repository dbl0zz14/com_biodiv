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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_MANAGESTUDENTS_LOGIN").'</div>';
	
}

else {
	
	print '<div class="panel">';
	print '<div class="panel-body" style="padding-top:0">';
	
	print '<div class="table-responsive">';
	print '<table class="table">';
	
	print '<thead>';
	print '<tr>';
		
	print '<th class="text-center">'.JText::_("COM_BIODIV_MANAGESTUDENTS_APPROVAL").'</th>';
	print '<th class="text-center">'.JText::_("COM_BIODIV_MANAGESTUDENTS_USERNAME").'</th>';
	print '<th>'.JText::_("COM_BIODIV_MANAGESTUDENTS_ACTIVITY").'</th>';
	print '<th>'.JText::_("COM_BIODIV_MANAGESTUDENTS_FILE").'</th>';
	
	print '</tr>';
	print '</thead>';
	print '<tbody>';
	
	
	
	// print '<div class="row">';
	
	// print '<div class="col-md-12">';
	
		
	foreach ( $this->tasks as $task ) {
		
		$student = $this->students[$task->person_id];
		$resourceSet = new Biodiv\ResourceSet ( $task->set_id );
		$files = $resourceSet->getFiles();
		
		print '<tr class="approveRow">';
		
		// --------------------------------- approve/reject
		print '<td style=padding-top:27px;">';
		
		print '<div class="row">';
		
		if ( $task->status == Biodiv\Badge::PENDING ) {
			print '<div class="col-md-12 text-center">';
			print '<div id="approveTask_'.$task->st_id.'" class="btn btn-sm btn-primary btn-block approveTask">' .
				JText::_("COM_BIODIV_MANAGESTUDENTS_APPROVE").'</div>';
			print '</div>'; // col-12
			print '<div class="col-md-12 text-center">';
			print '<div id="rejectTask_'.$task->st_id.'" class="btn btn-sm btn-default btn-block rejectTask">' .
				JText::_("COM_BIODIV_MANAGESTUDENTS_REJECT").'</div>';
			print '</div>'; // col-12
			print '<div  id="taskApproved_'.$task->st_id.'"class="col-md-12 text-center" style="display:none">';
			print '<i class="fa fa-check"></i>';
			print '</div>'; // col-12
			print '<div id="taskRejected_'.$task->st_id.'" class="col-md-12 text-center" style="display:none">';
			print '<i class="fa fa-times"></i>';
			print '</div>'; // col-12
		}
		else {
			print '<div class="col-md-12 text-center">';
			print '<i class="fa fa-check"></i>';
			print '</div>'; // col-12
		}
		print '</div>'; // row
		
		print '</td>'; 
		
		// ----------------------------------------------student avatar
		print '<td class="text-center" width="10%">';
		print '<img src="'.$student->image.'" class="img-responsive avatar" style="width:80%; margin:auto;"/>';
		print '<p><small>'.$student->username.'</small></p>';
		print '</td>'; 
		
		// ------------------------------------- task name
		print '<td style=padding-top:27px;">';
		print $task->name;
		print '</td>'; 
		
		print '<td>';
		if ( count($files) == 0 ) {
			print '<div class="row">';
			print '<div class="col-md-12">';
			print JText::_("COM_BIODIV_MANAGESTUDENTS_NO_FILES");
			print '</div>'; // col-12
			print '</div>'; // row
		}
		foreach ( $files as $resFile ) {
			$resourceFile = new Biodiv\ResourceFile ( $resFile["resource_id"], 
												$resFile["resource_type"],
												$resFile["person_id"],
												$resFile["school_id"],
												$resFile["access_level"],
												$resFile["set_id"],
												$resFile["upload_filename"],
												$resFile["title"],
												$resFile["description"],
												$resFile["source"],
												$resFile["external_text"],
												$resFile["filetype"],
												$resFile["is_pin"],
												$resFile["is_fav"],
												$resFile["is_like"],
												$resFile["num_likes"],
												$resFile["num_in_set"],
												$resFile["s3_status"],
												$resFile["url"]);
			$resourceFile->printHtml( $task->st_id );
		}
		print '</td>'; 
		
		
		print '</tr>';
		
	}

	print '</tbody>'; 

	print '</table>'; 
	print '</div>'; // table-responsive
	
	print '</div>'; // panel-body
	print '</div>'; // panel

}

?>