<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLHELP_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLHELP_NOT_SCH_USER").'</h2>';
}
else {
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "help");
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	print '<h2 class="hidden-sm hidden-md hidden-lg">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLHELP_HEADING").'</span>';
	print '</h2>';
	
	
	// --------------------- Main content
	
	print '<div class="row menuGridRow">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	
	// print '<h2>';
	// print '<div class="row">';
	// print '<div class="col-md-10 col-sm-10 col-xs-10">';
	// print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLHELP_HEADING").'</span> <small class="hidden-xs">'.JText::_("COM_BIODIV_SCHOOLHELP_SUBHEADING").'</small>';
	// print '</div>'; // col-10
	// print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
	// if ( $this->helpOption > 0 ) {
		// print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		// print '<i class="fa fa-info"></i>';
		// print '</div>'; // helpButton
	// }
	// print '</div>'; // col-2
	// print '</div>'; // row
	// print '</h2>';  
	
	if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
	
		print '<div class="studentHelpGrid">';
		
	
		print '<div class="studentHelpIntro">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_INTRO");
		print '<a href="'.$this->studentIntroLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center h3 vSpaced"><i class="fa fa-bicycle fa-3x optionsIcon"></i></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // studentHelpIntro
		
		
		
		print '<div class="studentHelpBadges">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_BADGES");
		print '<a href="'.$this->studentBadgesLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center vSpaced"><img src="'.$this->badgeSchemeIcon.'" class="img-responsive badgeSchemeImage" alt="'.$this->badgesLink.' avatar" /></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // studentHelpBadges
		
		
		
		print '<div class="studentHelpCommunity">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_COMMUNITY");
		print '<a href="'.$this->studentCommunityLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center vSpaced"><img src="'.$this->communityIcon.'" class="img-responsive badgeSchemeImage" alt="'.$this->communityLink.' avatar" /></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // studentHelpCommunity
		
		
		
		print '<div class="studentHelpFaqs">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_FAQS");
		print '<a href="'.$this->studentFaqsLink.'">';
		print '<div class="panel panel-default actionPanel" role="button" >';
		print '<div class="panel-body">';
		print '<div class="h4 panelHeading text-center">';
		print $buttonHeading;
		print '</div>';
		print '<div class="text-center h3 vSpaced"><i class="fa fa-comments-o fa-3x optionsIcon"></i></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // studentHelpFaqs
			
		print '</div>'; // studentHelpGrid
		
	}
	else {
		
		
		print '<div class="teacherHelpGrid">';
		
		print '<div class="teacherHelpIntro">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_INTRO");
		print '<a href="'.$this->introLink.'">';
		print '<div class="panel panel-default " role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 generalHelpColor">';
		print '<div><i class="fa fa-bicycle fa-lg"></i></div>';
		print '</div>'; // col-3
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpIntro
		
		
		print '<div class="teacherHelpBadges">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_BADGES");
		print '<a href="'.$this->badgesLink.'">';
		print '<div class="panel panel-default " role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 generalHelpColor">';
		print '<div class="helpImg"><img src="'.$this->badgeSchemeIcon.'" class="img-responsive " alt="'.$this->badgesLink.'" /></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpBadges
		
		
		print '<div class="teacherHelpCommunity">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_COMMUNITY");
		print '<a href="'.$this->communityLink.'">';
		print '<div class="panel panel-default " role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 generalHelpColor">';
		print '<div class="helpImg"><img src="'.$this->communityIcon.'" class="img-responsive " alt="'.$this->communityLink.' " /></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpCommunity
		
		
		print '<div class="teacherHelpFaqs">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_FAQS");
		print '<a href="'.$this->faqsLink.'">';
		print '<div class="panel panel-default " role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 generalHelpColor">';
		print '<div><i class="fa fa-comments-o fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpFaqs
		
		
		print '<div class="teacherHelpContact">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_CONTACT");
		print '<a href="'.$this->contactLink.'">';
		print '<div class="panel panel-default " role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 generalHelpColor">';
		print '<div><i class="fa fa-envelope-o fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpContact
		
		
		print '<div class="teacherHelpTeacherZone">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_TEACHER_ZONE");
		print '<a href="'.$this->teacherZoneLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 teacherColor">';
		print '<div><i class="fa fa-mortar-board fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpTeacherZone
		
		
		
		print '<div class="teacherHelpScheme">';
		
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_BADGE_SCHEME");
		print '<a href="'.$this->schemeLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 teacherColor">';
		print '<div><i class="fa fa-list-ul fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpScheme
		
		
		print '<div class="teacherHelpSchoolAdmin">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_SCHOOL_ADMIN");
		print '<a href="'.$this->adminLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 teacherColor">';
		print '<div><i class="fa fa-building-o fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpSchoolAdmin
		
		
		print '<div class="teacherHelpStudentProgress">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_STUDENT_PROGRESS");
		print '<a href="'.$this->progressLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 teacherColor">';
		print '<div><i class="fa fa-tasks fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpStudentProgress
		
		
		print '<div class="teacherHelpSchoolWork">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_SCHOOL_WORK");
		print '<a href="'.$this->workLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 teacherColor">';
		print '<div><i class="fa fa-file-text fa-lg "></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpSchoolWork
		
		
		
		print '<div class="teacherHelpResourceHub">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_HUB");
		print '<a href="'.$this->resourceHubLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 resourcesColor">';
		print '<div><i class="fa fa-database fa-lg"></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpResourceHub
		
		
		print '<div class="teacherHelpResourceSet">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_SET");
		print '<a href="'.$this->resourceSetLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 resourcesColor">';
		print '<div><i class="fa fa-files-o fa-lg"></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpResourceHub
		
		
		print '<div class="teacherHelpResource">';
			
		$buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE");
		print '<a href="'.$this->resourceLink.'">';
		print '<div class="panel panel-default" role="button" >';
		print '<div class="panel-body">';
		print '<div class="row h4">';
		print '<div class="col-md-3 col-sm-3 col-xs-3 resourcesColor">';
		print '<div><i class="fa fa-file-o fa-lg"></i></div>';
		print '</div>'; // col-2
		print '<div class="col-md-7 col-sm-7 col-xs-7">';
		print $buttonHeading;
		print '</div>'; // col-7
		print '<div class="col-md-2 col-sm-2 col-xs-2">';
		print '<div><i class="fa fa-angle-right fa-lg"></i></div>';
		print '</div>'; // col-2
		print '</div>'; // row
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // teacherHelpResourceHub
		
		
		
		print '</div>'; // teacherHelpGrid
		
		
		
		
		
		
		//print '<div class="teacherHelpLevel1">'; 
		
		// print '<div class="teacherHelpRowGrid">';
		
		// //print '<div class="teacherHelpIntro">';
		// print '<div class="teacherHelpLevel1">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_INTRO");
		// print '<a href="'.$this->introLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-bicycle fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpIntro
		
		// print '</div>'; // teacherHelpRowGrid
		
		
		// print '<div class="teacherHelpRowGrid">';
		// print '<div class="teacherHelpLevel1">';
		
		// //print '<div class="teacherHelpBadges">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_BADGES");
		// print '<a href="#teacherHelpLevel2Badges" data-toggle="collapse">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><img src="'.$this->badgeSchemeIcon.'" class="img-responsive " alt="'.$this->badgesLink.'" /></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center vSpaced"><img src="'.$this->badgeSchemeIcon.'" class="img-responsive badgeSchemeImage" alt="'.$this->badgesLink.' avatar" /></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpLevel1
		
				
		// //print '<div class="teacherHelpBadgeScheme">';
			
		// //print '</div>'; // level2
		
		// print '</div>'; // teacherHelpRowGrid
		
		// print '<div class="teacherHelpRowGrid">';
		// print '<div class="teacherHelpLevel1">';
		
		// //print '<div class="teacherHelpCommunity">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_COMMUNITY");
		// print '<a href="'.$this->communityLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><img src="'.$this->communityIcon.'" class="img-responsive " alt="'.$this->communityLink.' " /></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center vSpaced"><img src="'.$this->communityIcon.'" class="img-responsive badgeSchemeImage" alt="'.$this->communityLink.' avatar" /></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpLevel1
		// print '</div>'; // teacherHelpRowGrid
		
		
		
		// print '<div class="teacherHelpRowGrid">';
		// print '<div class="teacherHelpLevel1">';
		
		// //print '<div class="teacherHelpFaqs">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_FAQS");
		// print '<a href="'.$this->faqsLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-comments-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpLevel1
		// print '</div>'; // teacherHelpRowGrid
			
		
		
		// print '<div class="teacherHelpRowGrid">';
		// print '<div class="teacherHelpLevel1">';
		
		// //print '<div class="teacherHelpContact">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_CONTACT");
		// print '<a href="'.$this->contactLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpLevel1
		// print '</div>'; // teacherHelpRowGrid
		
		
		// print '<div class="teacherHelpRowGrid">';
		// print '<div class="teacherHelpLevel1">';
		
		// //print '<div class="teacherHelpTeacherZone">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_TEACHER_ZONE");
		
		// print '<a href="#teacherHelpLevel2Zone" data-toggle="collapse">';
		// print '<div class="panel panel-default packedPanel" role="button" data-toggle="collapse">';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpLevel1
		
		// print '<div class="teacherHelpLevel2">';
		
		// print '<div class="teacherHelpLevel2Grid">';
		
		// print '<div class="teacherHelpLevel2_1">';
		// //print '<div class="teacherHelpResources">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_HUB");
		// print '<a href="#teacherHelpResourcesGrid" data-toggle="collapse">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-files-o fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // level 2_1
		// print '<div class="teacherHelpLevel2_2">';
		
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_HUB");
		// print '<a href="#teacherHelpResourcesGrid" data-toggle="collapse">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-files-o fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_HUB");
		// print '<a href="#teacherHelpResourcesGrid" data-toggle="collapse">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-files-o fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_HUB");
		// print '<a href="#teacherHelpResourcesGrid" data-toggle="collapse">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-files-o fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // Level2_2
		// print '</div>'; // Level 2 grid
		
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_BADGE_SCHEME");
		// print '<a href="'.$this->schemeLink.'">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><img src="'.$this->badgeSchemeIcon.'" class="img-responsive " alt="'.$this->badgesLink.'" /></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// //print '<div class="teacherHelpSchoolAdmin">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_SCHOOL_ADMIN");
		// print '<a href="'.$this->adminLink.'">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// //print '</div>'; // teacherHelpSchoolAdmin
		
		
		// //print '<div class="teacherHelpStudentProgress">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_STUDENT_PROGRESS");
		// print '<a href="'.$this->progressLink.'">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// //print '</div>'; // teacherHelpStudentProgress
		
		
		// //print '<div class="teacherHelpSchoolWork">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_SCHOOL_WORK");
		// print '<a href="'.$this->workLink.'">';
		// print '<div class="panel panel-default packedPanel" role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// //print '</div>'; // teacherHelpSchoolWork
		
		
		
		
		
		// print '</div>'; // teacherHelpLevel2
		
		// print '</div>'; // teacherHelpRowGrid
		
		
		
		
		
		
		
		
		
		// //print '<div id="teacherHelpLevel2Badges" class="collapse">';
		
		// //print '<div class="teacherHelpBadgesGrid">';
		
		
		
		
		// //print '</div>'; // teacherHelpBadgesGrid
		
		// //print '</div>'; // teacherHelpLevel2Badges
		
		
		// print '</div>'; // teacherHelpMenuGrid
		
		
		// print '</div>'; // teacherHelpLevel1
		
		
		
		
		
		
		// print '<div id="teacherHelpLevel2Zone" class="collapse">';
		
		// print '<div class="teacherHelpTeacherZoneGrid collapse" >';
		
		// print '<div class="teacherHelpZoneResources">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_RESOURCE_HUB");
		// print '<a href="#teacherHelpResourcesGrid" data-toggle="collapse">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-files-o fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpZoneResources
		
		
		// print '<div class="teacherHelpSchoolAdmin">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_SCHOOL_ADMIN");
		// print '<a href="'.$this->adminLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpSchoolAdmin
		
		
		// print '<div class="teacherHelpStudentProgress">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_STUDENT_PROGRESS");
		// print '<a href="'.$this->progressLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpStudentProgress
		
		
		// print '<div class="teacherHelpSchoolWork">';
			
		// $buttonHeading = JText::_("COM_BIODIV_SCHOOLHELP_SCHOOL_WORK");
		// print '<a href="'.$this->workLink.'">';
		// print '<div class="panel panel-default " role="button" >';
		// print '<div class="panel-body">';
		// print '<div class="row h4">';
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-user-circle-o fa-lg "></i></div>';
		// print '</div>'; // col-2
		// print '<div class="col-md-8 col-sm-8 col-xs-8">';
		// print $buttonHeading;
		// print '</div>'; // col-8
		// print '<div class="col-md-2 col-sm-2 col-xs-2">';
		// print '<div class="text-center"><i class="fa fa-angle-right fa-lg"></i></div>';
		// print '</div>'; // col-2
		// print '</div>'; // row
		// print '</div>'; // panel-body
		// // print '<div class="panel-body">';
		// // print '<div class="h4 panelHeading text-center">';
		// // print $buttonHeading;
		// // print '</div>';
		// // print '<div class="text-center h3 vSpaced"><i class="fa fa-user-circle-o fa-3x optionsIcon"></i></div>';
		// // print '</div>'; // panel-body
		// print '</div>'; // panel
		// print '</a>';
		
		// print '</div>'; // teacherHelpSchoolWork
		
		// print '</div>'; // teacherHelpTeacherZoneGrid
		
		// print '</div>'; // teacherHelpLevel2Zone
	}
	
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
print '      <div class="modal-body">';
print '	        <div id="helpArticle" ></div>';
print '       </div>';
print '	      <div class="modal-footer">';
print '         <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '       </div>'; // modal-footer  	  
print '    </div>'; // modal-content
print '  </div>'; // modal-dialog
print '</div>';





JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);


?>





