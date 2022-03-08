<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.$this->translations['welcome']['translation_text'].'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.$this->translations['you_are']['translation_text'].'</h2>';

	print '<p>'.$this->translations['policies_text']['translation_text'].'</p>';
		
	print '<a href="'.$this->translations['policies_link']['translation_text'].'" target="_blank" rel="noopener noreferrer" class="btn btnInSpace">'.
		$this->translations['policies_btn']['translation_text'].'</a>';
	
	
	print '<div id="avatarArea">';
	
	print '<h3 class="text-center bigSpaced">'.$this->translations['choose_avatar']['translation_text'].'</h3>';
	
	$isFirst = true;
	$avatarCount = 0;
	foreach ( $this->avatars as $avatarId=>$avatar ) {
		
		$activeClass="";
		if ( $isFirst ) {
			$activeClass="active";
			$isFirst = false;
		}
		if ( $avatarCount%6 == 0 ) {
			print '<div class="row">';
		}
		print '<div class="col-md-2 text-center">';
		
		print '<button id="avatarBtn_'.$avatarId.'" class="avatarBtn '.$activeClass.'"><img src="'.$avatar->image.'" class="img-responsive" alt="'.$avatar->name.' avatar" /></button>';
		print '<h3>'.$avatar->name.'</h3>';
		
		print '</div>';
		
		$avatarCount += 1;
		
		if ( $avatarCount%6 == 0 ) {
			print '</div>'; // row
		}
	}
	if ( $avatarCount%6 != 0 ) {
		print '</div>'; // row
	}
	
	print '<button id="saveAvatar" class="btn btn-primary btn-lg spaced">'.$this->translations['save_avatar']['translation_text'].'</button>';
	
	print '</div>'; // row
	print '</div>'; // avatarArea
	
	if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['dash_page']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['student_dash']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::ECOLOGIST_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['ecol_dash']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	}

		
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav("ecologistdashboard");
	
	print '</div>';
		
	

	// -------------------------------  Main page content
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print $this->translations['heading']['translation_text'].' <small class="hidden-xs">'.$this->translations['subheading']['translation_text'].'</small>';
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
	
	
	
	print '<div class="row">';
	print '<div class="col-md-12">';
	print '<div class="msgBanner h5">';
	if ( $this->notifications ) {
		foreach ( $this->notifications as $note ) {
			print '<div>'.$note->message.'<div class="text-right btn closeNoteBtn"><i class="fa fa-times"></i></div></div>';
		}
	}
	print '</div>'; // msgBanner
	print '</div>'; // col-12
	print '</div>'; // row
	
	// ------------------------------------- Main page stuff
	
	print '<div id="displayArea">';
	
	print '<div class="row">';
	
	print '<div class="col-md-7">';
	
		
	print '<div class="row">';
	
	foreach ( $this->mySchools as $schoolId=>$schoolName ) {
	
		print '<div class="col-md-6">';
		
		// ---------------------------------- School target
		
		print '<div class="dashboardItem">';
		
		print '<div class="h3">'.$schoolName.'</div>';
		
		print '<div id="schoolData_'.$schoolId.'" class="schoolData"></div>';
		
		print '</div>'; // dashboardItem
		
		print '</div>'; // col-6
		
	}
	
	print '</div>'; // row - schools row
	
	print '</div>'; // col-7
	
	
	// ---------------------------------- Event feed
	
	print '<div class="col-md-5">';
	
	print '<div class="dashboardItem eventFeed">';
	
	print '<h3 class="text-center bes_page_heading">'.$this->translations['events_heading']['translation_text'].'</h3>';
	
	print '<div id="eventLog"></div>';
	
	print '</div>'; // dashboardItem
	
	print '</div>'; // col-5
	
	
	
	print '</div>'; // row
	
	print '</div>';
	
	
	
	print '</div>'; // col-12
	
	print '</div>'; // row // summary row
	
}


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
//print '        <h4 class="modal-title">'.$this->translations['review']['translation_text'].'</h4>';
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


	
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/ecologistdashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


?>



