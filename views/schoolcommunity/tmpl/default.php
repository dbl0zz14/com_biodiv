<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_NOT_SCH_USER").'</h2>';
}
else {
	
	$document = JFactory::getDocument();
	
	if ( $this->badgeId > 0 ) {
		$document->addScriptDeclaration("BioDiv.badge = '".$this->badgeId."';");
	}
	if ( $this->classId > 0 ) {
		$document->addScriptDeclaration("BioDiv.classId = '".$this->classId."';");
	}

	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "schoolcommunity");
	
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
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HEADING").'</span>';
	print '</h2>';
	
	print '<div id="displayArea">';
	
	
	// print '<h2>';
	// print '<div class="row">';
	// print '<div class="col-md-10 col-sm-10 col-xs-10">';
	// print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_HEADING").'</span> <small class="hidden-xs">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SUBHEADING").'</small>';
	// print '</div>'; // col-10
	// print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
	// if ( $this->helpOption > 0 ) {
		// print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		// print '<i class="fa fa-info"></i>';
		// print '</div>'; // helpButton
	// }
	// print '</div>'; // col-2
	// print '</div>'; // row
	// print '</h2>';  
	print '<div class="row">';
	if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div class="col-md-2 col-sm-2 col-xs-3">';
		print '<div class="newPost">';
		print '<div class="panel panel-default actionPanel uploadPost" role="button" data-toggle="modal" data-target="#newPostModal">';
		
		print '<div class="panel-body">';
		
		// //print '<div class="h4 panelHeading ">';
		// print '<div class="h4 hidden-xs">';
		// print JText::_("COM_BIODIV_SCHOOLCOMMUNITY_NEW_POST");
		// print '</div>';
		
		// print '<div class="text-center"><i class="fa fa-2x fa-plus newPostIcon"></i></div>';
	
		print '<div class="row hidden-xs">';
		print '<div class="col-md-12 col-sm-12 col-xs-12 text-center h4">';
		print '<div class="newText">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_NEW_POST").'</div>';
		print '</div>';
		print '</div>'; // row

		print '<div class="row">';
		print '<div class="text-center"><i class="fa fa-2x fa-plus newPostIcon"></i></div>';
		print '</div>'; // row
	
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		print '</div>'; // newPost
		
		print '</div>'; // col-2
	}
	
	print '<div class="col-md-10 col-sm-10 col-xs-9">';
	
	//print '<div class="btn-group filterBtnGroup" role="group" class="largeFilterButtons" aria-label="Filter posts button group">';
	print '<div class="btn-group filterBtnGroup" role="group" aria-label="Filter posts button group">';
		
	//print '<div id="school_All" class="btn school_filter communityBtn">';
	print '<div id="school_All" class="btn school_filter filterBtn">';
		
	$activeClass = 'activeFilterPanel';
	$imageSrc = $this->allPostsImg;
	$activeImageSrc = $this->allPostsActiveImg;
	
	print '<div class="panel panel-default filterPanel toggleActive '.$activeClass.'" role="button">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12 text-center"><img src="'.$activeImageSrc.'" class="filterBadgesIcon" alt="all badges icon" data-icon="'.JURI::root().'/'.$imageSrc.'" data-activeicon="'.JURI::root().'/'.$activeImageSrc.'"/></div>';

	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_CLEAR_FILTERS").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	
	print '</div>'; // panel
	
	// print '<div class="h5 hidden-sm hidden-md hidden-lg">';
	// print JText::_("COM_BIODIV_SCHOOLCOMMUNITY_CLEAR_FILTERS");
	// print '</div>';
		
	print '</div>';
	
	
	
	//print '<div id="mySchool_'.$this->schoolUser->school_id.'" class="btn school_filter communityBtn">';
	print '<div id="mySchool_'.$this->schoolUser->school_id.'" class="btn school_filter filterBtn">';
		
	$imageSrc = $this->schoolUser->school_logo;
	
	print '<div class="panel panel-default filterPanel toggleActive " role="button">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="all badges icon" data-icon="'.JURI::root().'/'.$imageSrc.'" data-activeicon="'.JURI::root().'/'.$imageSrc.'"/></div>';

	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MY_SCHOOL").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	
	print '</div>'; // panel
	
	// print '<div class="h5 hidden-sm hidden-md hidden-lg">';
	// print JText::_("COM_BIODIV_SCHOOLCOMMUNITY_MY_SCHOOL");
	// print '</div>';
	
	print '</div>';
	
	
	//print '<div class="btn communityBtn openSearch">';
	print '<div class="btn filterBtn openSearch">';
		
	$imageSrc = $this->searchSchoolsImg;
	$activeImageSrc = $this->searchSchoolsActiveImg;
	
	print '<div href="#searchRow" class="panel panel-default filterPanel toggleActive " role="button" data-toggle="collapse">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12 text-center"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="search schools icon" data-icon="'.JURI::root().'/'.$imageSrc.'" data-activeicon="'.JURI::root().'/'.$activeImageSrc.'"/></div>';

	print '</div>'; // row
	 
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SEARCH_SCHOOLS").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	
	print '</div>'; // panel
	
	// print '<div class="h5 hidden-sm hidden-md hidden-lg vSpaced">';
	// print JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SEARCH_SCHOOLS");
	// print '</div>';
	
	print '</div>'; // communityBtn
	
		
	print '</div>'; // btnGroup
	
	print '</div>'; // col-10
	
	print '</div>'; // row
		
	
	print '<div id="searchRow" class="row collapse">';
	
	print '<div class="col-md-6 col-sm-6 col-xs-12">';
	
	// -------------------------- School search 
	
	print '<div class="form-group has-feedback schoolSearch">';
	print '  <div class="input-group">';
	print '    <span class="input-group-addon" style="background-color:#FFFFFF; border-bottom-left-radius:25px; border-top-left-radius:25px;"><span class="glyphicon glyphicon-search"></span></span>';
	print '    <input type="search" class="form-control" id="searchSchools" placeholder="'.
			JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SEARCH_SCHOOLS").
			'" style="border-left: 0px;border-bottom-right-radius:25px; border-top-right-radius:25px;">';
	print '  </div>';
	print '</div>	';
	// print '</div>'; // col-6
	
	// print '<div class="col-md-5 col-sm-4 col-xs-12">';
	print '<div class="list-group btn-group-vertical btn-block schoolList" role="group" aria-label="School Buttons" style="display:none;">';
	
	// Pinned resources and latest upload
	
	foreach($this->schools as $school){
		
		$schoolId = $school->schoolId;
		$schoolName = $school->schoolName;
				
		print '<button type="button" id="school_'.$schoolId.'" class="list-group-item btn btn-block school_btn" style="white-space: normal;">';
		
		print '<h5>'.$schoolName.'</h5>';
		
		print '</button>';
	}

	print '</div>'; // list-group
	
	print '</div>'; // col-3
	
	
	print '</div>'; // row
	
	print '<div id="postsArea"></div>';
	
	
	
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
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


