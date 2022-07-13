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

	print '<form id="resourceUploadForm">';
	
	$pageNum = Biodiv\ResourceFile::printMetaCapture();
	
	$pageNum++;

	// ------------------------- Upload page
	print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
	
	print '<h4>'.$this->translations['if_ready']['translation_text'].'</h4>';
	
	print '<div id="resourceMetaErrorMsg" style="display:none">'.$this->translations['form_error']['translation_text'].'</div>';
	
	print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn  hideMetaError"  >';
	print $this->translations['back']['translation_text'];
	print '</div>'; // resourceBack
	
	print '<button type="submit" id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceSaveBtn chooseFiles" >'.$this->translations['create_set']['translation_text'].'</button>';
	
	print '</div>'; // resourceMeta
	
	$pageNum++;
	
	print '<div id="uploadFilesPage" class="uploadFiles" style="display:none">';
	
	print '<div id="uploadFiles"></div>';
	
	
	print '</div>'; // newUpload


	
	print '</form>';

	

}

?>