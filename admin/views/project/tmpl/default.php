<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

printAdminMenu("PROJECT");

//print '<a href="?option=com_biodiv"><button type="button">MammalWeb Admin Home</button></a>';

print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>View project: '.$this->project->project_prettyname.'</h2>';

print '<section>';

print '<p>';
print ' <a href="?option=com_biodiv&view=editproject&id='.$this->projectId.'" ><button type="button" class="btn btn-primary js-stools-btn-clear" title="Edit project" data-original-title="Edit project">Edit project</button></a>';
print '</p>';

print '</section>';

print '<section>';


print '<div class="span5">';

print '<h3>Main details</h3>';

print '<table class="table table-striped">';
print '<tbody>';
print '<tr>';
print '<th>Display name</th>';
print '<td>'.$this->project->project_prettyname.'</td>';
print '</tr>';
print '<tr>';
print '<th>Name</th>';
print '<td>'.$this->project->project_name.'</td>';
print '</tr>';
print '<tr>';
print '<th>Description</th>';
print '<td>'.$this->project->project_description.'</td>';
print '</tr>';
print '<tr>';
print '<th>Id</th>';
print '<td>'.$this->projectId.'</td>';
print '</tr>';
print '<tr>';
print '<th>Image folder</th>';
print '<td>'.$this->project->dirname.'</td>';
print '</tr>';
print '<tr>';
print '<th>Image filename</th>';
print '<td>'.$this->project->image_file.'</td>';
print '</tr>';
print '<tr>';
print '<th>Article id</th>';
print '<td>'.$this->project->article_id.'</td>';
print '</tr>';
print '<tr>';
print '<th>Parent project</th>';
print '<td>'.$this->project->parent_name.'</td>';
print '</tr>';
print '<tr>';
print '<th>Access level</th>';
print '<td>'.$this->accessLevels[$this->project->access_level].'</td>';
print '</tr>';
print '<tr>';
print '<th>Listing level</th>';
print '<td>'.$this->project->listing_level.'</td>';
print '</tr>';
print '<tr>';
print '<th>Classification priority</th>';
print '<td>'.$this->project->priority.'</td>';
print '</tr>';
print '<tr>';
print '<th>BES Encounters school?</th>';
print '<td>';
if ( $this->project->school_id ) {
	print $this->project->school_name;
}
else {
	print 'No';
}
print '</td>';
print '</tr>';
print '</tbody>';
print '</table>';

print '</div>'; // span5

print '<div class="span5">';
print '<h3>Species lists</h3>';
print '<table class="table table-striped">';
print '<tbody>';
if ( count($this->speciesLists) == 0 ) {
	print '<tr><td>None</td></th>';
}
else {
	foreach ( $this->speciesLists as $id=>$name ) {
		print '<tr><td>'.$name.'</td></th>';
	}
}
print '</tbody>';
print '</table>';

print '<br/>';

print '<h3>Project page display options</h3>';
print '<table class="table table-striped">';
print '<tbody>';
if ( count($this->displayOptions) == 0 ) {
	print '<tr><td>None</td></th>';
}
else {
	foreach ( $this->displayOptions as $id=>$name ) {
		print '<tr><td>'.$name.'</td></th>';
	}
}
print '</tbody>';
print '</table>';

print '<p>';
print '<a href="'.BIODIV_ROOT.'&view=projecthome&project_id='.$this->projectId.'" target="_blank" rel="noopener noreferrer" ><button type="button" class="btn js-stools-btn-clear" title="View project page" data-original-title="View project page">View project page in new tab</button></a>';
print '</p>';


print '<br/>';

print '<h3>Project admins</h3>';
print '<table class="table table-striped">';
if ( count($this->projectAdmins) == 0 ) {
	print '<tbody>';
	print '<tr><td>None</td></th>';
	print '</tbody>';
}
else {
	print '<thead>';
	print '<tr><th>Username</th><th>Name</th><th>Email</th></tr>';
	print '</thead>';
	print '<tbody>';
	foreach ( $this->projectAdmins as $id=>$user ) {
		print '<tr><td>'.$user->username.'</td><td>'.$user->name.'</td><td>'.$user->email.'</td></th>';
	}
	print '</tbody>';
}
print '</table>';


print '</div>'; // span5

print '</section>';


print '</div>'; // span10

//print '<input type="hidden" name="task" value="createusers"/>';
//echo JHtml::_('form.token'); 



print '<div id="addAdminModal" class="modal modal-sm fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
// print '      <div class="modal-header text-right">';
// print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">Close &times;</div>';
// print '      </div>';
print '     <div class="modal-body">';
print '     <h3>Add project admin</h3>';
print '	    <div>Add user stuff here</div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



JHTML::script("com_biodiv/admin.js", true, true);

?>
