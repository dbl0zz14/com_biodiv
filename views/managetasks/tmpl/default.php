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
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("managetasks");
		
		print '</div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateBackAndLogout();
		//Biodiv\SchoolCommunity::generateStudentMasthead();
	}

	
	print '<div id="controlsArea" class="row">';
	
	print '<div class="col-md-12">';

	print '<div class="btn-group" role="group" aria-label="Toggle tasks buttons">';
  

	print '<div id="viewMyTasks" class="btn btn-info chooseModule manageTasksBtn">';
	print $this->translations['my_tasks']['translation_text'];
	print '</div>';
	
	if ( $this->isEcologist ) { 
		//print '<div id="viewTeacherTasks_'.$this->moduleId.'" class="btn btn-info manageTasksBtn allTeacherTasks">';
		print '<div id="viewTeacherTasks" class="btn btn-info manageTasksBtn chooseTeacherModule">';
		print $this->translations['all_teacher_tasks']['translation_text'];
		print '</div>';
	}
	
	//print '<div id="viewStudentTasks" class="btn btn-info manageTasksBtn allStudentBadges">';
	print '<div id="viewStudentTasks" class="btn btn-info manageTasksBtn chooseStudentModule">';
	print $this->translations['all_student_tasks']['translation_text'];
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
JHTML::script("com_biodiv/managetasks.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js", true, true);


?>