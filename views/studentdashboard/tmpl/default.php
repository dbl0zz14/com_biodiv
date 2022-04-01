<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else if ( !$this->isStudent ) {
	print '<h2>'.$this->translations['not_student']['translation_text'].'</h2>';
}
else if ( $this->firstLoad ) {
	
	print '<div class="row">';
	print '<div class="col-md-12">';

	print '<h1 class="text-center">'.$this->translations['welcome']['translation_text'].'</h1>';
	
	print '<h2 class="text-center bigSpaced">'.$this->translations['you_are']['translation_text'].'</h2>';

	//print_r ( $this->avatars );
	
	//print '<form id="avatarForm">';
	
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
	
	print '<button id="saveAvatar" class="btn btn-info btn-lg spaced">'.$this->translations['save_avatar']['translation_text'].'</button>';
	
	print '</div>'; // row
	print '</div>'; // avatarArea

	print '<div id="goToDash" class="text-center" style="display:none"><a href="'.$this->translations['student_dash']['translation_text'].'"><button class="btn btn-info btn-lg studentDashboard bigSpaced">'.$this->translations['dashboard']['translation_text'].'</button></a></div>';
	
	
	print '</div>'; // col-12
	print '</div>'; // row
}
else {
		
	Biodiv\SchoolCommunity::generateStudentMasthead( $this->helpOption, null, $this->myTotalPoints, $this->totalBadges, $this->totalStars ); 
	
	
	//print '<div id="studentDashRow" class="row" data-resource_type="'.$this->resourceTypeId.'">';
	print '<div id="studentDashRow" class="row" >';
	print '<div class="col-md-12">';
	
	print '<h2 class="greenHeading">'.$this->translations['heading']['translation_text'].'</h2>';
	print '<h3 style="margin-bottom:20px;">'.$this->translations['subheading']['translation_text'].'</h3>';

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
	
	
	/*
	print '<div class="col-md-3">';
	
	// ---------------------------------- Celebration
	
	//print '<div class="col-md-3">';
	
	print '<div class="panel panel-default greenPanel">';
	print '<div class="panel-body">';
	
	print '<div id="studentCelebration" class="studentCelebration"></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	//print '</div>'; // col-3
	
	// ---------------------------------- Student target
	
	//print '<div class="col-md-3">';
	
	print '<div class="panel panel-default bluePanel">';
	print '<div class="panel-body">';
	
	print '<div id="studentTarget" class="studentTarget"></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	//print '</div>'; // col-3
	
	print '</div>'; // col-3
	
	*/
	
	
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
	
	
	/*
	// ------------------------------------------------------------------- browse tasks
	
	print '<div class="col-md-4 col-sm-6 col-xs-12 showWork text-center">';
	print '<a href="'.$this->translations['badges_link']['translation_text'].'" >';
	print '<div class="panel panel-default  actionPanel ">';
	
	print '<div class="panel-body">';
	print '<div><img src="'.$findIcon.'"  class="img-responsive" alt="Select activity icon" /></div>';
	print '</div>'; // panel-body
	
	print '<div class="panel-footer h4">';
	print $this->translations['browse_tasks']['translation_text'];
	print '</div>';
	
	print '</div>'; // panel
	print '</a>';
	print '</div>'; //col-4
	
	
	// -------------------------------------- complete tasks - collect badges
	
	print '<div class="col-md-4 col-sm-6 col-xs-12 showComplete text-center">';
	
	print '<a href="'.$this->translations['collection_link']['translation_text'].'" >';
	
	print '<div class="panel panel-default actionPanel">';
	
	print '<div class="panel-body">';
	if ( $this->numToCollect > 0 ) {
		print '<span class="label label-primary notifyLabel">'.$this->numToCollect.'</span>';
		
	}
	print '<div><img src="'.$collectIcon.'" class="img-responsive" alt="Collect badge icon" /></div>';
	print '</div>';
	print '<div class="panel-footer h4">';
	print $this->translations['collect_badge']['translation_text'];
	print '</div>';
	print '</div>';
	print '</a>';
	
	print '</div>';
	
	
	// ------------------------------------------------------------------- show my work
	
	print '<div class="col-md-4 col-sm-6 col-xs-12 showWork text-center">';
	print '<a href="'.$this->translations['schoolwork_link']['translation_text'].'" >';
	print '<div class="panel panel-default actionPanel">';
	print '<div class="panel-body">';

	print '<div><img src="'.$workIcon.'" class="img-responsive" alt="View documents icon" /></div>';
	
	print '</div>'; // panel-body
	
	print '<div class="panel-footer h4">';
	print $this->translations['show_work']['translation_text'];
	print '</div>';
	
	print '</div>'; // panel
	print '</a>';
	print '</div>';


	// ------------------------------------------------------------- wild space / learn about species
	print '<div class="col-md-4 col-sm-6 col-xs-12 text-center">';
	print '<a href="'.$this->translations['wild_space_link']['translation_text'].'" >';
	print '<div class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	
	print '<div><img src="'.$speciesIcon.'" class="img-responsive" alt="View species icon" /></div>';
	
	
	print '</div>'; // panel-body
	
	print '<div class="panel-footer h4">';
	print $this->translations['learn_species']['translation_text'];
	print '</div>';
	print '</div>';
	print '</a>';
	print '</div>'; // col-4
	
	
	// ------------------------------------------------------------- my school
	print '<div class="col-md-4 col-sm-6 col-xs-12 text-center">';
	print '<a href="'.$this->translations['school_dash']['translation_text'].'" >';
	print '<div class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	
	print '<div><img src="'.$schoolIcon.'" class="img-responsive" alt="My school icon" /></div>';
	
	
	print '</div>'; // panel-body
	
	print '<div class="panel-footer h4">';
	print $this->translations['school_page']['translation_text'];
	print '</div>';
	print '</div>';
	print '</a>';
	print '</div>'; // col-4
	
	
	// ----------------------------------------------------------- community
	print '<div class="col-md-4 col-sm-6 col-xs-12 text-center">';
	print '<a href="'.$this->translations['community_dash']['translation_text'].'" >';
	print '<div class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	
	print '<div><img src="'.$communityIcon.'" class="img-responsive" alt="All schools icon" /></div>';
	
	print '</div>'; // panel-body
	print '<div class="panel-footer h4">';
	print $this->translations['community_page']['translation_text'];
	print '</div>';
	print '</div>'; // panel
	
	print '</div>'; // col-4
	*/
	print '<div class="col-md-12">';
	print '<div class="gridContainer">';
	
	print '<div class="findActivity">';
	
	print '<a href="'.$this->translations['badges_link']['translation_text'].'" >';
	print '<div id="findActivityPanel" class="panel panel-default  actionPanel ">';
	print '<div class="panel-body">';
	print '<div class="h2">'.$this->translations['browse_tasks']['translation_text'].'</div>';
	print '<div><img src="'.$findIcon.'"  class="img-responsive" alt="Select activity icon" /></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>'; // findActivity
	
	
	print '<div class="collectBadge">';
	
	print '<a href="'.$this->translations['collection_link']['translation_text'].'" >';
	print '<div id="collectBadgePanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	if ( $this->numToCollect > 0 ) {
		print '<span class="label label-primary notifyLabel">'.$this->numToCollect.'</span>';
		
	}
	print '<div class="h3 panelHeading">'.$this->translations['collect_badge']['translation_text'].'</div>';
	print '<div><img src="'.$collectIcon.'" class="img-responsive" alt="Collect badge icon" /></div>';
	print '</div>';
	print '</div>';
	print '</a>';
	
	print '</div>'; // collectBadge
	
	
	print '<div class="viewSpecies">';
	
	print '<a href="'.$this->translations['wild_space_link']['translation_text'].'" >';
	print '<div id="viewSpeciesPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.$this->translations['learn_species']['translation_text'].'</div>';
	print '<div><img src="'.$speciesIcon.'" class="img-responsive" alt="View species icon" /></div>';
	print '</div>'; // panel-body
	print '</div>';
	print '</a>';
	
	print '</div>'; // viewSpecies
	
	
	print '<div class="reviewWork">';
	
	print '<a href="'.$this->translations['schoolwork_link']['translation_text'].'" >';
	print '<div id="reviewWorkPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.$this->translations['show_work']['translation_text'].'</div>';
	print '<div><img src="'.$workIcon.'" class="img-responsive" alt="View documents icon" /></div>';
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	
	print '</div>';
	
	print '<div class="visitSchool">';
	
	print '<a href="'.$this->translations['school_dash']['translation_text'].'" >';
	print '<div id="visitSchoolPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.$this->translations['school_page']['translation_text'].'</div>';
	print '<div><img src="'.$schoolIcon.'" class="img-responsive" alt="My school icon" /></div>';
	print '</div>'; // panel-body
	print '</div>';
	print '</a>';
	
	print '</div>'; // visitSchool
	
	print '<div class="visitCommunity">';
	
	print '<a href="'.$this->translations['community_dash']['translation_text'].'" >';
	print '<div id="visitCommunityPanel" class="panel panel-default actionPanel">';
	print '<div class="panel-body">';
	print '<div class="h3 panelHeading">'.$this->translations['community_page']['translation_text'].'</div>';
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
JHTML::script("com_biodiv/studentdashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);




?>

<!-- svg xmlns="http://www.w3.org/2000/svg" viewBox="-25 0 150 100" width="150" height="100">

<path id="path" stroke="none" fill="none" d="m -678.90932,548.92826 c 110.28708,-51.92087 220.57404,-103.84167 294.8748,-159.16471 74.30077,-55.32304 112.61398,-114.0461 144.66191,-184.05043 32.04792,-70.00433 57.82877,-151.285628 57.29154,-207.5035801 -0.53723,-56.2179519 -27.39228,-87.3698119 -51.92059,-103.6623499 -24.52831,-16.29254 -46.72848,-17.72481 -59.08223,1.61173 -12.35376,19.336541 -14.86023,59.440085 9.66856,86.295729 24.52879,26.8556433 76.09049,40.462202 182.61923,34.911743 106.5287412,-5.55046 268.01712,-30.257108 408.0254,-69.824895 140.00828,-39.567788 258.52859,-93.994027 306.15235,-128.727287 47.62377,-34.73326 24.34938,-49.77209 -7.34065,-54.78519 -31.69003,-5.01311 -71.79359,-1.6e-4 -94.35246,17.904 -22.55888,17.90415 -27.57182,48.69794 6.62521,74.6585 34.19704,25.96055 107.60084,47.086523 187.09417,46.011998 79.49334,-1.074524 165.07144,-24.348908 220.03602,-52.995158 54.96458,-28.64625 79.31316,-62.66265 103.66226,-96.67978" />


</svg>


<svg id="bee" viewBox="-10.5 -10.5 21 21" style="width: 50px; height: auto;">
  <g transform="rotate(90) translate(0 -4)">
    <g stroke="currentColor">
      <circle fill="currentColor" r="4" stroke-width="2.5" />
      <g fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path transform="rotate(45) translate(0 -4)" d="M 0 0 v -3" />
        <path transform="rotate(-45) translate(0 -4)" d="M 0 0 v -3" />
        <g fill="hsl(200, 80%, 90%)">
          <path transform="rotate(15)" d="M 0 0 h 7 a 3 3 0 0 1 0 6 q -4 0 -7 -6" />
          <path transform="scale(-1 1) rotate(15)" d="M 0 0 h 7 a 3 3 0 0 1 0 6 q -4 0 -7 -6" />
        </g>
        <g fill="hsl(50, 80%, 50%)">
          <path d="M 0 0 c 2 6 8 10 0 12 -8 -2 -2 -6 0 -12" />
        </g>
      </g>
    </g>
  </g>
</svg -->




