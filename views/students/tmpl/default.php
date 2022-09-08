<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "ManageStudents template called" );


if ( !$this->personId ) {
	// Please log in button
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("students");
		
		print '</div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateBackAndLogout();
		//Biodiv\SchoolCommunity::generateStudentMasthead();
	}

	//print '<h2>'.$this->translations['heading']['translation_text'].' <small>'.$this->translations['subheading']['translation_text'].'</small></h2>';
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.$this->translations['heading']['translation_text'].'</span> <small class="hidden-xs">'.$this->translations['subheading']['translation_text'].'</small>';
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
	
	print '<div id="controlsArea" class="row">';
	
	print '<div class="col-md-12">';

	print '<div class="btn-group studentsBtnGroup" role="group" aria-label="message filters">';
  

	print '<div class="btn btn-info manageStudentsTab manageTasksBtn active ">';
	print $this->translations['approve_tasks']['translation_text'];
	print '</div>';
	
	print '<div class="btn btn-info schoolTaskTab  manageTasksBtn ">';
	print $this->translations['school_task']['translation_text'];
	print '</div>';
	
	print '<div class="btn btn-info studentProgressTab  manageTasksBtn ">';
	print $this->translations['student_progress']['translation_text'];
	print '</div>';
	
	print '<div class="btn btn-info studentAccountsTab  manageTasksBtn ">';
	print $this->translations['student_users']['translation_text'];
	print '</div>';
	
	print '</div>'; // btn-group
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	
	print '<div id="displayArea" class="fullPageHeight"></div>';
	

	print '</div>'; // col-10 or 12
	
	print '</div>'; // row
	
	
}


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
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



JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/students.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js", true, true);


?>