<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
}
else {
	
	if ( $this->sent ) {
		
		//print '<h4>' . $this->translations['sent_messages']['translation_text'] . '</h4>';
		
		print '<div class="panel">';
		print '<div class="panel-body">';
		
		print '<div class="table-responsive">';
		print '<table class="table">';
		print '<thead>';
		print '<th width="15%" class="text-center">'.$this->translations['to']['translation_text'].'</th><th>Message</th><th>Date sent</th>';
		print '</thead>';
		print '<tbody>';
		
		foreach ( $this->messages as $message ) {
				
			print '<div class="tr">';
			
			print '<td>';
			
			print '<div class="row">';
			
			print '<div class="col-md-6 col-md-offset-3 text-center"><img class="img-responsive avatar" src="'.$message->avatar.'" /></div>';
			
			print '<div class="col-md-12  text-center">'.$message->username.'</div>';
			
			print '</div>'; // row
			
			print '</td>'; 
			
			if ( $message->read_flag == 0 ) {
				print '<strong>';
			}
			
			print '<td>'.$message->text.'</td>';
			
			print '<td>'.$message->timestamp.'</td>';
			
			if ( $message->read_flag == 0 ) {
				print '</strong>';
			}
			
			print '</tr>'; // row
			
			
		}
		
		print '</tbody>';
		print '</table>';
		print '</div>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
	}
	else {
		
		//print '<h4>' . $this->translations['messages']['translation_text'] . '</h4>';
		
		print '<div class="panel">';
		print '<div class="panel-body">';
		
		print '<div class="table-responsive">';
		print '<table class="table">';
		print '<thead>';
		print '<th width="15%" class="text-center">'.$this->translations['from']['translation_text'].'</th><th>Message</th><th>Date sent</th><th></th><th></th><th></th>';
		print '</thead>';
		print '<tbody>';
		//print '<div id="displaySelectedCharts_'. $groupId .'" class="displaySelectedCharts"></div>';
		//print '<tr></tr>';
	
		// print '<div class="row">';
			
		// print '<div class="col-md-2 col-sm-2 col-xs-2 text-center"><p>'.$this->translations['from']['translation_text'].'</p></div>';
		
		// print '</div>'; // row
		
		//print '<div class="list-group list-group-flush" role="group" aria-label="New message list group">';
		
		foreach ( $this->messages as $message ) {
				
			//print '<div class="row list-group-item">';
			print '<tr>';
			
			print '<td>';
			
			print '<div class="row">';
			
			print '<div class="col-md-6 col-md-offset-3 text-center"><img class="img-responsive avatar" src="'.$message->avatar.'" /></div>';
			
			print '<div class="col-md-12  text-center">'.$message->username.'</div>';
			
			print '</div>'; // row
			
			print '</td>'; // col-1
			
			$newMessageClass = "";
			
			if ( $message->read_flag == 0 ) {
				print '<strong>';
				$newMessageClass = "newMessage";
			}
			
			print '<td class="'.$newMessageClass.'">'.$message->text.'</td>';
			
			print '<td>'.$message->timestamp.'</td>';
			
			if ( $message->read_flag == 0 ) {
				print '</strong>';
			}
			
			print '<td ><button id="reply_'.$message->message_id.'" class="btn replyBtn" data-sender="'.$message->from_person.'">'.$this->translations['reply']['translation_text'].'</button></td>';
			
			print '<td id="replyFlag_'.$message->message_id.'" >';
			
			if ( $message->reply_flag == 1 ) {
				print '<i class="fa fa-reply"></i>';
			}
			else {
				print '<i class="fa fa-reply" style="display:none"></i>';
			}
			
			print '</td>';
			
			print '<td ><button id="report_'.$message->message_id.'" class="btn reportBtn" data-sender="'.$message->from_person.'">'.$this->translations['report']['translation_text'].'</button></td>';
			
			print '</tr>'; // row
		}
		
		print '</tbody>';
		print '</table>';
		
		print '</div>'; // table-responsive
		
		print '</div>'; // panel-body
		print '</div>'; // panel
			
		$this->messageList->messagesRead();
	}
	
}


?>