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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
	
}

else if ( $this->isStaff ) {
		
	print '<h2>'.$this->translations['add']['translation_text'].'</h2>';
	
	print '<h4>'.$this->translations['edit_later']['translation_text'].'</h4>';
	

	print '<form id="resourceAddForm">';
	
	//$pageNum = Biodiv\ResourceFile::printMetaCapture();
	
	$pageNum=1;

	// ------------------------- Upload page
	// print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" >';
	
	// print '<h4>'.$this->translations['if_ready']['translation_text'].'</h4>';
	
	// print '<button type="submit" id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg chooseFiles" >'.$this->translations['choose_files']['translation_text'].'</button>';
	
	// print '</div>'; // resourceMeta
	
	// $pageNum++;
	
	print '<div id="uploadFilesPage" class="uploadFiles">';
	
	print '<div id="uploadFiles"></div>';
	
	print '<div id="resourceSet" data-set_id="'.$this->setId.'"></div>';
	print '<div class="row">';

	print '<div class="col-md-12">';

	print '<button id="resourceuploader" >'.$this->translations['choose_files']['translation_text'].'</button>';
	//print '<div id="resourceuploader"></div>';
	print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';
	
	print '<div id="errorMessage" class="h4"></div>';


	print '</div>'; // col-md-12


print '</div>'; // row
	
	print '</div>'; // newUpload


	
	print '</form>';

	

}

?>