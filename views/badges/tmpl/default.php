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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BADGES_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	
	print '<h2>'.JText::_("COM_BIODIV_BADGES_NOT_SCH_USER").'</h2>';
	
}
else if ( $this->notMyClass ) {
	
	print '<h2>'.JText::_("COM_BIODIV_BADGES_NOT_MY_CLASS").'</h2>';
	
}
else if ( $this->chooseClass ) {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, $this->classId, "badges");
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		
		print '<div id="helpButton_badges" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	
	// --------------------- Main content
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2 class="hidden-sm hidden-md hidden-lg">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_BADGES_HEADING").'</span>';
	print '</h2>';
	
	print '<div id="displayArea">';
	
	//print '<h3>'.JText::_("COM_BIODIV_BADGES_CHOOSE_CLASS").'</h3>';
	print '<div class="row">';
	
	if ( count($this->classes) == 0 ) {
		print '<div class="col-md-12 col-sm-12 col-xs-12">';
		print '<h3>'.JText::_("COM_BIODIV_BADGES_NO_CLASS").'</h3>';
		print '<h4>'.JText::_("COM_BIODIV_BADGES_CLASS_EXPLAIN").'</h4>';
		print '<a href="bes-school-admin"><button type="button" class="btn btn-info" >'.JText::_("COM_BIODIV_BADGES_CLASS_SETUP").'</button></a>';

		print '</div>'; // col-12
	}
	
	print '</div>'; // row
	
	print '<div class="row">';
	
	$inactiveFound = false;

	foreach ( $this->classes as $nextClass ) {
		
		if ( !$inactiveFound && $nextClass->is_active == false ) {
			$inactiveFound = true;
			print '</div>'; // active row end
			print '<div class="row">';
			print '<div class="col-md-12">';
			print '<div href="#inactiveClasses" class="btn btn-info btn-lg vSpaced" role="button" data-toggle="collapse" >'.JText::_("COM_BIODIV_BADGES_SHOW_INACTIVE_CLASSES").'</div>';
			print '</div>'; // inactive button
			print '<div id="inactiveClasses" class="collapse">';
		}
		
		
		print '<div class="col-md-3 col-sm-4 col-xs-12">';
		
		$pageLink = 'bes-badges?class_id='.$nextClass->class_id;
		
		if ( $this->help ) {
			$pageLink .= '&help=1';
		}
		
		print '<a href="'.$pageLink.'">';
		print '<div class="panel panel-default actionPanel chooseClassPanel" role="button" >';
		print '<div class="panel-body">';
		
		print '<div class="h4 panelHeading">';
		print $nextClass->name;
		print '</div>';
		
		print '<div class="text-center"><img src="'.$nextClass->image.'" class="img-responsive" alt="'.$nextClass->name.' avatar" /></div>';
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		
		print '</div>'; // col-3
		
	}
	
	if ( $inactiveFound ) {
		print '</div>'; // inactiveClasses
	}
	
	print '</div>'; // row
	
	print '</div>'; // displayArea
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
}
else {
	
	if ( $this->classId ) {
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("BioDiv.classId = '".$this->classId."';");
	}
	
	if ( $this->newBadgeId ) {
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("BioDiv.newBadgeId = '".$this->newBadgeId."';");
	}
	else if ( $this->newAwardId ) {
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("BioDiv.newAwardId = '".$this->newAwardId."';");
	}
	
	if ( $this->help ) {
		Biodiv\Help::printBadgesHelp( $this->schoolUser, $this->classId );
	}
	
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, $this->classId, "badges");
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		
		print '<div id="helpButton_badges" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	// --------------------- Main content
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2 class="hidden-sm hidden-md hidden-lg">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_BADGES_HEADING").'</span>';
	print '</h2>';
	
	print '<div id="displayArea">';
	
	if ( !$this->toCollect ) {
	
		print '<div class="panel">';
		
		print '<div class="row largeFilterButtons hidden-xs">';
		
		print '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		
		
		// ---------------------------------- all badges
		
		print '<div class="btn-group badgesItemWidth" role="group" aria-label="Clear filter button">';
		
		print '<div id="badgeFilter_All" class="btn filterBadgesBtn activeFilter ">';
			
		// $activeClass = '';
		// $imageSrc = $this->allBadgesImg;
		
		$image = $this->allBadgesImg;
		$activeImage = $this->allBadgesActiveImg;
		$colorClass = "";
		$activeClass = "";
		$imageSrc = JURI::root().$image;
		$activeImageSrc = JURI::root().$activeImage;
		$activeClass = "activeFilterPanel";
		$currImageSrc = JURI::root().$activeImage;
		
	
		print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
		print '<div class="panel-body">';
		
		print '<div class="row">';
		
		//print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="all badges icon" /></div>';
		print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="all badges icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	
		print '</div>'; // row
		
		print '<div class="row">';
		print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_BADGES_CLEAR_FILTERS").'</div>';
		print '</div>'; // row
		
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		//print $module->name;
		print '</div>';
			
		print '</div>'; // btnGroup
		
		
		
		
		//print '<div class="btn-group badgesItemWidth hidden-xs" role="group" aria-label="More badge filter buttons">';
		print '<div class="btn-group badgesItemWidth" role="group" aria-label="More badge filter buttons">';
		
		foreach ( $this->allModules as $module ) {
			
			print '<div id="badgeFilter_' . $module->name.'" class="btn filterBadgesBtn ">';
			
			// $activeClass = '';
			// $imageSrc = $module->icon;
			
			$image = $module->icon;
			$activeImage = $module->white_icon;
			$colorClass = "";
			$activeClass = "";
			$imageSrc = JURI::root().$image;
			$activeImageSrc = JURI::root().$activeImage;
			$activeClass = "";
			$currImageSrc = $imageSrc;
		
			
			//print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
			print '<div class="panel panel-default filterPanel toggleActive ">';
			print '<div class="panel-body">';
			
			// -------------------------------------- module icon
			print '<div class="row">';
			
			//print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="module icon" /></div>';
			print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="module icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	

			print '</div>'; // row
			
			print '<div class="row">';
			print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$module->name.'</div>';
			print '</div>'; // row
			
			print '</div>'; // panel-body
			
			print '</div>'; // panel
			
			print '</div>';
			
		}
		
		print '</div>'; // btnGroup
		
		
		
		//print '<div class="btn-group badgesItemWidth hidden-xs" role="group" aria-label="More resource filter buttons">';
		print '<div class="btn-group badgesItemWidth" role="group" aria-label="More resource filter buttons">';
		
		foreach ( $this->badgeGroups as $badgeGroup ) {
			
			print '<div id="badgeFilter_' . $badgeGroup->name.'" class="btn filterBadgesBtn ">';
			
			// $activeClass = '';
			// $imageSrc = $badgeGroup->icon;
			$image = $badgeGroup->icon;
			$activeImage = $badgeGroup->inverse_icon;
			$colorClass = "";
			$activeClass = "";
			$imageSrc = JURI::root().$image;
			$activeImageSrc = JURI::root().$activeImage;
			$activeClass = "";
			$currImageSrc = $imageSrc;
		
			
			print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
			print '<div class="panel-body">';
			
			// -------------------------------------- module icon
			print '<div class="row">';
			
			//print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="badge group icon" /></div>';
			print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="badge group icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	

			print '</div>'; // row
			
			print '<div class="row">';
			print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$badgeGroup->name.'</div>';
			print '</div>'; // row
			
			print '</div>'; // panel-body
			
			print '</div>'; // panel
			
			print '</div>';
		}	
		
		print '</div>'; // btnGroup
		
		print '</div>'; //col-12
		
		print '</div>'; //largeFilterButtons row
		
		
		
		
		
		print '<div class="row smallFilterButtons hidden-lg hidden-md hidden-sm">';
		
		print '<div class="col-md-12">';
		
		print '<div class="smallFilterGrid">';
		
		print '<div class="allBadgesButton">';
		
		// ---------------------------------- all badges
		
		print '<div class="btn-group badgesItemWidth" role="group" aria-label="Clear filter button">';
		
		print '<div id="badgeFilter_All" class="btn filterBadgesBtn activeFilter ">';
			
		// $activeClass = '';
		// $imageSrc = $this->allBadgesImg;
		
		$image = $this->allBadgesImg;
		$activeImage = $this->allBadgesActiveImg;
		$colorClass = "";
		$activeClass = "";
		$imageSrc = JURI::root().$image;
		$activeImageSrc = JURI::root().$activeImage;
		$activeClass = "activeFilterPanel";
		$currImageSrc = JURI::root().$activeImage;
		
	
		print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
		print '<div class="panel-body">';
		
		print '<div class="row">';
		
		//print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="all badges icon" /></div>';
		print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="all badges icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	
		print '</div>'; // row
		
		print '<div class="row hidden-xs">';
		print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_BADGES_CLEAR_FILTERS").'</div>';
		print '</div>'; // row
		
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		print '</div>';
			
		print '</div>'; // btnGroup
		
		print '</div>'; // allBadgesButton
		
		//print '<div class="col-xs-2 col-xs-offset-8">'
		
		
		// print '<div class="btn-group badgesItemWidth" role="group" aria-label="Clear filter button">';
		
		// print '<div id="badgeFilter_All" class="btn filterBadgesBtn activeFilter ">';
			
		// // $activeClass = '';
		// // $imageSrc = $this->allBadgesImg;
		
		// $image = $this->filterImg;
		// $activeImage = $this->filterActiveImg;
		// $colorClass = "";
		// $activeClass = "";
		// $imageSrc = JURI::root().$image;
		// $activeImageSrc = JURI::root().$activeImage;
		// $activeClass = "activeFilterPanel";
		// $currImageSrc = JURI::root().$activeImage;
		
	
		// print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
		// print '<div class="panel-body">';
		
		// print '<div class="row small-gutter">';
		
		// //print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="all badges icon" /></div>';
		// print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="all badges icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	
		// print '</div>'; // row
		
		// print '<div class="row hidden-xs">';
		// print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_BADGES_FILTER").'</div>';
		// print '</div>'; // row
		
		// print '</div>'; // panel-body
		
		// print '</div>'; // panel
		
		// print '</div>';
			
		// print '</div>'; // btnGroup
		
		
		
		
		print '<div class="moduleButtons">';
		
		//print '<div class="btn-group badgesItemWidth hidden-xs" role="group" aria-label="More badge filter buttons">';
		print '<div class="btn-group badgesItemWidth" role="group" aria-label="More badge filter buttons">';
		
		foreach ( $this->allModules as $module ) {
			
			print '<div id="badgeFilter_' . $module->name.'" class="btn filterBadgesBtn ">';
			
			// $activeClass = '';
			// $imageSrc = $module->icon;
			
			$image = $module->icon;
			$activeImage = $module->white_icon;
			$colorClass = "";
			$activeClass = "";
			$imageSrc = JURI::root().$image;
			$activeImageSrc = JURI::root().$activeImage;
			$activeClass = "";
			$currImageSrc = $imageSrc;
		
			
			//print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
			print '<div class="panel panel-default filterPanel toggleActive ">';
			print '<div class="panel-body">';
			
			// -------------------------------------- module icon
			print '<div class="row">';
			
			print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="module icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	

			print '</div>'; // row
			
			print '</div>'; // panel-body
			
			print '</div>'; // panel
			
			print '</div>';
			
		}
		
		
		print '</div>'; // btnGroup
		
		print '</div>'; // moduleButtons
		
		
		print '<div class="badgeGroupButtons">';
		
		//print '<div class="btn-group badgesItemWidth hidden-xs" role="group" aria-label="More resource filter buttons">';
		print '<div class="btn-group badgesItemWidth" role="group" aria-label="More resource filter buttons">';
		
		foreach ( $this->badgeGroups as $badgeGroup ) {
			
			print '<div id="badgeFilter_' . $badgeGroup->name.'" class="btn filterBadgesBtn ">';
			
			// $activeClass = '';
			// $imageSrc = $badgeGroup->icon;
			$image = $badgeGroup->icon;
			$activeImage = $badgeGroup->inverse_icon;
			$colorClass = "";
			$activeClass = "";
			$imageSrc = JURI::root().$image;
			$activeImageSrc = JURI::root().$activeImage;
			$activeClass = "";
			$currImageSrc = $imageSrc;
		
			
			print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'">';
			print '<div class="panel-body">';
			
			// -------------------------------------- module icon
			print '<div class="row">';
			
			print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="badge group icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	

			print '</div>'; // row
			
			print '</div>'; // panel-body
			
			print '</div>'; // panel
			
			print '</div>';
		}	
		
		print '</div>'; // btnGroup
		
		
		print '</div>'; // moduleBtns
		
		print '</div>'; // smallFilterGrid
		
		print '</div>'; // col-12
		
		
		print '</div>'; //smallFilterButtons row
		
		
		
		
		
		
		
		$badgeNum = 0;
		foreach ( $this->badges as $level=>$levelBadges ) {
			
			
			print '<h2 class="badgeHeading greenHeading badgesItemWidth">'.JText::_("COM_BIODIV_BADGES_LEVEL_".$level).'</h2>';
			
			foreach ( $levelBadges as $badge ) {
				
				if ( $badgeNum % 4 == 0 ) {
					print '<div class="row">';
				}
				print '<div class="col-md-3 col-sm-3 col-xs-6">';
				print '<div class="badgesItem badge_All badge_'.$badge->getModuleName().' badge_'.$badge->getBadgeGroupName().'">';
				$badge->printBadge();
				print '</div>'; // badgesItem
				print '</div>'; // col-3
				if ( $badgeNum % 4 == 3 ) {
					print '</div>'; // row
				}
				
				$badgeNum += 1;
				
			}
			
			print '<div class="row">';
			print '<div class="col-md-2 col-sm-2 col-xs-6 col-md-offset-5 col-sm-offset-5 col-xs-offset-3">';
			if ( array_key_exists($level, $this->awards) ) {
				
				$levelAward = $this->awards[$level];
				print '<div class="badgesItemHeight">';
				
				print '<div role="button" class="printCert" data-toggle="modal" '.
				'data-target="#certificateModal" data-pdfurl="'.JURI::root().$levelAward->getCertificate().'" '.
				'data-pdfname="'.$this->certificateData->name.'" data-pdfdate="'.$levelAward->getAwardDate().'">';
				$levelAward->printAward();
				print '</div>';
				
				print '</div>';
				
			}
			else {
				
				print '<div class="badgesItemHeight">';
				$blankAward = $this->blankAwards[$level];
				$blankAward->printAward();
				print '</div>';
				
			}
			print '</div>'; // col-2, offset
			print '</div>'; // row
			
		}
		
		print '</div>';
	}
	
	print '</div>'; // displayArea
	
	print '</div>'; // panel
	print '</div>'; // col-12
	
	print '</div>'; // row
	
}


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGES_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



