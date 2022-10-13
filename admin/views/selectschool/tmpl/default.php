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

printAdminMenu("SCHOOL");

print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>Select a school</h2>';


print '<table class="table table-striped" id="schoolList">';
print '<thead>';
print '<tr>';
print '<th class="nowrap">';
print 'School name';
print '</th>';
print '<th class="nowrap">';
print 'School id';
print '</th>';
print '</tr>';
print '</thead>';
						
						
foreach ( $this->schools as $schoolId=>$school ) {
	
	print '<tr>';
	print '<td>';
	print '<a href="?option=com_biodiv&view=school&id='.$schoolId.'">';
	print $school->name;
	print '</a>';
	print '</td>';
	print '<td>';
	print $school->school_id;
	print '</td>';
	print '</tr>';
	
}

print '</table>';


print '</div>';



JHTML::script("com_biodiv/admin.js", true, true);

?>
