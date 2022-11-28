<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// Set some variables for use in the Javascript
$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.waitText = '".$this->waitText."';");
$document->addScriptDeclaration("BioDiv.doneText = '".$this->doneText."';");
$document->addScriptDeclaration("BioDiv.genText = '".$this->genText."';");

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ADMINDASHBOARD_LOGIN").'</div>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.JText::_("COM_BIODIV_ADMINDASHBOARD_WELCOME").'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.JText::_("COM_BIODIV_ADMINDASHBOARD_YOU_ARE").'</h2>';

	print '<div id="avatarArea">';
	
	print '<h3 class="text-center bigSpaced">'.JText::_("COM_BIODIV_ADMINDASHBOARD_CHOOSE_AVATAR").'</h3>';
	
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
	
	print '<button id="saveAvatar" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_ADMINDASHBOARD_SAVE_AVATAR").'</button>';
	
	print '</div>'; // row
	print '</div>'; // avatarArea
	
	if ( $this->isAdmin ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.JText::_("COM_BIODIV_ADMINDASHBOARD_ADMIN_DASH").'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.JText::_("COM_BIODIV_ADMINDASHBOARD_DASHBOARD").'</button></a></div>';
	}


		
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav("admindashboard");
	
	print '</div>';
		
	

	// -------------------------------  Main page content
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print JText::_("COM_BIODIV_ADMINDASHBOARD_HEADING").' <small class="hidden-xs">'.JText::_("COM_BIODIV_ADMINDASHBOARD_SUBHEADING").'</small>';
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
	
	
	
	// print '<div class="row">';
	// print '<div class="col-md-12">';
	// print '<div class="msgBanner h5">';
	// if ( $this->notifications ) {
		// foreach ( $this->notifications as $note ) {
			// print '<div>'.$note->message.'<div class="text-right btn closeNoteBtn"><i class="fa fa-times"></i></div></div>';
		// }
	// }
	// print '</div>'; // msgBanner
	// print '</div>'; // col-12
	// print '</div>'; // row
	
	// ------------------------------------- Main page stuff
	
	print '<div id="displayArea">';
	
	
	
	print '<div class="row">';
	
	print '<div class="col-md-12">';
	
	if ( $this->adminSummary ) {
		
		print '<div class="adminSummaryGrid">';
	
		print '<div class="resourcesAdminSummary">';
		
		//print "<p>Total resources = " . $this->adminSummary->numResources . "</p>";
		
		print '<div class="panel actionPanel">';
		print '<div class="panel-body">';
		
		print '<div class="summaryItemGrid">';
		
		print '<div class="summaryItemIcon  h3 text-center bigSpaced">';
		
		print '<i class="fa fa-files-o fa-2x"></i>';
		
		print '</div>'; // summaryIcon
		
		print '<div class="summaryItemName h3 panelHeading text-center">';
		
		print JText::_("COM_BIODIV_ADMINDASHBOARD_RESOURCES");
		
		print '</div>'; // summaryName
		
		print '<div class="summaryItemNumber h2 text-center">';
		
		print '<big>'.$this->adminSummary->numResources.'</big>';
		
		print '</div>'; // summaryNumber
		
		print '<div class="summaryItemText h4 text-center">';
		
		print JText::_("COM_BIODIV_ADMINDASHBOARD_UPLOADED");
		
		print '</div>'; // summaryText
		
		print '<div class="summaryItemBtn text-center">';
			
		print '<div class="btn btn-lg btn-primary report-btn" role="button" data-report-type="'.$this->resourceReportId.'">'.JText::_("COM_BIODIV_ADMINDASHBOARD_VIEW").'</div>';
		
		print '</div>'; // summaryBtn
		
		print '</div>'; // summaryItemGrid
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // resourcesSummary
		
		
		print '<div class="userAdmin">';
		
		//print "<p>Total resources = " . $this->adminSummary->numResources . "</p>";
		
		print '<div class="panel actionPanel usersColor">';
		print '<div class="panel-body">';
		
		print '<div class="summaryItemGrid">';
		
		print '<div class="summaryItemIcon h3 text-center bigSpaced">';
		
		print '<i class="fa fa-user-o fa-2x"></i>';
		
		print '</div>'; // summaryIcon
		
		print '<div class="summaryItemName h3 panelHeading text-center">';
		
		print JText::_("COM_BIODIV_ADMINDASHBOARD_USER_ADMIN");
		
		print '</div>'; // summaryName
		
		print '<div class="summaryItemNumber h2 text-center">';
		
		print '<big>'.$this->adminSummary->numActiveUsers.'</big>';
		
		print '</div>'; // summaryNumber
		
		print '<div class="summaryItemText h4 text-center">';
		
		print JText::_("COM_BIODIV_ADMINDASHBOARD_ACTIVE_USERS");
		
		print '</div>'; // summaryText
		
		print '<div class="summaryItemBtn text-center">';
			
		print '<a href="'.JText::_("COM_BIODIV_ADMINDASHBOARD_USER_ADMIN_PAGE").'"><div class="btn btn-lg btn-default" role="button">'.JText::_("COM_BIODIV_ADMINDASHBOARD_VIEW_ADMIN").'</div></a>';
		
		print '</div>'; // summaryBtn
		
		print '</div>'; // summaryItemGrid
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		print '</div>'; // userAdmin

		
		foreach ( $this->moduleIds as $moduleId ) {
			
			$module = $this->modules[$moduleId];
			$studentTotal = 0;
			$teacherTotal = 0;
			if ( array_key_exists( $moduleId, $this->adminSummary->studentBadges ) ) {
				$studentTotal = $this->adminSummary->studentBadges[$moduleId];
			}
			if ( array_key_exists( $moduleId, $this->adminSummary->teacherBadges ) ) {
				$teacherTotal = $this->adminSummary->teacherBadges[$moduleId];
			}
			$totalComplete = $studentTotal + $teacherTotal;
			
			print '<div class="'.$module->class_stem.'AdminSummary">';
			print '<div class="panel actionPanel '.$module->class_stem.'Color">';
			print '<div class="panel-body">';
			
			print '<div class="summaryItemGrid">';
		
			print '<div class="summaryItemIcon h3 text-center">';
			
			//print '<img src="'.$module->icon.'"  class="'.$module->class_stem.'Img img-responsive" alt="'.$module->name.' icon" />';
			
			$moduleIcon = $module->icon;
			if ( $module->dark_bg ) {
				$moduleIcon = $module->white_icon;
			}
			
			print '<img class="img-responsive adminIcon'.$module->name.'" src="'.$moduleIcon.'" alt="'.$module->name.' icon" />';
			
			print '</div>'; // summaryIcon
			
			print '<div class="summaryItemName h3 panelHeading text-center">';
		
			print $module->name;
			
			print '</div>'; // summaryName
		
			print '<div class="summaryItemNumber h2 text-center">';
			
			print '<big>'.$totalComplete.'</big>';
			
			print '</div>'; // summaryNumber
			
			print '<div class="summaryItemText h4 text-center">';
			
			print JText::_("COM_BIODIV_ADMINDASHBOARD_ACTIVITIES");
			
			print '</div>'; // summaryText
			
			print '<div class="summaryItemBtn text-center">';
			
			print "<div class='btn btn-lg btn-primary report-btn' role='button' data-report-type='".$module->report_id."' data-filter='{\"module\":\"".$module->module_id."\"}'>".JText::_("COM_BIODIV_ADMINDASHBOARD_VIEW")."</div>";
			
			print '</div>'; // summaryBtn
			
			print '</div>'; // summaryItemGrid
			
			print '</div>'; // panel-body
			print '</div>'; // panel
			print '</div>'; // AdminSummary
		
		}
		
		
		
		print '</div>'; // adminSummary grid	
	}
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	
	print '<div class="row">';
	print '<div class="col-md-12">';
	
	print '<div id="adminReportPanel" class="panel vSpaced" style="display:none">';
	print '<div class="panel-body">';
	print '<div id="report_display"></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // col-12
	print '</div>'; // row
	

	
	// print '</div>'; // col-12
	
	// print '</div>'; // row
	
	print '</div>'; // display area 
	
	
	
	print '</div>'; // col-12
	
	print '</div>'; // row // summary row
	
}


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
//print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_ADMINDASHBOARD_REVIEW").'</h4>';
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
JHTML::script("com_biodiv/admindashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/report.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


?>



