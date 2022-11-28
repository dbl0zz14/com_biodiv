<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_PROJECTUSERS_LOGIN").'</div>';
	
}
else if ( !$this->access ) {
	
	print '<div class="row">';
	print '<div class="col-xs-12 col-sm-12 col-md-12 h4">'.JText::_("COM_BIODIV_PROJECTUSERS_NO_ACCESS").'</div>';
	print '</div>';
	
}
else {
	
	print '<div class="row">';
	print '<div class="col-xs-12 col-sm-12 col-md-12">';
	
	print '<h2>'.$this->projectName . ' ' . JText::_("COM_BIODIV_PROJECTUSERS_PROJECT_USERS") .'</h2>';
	
	print '<button type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#addUserModal">'.JText::_("COM_BIODIV_PROJECTUSERS_ADD_USERS").'</button>';
	
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
	print '<th>'.JText::_("COM_BIODIV_PROJECTUSERS_USERNAME").'</th>';
	print '<th>'.JText::_("COM_BIODIV_PROJECTUSERS_EMAIL").'</th>';
	print '<th>'.JText::_("COM_BIODIV_PROJECTUSERS_ROLE").'</th>';
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
print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_PROJECTUSERS_ADD_FOR").' ' . $this->projectName.'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div>';
print '     <h4>'.JText::_("COM_BIODIV_PROJECTUSERS_ADD_HELP").'</h4>';
print '     <label for="emailsInput">'.JText::_("COM_BIODIV_PROJECTUSERS_EMAILS_LABEL").'</label>';
print '     <textarea id="emailsInput" name="emailsInput" ></textarea>';
print '     </div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" id="saveUsers" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_PROJECTUSERS_SAVE").'</button>';
print '        <button type="button" class="btn" data-dismiss="modal">'.JText::_("COM_BIODIV_PROJECTUSERS_CANCEL").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';

?>