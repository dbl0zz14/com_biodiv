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
		
	print '<h2>'.$this->translations['edit']['translation_text'].'</h2>';

	print '<form id="resourceEditForm">';
	
	print '<input type="hidden" name="resourceId" value="'.$this->resourceId.'"/>';
	
	$pageNum = Biodiv\ResourceFile::printMetaCapture( $this->resourceId );
	

	$pageNum++;

	// ------------------------- Upload page
	print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
	
	print '<h4>'.$this->translations['save_changes']['translation_text'].'</h4>';
	
	print '<div id="resourceMetaErrorMsg" style="display:none">'.$this->translations['save_error']['translation_text'].'</div>';
	
	print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn hideMetaError"  >';
	print $this->translations['back']['translation_text'];
	print '</div>'; // resourceBack
	
	print '<button type="submit" id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceSaveBtn updateResource" >'.$this->translations['save']['translation_text'].'</button>';
	
	print '</div>'; // resourceMeta
	
		
	print '</form>';

	

}

?>