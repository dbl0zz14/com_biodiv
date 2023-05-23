<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_TEACHERZONE_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_TEACHERZONE_NOT_SCH_USER").'</h2>';
}
else {
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "teacherzone");
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		
		print '<div id="helpButton_teacherzone" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}

	// --------------------- Main content
	
	print '<div class="row ">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2 class="hidden-sm hidden-md hidden-lg">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_TEACHERZONE_HEADING").'</span>';
	print '</h2>';
	
	print '<div id="displayArea menuGridRow">';
	
	
	print '<div class="teacherZoneGrid">';
	
	print '<div class="teacherZoneResourceHub">';
		
	$buttonHeading = JText::_("COM_BIODIV_TEACHERZONE_RESOURCE_HUB");
	print '<a href="'.$this->resourceHubLink.'">';
	print '<div class="panel panel-default actionPanel" role="button" >';
	print '<div class="panel-body">';
	print '<div class="h4 panelHeading text-center">';
	print $buttonHeading;
	print '</div>';
	print '<div class="text-center h3 vSpaced resourcesColor"><i class="fa fa-database fa-3x optionsIcon"></i></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>'; // teacherZoneResourceHub
	
	print '<div class="teacherZoneBadgeScheme">';
		
	$buttonHeading = JText::_("COM_BIODIV_TEACHERZONE_BADGE_SCHEME");
	print '<a href="'.$this->badgeSchemeLink.'">';
	print '<div class="panel panel-default actionPanel" role="button" >';
	print '<div class="panel-body">';
	print '<div class="h4 panelHeading text-center">';
	print $buttonHeading;
	print '</div>';
	print '<div class="text-center vSpaced"><img src="'.$this->badgeSchemeIcon.'" class="img-responsive badgeSchemeImage" alt="'.$this->badgeSchemeLink.' avatar" /></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>'; // teacherZoneBadgeScheme
	
	if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		
		print '<div class="teacherZoneSchoolAdmin">';
			
		$buttonHeading = JText::_("COM_BIODIV_TEACHERZONE_SCHOOL_ADMIN");
		print '<a href="'.$this->schoolAdminLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center h3 vSpaced teacherColor"><i class="fa fa-building-o fa-3x optionsIcon"></i></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherZoneSchoolAdmin
	
	
		print '<div class="teacherZoneStudentProgress">';
			
		$buttonHeading = JText::_("COM_BIODIV_TEACHERZONE_STUDENT_PROGRESS");
		print '<a href="'.$this->studentProgressLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center h3 vSpaced teacherColor"><i class="fa fa-tasks fa-3x optionsIcon"></i></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherZoneStudentProgress
	
	
	
		print '<div class="teacherZoneStudentWork">';
			
		$buttonHeading = JText::_("COM_BIODIV_TEACHERZONE_WORK");
		print '<a href="'.$this->workLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center h3 vSpaced teacherColor"><i class="fa fa-file-text fa-3x optionsIcon"></i></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherZoneStudentWork
	
	}
	if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::ADMIN_ROLE  ) {
		
		print '<div class="teacherZoneSchoolAdmin">'; // cheat the layout
			
		$buttonHeading = JText::_("COM_BIODIV_TEACHERZONE_WORK");
		print '<a href="'.$this->workLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center h3 vSpaced teacherColor"><i class="fa fa-file-text fa-3x optionsIcon"></i></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherZoneSchoolAdmin

	}
	
	print '</div>'; // teacherZoneGrid
	
	
	// print '</div>'; // col-12
	
	// print '</div>'; // row
	
	print '</div>'; // displayArea
	
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
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_TEACHERZONE_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';





JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
//JHTML::script("com_biodiv/resourcelist.js", true, true);
//JHTML::script("com_biodiv/resourceupload.js", true, true);
//JHTML::script("com_biodiv/schoolcommunity.js", true, true);
//JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);




?>





