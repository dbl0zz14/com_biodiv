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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCESETEDIT_LOGIN").'</div>';
	
}

else {
		
	print '<h2>'.JText::_("COM_BIODIV_RESOURCESETEDIT_EDIT").'</h2>';

	print '<form id="editSetForm">';
	
	print '<input type="hidden" name="setId" value="'.$this->setId.'"/>';
	
	// Name the upload
	$title = "";
	if ( $this->resourceSet ) {
		$title = $this->resourceSet->getSetName();
	}
	print '<label for="uploadName"><h4>'.\JText::_("COM_BIODIV_RESOURCESETEDIT_NAME_UPLOAD").'</h4></label>';
	print '<input type="text" id="uploadName" name="uploadName" value = "'.$title.'">';
	print '<div id="uploadNameCount" class="text-right" data-maxchars="'.Biodiv\ResourceFile::MAX_TITLE_CHARS.'">0/'.Biodiv\ResourceFile::MAX_TITLE_CHARS.'</div>';
	
	
	// Describe the upload
	$desc = "";
	if ( $this->resourceSet ) {
		$desc = $this->resourceSet->getSetText();
	}

	print '<label for="uploadDescription"><h4>'.\JText::_("COM_BIODIV_RESOURCESETEDIT_UPLOAD_DESC").'</h4></label>';
	print '<textarea id="uploadDescription" name="uploadDescription" rows="2" cols="100" >'.$desc.'</textarea>';
	print '<div id="uploadDescriptionCount" class="text-right" data-maxchars="'.Biodiv\ResourceFile::MAX_DESC_CHARS.'">0/'.Biodiv\ResourceFile::MAX_DESC_CHARS.'</div>';

	
	print '<h4>'.JText::_("COM_BIODIV_RESOURCESETEDIT_SAVE_CHANGES").'</h4>';
	
	print '<div id="resourceMetaErrorMsg" style="display:none">'.JText::_("COM_BIODIV_RESOURCESETEDIT_SAVE_ERROR").'</div>';
	
	print '<button type="submit" class="btn btn-primary btn-lg resourceSaveBtn updateResource" >'.JText::_("COM_BIODIV_RESOURCESETEDIT_SAVE").'</button>';
	
	
		
	print '</form>';

	

}

?>