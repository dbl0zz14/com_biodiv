<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_LOGIN").'</div>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_WELCOME").'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_YOU_ARE").'</h2>';

	print '<p>'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_POLICIES_TEXT").'</p>';
		
	print '<a href="'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_POLICIES_LINK").'" target="_blank" rel="noopener noreferrer" class="btn btnInSpace">'.
		JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_POLICIES_BTN").'</a>';
	
	
	print '<div id="avatarArea">';
	
	print '<h3 class="text-center bigSpaced">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_CHOOSE_AVATAR").'</h3>';
	
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
	
	print '<button id="saveAvatar" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_SAVE_AVATAR").'</button>';
	
	print '</div>'; // row
	print '</div>'; // avatarArea
	
	if ( $this->mySchoolRole == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_DASH_PAGE").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_DASHBOARD").'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_STUDENT_DASH").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_DASHBOARD").'</button></a></div>';
	}
	else if ( $this->mySchoolRole == Biodiv\SchoolCommunity::ECOLOGIST_ROLE ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_ECOL_DASH").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_DASHBOARD").'</button></a></div>';
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
	print JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_HEADING").' <small class="hidden-xs">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_SUBHEADING").'</small>';
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
		
		//print '<div class="dashboardItem">';
		
		print '<div class="panel panel-default">';
		print '<div class="panel-body">';
	
		print '<div class="row">';
	
		print '<div class="col-md-12 h3">'.$schoolName.'</div>';
		
		print '</div>';
	
		//print '<div class="h3">'.$schoolName.'</div>';
		
		print '<div id="schoolData_'.$schoolId.'" class="schoolData text-center"></div>';
		
		//print '</div>'; // dashboardItem
		
		print '</div>'; // panel-body
		print '</div>'; // panel
	
		print '</div>'; // col-6
		
	}
	
	print '</div>'; // row - schools row
	
	print '</div>'; // col-7
	
	
	// ---------------------------------- Event feed
	
	print '<div class="col-md-5">';
	
	//print '<div class="dashboardItem eventFeed">';
	
	print '<div class="panel panel-default eventFeed">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12 h3">'.JText::_("COM_BIODIV_ECOLOGISTDASHBOARD_EVENTS_HEADING").'</div>';
	
	print '</div>';
	
	print '<div id="eventLog"></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	
	//print '</div>'; // dashboardItem
	
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
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
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
JHTML::script("com_biodiv/ecologistdashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


?>



