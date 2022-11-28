<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_STUDENTDASHBOARD_LOGIN").'</div>';
}
else if ( !$this->isStudent ) {
	print '<h2>'.JText::_("COM_BIODIV_STUDENTDASHBOARD_NOT_STUDENT").'</h2>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_WELCOME").'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_YOU_ARE").'</h2>';

	//print_r ( $this->avatars );
	
	//print '<form id="avatarForm">';
	
	print '<div id="avatarArea">';
	
	print '<h3 class="text-center bigSpaced">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_CHOOSE_AVATAR").'</h3>';
	
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
	
	print '<button id="saveAvatar" class="btn btn-info btn-lg spaced">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_SAVE_AVATAR").'</button>';
	
	print '</div>'; // row
	print '</div>'; // avatarArea

	print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_STUDENT_DASH").'"><button class="btn btn-info btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_DASHBOARD").'</button></a></div>';
	
	
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
		
	Biodiv\SchoolCommunity::generateStudentMasthead( $this->helpOption, null, 0, 0, 0, false, true );
	
	
	print '<div id="studentDashRow" class="row" >';
	print '<div class="col-md-12">';
	
	print '<h2 class="greenHeading">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_HEADING").'</h2>';
	print '<h3 style="margin-bottom:20px;">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_SUBHEADING").'</h3>';

	print '</div>'; // col-12
	print '</div>'; // row
	
	print '<div class="row">';
	
	
	// -------------------------- Main display area
	
	print '<div class="col-md-12 col-sm-12 col-xs-12" >';
	
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
	
	$schoolSettings = getSetting ( "school_icons" );
			
	$settingsObj = json_decode ( $schoolSettings );
	
	print '<div id="displayArea">';
	
	
	print '<div class="row homePageRow">';
	
	
		
	
	$findIcon = "";
	if (property_exists($settingsObj, 'find')) {
		$findIcon = $settingsObj->find;
	}
	$collectIcon = "";
	if (property_exists($settingsObj, 'collect')) {
		$collectIcon = $settingsObj->collect;
	}
	$workIcon = "";
	if (property_exists($settingsObj, 'work')) {
		$workIcon = $settingsObj->work;
	}
	$speciesIcon = "";
	if (property_exists($settingsObj, 'species')) {
		$speciesIcon = $settingsObj->species;
	}
	$schoolIcon = "";
	if (property_exists($settingsObj, 'school')) {
		$schoolIcon = $settingsObj->school;
	}
	$communityIcon = "";
	if (property_exists($settingsObj, 'community')) {
		$communityIcon = $settingsObj->community;
	}
	
	
	
	print '<div class="col-md-12">';
	print '<div class="gridContainer">';
	
	print '<div class="findActivity">';
	
	print '<a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_BADGES_LINK").'" >';
	print '<div id="findActivityPanel" class="panel panel-default  actionPanel ">';
	print '<div class="panel-body">';
	print '<div class="h2">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_BROWSE_TASKS").'</div>';
	print '<div><img src="'.$findIcon.'"  class="img-responsive" alt="Select activity icon" /></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>'; // findActivity
	
	
	print '<div class="collectBadge">';
	
	print '<a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_COLLECTION_LINK").'" >';
	print '<div id="collectBadgePanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	if ( $this->numToCollect > 0 ) {
		print '<span class="label label-primary notifyLabel">'.$this->numToCollect.'</span>';
		
	}
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_COLLECT_BADGE").'</div>';
	print '<div><img src="'.$collectIcon.'" class="img-responsive" alt="Collect badge icon" /></div>';
	print '</div>';
	print '</div>';
	print '</a>';
	
	print '</div>'; // collectBadge
	
	
	print '<div class="viewSpecies">';
	
	print '<a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_WILD_SPACE_LINK").'" >';
	print '<div id="viewSpeciesPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_LEARN_SPECIES").'</div>';
	print '<div><img src="'.$speciesIcon.'" class="img-responsive" alt="View species icon" /></div>';
	print '</div>'; // panel-body
	print '</div>';
	print '</a>';
	
	print '</div>'; // viewSpecies
	
	
	print '<div class="reviewWork">';
	
	print '<a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_SCHOOLWORK_LINK").'" >';
	print '<div id="reviewWorkPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_SHOW_WORK").'</div>';
	print '<div><img src="'.$workIcon.'" class="img-responsive" alt="View documents icon" /></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>';
	
	print '<div class="visitSchool">';
	
	print '<a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_SCHOOL_DASH").'" >';
	print '<div id="visitSchoolPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_SCHOOL_PAGE").'</div>';
	print '<div><img src="'.$schoolIcon.'" class="img-responsive" alt="My school icon" /></div>';
	print '</div>'; // panel-body
	print '</div>';
	print '</a>';
	
	print '</div>'; // visitSchool
	
	print '<div class="visitCommunity">';
	
	print '<a href="'.JText::_("COM_BIODIV_STUDENTDASHBOARD_COMMUNITY_DASH").'" >';
	print '<div id="visitCommunityPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_STUDENTDASHBOARD_COMMUNITY_PAGE").'</div>';
	print '<div><img src="'.$communityIcon.'" class="img-responsive" alt="All schools icon" /></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>'; // visitCommunity
	
	
	print '</div>'; // gridContainer
	print '</div>'; // col-12
	
	
	print '<div class="col-md-12">';
	if ( $this->encourage ) {
		print '<div class="h4 text-center bigSpaced" style="margin-top:20px">'.$this->encourage.'</div>';
	}
	
	print '</div>'; // col-12
	
	print '</div>'; // row homePageRow
	
	print '</div>';	// display area
	
	print '</div>'; // col-12
	
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
print '        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '      </div>'; // modal-footer
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal-dialog
print '</div>'; // helpModal


JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/studentdashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);




?>