print '<div id="newPostModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="postArea" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_CANCEL").'</button>';
print '      </div>';
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // newPostModal



print '<div id="deletePostModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';
print '     <h3>'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_SURE").'</h3>';
print '     <div class="row">';
print '     <div class="col-md-4 col-md-offset-2 col-sm-4 col-sm-offset-2 col-xs-6 text-center">';
print '     <button id="deleteNow" type="button" class="btn btn-default btn-lg vSpaced" data-set="">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_DELETE").'</button>';
print '     </div>'; // col-6
print '     <div class="col-md-4 col-sm-4 col-xs-6 text-center">';
print '     <button type="button" class="btn btn-default btn-lg vSpaced" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_CANCEL").'</button>';
print '     </div>'; // col-6
print '     </div>'; // row
//print '	    <div id="postArea" ></div>';
print '      </div>';
// print '	  <div class="modal-footer">';
// print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_CANCEL").'</button>';
// print '      </div>';
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // deletePostModal




print '<div id="badgeCompleteModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <h2>'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_BADGE_COMPLETE").'</h2>';
print '      </div>';
print '	  <div class="modal-footer">';
$href = "bes-badges";
if ( $this->classId > 0 ) {
	$href .= "?class_id=" . $this->classId;
}
print '        <a href="'.$href.'">';
print '        <button id="badgePage" type="button" class="btn btn-lg btn-primary" >'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_COLLECT").'</button>';
print '        </a>';
print '        <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLCOMMUNITY_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // badgeCompleteModal


JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/schoolcommunity.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);


//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js", true, true);




?>





