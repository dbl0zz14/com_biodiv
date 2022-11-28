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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCE_LOGIN").'</div>';
	
}

else if ( $this->resourceId ) {
	
	print '<div class="row">';
	
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("resourcehub");
		
		print '</div>';
		
		 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
		
		print '</div>';
	}
	
	print '</div>'; // row
	
	print '<div class="row">';
				
	print '<div class="col-md-2 col-sm-4 col-xs-4">';

	error_log ( "Back = " . JText::_("COM_BIODIV_RESOURCE_BACK") );
	print '<button class="btn btn-primary backBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_RESOURCE_BACK");
	print '</button>';
	
	print '</div>'; // col-1

	print '</div>'; // row
				
	print '<div id="displayArea">';
				
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_RESOURCE_HEADING").'</span> <small class="hidden-xs hidden-sm">'.JText::_("COM_BIODIV_RESOURCE_SUBHEADING").'</small> ';
	print '</div>'; // col-10
	print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	print '</div>'; // col-2
	print '</div>'; // row
	print '</h2>'; 
	
	
	$this->resourceFile->printFull();
	
	print '</div>'; // displayArea

}
else {
	print ('<div class="col-md-12" >'.JText::_("COM_BIODIV_RESOURCE_NO_FILE").'</div>');
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
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resource.js", true, true);

?>