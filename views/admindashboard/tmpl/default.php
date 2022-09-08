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
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.$this->translations['welcome']['translation_text'].'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.$this->translations['you_are']['translation_text'].'</h2>';

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
	
	if ( $this->isAdmin ) {
		print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['admin_dash']['translation_text'].'"><button class="btn btn-primary btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
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
		
		print '<div class="summaryItemIcon  h3 text-center vSpaced">';
		
		print '<i class="fa fa-files-o fa-3x"></i>';
		
		print '</div>'; // summaryIcon
		
		print '<div class="summaryItemName h3 panelHeading">';
		
		print $this->translations['resources']['translation_text'];
		
		print '</div>'; // summaryName
		
		print '<div class="summaryItemNumber h2 text-center">';
		
		print '<big>'.$this->adminSummary->numResources.'</big>';
		
		print '</div>'; // summaryNumber
		
		print '<div class="summaryItemText h4 text-center">';
		
		print $this->translations['uploaded']['translation_text'];
		
		print '</div>'; // summaryText
		
		print '<div class="summaryItemBtn text-center">';
			
		print '<div class="btn btn-lg btn-primary report-btn" role="button" data-report-type="'.$this->resourceReportId.'">'.$this->translations['view']['translation_text'].'</div>';
		
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
		
		print '<div class="summaryItemIcon h3 text-center vSpaced">';
		
		print '<i class="fa fa-user-o fa-3x"></i>';
		
		print '</div>'; // summaryIcon
		
		print '<div class="summaryItemName h3 panelHeading">';
		
		print $this->translations['user_admin']['translation_text'];
		
		print '</div>'; // summaryName
		
		print '<div class="summaryItemNumber h2 text-center">';
		
		print '<big>'.$this->adminSummary->numActiveUsers.'</big>';
		
		print '</div>'; // summaryNumber
		
		print '<div class="summaryItemText h4 text-center">';
		
		print $this->translations['active_users']['translation_text'];
		
		print '</div>'; // summaryText
		
		print '<div class="summaryItemBtn text-center">';
			
		print '<a href="'.$this->translations['user_admin_page']['translation_text'].'"><div class="btn btn-lg btn-default" role="button">'.$this->translations['view_admin']['translation_text'].'</div></a>';
		
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
			
			print '<div class="summaryItemName h3 panelHeading">';
		
			print $module->name;
			
			print '</div>'; // summaryName
		
			print '<div class="summaryItemNumber h2 text-center">';
			
			print '<big>'.$totalComplete.'</big>';
			
			print '</div>'; // summaryNumber
			
			print '<div class="summaryItemText h4 text-center">';
			
			//print $module->name . ' ' . $this->translations['activities']['translation_text'];
			print $this->translations['activities']['translation_text'];
			
			print '</div>'; // summaryText
			
			print '<div class="summaryItemBtn text-center">';
			
			//print '<div class="btn btn-lg btn-primary report-btn" role="button" data-report-type="'.$module->report_id.'" data-filter="{'.'"'.'module":\''.$module->name.'\'}">'.$this->translations['view']['translation_text'].'</div>';
			print "<div class='btn btn-lg btn-primary report-btn' role='button' data-report-type='".$module->report_id."' data-filter='{\"module\":\"".$module->module_id."\"}'>".$this->translations['view']['translation_text']."</div>";
			
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
JHTML::script("com_biodiv/admindashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/report.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


?>



