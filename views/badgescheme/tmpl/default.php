<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BADGESCHEME_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_BADGESCHEME_NOT_SCH_USER").'</h2>';
}
else {
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "teacherzone");
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		
		print '<div id="helpButton_badgescheme" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}

	// --------------------- Main content
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<div id="displayArea">';
	
	print '<a href="'.$this->educatorPage.'" class="btn btn-success homeBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_BADGESCHEME_EDUCATOR_ZONE");
	print '</a>';
	
	print '<h2><span class="greenHeading">'.JText::_("COM_BIODIV_BADGESCHEME_HEADING").'</span></h2>';
	
	print '<div class="panel schemePanel">';
		
	foreach ( $this->badges as $lockLevel=>$levelBadges ) {
		
		$classAward = $this->awards[Biodiv\SchoolCommunity::TEACHER_ROLE][$lockLevel];
		$studentAward = $this->awards[Biodiv\SchoolCommunity::STUDENT_ROLE][$lockLevel];
		
		print '<h3 class="text-center vSpaced thickFont">'.JText::_("COM_BIODIV_BADGESCHEME_BADGES_".$lockLevel).'</h3>';
		
		print '<div class="badgeSchemeAwardGrid">';
		
		print '<div class="badgeSchemeAwardClass text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_CLASS_AWARD");
		print '</div>';
		
		print '<div class="badgeSchemeAwardClassImg text-center">';
		print '<img src="'.$classAward->image.'" class="img-responsive  badgeSchemeIcon" alt="class award icon for level '.$lockLevel.'" />';
		print '</div>';
		
		print '<div class="badgeSchemeAwardSchool text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_STUDENT_AWARD");
		print '</div>';
		
		print '<div class="badgeSchemeAwardSchoolImg text-center">';
		print '<img src="'.$studentAward->image.'" class="img-responsive  badgeSchemeIcon" alt="class award icon for level '.$lockLevel.'" />';
		print '</div>';
		
		print '</div>'; // badgeSchemeAwardGrid
		
		print '<h4 class="text-center vSpaced">'.JText::_("COM_BIODIV_BADGESCHEME_COMPLETE_".$lockLevel).'</h4>';
		
		
		print '<div class="row small-gutter badgeSchemeRow badgeSchemeHeader hidden-xs">';
			
		print '<div class="col-md-12">';
		
		print '<div class="badgeSchemeGrid h4 thickFont ">';
		
		print '<div class="badgeSchemeClassOnly text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_CLASS_ONLY");
		print '</div>'; // classOnlyBadge
		
		print '<div class="badgeSchemeBadgeGroup text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_TYPE");
		print '</div>'; // badgeSchemeBadgeGroup
		
		print '<div class="badgeSchemeModule text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_MODULE");
		print '</div>'; // badgeSchemeModule
		
		print '<div class="badgeSchemeBadge text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_BADGE");
		print '</div>'; // badgeSchemeBadge
		
		print '<div class="badgeSchemeBadgeDesc">';
		print JText::_("COM_BIODIV_BADGESCHEME_ACTIVITY");
		print '</div>'; // badgeSchemeBadgeDesc
		
		print '<div class="badgeSchemeResources text-center">';
		print JText::_("COM_BIODIV_BADGESCHEME_RESOURCES");
		print '</div>'; // badgeSchemeResources
		
		print '</div>'; // badgeSchemeGrid
		
		print '</div>'; // col-12
		
		print '</div>'; // row
	
		
		foreach ( $levelBadges as $badge ) {
			
			$badgeId = $badge->getBadgeId();
			
			$groupId = $badge->getBadgeGroupId();
			$groupName = $this->badgeGroups[$groupId]->name;
			$groupDesc = $this->badgeGroups[$groupId]->description;
	
			$moduleId = $badge->getModuleId();
			$moduleName = $this->modules[$moduleId]->name;
			$moduleDesc = $this->modules[$moduleId]->description;
	
			print '<div class="row small-gutter badgeSchemeRow">';
			
			print '<div class="col-md-12">';
			
			print '<div class="hidden-lg hidden-md hidden-sm"><hr/></div>';
			
			print '<div class="badgeSchemeGrid">';
			
			print '<div class="badgeSchemeClassOnly text-center">';
			if ( !($badge->getLinkedBadge() > 0) ) {
				 print '<i class="fa fa-check fa-2x hidden-xs" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_BADGESCHEME_CLASS_ONLY_TOOLTIP").'"></i>';
                 print '<div class="hidden-lg hidden-md hidden-sm">'.JText::_("COM_BIODIV_BADGESCHEME_CLASS_ONLY_TOOLTIP").'</div>'; 
			}
			print '</div>'; // classOnlyBadge
			
			print '<div class="badgeSchemeBadgeGroup text-center">';
			print '<img src="'.$badge->getBadgeGroupIcon().'" class="img-responsive  badgeSchemeIcon" alt="icon for badge type '.$groupName.'" data-toggle="tooltip" title="'.$groupName.'. '.$groupDesc.'" />';
			print '</div>'; // badgeSchemeBadgeGroup
			
			print '<div class="badgeSchemeModule text-center">';
			print '<img src="'.$badge->getModuleIcon().'" class="img-responsive  badgeSchemeIcon" alt="icon for module '.$moduleName.'" data-toggle="tooltip" title="'.$moduleName.'. '.$moduleDesc.'" />';
			print '</div>'; // badgeSchemeModule
			
			print '<div class="badgeSchemeBadge text-center">';
			print '<img src="'.$badge->getBadgeImage().'" class="img-responsive  badgeSchemeImage" alt="icon for badge '.$badge->getBadgeName().'" />';print '</div>'; // badgeSchemeBadge
			
			print '<div class="badgeSchemeBadgeName">';
			print $badge->getBadgeName();
			print '</div>'; // badgeSchemeBadgeName
			
			print '<div class="badgeSchemeBadgeDesc">';
			print '<button id="badgeArticle_'.$badgeId.'" class="btn btn-info multilineBtn badgeArticle">'.$badge->getDescription().'</button>';
			print '</div>'; // badgeSchemeBadgeDesc
			
			print '<div class="badgeSchemeResources text-center">';
			$numResources = $badge->getNumResources();
			if ( $numResources == 0 ) {
				print "0 " . JText::_("COM_BIODIV_BADGESCHEME_RESOURCES_PL");
			}
			else if ( $numResources == 1 ) {
				$resStr = JText::_("COM_BIODIV_BADGESCHEME_VIEW_RES") . ' ' . $numResources . ' ' . JText::_("COM_BIODIV_BADGESCHEME_RESOURCE");
				print '<a href="bes-search-resources?badge='.$badgeId.'">';
				print '<button class="btn btn-info">'.$resStr.'</button>';
				print '</a>';
			}
			else {
				$resStr = JText::_("COM_BIODIV_BADGESCHEME_VIEW_RES") . ' ' . $numResources . ' ' . JText::_("COM_BIODIV_BADGESCHEME_RESOURCES_PL");
				print '<a href="bes-search-resources?badge='.$badgeId.'">';
				print '<button class="btn btn-info">'.$resStr.'</button>';
				print '</a>';
			}
			print '</div>'; // badgeSchemeResources
			
			print '</div>'; // badgeSchemeGrid
			
			print '</div>'; // col-12
			
			print '</div>'; // row
					
		}
		
		print '<hr/>';
		
	}
	
	
	print '<div class="badgeSchemeKey">';
	print '<div class="keyGrid"><div class="keyImage h3 text-center thickFont vSpaced">'.JText::_("COM_BIODIV_BADGESCHEME_KEY").'</div></div>';
	
	foreach ( $this->badgeGroups as $badgeGroup ) {
		
		print '<div class="keyGrid">';
		
		print '<div class="keyImage">';
		print '<img src="'.$badgeGroup->icon.'" class="img-responsive  badgeSchemeIcon" alt="icon for badge type '.$badgeGroup->name.'" />';
		print '</div>';
		
		print '<div class="keyName">';
		print $badgeGroup->name;
		print '</div>';
		
		print '<div class="keyDesc">';
		print $badgeGroup->description;
		print '</div>';
		
		print '</div>';
	}
	
	foreach ( $this->modules as $module ) {
		
		print '<div class="keyGrid">';
		
		print '<div class="keyImage">';
		print '<img src="'.$module->icon.'" class="img-responsive  badgeSchemeIcon" alt="icon for badge type '.$module->name.'" />';
		print '</div>';
		
		print '<div class="keyName">';
		print $module->name;
		print '</div>';
		
		print '<div class="keyDesc">';
		print $module->description;
		print '</div>';
		
		print '</div>';
	}
	
	print '</div>'; // badgeSchemeKey
	
	
	
	print '</div>'; // panel
	
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
print '      <div class="modal-body">';
print '	        <div id="helpArticle" ></div>';
print '       </div>';
print '	      <div class="modal-footer">';
print '         <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '       </div>'; // modal-footer  	  
print '    </div>'; // modal-content
print '  </div>'; // modal-dialog
print '</div>';


// ------------------------------ Badge article modal
print '<div id="badgeModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="badgeArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
	print '        <a href="'.$this->badgesLink.'"><button type="button" class="btn btn-primary">'.JText::_("COM_BIODIV_BADGESCHEME_BADGES").'</button></a>';
}
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGESCHEME_CLOSE").'</button>';
print '      </div>';	  	  
print '    </div>';

print '  </div>';
print '</div>';


JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/badgescheme.js", true, true);

// JHTML::script("com_biodiv/resourcelist.js", true, true);
// JHTML::script("com_biodiv/resourceupload.js", true, true);
// JHTML::script("com_biodiv/schoolcommunity.js", true, true);
// JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);




?>





