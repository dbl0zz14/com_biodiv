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

printAdminMenu("USERS");

//print '<a href="?option=com_biodiv"><button type="button">MammalWeb Admin Home</button></a>';

print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>MammalWeb batch user creation</h2>';


print '<h3>Batch create users here</h3>';
print '<p>Please make sure T&Cs are agreed before creating the users</p>';

//print '<form action="index.php?option=com_biodiv&task=createusers" method="post" id="createSchoolUsers" class="biodivForm">';
print '<form id="createSchoolUsers" class="biodivForm">';

print 'Is there a T&Cs agreement in place to cover these users?<br>';
print '<label class="form-control" style="padding:5px 0 10px" >';
print '<input type="radio" id="tandCsCheckedYes" name="tandCsChecked" value="1" />';
print 'Yes';
print '</label>';


print '<label for="fileStem">File stem:</label>';
print '  <input type="text" id="fileStem" name="fileStem">';

print '<label for="userStem">Username stem:</label>';
print '  <input type="text" id="userStem" name="userStem">';

print '<label for="passwordStem">Password stem:</label>';
print '  <input type="text" id="passwordStem" name="passwordStem">';

print '<label for="emailDomain">Email domain (ie what to put after @):</label>';
print '  <input type="text" id="emailDomain" name="emailDomain">';

print '<label for="numUsers">Num users required (between 1 and 30):</label>';
print '  <input type="number" id="numUsers" name="numUsers" min="1" max="30">';

print '<label for="startingNum">Starting number (use when you have some users already):</label>';
print '  <input type="number" id="startingNum" name="startingNum" min="1" max="2000">';


print '<label for="userGroup">Choose a userGroup (optional):</label>';
print '<select id="userGroup" name="userGroup">';


foreach ( $this->userGroups as $id=>$title ) {
	print '<option value="'.$id.'">'.$title.'</option>';
}
print '</select><br>';

print '<label for="project">Choose a project:</label>';
print '<select id="project" name="project">';


foreach ( $this->projects as $projectId=>$project ) {
	print '<option value="'.$projectId.'">'.$project->project_prettyname.'</option>';
}
print '</select><br>';

print '<br>Are these school users (students)?<br>';
print '<label class="form-control"  style="padding:5px 0 10px" >';
print '<input type="radio" id="addToSchoolYes" name="addToSchool" value="1">';
print 'Yes';
print '</label>';

print '<label for="school">Choose a school:</label>';
print '<select id="school" name="school">';


foreach ( $this->schools as $schoolId=>$school ) {
	print '<option value="'.$schoolId.'">'.$school->name.'</option>';
}
print '</select><br>';

print '<input class="btn btn-primary" type="submit" value="Create users" />';

print '</form>';

print '<div id="newUsersMsg"></div>';
print '<div id="newUsers"></div>';

print '</div>';

//print '<input type="hidden" name="task" value="createusers"/>';
echo JHtml::_('form.token'); 



JHTML::script("com_biodiv/admin.js", true, true);

?>
