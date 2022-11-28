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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BROWSETASKS_LOGIN").'</div>';
	
}

else {
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div class="col-md-2 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("schoolcommunity");
		
		print '</div>';
		
		print '<div class="col-md-10 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
	}
	
	print '<div id="displayArea" class="fullPageHeight">';
	
	print '</div>';

	print '</div>'; // col-10 or 12
	
	print '</div>'; // row
}

print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/browsebadges.js", true, true);
JHTML::script("com_biodiv/browsetasks.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js", true, true);


?>