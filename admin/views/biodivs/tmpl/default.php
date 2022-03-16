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


print '<h2>MammalWeb admin page</h2>';

if ( $this->deleted ) {
	
	print '<h4>Files deleted</h4>';
}

print '<h3>Download created users here</h3>';
$reportRoot = JPATH_SITE."/biodivimages/reports";

$filePath = $reportRoot."/school";

$files = array();
if ( file_exists ( $filePath ) ) {
	// Returns array of files
	$files = scandir($filePath . "/");
}

$numFiles = count ( $files ) - 2;
if ( $numFiles == 0 ) {
	print '<p>No files to download</p>';
}

if ( $numFiles > 0 ) {
	print '<table>';
	print '<tbody>';

	foreach ( $files as $toDownload ) {
		$ext =  JFile::getExt($toDownload);
		if ( $ext == "csv" ) {
			print '<tr>';
			$downloadURL = JURI::root()."/biodivimages/reports/school/".$toDownload;
			print '<td><a href="'.$downloadURL.'" download="'.$toDownload.'">Download '.$toDownload.'</a></td>';
			print '</tr>';
		}
	}

	print '</tbody>';
	print '</table>';
	
	print 'Please ensure you have downloaded all files before deleting';
	print '<form action="index.php?option=com_biodiv&task=deletefiles" method="post" id="deleteUserFiles"><input type="submit" value="Delete files"></form>';

}


print '<h3>Batch create users here</h3>';
print '<p>Please make sure T&Cs are agreed before creating the users</p>';

print '<form action="index.php?option=com_biodiv&task=createusers" method="post" id="createSchoolUsers" class="biodivForm">';

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

// print '<label for="userGroup">User group to add (optional):</label>';
// print '  <input type="number" id="userGroup" name="userGroup" >';

print '<label for="userGroup">Choose a userGroup (optional):</label>';
print '<select id="userGroup" name="userGroup">';

//print_r ( $this->schools );
foreach ( $this->userGroups as $id=>$title ) {
	print '<option value="'.$id.'">'.$title.'</option>';
}
print '</select><br>';

print '<label for="project">Choose a project:</label>';
print '<select id="project" name="project">';

//print_r ( $this->schools );
foreach ( $this->projects as $projectId=>$projectName ) {
	print '<option value="'.$projectId.'">'.$projectName.'</option>';
}
print '</select><br>';

print '<br>Are these school users (students)?<br>';
print '<label class="form-control"  style="padding:5px 0 10px" >';
print '<input type="radio" id="addToSchoolYes" name="addToSchool" value="1">';
print 'Yes';
print '</label>';

print '<label for="school">Choose a school:</label>';
print '<select id="school" name="school">';

//print_r ( $this->schools );
foreach ( $this->schools as $schoolId=>$schoolName ) {
	print '<option value="'.$schoolId.'">'.$schoolName.'</option>';
}
print '</select><br>';

print '<input type="submit" value="Create Users">';

print '</form>';

//print '<input type="hidden" name="task" value="createusers"/>';
echo JHtml::_('form.token'); 

?>