print '<div id="badgeModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="badgeArticle" ></div>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



print '<div id="speciesModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>';
print '     <div class="modal-body">';
print '     <ul class="nav nav-tabs modalNav">';
print '       <li id="speciesTabItem" class="active"><a data-toggle="tab" href="#speciesTab">'.JText::_("COM_BIODIV_BADGES_SPECIES").'</a></li>';
print '       <li id="activityTabItem" ><a data-toggle="tab" href="#activityTab">'.JText::_("COM_BIODIV_BADGES_ACTIVITY").'</a></li>';
print '     </ul>';
print '     <div class="tab-content">';
print '       <div id="speciesTab" class="tab-pane fade in active">';
print '	      <div id="speciesArticle" ></div>';
print '       </div>';
print '       <div id="activityTab" class="tab-pane fade">';
print '	      <div id="activityArticle" ></div>';
print '       </div>';
print '     </div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGES_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



print '<div id="awardModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
//print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="besAward"></div>';
print '      </div>';
print '	  <div class="modal-footer">';
if ( $this->certificateData and $this->newAward ) {
	print '<button id="printCert" type="button" class="btn btn-lg btn-info" data-dismiss="modal" data-toggle="modal" '.
				'data-target="#certificateModal" data-pdfurl="'.JURI::root().$this->newAward->getCertificate().'" '.
				'data-pdfname="'.$this->certificateData->name.'" data-pdfdate="'.$this->newAward->getAwardDate().'">'.JText::_("COM_BIODIV_BADGES_PRINT_CERT").'</button>';
}
print '        <button type="button" class="btn btn-lg btn-primary reloadPage" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGES_CONTINUE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


