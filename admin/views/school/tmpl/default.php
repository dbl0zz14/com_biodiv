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

print '<h2>View school: '.$this->school->name.'</h2>';

print '<section>';

print '<div class="span2">';
print '<a href="?option=com_biodiv&view=editschool&id='.$this->schoolId.'" ><button type="button" class="btn btn-primary js-stools-btn-clear" title="Edit school" data-original-title="Edit school">Edit school</button></a>';
print '</div>';
print '<div class="span4">';
print '<a href="?option=com_biodiv&view=addschoolusers&id='.$this->schoolId.'" ><button type="button" class="btn btn-primary js-stools-btn-clear" >Add school user</button></a>';
print '</div>';

print '</section>';



print '<section>';

print '<div class="span8">';

print '<h3>School details</h3>';

print '<p><img src="'.JURI::root().$this->school->image.'" style="height:20vh"></p>';

print '<table class="table table-striped">';
print '<tbody>';
print '<tr>';
print '<th>School name</th>';
print '<td>'.$this->school->name.'</td>';
print '</tr>';
print '<tr>';
print '<th>Id</th>';
print '<td>'.$this->schoolId.'</td>';
print '</tr>';
print '<tr>';
print '<th>Image</th>';
print '<td>';
//print '<img src="'.JURI::root().$this->school->image.'">';
//print '<p>'.$this->school->image.'</p>';
print $this->school->image;
print '</td>';
print '</tr>';
print '<tr>';
print '<th>Project id</th>';
print '<td>'.$this->school->project_id.'</td>';
print '</tr>';
print '<tr>';
print '<th>Project name</th>';
print '<td>'.$this->school->project.'</td>';
print '</tr>';
print '</tbody>';
print '</table>';

print '</div>'; // span8

print '</section>';

print '</div>'; // span10

//print '<input type="hidden" name="task" value="createusers"/>';
//echo JHtml::_('form.token'); 



?>
