<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Help {
	
	const STUDENT_SYSTEM_BADGE = 1;
	const STUDENT_USER_BADGE = 2;
	const CLASS_SYSTEM_BADGE = 29;
	const CLASS_USER_BADGE = 30;
	const RESOURCE_SET = 2234;
	
	
	function __construct( $id )
	{
		
	}
	
	
	public static function printChooseClassHelp ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		if ( $schoolUser ) {
			
			// ----------------------- help overlay -------------------
			
			print '<div class="helpOverlay"></div>';
	
	
			// ----------------------- help welcome
	
			print '<div id="badgesHelp_1" class="instructions help_1">';
			
			print '<h3>'.\JText::_("COM_BIODIV_CHOOSE_CLASS_PAGE").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_FIRST_CHOOSE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-primary skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			
			print '</div>'; // row
			
			print '</div>';
			
		}
	}
	
	
	public static function printBadgesHelp ( $schoolUser = null, $classId = null ) {
		
		if ( !$schoolUser ) {
			
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		if ( $schoolUser ) {
			
			// ----------------------- help overlay -------------------
			
			print '<div class="helpOverlay"></div>';
	
	
			// ----------------------- help welcome
	
			print '<div id="badgesHelp_1" class="instructions help_1">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_BADGES_PAGE").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_HELP_TOUR").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_1" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help scroll down
	
			print '<div id="badgesHelp_2" class="instructions bottom hidden help_2">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_SCROLL").'</h3>';
			print '<h3>'.'.'.'</h3>';
			print '<h3>'.'.'.'</h3>';
			print '<h3>'.'.'.'</h3>';
			print '<h3>'.'.'.'</h3>';
			print '<h3>'.'.'.'</h3>';
			print '<h3>'.'.'.'</h3>';
			print '<h3>'.'.'.'</h3>';
			
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				print '<h3>'.\JText::_("COM_BIODIV_HELP_COMPLETE_FOR_TROPHY").'</h3>';
			}
			else {
				print '<h3>'.\JText::_("COM_BIODIV_HELP_COMPLETE_FOR_STAR").'</h3>';
			}
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_2" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_GOT_IT").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';

	
			// ----------------------- help filter buttons 
			
			print '<div id="badgesHelp_3" class="instructions top hidden help_3">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_FILTER").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_3" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
	
	
			// ----------------------- help tap badge
	
			print '<div id="badgesHelp_4" class="instructions bottomleft hidden help_4">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_BADGE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_4" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
	
	
			// ------------------------help badge article example - user counted badge
			
			print '<div id="mockModal_5" class="mockModal hidden help_5 help_6 ">';
			
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				$badgeId = self::CLASS_USER_BADGE;
			}
			else {
				$badgeId = self::STUDENT_USER_BADGE;
			}
			$userBadge = Badge::createFromId ( $schoolUser, $classId, $badgeId );
			$userBadge->printBadgeHeader();
			print \JText::_("COM_BIODIV_HELP_ACTIVITY");
			
			print '<hr>';
		 
			print '<div class="row">';
			
			print '<button type="button" class="btn btn-primary btn-lg hSpaced" >'.\JText::_("COM_BIODIV_BADGEARTICLE_COMPLETE").'</button>';
			
			print '<button type="button" class="btn btn-info btn-lg hSpaced">'.\JText::_("COM_BIODIV_BADGEARTICLE_CLOSE").'</button>';
			
			print '</div>'; // row
			
			print '</div>'; // mockModal_5
			
			
			// ----------------------- help badge article 
	
			print '<div id="badgesHelp_5" class="instructions left hidden help_5">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_ARTICLE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_5" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
		
	
			// ----------------------- help complete button 
	
			print '<div id="badgesHelp_6" class="instructions bottom hidden help_6">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_COMPLETE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_6" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
	
	
			// ------------------------help badge article example - user counted badge
			
			print '<div id="mockModal_7" class="mockModal hidden help_7">';
			
			$userBadge->printBadgeComplete();
			
			print '<div class="vSpaced">';
	
			print '<label for="uploadDescription"><h3>'.\JText::_("COM_BIODIV_BADGECOMPLETE_UPLOAD_DESC").'</h3></label>';
			print '<textarea id="uploadDescription" name="uploadDescription"></textarea>';
			print '<div id="uploadDescriptionCount" class="text-right" data-maxchars="'.ResourceFile::MAX_DESC_CHARS.'">0/'.ResourceFile::MAX_DESC_CHARS.'</div>';
				
			print '</div>';
			
			print '<div class="vSpaced">';
			
			print '<h3>'.\JText::_("COM_BIODIV_BADGECOMPLETE_UPLOAD").'</h3>';
			
			print '<button class="btn btn-primary btn-lg spaced ">'.\JText::_("COM_BIODIV_BADGECOMPLETE_CREATE_SET").'</button>';
				
			print '<button type="button" class="btn btn-default btn-lg hSpaced ">'.\JText::_("COM_BIODIV_BADGECOMPLETE_NO_FILES").'</button>';
			
			print '<button type="button" class="btn btn-default btn-lg hSpaced ">'.\JText::_("COM_BIODIV_BADGECOMPLETE_CANCEL").'</button>';
			
			print '</div>';
			
			print '</div>'; // mockModal_7
			
			
			// ----------------------- help enter details 
	
			print '<div id="badgesHelp_7" class="instructions left hidden help_7">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_ENTER").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_7" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
	
			
			// ------------------------help badge article example - system badge
			
			print '<div id="mockModal_8" class="mockModal hidden help_8">';
			
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				$badgeId = self::CLASS_SYSTEM_BADGE;
			}
			else {
				$badgeId = self::STUDENT_SYSTEM_BADGE;
			}
			
			$systemBadge = Badge::createFromId ( $schoolUser, $classId, $badgeId );
			$systemBadge->printBadgeHeader();
			print \JText::_("COM_BIODIV_HELP_ACTIVITY");
			
			print '<hr>';
		 
			print '<div class="row">';
			
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button type="button" class="btn btn-info btn-lg " data-dismiss="modal">'.\JText::_("COM_BIODIV_BADGEARTICLE_CLOSE").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>'; // mockModal_2
			
			
			// ----------------------- help badge article 
	
			print '<div id="badgesHelp_8" class="instructions left hidden help_8">';
		
			print '<h3>'.\JText::_("COM_BIODIV_HELP_SYSTEM_ARTICLE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_8" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
		
	
			// ----------------------- help status bar
	
			print '<div id="badgesHelp_9" class="instructions topright hidden help_9">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_STATUS_BAR").'</h3>';
			
			if ( $schoolUser->role_id == SchoolCommunity::TEACHER_ROLE ) {
				print '<h3>'.\JText::_("COM_BIODIV_HELP_SEE_PROGRESS_CLASS").'</h3>';
			}
			else {
				print '<h3>'.\JText::_("COM_BIODIV_HELP_SEE_PROGRESS").'</h3>';
			}
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" >'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_9" type="button" class="btn btn-lg btn-primary nextHelp" >'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help end
	
			print '<div id="badgesHelp_10" class="instructions hidden help_10">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_END_TOUR").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_HELP_HAVE_FUN").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-6">';
			print '<button id="nextHelp_10" type="button" class="btn btn-lg btn-primary skipHelp">'.\JText::_("COM_BIODIV_HELP_CONTINUE").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			
			
			
			
		}
	
	}
	
	
	public static function printResourceHubHelp ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		if ( $schoolUser ) {
			
			// ----------------------- help overlay -------------------
			
			print '<div class="helpOverlay"></div>';
	
	
			// ----------------------- help welcome
	
			print '<div id="resourceHubHelp_1" class="instructions help_1">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_PAGE").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_TOUR").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_1" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help resource cards
	
			print '<div id="resourceHubHelp_2" class="instructions bottom hidden help_2">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_SCROLL").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_2" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_RESOURCES_DETAIL").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			
			// ------------------------show elements of resource card
			
			print '<div id="mockModal_3" class="mockModal hidden help_3 help_4 help_5 help_6 help_7 help_8 help_9 help_10">';
			
			$setId = self::RESOURCE_SET;
			
			$set = ResourceSet::createFromId ($setId );
			
			print '<div class="row">';
			
			print '<div class="col-md-6 col-sm-6 col-md-offset-3">';
											
			$set->printCard();
			
			print '</div>'; // col-6
			
			print '</div>'; // row
			
			print '</div>'; // mockModal_3
			
			
			// ----------------------- help explain resource card
	
			print '<div id="resourceHubHelp_3" class="instructions right hidden help_3">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_CARDS").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_3" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			// ----------------------- help explain resource card
	
			print '<div id="resourceHubHelp_4" class="instructions right hidden help_4">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_LOOK_THROUGH").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_4" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain resource type
	
			print '<div id="resourceHubHelp_5" class="instructions top hidden help_5">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_CENTRE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_5" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain resource file type
	
			print '<div id="resourceHubHelp_6" class="instructions top hidden help_6">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_LEFT").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_6" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain resource share level
	
			print '<div id="resourceHubHelp_7" class="instructions top hidden help_7">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_RIGHT").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_7" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain resource like and bookmark
	
			print '<div id="resourceHubHelp_8" class="instructions right hidden help_8">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_LIKE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_8" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain resource title and tags
	
			print '<div id="resourceHubHelp_9" class="instructions left hidden help_9">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_TITLE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_9" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain resource badge
	
			print '<div id="resourceHubHelp_10" class="instructions bottom hidden help_10">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_BADGES").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_TAP_BADGE").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_10" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_GOT_IT").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain tap card
	
			print '<div id="resourceHubHelp_11" class="instructions bottom hidden help_11">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_TAP_CARD").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_11" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain show featured resources
	
			print '<div id="resourceHubHelp_12" class="instructions top hidden help_12">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_FEATURED").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_12" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain filter buttons
	
			print '<div id="resourceHubHelp_13" class="instructions bottom hidden help_13">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_SEARCH").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_13" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help explain clear filters
	
			print '<div id="resourceHubHelp_14" class="instructions bottom hidden help_14">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_CLEAR").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_14" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_GOT_IT").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help add resource
	
			print '<div id="resourceHubHelp_15" class="instructions left hidden help_15">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_NEW").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_15" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help fill resource form
	
			print '<div id="resourceHubHelp_16" class="instructions left hidden help_16">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_FILL_IN").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_16" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			// ----------------------- help fill resource form
	
			print '<div id="resourceHubHelp_17" class="instructions hidden help_17">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_FINISH").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_ENJOY").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-6">';
			print '<button id="nextHelp_17" type="button" class="btn btn-lg btn-primary skipHelp">'.\JText::_("COM_BIODIV_HELP_CONTINUE").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
			
			
		}
	}
	
	public static function printSchoolAdminHelp ( $schoolUser = null ) {
		
		if ( !$schoolUser ) {
			
			$schoolUser = SchoolCommunity::getSchoolUser();
		}
		
		if ( $schoolUser ) {
			
			// ----------------------- help overlay -------------------
			
			print '<div class="helpOverlay"></div>';
	
	
			// ----------------------- help welcome
	
			print '<div id="schoolAdminHelp_1" class="instructions help_1">';
			
			print '<h3>'.\JText::_("COM_BIODIV_HELP_RESOURCES_PAGE").'</h3>';
			print '<h3>'.\JText::_("COM_BIODIV_HELP_TOUR").'</h3>';
			
			print '<div class="row helpButtons">';
			print '<div class="col-md-6  col-sm-6 col-xs-6text-right">';
			print '<button type="button" class="btn btn-lg btn-info skipHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_SKIP").'</button>';
			print '</div>'; // col-6
			print '<div class="col-md-6 col-sm-6 col-xs-6">';
			print '<button id="nextHelp_1" type="button" class="btn btn-lg btn-primary nextHelp" data-dismiss="modal">'.\JText::_("COM_BIODIV_HELP_OK").'</button>';
			print '</div>'; // col-6
			print '</div>'; // row
			
			print '</div>';
			
		}
	}
	
	
}



?>

