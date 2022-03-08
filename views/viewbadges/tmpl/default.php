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
		
		print '<div id="badgesButton_'.$groupId.'" class="btn '. $colorClass . ' text-center browseBadgesBtn viewGroupBtn">';
		
		print '<div class="panel panel-default">';
		print '<div class="panel-body">';
		
		
		// -------------------------------------- badge group icon
		print '<div class="row">';
		
		$imageSrc = JURI::root().$this->badgeIcons[$groupId];
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
	
	
	
	print '</div>'; // col-12 
	
	print '</div>'; // row smallBrowseButtons
	
	
	print '<div id="displayBadges"><div class="noBadges"></div></div>';
	
}



?>