<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// error_log ( "Task upload template called" );

// error_log ( "Resource upload template called" );

if ( !$this->personId ) {
	// Please log in button
	print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {

	if ( count ( $this->schoolRoles ) > 1 ) {
		error_log ("More than one school role found for user " . $this->personId );
		print '<h2>'.$this->translations['too_many_roles']['translation_text'].'</h2>';

	}
	else if ( count ( $this->schoolRoles ) == 0 ) {
		error_log ("No school role found for user " . $this->personId );
		print '<h2>'.$this->translations['no_role']['translation_text'].'</h2>';

	}
	else {
		
		//print '<h2>'.$this->translations['you_completed']['translation_text'].' '.$this->taskName.' '.$this->translations['task']['translation_text'].' '.
		//		$this->translations['from']['translation_text'].' '.$this->badgeGroup.' '.$this->translations['badge']['translation_text'].' '.
		//		$this->lockLevel. ': '.$this->badgeName.'</h2>';

		

		print '<div class="row">';

		print '<div class="col-md-12">';
		
		print '<h2>'.$this->translations['well_done']['translation_text'].'</h2>';
		print '<h3>'.$this->translations['upload']['translation_text'].'</h3>';
		print '<h3>'.$this->uploadName.'</h3>';
		
		print '</div>';
		
		print '<form id="resourceUploadForm">';
	
	
		$schoolId = $this->schoolRoles[0]['school_id'];
		$schoolRoleId = $this->schoolRoles[0]['role_id'];
		
		$resourceTypeId = codes_getCode ( "Task", "resource" );
		
		print "<input type='hidden' name='school' value='" . $schoolId . "'/>";
		print "<input type='hidden' name='resourceType' value='" . $resourceTypeId . "'/>";
		print "<input type='hidden' name='task' value='" . $this->taskId . "'/>";
		print "<input type='hidden' name='uploadName' value='" . $this->uploadName . "'/>";
		
		
		

/*
		print '<div class="col-md-12">';
		
		// Name the upload
		
		print '<label for="uploadName"><h4>'.$this->translations['name_upload']['translation_text'].'</h4></label>';
		print '<input type="text" id="uploadName" name="uploadName">';
		print '<h2></h2>';
		print '</div>';
*/

		print '<div class="col-md-12">';
		
		// Describe the upload
		print '<label for="uploadDescription"><h4>'.$this->translations['upload_desc']['translation_text'].'</h4></label>';
		print '<textarea id="uploadDescription" name="uploadDescription" rows="2" cols="100"></textarea>';
		print '<h2></h2>';
		print '</div>';



		//print '<div class="col-md-3">';

		print '<button type="submit" id="readytoupload" class="btn btn-primary btn-lg spaced chooseFiles">'.$this->translations['create_set']['translation_text'].'</button>';
		//print '<h2></h2>';
		//print '</div>'; // col-md-3

		
		
		//print '<div class="col-md-3">';

		print '<button id="doneNoFiles_'.$this->taskId.'" class="btn btn-default btn-lg spaced doneNoFiles">'.$this->translations['no_files']['translation_text'].'</button>';
		//print '<h2></h2>';
		//print '</div>'; // col-md-3


		//print '<div class="col-md-3">';

		print '<button class="btn btn-default btn-lg spaced browseBadges">'.$this->translations['back_activities']['translation_text'].'</button>';
		//print '<h2></h2>';
		//print '</div>'; // col-md-3
		
		print '</form>';

		print '</div>'; // row
	}
}

?>