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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCEEDIT_LOGIN").'</div>';
	
}

else {
		
	print '<h2>'.JText::_("COM_BIODIV_RESOURCEEDIT_EDIT").'</h2>';

	print '<form id="resourceEditForm">';
	
	print '<input type="hidden" name="resourceId" value="'.$this->resourceId.'"/>';
	
	$pageNum = Biodiv\ResourceFile::printMetaCapture( $this->resourceId );
	

	$pageNum++;

	// ------------------------- Upload page
	print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
	
	print '<h4>'.JText::_("COM_BIODIV_RESOURCEEDIT_SAVE_CHANGES").'</h4>';
	
	print '<div id="resourceMetaErrorMsg" style="display:none">'.JText::_("COM_BIODIV_RESOURCEEDIT_SAVE_ERROR").'</div>';
	
	print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn hideMetaError"  >';
	print JText::_("COM_BIODIV_RESOURCEEDIT_BACK");
	print '</div>'; // resourceBack
	
	print '<button type="submit" id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceSaveBtn updateResource" >'.JText::_("COM_BIODIV_RESOURCEEDIT_SAVE").'</button>';
	
	print '</div>'; // resourceMeta
	
		
	print '</form>';

	

}

?>