print '<div id="badgeCompleteModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
//print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>'; // modal-header
print '     <div class="modal-body">';
print '	    <div id="badgeComplete"></div>';
print '      </div>'; // modal-body
//print '	  <div class="modal-footer">';
//print '        <button type="button" class="btn btn-lg btn-primary reloadPage" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGES_CANCEL").'</button>';
//print '      </div>'; // modal-footer
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal-dialog
print '</div>'; // modal


print '<div id="badgeCollectModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
//print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="besBadge"></div>';
print '      </div>';
print '	  <div class="modal-footer text-center">';
print '        <button type="button" class="btn btn-lg btn-primary reloadPage" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGES_COLLECT_BADGE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


print '<div id="certificateModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
//print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <iframe id="besCertificate"></iframe>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-lg btn-primary reloadPage" data-dismiss="modal" >'.JText::_("COM_BIODIV_BADGES_CANCEL").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';




JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/badges.js", true, true);

if ( $this->help ) {
	JHTML::script("com_biodiv/help.js", true, true);
}
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js", true, true);
JHTML::script("https://unpkg.com/pdf-lib", true, true);
//JHTML::script("https://unpkg.com/pdf-lib@1.4.0/dist/pdf-lib.min.js", array('cross_origin' => ''));

//JHTML::script("https://unpkg.com/leaflet@1.7.1/dist/leaflet.js", array('integrity' => 'sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==', 'cross_origin' => ''));


//JHTML::script("https://unpkg.com/downloadjs@1.4.7", true, true);

?>





