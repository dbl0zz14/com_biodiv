<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "BadgeProgress template called" );

if ( !$this->personId ) {
	
	// Please log in button
	print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
	
	
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
	

	print '<div class="row largeBrowseButtons">';
	
	print '<div class="col-md-12">';
	
	print '<div class="btn-group browseBtnGroup" role="group" aria-label="Badge group buttons">';
	
	//print '<div class="browseBtnGroupHeading">'.$this->translations['by_type']['translation_text'].'</div>';
	
	foreach ( $this->badgeGroups as $badgeGroup ) {
	
		$groupId = $badgeGroup[0];
		$groupName = $badgeGroup[1];
		$colorClass = $this->badgeColorClasses[$groupId];
		$color = $this->badgeColors[$groupId];
		$image = $this->badgeImages[$groupId];
		$icon = $this->badgeIcons[$groupId];
		
		print '<div id="badgesButton_'.$groupId.'" class="btn '. $colorClass . ' text-center browseBadgesBtn browseGroupBtn">';
		
		print '<div class="panel panel-default '. $colorClass .'_active_bg">';
		print '<div class="panel-body">';
		
		
		// ---------------------------------------- points top right
		print '<div class="row badgeStats">';
		
		print '<div class="col-md-6 col-md-offset-6  col-sm-12 col-xs-12 text-right " ><div class="badgePoints">'.$this->badgeGroupSummary[$groupId]["numPoints"].'</div></div>';
		print '</div>'; // row
		
		// ------------------------------------- badge group name 
		// print '<div class="row">';
		// print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$groupName.'</div>';
		// print '</div>'; // row
		
		// -------------------------------------- badge group icon
		print '<div class="row">';
		
		//print '<div class="col-md-6 col-md-offset-3 browseBadgeImg"><img class="img-responsive" src="'.$image.'" alt="badge image"/></div>';
		
		if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
			$imageSrc = JURI::root().$this->badgeNoStars[$groupId];
		}
		else if ( array_key_exists($groupId, $this->stars) ) {
			$numStars = $this->stars[$groupId]->num_stars;
			//error_log ( "Group id $groupId, num stars = $numStars" );
			$imageSrc = JURI::root().$this->badgeStarImages[$groupId][$numStars];
		}
		else {
			$imageSrc = JURI::root().$this->badgeStarImages[$groupId][0];
		}
		
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
	
	$schoolSettings = getSetting ( "school_icons" );
			
	$settingsObj = json_decode ( $schoolSettings );
	
	$completeIcon = "";
	if (property_exists($settingsObj, 'complete')) {
		$completeIcon = $settingsObj->complete;
	}
	$unlockedIcon = "";
	if (property_exists($settingsObj, 'unlocked')) {
		$unlockedIcon = $settingsObj->unlocked;
	}
	$hintIcon = "";
	if (property_exists($settingsObj, 'hint')) {
		$hintIcon = $settingsObj->hint;
	}
	
	print '<div class="btn-group browseBtnGroup pull-right" role="group" aria-label="Filter tasks">';
	
	print '<div class="btn text-center browseBadgesBtn completeTasks">';
		
	print '<div class="panel panel-default filter_active_bg">';
	print '<div class="panel-body">';
	
	// ------------------------------------- button name 
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$this->translations['complete']['translation_text'].'</div>';
	print '</div>'; // row
	
	// -------------------------------------- done icon
	print '<div class="row">';
	print '<div class="col-md-12 browseBadgeImg "><img src="'.$completeIcon.'" class="img-responsive" alt="Completed activities icon" /></div>';

	print '</div>'; // row
	
	
	// print '<div class="row badgeStats">';
	// print '<div class="col-md-12 col-sm-12 col-xs-12 text-center ">'.$this->translations['done']['translation_text'].'</div>';
	
	// // print '<div class="col-md-7 col-sm-7 col-xs-7 text-right ">'.$this->badgeGroupSummary[$groupId]["numPoints"].' '.
			// // $this->translations['points']['translation_text'].'</div>';
	// print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$this->translations['done']['translation_text'].'</div>';
	print '</div>'; // row
		
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // complete btn
	
	print '<div class="btn text-center browseBadgesBtn unlockedTasks">';
		
	print '<div class="panel panel-default filter_active_bg">';
	print '<div class="panel-body">';
	
	// ------------------------------------- button name 
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$this->translations['unlocked']['translation_text'].'</div>';
	print '</div>'; // row
	
	// -------------------------------------- unlocked icon
	print '<div class="row">';
	
	//print '<div class="col-md-12 browseBadgeImg " style="padding:12px;"><i class= "fa fa-2x fa-unlock" aria-hidden= "true" ></i></div>';
	print '<div class="col-md-12 browseBadgeImg " ><img src="'.$unlockedIcon.'" class="img-responsive" alt="Unlocked activities icon" /></div>';

	print '</div>'; // row
	
	
	// print '<div class="row badgeStats">';
	// print '<div class="col-md-12 col-sm-12 col-xs-12 text-center ">'.$this->translations['to_do']['translation_text'].'</div>';
	
	// print '</div>'; // row
		
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$this->translations['to_do']['translation_text'].'</div>';
	print '</div>'; // row
		
		
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // suggest btn
		
	
	print '<div class="btn text-center browseBadgesBtn suggestTask">';
		
	print '<div class="panel panel-default filter_active_bg">';
	print '<div class="panel-body">';
	
	// ------------------------------------- button name 
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$this->translations['not_sure']['translation_text'].'</div>';
	print '</div>'; // row
	
	// -------------------------------------- suggest icon
	print '<div class="row">';
	
	print '<div class="col-md-12 browseBadgeImg "><img src="'.$hintIcon.'" class="img-responsive" alt="Get a suggestion icon" /></div>';

	print '</div>'; // row
	
	
	// print '<div class="row badgeStats">';
	// print '<div class="col-md-12 col-sm-12 col-xs-12 text-center ">'.$this->translations['suggest']['translation_text'].'</div>';
	
	// print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center browseGroupHeading">'.$this->translations['suggest']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // suggest btn
		
	print '</div>'; // btn-group
	
	print '</div>'; // col-1 
	
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
		print '<div id="badgesButton_'.$groupId.'" class="btn '. $colorClass . ' text-center  browseBadgesBtn browseGroupBtn">';
		
		print '<div class="panel panel-default">';
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
	
	//print '</div>'; // col-8
	
	
	//print '<div class="col-md-4 col-sm-4 col-xs-4">';
	
	print '<div class="btn-group browseBtnGroup" role="group" aria-label="Filter tasks">';
	
	print '<div class="btn text-center browseBadgesBtn completeTasks">';
		
	print '<div class="panel panel-default">';
	print '<div class="panel-body">';
	
	// -------------------------------------- done icon
	print '<div class="row">';
	
	print '<div class="col-md-12 browseBadgeImg "><i class= "fa fa-2x fa-check" aria-hidden= "true" ></i></div>';

	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // complete btn
	
	print '<div class="btn text-center  browseBadgesBtn unlockedTasks">';
		
	print '<div class="panel panel-default">';
	print '<div class="panel-body">';
	
	// -------------------------------------- unlocked icon
	print '<div class="row">';
	
	print '<div class="col-md-12 browseBadgeImg "><i class= "fa fa-2x fa-unlock" aria-hidden= "true" ></i></div>';

	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // suggest btn
		
	
	print '<div class="btn text-center  browseBadgesBtn suggestTask">';
		
	print '<div class="panel panel-default">';
	
	print '<div class="panel-body">';
	
	// -------------------------------------- suggest icon
	print '<div class="row">';
	
	print '<div class="col-md-12 browseBadgeImg "><i class= "fa fa-2x fa-lightbulb-o" aria-hidden= "true" ></i></div>';

	print '</div>'; // row
	
	print '</div>'; // panel-body
	
	print '</div>'; // panel
		
	print '</div>'; // suggest btn
		
	print '</div>'; // btn-group
	
	print '</div>'; // col-1 
	
	print '</div>'; // row largeBrowseButtons
	
	
	
	
	
	print '<div id="displayBadges"><div class="noBadges"></div></div>';
	//print '</div>'; // row
	
	//print '</div>'; // col-10 or 12
	
	//print '</div>'; // row
}



?>