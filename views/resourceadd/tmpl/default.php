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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCEADD_LOGIN").'</div>';
	
}

else if ( $this->isStaff ) {
		
	print '<h2>'.JText::_("COM_BIODIV_RESOURCEADD_ADD").'</h2>';
	
	print '<h4>'.JText::_("COM_BIODIV_RESOURCEADD_EDIT_LATER").'</h4>';
	

	print '<form id="resourceAddForm">';
	
	$pageNum=1;

	
	
	print '<div id="uploadFilesPage" class="uploadFiles">';
	
	print '<div id="uploadFiles"></div>';
	
	print '<div id="resourceSet" data-set_id="'.$this->setId.'"></div>';
	print '<div class="row">';

	print '<div class="col-md-12">';

	print '<button id="resourceuploader" >'.JText::_("COM_BIODIV_RESOURCEADD_CHOOSE_FILES").'</button>';
	
	print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';
	
	print '<div id="errorMessage" class="h4"></div>';


	print '</div>'; // col-md-12


print '</div>'; // row
	
	print '</div>'; // newUpload


	
	print '</form>';

	

}

?>