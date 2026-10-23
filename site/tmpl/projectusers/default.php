<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Language\Text;

if ( !$this->personId ) {
	
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.Text::_("COM_BIODIV_PROJECTUSERS_LOGIN").'</div>';
	
}
else if ( !$this->access ) {
	
	print '<div class="row">';
	print '<div class="col-xs-12 col-sm-12 col-md-12 h4">'.Text::_("COM_BIODIV_PROJECTUSERS_NO_ACCESS").'</div>';
	print '</div>';
	
}
else {
	
	print '<div class="row">';
	print '<div class="col-xs-12 col-sm-12 col-md-12">';
	
	print '<h2>'.$this->projectName . ' ' . Text::_("COM_BIODIV_PROJECTUSERS_PROJECT_USERS") .'</h2>';
	
	print '<button type="button" class="btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">'.Text::_("COM_BIODIV_PROJECTUSERS_ADD_USERS").'</button>';
	
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
	print '<th>'.Text::_("COM_BIODIV_PROJECTUSERS_USERID").'</th>';
	print '<th>'.Text::_("COM_BIODIV_PROJECTUSERS_USERNAME").'</th>';
	print '<th>'.Text::_("COM_BIODIV_PROJECTUSERS_EMAIL").'</th>';
	print '<th>'.Text::_("COM_BIODIV_PROJECTUSERS_ROLE").'</th>';
	print '</tr>';
	print '</thead>';
	print '<tbody>';

	foreach ( $this->users as $currUser ) {
		print '<tr>';
		print '<td>'.$currUser->person_id.'</td>';
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
print '        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.Text::_("COM_BIODIV_PROJECTUSERS_ADD_FOR").' ' . $this->projectName.'</h4>';
print '      </div>';
print '     <div class="modal-body">';
//print '	    <div>';
print '     <h4>'.Text::_("COM_BIODIV_PROJECTUSERS_ADD_HELP").'</h4>';
print '     <label for="emailsInput">'.Text::_("COM_BIODIV_PROJECTUSERS_EMAILS_LABEL").'</label>';
print '     <textarea id="emailsInput" name="emailsInput" ></textarea>';
//print '     </div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" id="saveUsers" class="btn btn-primary" data-bs-dismiss="modal">'.Text::_("COM_BIODIV_PROJECTUSERS_SAVE").'</button>';
print '        <button type="button" class="btn" data-bs-dismiss="modal">'.Text::_("COM_BIODIV_PROJECTUSERS_CANCEL").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';

?>