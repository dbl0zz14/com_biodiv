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

print '</div>'; // span5

print '</section>';


print '</div>'; // span10

//print '<input type="hidden" name="task" value="createusers"/>';
//echo JHtml::_('form.token'); 



JHTML::script("com_biodiv/admin.js", true, true);

?>
