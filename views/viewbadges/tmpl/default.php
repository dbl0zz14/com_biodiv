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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_VIEWBADGES_LOGIN").'</div>';
	
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
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
	}
	
	print '<div class="row moduleButtons">';
	
	print '<div class="col-md-12">';
	
	print '<div class="btn-group moduleBtnGroup pull-right" role="group" aria-label="Switch module buttons">';
	
	foreach ( $this->allModules as $module ) {
		print '<div id="moduleButton_' . $module->module_id.'" class="btn moduleBtn">';
		
		$activeClass = '';
		$imageSrc = $module->icon;
		
		if ( $module->module_id == $this->moduleId ) {
			$activeClass = 'active'.$module->name;
			$imageSrc = $module->white_icon;
		}
		
		if ( $this->teacher ) {
			print '<a href="'.$module->teacher_badge_url.'">';
		}
		else {
			print '<a href="'.$module->student_badge_url.'">';
		}
		
		$activeClass = '';
		if ( $module->module_id == $this->moduleId ) {
			$activeClass = 'active'.$module->name;
		}
		print '<div class="panel panel-default '.$activeClass.'">';
		print '<div class="panel-body">';
		
		// -------------------------------------- module icon
		print '<div class="row">';
		
		$imageSrc = $module->icon;
		print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="switchModuleIcon'.$module->name.' " alt="module icon" /></div>';

		print '</div>'; // row
		
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		print '</a>';
		
		print '</div>';
	}
	
	print '</div>'; // moduleBtnGrp
	print '</div>'; // col-12
	print '</div>'; // row
	
	$module = $this->allModules[$this->moduleId];
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	$textKey = 'heading_'.$module->class_stem;
	if ( $this->teacher ) {
		$textKey = 't_heading_'.$module->class_stem;
	}
	$textKeyUpper = strtoupper($textKey);
	print JText::_("COM_BIODIV_VIEWBADGES_".$textKeyUpper).' <small class="hidden-xs">'.JText::_("COM_BIODIV_VIEWBADGES_SUBHEADING").'</small>';
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
	

	print '<div class="row largeBrowseButtons">';
	
	print '<div class="col-md-12">';
	
	print '<div class="btn-group browseBtnGroup" role="group" aria-label="Badge group buttons">';
	
	$moduleId = $this->moduleId;
	
	foreach ( $this->badgeGroups as $badgeGroup ) {
	
		$groupId = $badgeGroup[0];
		$groupName = $badgeGroup[1];
		$colorClass = $this->badgeColorClasses[$groupId];
		$color = $this->badgeColors[$groupId];
		$image = $this->badgeImages[$groupId];
		$icon = $this->badgeIcons[$groupId];
		$noStarsImage = $this->badgeNoStars[$groupId];
		
		if ( $this->teacher ) {
			print '<div id="badgesButton_'.$moduleId.'_'.$groupId.'" class="btn '. $colorClass . ' text-center browseBadgesBtn viewTeacherGroupBtn">';
		}
		else {
			print '<div id="badgesButton_'.$moduleId.'_'.$groupId.'" class="btn '. $colorClass . ' text-center browseBadgesBtn viewGroupBtn">';
		}
		
		print '<div class="panel panel-default '. $colorClass .'_active_bg">';
		print '<div class="panel-body">';
		
		
		// -------------------------------------- badge group icon
		print '<div class="row">';
		
		$imageSrc = JURI::root().$noStarsImage;
		print '<div class="col-md-12 browseBadgeImg '.$colorClass.'_text"><img src="'.$imageSrc.'" class="img-responsive" alt="badge group icon" /></div>';

		print '</div>'; // row
		
		
		
		print '<div class="row">';
		print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$groupName.'</div>';
		print '</div>'; // row
		
		
		
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		print '</div>'; // badgesButton
	}
	
	print '</div>'; // btn-group
	
	
	
	print '</div>'; // col-12 
	
	print '</div>'; // row largeBrowseButtons
	
	
	
	print '<div class="row smallBrowseButtons">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">';
	
	print '<div class="btn-group browseBtnGroup" role="group" aria-label="Badge group buttons">';
	
	foreach ( $this->badgeGroups as $badgeGroup ) {
	
		$groupId = $badgeGroup[0];
		$colorClass = $this->badgeColorClasses[$groupId];
		$color = $this->badgeColors[$groupId];
		$image = $this->badgeImages[$groupId];
		$icon = $this->badgeIcons[$groupId];
		//$zeroStar = $this->badgeZeroStar[$groupId];
			
		//print '<div id="badgesButton_'.$groupId.'" class="col-md-2 col-sm-4 col-xs-4 btn '. $colorClass . ' text-center browseGroupBadges">';
		print '<div id="badgesButton_'.$moduleId.'_'.$groupId.'" class="btn '. $colorClass . ' text-center  browseBadgesBtn browseGroupBtn">';
		
		print '<div class="panel panel-default ">';
		print '<div class="panel-body">';

		
		// -------------------------------------- badge group icon
		print '<div class="row">';
		
		//print '<div class="col-md-12 browseBadgeImg '.$colorClass.'_text"><i class= "fa fa-2x '.$icon.'" aria-hidden= "true" ></i></div>';
		print '<div class="col-md-8 col-md-2-offset browseBadgeImg '.$colorClass.'_text"><img src="'.$icon.'" class="img-responsive" alt="badge group icon" /></div>';

		print '</div>'; // row
		
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		print '</div>'; // badgesButton
	}
	
	print '</div>'; // btn-group
	
	
	
	print '</div>'; // col-12 
	
	print '</div>'; // row smallBrowseButtons
	
	
	print '<div id="displayBadges"><div class="noBadges"></div></div>';
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
}

JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/viewbadges.js", true, true);

?>