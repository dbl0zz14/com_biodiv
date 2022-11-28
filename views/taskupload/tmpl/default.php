<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_TASKUPLOAD_LOGIN").'</div>';
	
}

else {

	if ( (count ( $this->schoolRoles ) > 1)  and !(Biodiv\SchoolCommunity::isEcologist()) ) {
		
		error_log ("More than one school role found for user " . $this->personId );
		print '<h2>'.JText::_("COM_BIODIV_TASKUPLOAD_TOO_MANY_ROLES").'</h2>';

	}
	else if ( count ( $this->schoolRoles ) == 0 ) {
		error_log ("No school role found for user " . $this->personId );
		print '<h2>'.JText::_("COM_BIODIV_TASKUPLOAD_NO_ROLE").'</h2>';

	}
	else {
		
		print '<div id="uploadFiles">';
		
		print '<div class="row">';

		print '<div class="col-md-12">';
		
		print '<h2>'.JText::_("COM_BIODIV_TASKUPLOAD_WELL_DONE").'</h2>';
		print '<h3>'.JText::_("COM_BIODIV_TASKUPLOAD_UPLOAD").'</h3>';
		print '<h3>'.$this->uploadName.'</h3>';
		
		print '</div>';
		
		print '<form id="taskUploadForm">';
	
	
		$schoolId = $this->schoolRoles[0]['school_id'];
		$schoolRoleId = $this->schoolRoles[0]['role_id'];
		
		$resourceTypeId = codes_getCode ( "Task", "resource" );
		
		print "<input type='hidden' name='school' value='" . $schoolId . "'/>";
		print "<input type='hidden' name='resourceType' value='" . $resourceTypeId . "'/>";
		print "<input type='hidden' name='task' value='" . $this->taskId . "'/>";
		print "<input type='hidden' name='uploadName' value='" . $this->uploadName . "'/>";
		print "<input type='hidden' name='source' value='user'/>";
		print "<input type='hidden' name='tags' value='[".$this->moduleTagId."]'/>";
		
		
		print '<div class="col-md-12">';
		
		// Describe the upload
		print '<label for="uploadDescription"><h4>'.JText::_("COM_BIODIV_TASKUPLOAD_UPLOAD_DESC").'</h4></label>';
		print '<textarea id="uploadDescription" name="uploadDescription" rows="2" cols="100"></textarea>';
		print '<h2></h2>';
		print '</div>';


		print '<button type="submit" id="readytoupload" class="btn btn-primary btn-lg spaced chooseFiles">'.JText::_("COM_BIODIV_TASKUPLOAD_CREATE_SET").'</button>';
			
		
		print '<button id="doneNoFiles_'.$this->taskId.'" class="btn btn-default btn-lg spaced doneNoFiles">'.JText::_("COM_BIODIV_TASKUPLOAD_NO_FILES").'</button>';
		
		
		print '</form>';

		print '</div>'; // row
		
		print '</div>'; 
	}
}

?>