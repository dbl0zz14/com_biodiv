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

print '<h2>Select a project</h2>';


print '<table class="table table-striped" id="userList">';
print '<thead>';
print '<tr>';
print '<th class="nowrap">';
//print '<a href="#" onclick="return false;" class="js-stools-column-order hasPopover" data-order="a.name" data-direction="DESC" data-name="Name" title="" data-content="Select to sort by this column" data-placement="top" data-original-title="Name">
//Name<span class="icon-arrow-up-3"></span></a>
print 'Display name';
print '</th>';
print '<th class="nowrap">';
print 'Description';
print '</th>';
print '<th class="nowrap">';
print 'ID';
print '</th>';
print '<th class="nowrap">';
print 'Access';
print '</th>';
print '</tr>';
print '</thead>';
						
						
foreach ( $this->projects as $projectId=>$project ) {
	
	print '<tr>';
	print '<td>';
	print '<a href="?option=com_biodiv&view=project&id='.$projectId.'">';
	print $project->project_prettyname;
	print '</a>';
	print '</td>';
	print '<td>';
	print $project->project_description;
	print '</td>';
	print '<td>';
	print $projectId;
	print '</td>';
	print '<td>';
	print $this->accessLevels[$project->access_level];
	print '</td>';
	print '</tr>';
	
}

print '</table>';


print '</div>';



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/admin.js", true, true);

?>
