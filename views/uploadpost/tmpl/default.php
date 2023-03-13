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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_UPLOADPOST_LOGIN").'</div>';
}

else {
		
	print '<h2>'.JText::_("COM_BIODIV_UPLOADPOST_UPLOAD").'</h2>';

	print '<form id="uploadPostForm">';
	
	print "<input type='hidden' name='post' value='1'/>";
	
	$pageNum = Biodiv\ResourceFile::printPostMetaCapture();
	
	$pageNum++;

	// ------------------------- Upload page
	print '<div id="resourceMeta_'.$pageNum.'"  class="metaPage" style="display:none">';
	
	print '<h4>'.JText::_("COM_BIODIV_UPLOADPOST_IF_READY").'</h4>';
	
	print '<div id="resourceMetaErrorMsg" style="display:none">'.JText::_("COM_BIODIV_UPLOADPOST_FORM_ERROR").'</div>';
	
	print '<div id="resourceBack_'.$pageNum.'" class="btn btn-default btn-lg resourceBackBtn  hideMetaError"  >';
	print JText::_("COM_BIODIV_UPLOADPOST_BACK");
	print '</div>'; // resourceBack
	
	print '<button type="submit" id="resourceNext_'.$pageNum.'" class="btn btn-primary btn-lg resourceSaveBtn chooseFiles" >'.JText::_("COM_BIODIV_UPLOADPOST_CREATE_SET").'</button>';
	
	print '</div>'; // resourceMeta
	
	$pageNum++;
	
	print '<div id="uploadFilesPage" class="uploadFiles" style="display:none">';
	
	print '<div id="uploadFiles"></div>';
	
	
	print '</div>'; // uploadFilesPage


	
	print '</form>';

	

}

?>