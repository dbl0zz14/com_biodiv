<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "Resource upload template called" );

if ( !$this->personId ) {
	// Please log in button
	print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
		
	print '<h2>'.$this->translations['upload']['translation_text'].'</h2>';

	print '<div class="row">';

	print '<form id="resourceUploadForm">';

	print '<div class="col-md-12">';

	// Create dropdown of schools
	print '<label for="school"><h4>'.$this->translations['choose_school']['translation_text'].'</h4></label>';
	print "<select id = 'school' name = 'school'>"; // class = 'form-control'>";

	$isFirst = true;
	$firstId = null;
	foreach($this->schoolRoles as $schoolRole){
		
		if ( $isFirst ) {
			// Default to first project
			print "<option value='".$schoolRole['school_id']."' selected>".$schoolRole['name']."</option>";
			$isFirst = false;
			$firstId = $schoolRole['school_id'];
		}
		else {
			print "<option value='".$schoolRole['school_id']."'>".$schoolRole['name']."</option>";
		}
	}

	print "</select>";
	print '<h2></h2>';

	print '</div>';

	print '<div class="col-md-12">';

	// Create dropdown of resource types
	print '<label for="resourceType"><h4>'.$this->translations['choose_type']['translation_text'].'</h4></label>';
	print "<select id = 'resourceType' name = 'resourceType'>"; // class = 'form-control'>";

	$isFirst = true;
	$firstTypeId = null;
	foreach($this->resourceTypes as $resType){
		if ( $isFirst ) {
			// Default to first project
			print "<option value='".$resType[0]."' selected>".$resType[1]."</option>";
			$isFirst = false;
			$firstTypeId = $resType[0];
		}
		else {
			print "<option value='".$resType[0]."'>".$resType[1]."</option>";
		}
	}

	print "</select>";
	print '<h2></h2>';

	print '</div>';


	print '<div class="col-md-12">';
	// Name the upload

	print '<label for="uploadName"><h4>'.$this->translations['name_upload']['translation_text'].'</h4></label>';
	print '<input type="text" id="uploadName" name="uploadName">';
	print '<h2></h2>';
	print '</div>';


	print '<div class="col-md-12">';
	// Name the upload

	print '<label for="uploadDescription"><h4>'.$this->translations['upload_desc']['translation_text'].'</h4></label>';
	//print '<input type="text" id="uploadDescription" name="uploadDescription">';
	print '<textarea id="uploadDescription" name="uploadDescription" rows="2" cols="100"></textarea>';
	print '<h2></h2>';
	print '</div>';



	print '<div class="col-md-12">';

	print '<button type="submit" id="readytoupload" class="btn btn-info chooseFiles"><h4>'.$this->translations['create_set']['translation_text'].'</h4></button>';
	//print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';
	print '<h2></h2>';
	print '</div>'; // col-md-12


	print '</form>';

	//print '<div>Choose file below here</div>';
	//print '<div id="resourceuploader">'.$this->translations['choose_files']['translation_text'].'</div>';
	//print '<div id="resourceuploader"></div>';
	//print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';

	print '</div>'; // row

}

?>