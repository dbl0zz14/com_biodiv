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
else if ( !$this->access ) {
	
	print '<div class="row">';
	print '<div class="col-xs-12 col-sm-12 col-md-12 h4">'.$this->translations['no_access']['translation_text'].'</div>';
	print '</div>';
	
}
else {
	
	print '<div class="row">';
	print '<div class="col-xs-12 col-sm-12 col-md-12">';
	
	print '<h2>'.$this->projectName . ' ' . $this->translations['project_users']['translation_text'] .'</h2>';
	
	print '<button type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#addUserModal">'.$this->translations['add_users']['translation_text'].'</button>';
	
	if ( $this->userMessages ) {
		
		print '<p></p>';
		print '<div class="messages well">';
		foreach ( $this->userMessages as $message ) {
			print '<div>'.$message.'</div>';
		}
		print '</div>';
	}
	
	print '<div class="table-responsive">';
	
	print '<table class="table table-striped">';
	print '<thead>';
	print '<tr>';
	print '<th>'.$this->translations['username']['translation_text'].'</th>';
	print '<th>'.$this->translations['email']['translation_text'].'</th>';
	print '<th>'.$this->translations['role']['translation_text'].'</th>';
	print '</tr>';
	print '</thead>';
	print '<tbody>';

	foreach ( $this->users as $currUser ) {
		print '<tr>';
		print '<td>'.$currUser->username.'</td>';
		print '<td>'.$currUser->email.'</td>';
		print '<td>'.$currUser->role.'</td>';
		print '</tr>';
	}
	
	print '</tbody>';
	print '</table>';
	
	print '</div>'; // table-responsive
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
}


print '<div id="addUserModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog ">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.$this->translations['add_for']['translation_text'].' ' . $this->projectName.'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div>';
print '     <h4>'.$this->translations['add_help']['translation_text'].'</h4>';
print '     <label for="emailsInput">'.$this->translations['emails_label']['translation_text'].'</label>';
print '     <textarea id="emailsInput" name="emailsInput" ></textarea>';
print '     </div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" id="saveUsers" class="btn btn-primary" data-dismiss="modal">'.$this->translations['save']['translation_text'].'</button>';
print '        <button type="button" class="btn" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';

?>