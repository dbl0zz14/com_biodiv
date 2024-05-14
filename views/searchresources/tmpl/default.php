<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SEARCHRESOURCES_LOGIN").'</div>';
	
}
else if ( !$this->schoolUser or $this->schoolUser->role_id == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
	print '<h2>'.JText::_("COM_BIODIV_SEARCHRESOURCES_NO_ACCESS").'</h2>';
	
}
else {
	
	if ( $this->help ) {
		Biodiv\Help::printResourceHubHelp( $this->schoolUser );
	}
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 

	Biodiv\SchoolCommunity::generateNav( $this->schoolUser, null, "teacherzone");
	
	print '</div> <!-- end col-12 -->';
	
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_resourcehub" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
	print '<a href="'.$this->educatorPage.'" class="btn btn-success homeBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_SEARCHRESOURCES_EDUCATOR_ZONE");
	print '</a>';
	
				
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_SEARCHRESOURCES_HEADING").'</span> <small class="hidden-xs hidden-sm">'.JText::_("COM_BIODIV_SEARCHRESOURCES_SUBHEADING").'</small> ';
	print '</div>'; // col-12
	print '</div>'; // row
	print '</h2>'; 
	

	$currSetId = 0;
	
	// Filters here
	$searchPage = JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_PAGE");
	
	print '<div class="row largeFilterButtons">';
	
	print '<div class="col-md-2 col-sm-2 col-xs-2">';
	print '<div class="newPost">';
	//print '<div class="panel panel-default actionPanel resourceUpload" role="button" data-toggle="modal" data-target="#uploadModal">';
	print '<div class="panel panel-default resourceUpload" role="button" data-toggle="modal" data-target="#uploadModal">';
	print '<div class="panel-body">';
	
	// print '<div class="h4 panelHeading hidden-xs">';
	// print JText::_("COM_BIODIV_SEARCHRESOURCES_NEW");
	// print '</div>';
	//print '<div class="text-center"><i class="fa fa-3x fa-plus newPostIcon"></i></div>';

	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center h4">';
	print '<div class="newText">'.JText::_("COM_BIODIV_SEARCHRESOURCES_NEW").'</div>';
	print '</div>';
	print '</div>'; // row

	print '<div class="row">';
	print '<div class="text-center"><i class="fa fa-2x fa-plus newPostIcon"></i></div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</div>'; // newPost
	
	print '</div>'; // col-2
	
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	
	
	print '<div class="btn-group filterBtnGroup" role="group" aria-label="More resource filter buttons">';
	
	// ---------------------------------- clear filters
	$image = $this->clearFiltersImg;
	$activeImage = $this->clearFiltersActiveImg;
	$colorClass = "";
	$activeClass = "";
	$imageSrc = JURI::root().$image;
	$activeImageSrc = JURI::root().$activeImage;
	if ( $this->activeBtn == "all" ) {
		$activeClass = "activeFilterPanel";
		$currImageSrc = JURI::root().$activeImage;
	}
	else {
		$currImageSrc = JURI::root().$image;
	}
	print '<div id="clearFilters" class="btn filterBtn clearFilters'. $colorClass . ' text-center">';
	
	print '<div class="panel panel-default filterPanel '. $activeClass .'">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	//$imageSrc = JURI::root().$imageSrc;
	//print '<div class="col-md-12"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="clear filters icon" width="60px" data-icon="'.JURI::root().'/'.$imageSrc.'" data-activeicon="'.JURI::root().'/'.$activeImageSrc.'"/></div>';
	print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="clear filters icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SEARCHRESOURCES_CLEAR_FILTERS").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterBtn
	
	
	//print '</div>'; // btnGroup
	
	
	
	//print '<div class="btn-group filterBtnGroup" role="group" aria-label="Featured resources">';
	
	// ---------------------------------- featured resources
	$image = $this->featuredImg;
	$activeImage = $this->featuredActiveImg;
	$colorClass = "";
	$activeClass = "";
	$imageSrc = JURI::root().$image;
	$activeImageSrc = JURI::root().$activeImage;
	if ( $this->activeBtn == "featured" ) {
		$activeClass = "activeFilterPanel";
		$currImageSrc = JURI::root().$activeImage;
	}
	else {
		$currImageSrc = JURI::root().$image;
	}
	
	print '<div id="showFeatured" class="btn filterBtn showFeatured'. $colorClass . ' text-center">';
	
	print '<div class="panel panel-default filterPanel '. $activeClass .'">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	//$imageSrc = JURI::root().$image;
	//print '<div class="col-md-12"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="featured resources icon" width="60px"/></div>';
	print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="featured resources icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SEARCHRESOURCES_FEATURED").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterBtn
	
	
	//print '</div>'; // btnGroup
	
		
	
	//print '<div class="btn-group filterBtnGroup" role="group" aria-label="More resource filter buttons">';
	
	// ---------------------------------- more filters
	$image = $this->moreFiltersImg;
	$activeImage = $this->moreFiltersActiveImg;
	$colorClass = "";
	$activeClass = "";
	$imageSrc = JURI::root().$image;
	$activeImageSrc = JURI::root().$activeImage;
	if ( $this->activeBtn == "filter" ) {
		$activeClass = "activeFilterPanel";
		$currImageSrc = JURI::root().$activeImage;
	}
	else {
		$currImageSrc = JURI::root().$image;
	}
	
	print '<div id="moreFilters" class="btn filterBtn  '. $colorClass . ' text-center" data-toggle="modal" data-target="#filterModal">';
	
	print '<div class="panel panel-default filterPanel '. $activeClass .'">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="quick search icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	//print '<div class="col-md-12"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="quick search icon" /></div>';
	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SEARCHRESOURCES_MORE_FILTERS").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	
	//print '</div>'; // btnGroup
	
	
	
	
	//print '</div>'; // col-7
	
	
	//print '<div class="col-md-3">';
	
	
	//print '<div class="btn-group filterBtnGroup" role="group" aria-label="Open search button">';
	
	// ---------------------------------- show the text search row
	$image = $this->textSearchImg;
	$activeImage = $this->textSearchActiveImg;
	$colorClass = "";
	$activeClass = "";
	$imageSrc = JURI::root().$image;
	$activeImageSrc = JURI::root().$activeImage;
	if ( $this->activeBtn == "search" ) {
		$activeClass = "activeFilterPanel";
		$currImageSrc = JURI::root().$activeImage;
	}
	else {
		$currImageSrc = JURI::root().$image;
	}
	
	print '<div href="#searchRow" class="btn filterBtn'. $colorClass . ' text-center" data-toggle="collapse" style="padding:0">';
	
	print '<div class="panel panel-default filterPanel '. $activeClass .'">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="quick search icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	//print '<div class="col-md-12"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="text search icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterBtn
	
	
	print '</div>'; // btnGroup
	
	print '</div>'; // col-9
	
	
	print '</div>'; //largeFilterButtons row
	
	
	if ( $this->searchStr ) {
		print '<div id="searchRow" class="row collapse in">';
	}
	else {
		print '<div id="searchRow" class="row collapse">';
	}
	
	print '<div class="col-md-10 col-sm-10 col-xs-12">';

	print '<div class="searchRes">';

	//$searchPage = JURI::root() . "/" . JText::_("COM_BIODIV_RESOURCEHUB_SEARCH_PAGE");

	print '<form class="form-inline hidden-xs" action="'.$searchPage.'" method = "GET">';
	
	if ( $this->searchStr ) {
		print '    <input type="search" name="search" class="form-control" id="searchResources" value="'.$this->searchStr.'" style="border-right: 0px;border-bottom-left-radius:25px; border-top-left-radius:25px;">';
		print '<button class="btn btn-default clearFilters emptySearchBtn" type="button" ><span class="glyphicon glyphicon-remove" ></span></button>';
	
	}
	else {
		print '    <input type="search" name="search" class="form-control" id="searchResources" placeholder="'.JText::_("COM_BIODIV_SEARCHRESOURCES_SEARCH_RESOURCES").'" style="border-right: 0px;border-bottom-left-radius:25px; border-top-left-radius:25px;">';
	}
	
	print '<button class="btn btn-default searchResourcesBtn" type="submit" ><span class="glyphicon glyphicon-search" ></span></button>';
	
	
	print '</form>';
	
	print '<form class="vSpaced hidden-lg hidden-sm" action="'.$searchPage.'" method = "GET">';
	
	print '  <div class="input-group">';
	print '    <span class="input-group-addon" style="background-color:#FFFFFF; border-bottom-left-radius:25px; border-top-left-radius:25px;"><span class="glyphicon glyphicon-search"></span></span>';
	print '    <input type="search" name="search" class="form-control" id="searchResourcesSmall" placeholder="Search..." style="border-top-right-radius:25px;border-bottom-right-radius:25px;">';
	print '</div>'; // input-group
		
	print '</form>'; 

	print '</div>'; // searchRes
	
	print '</div>'; // col-8
	
	
	print '</div>'; // searchRow 
	
	
	// if ( $this->displayPinned ) {
		
		// // print '<div class="row">';
		
		// // print '<div class="col-md-12">';
		// // print '<div class="panel panel-default actionPanel" role="button" >';
		// // print '<div class="panel-body">';
		
		// print '<div class="h4 panelHeading">';
		// print JText::_("COM_BIODIV_SEARCHRESOURCES_SUGGESTED");
		// print '</div>';
		
		// print '<div class="row">';
		
		// $currPin = 1;
		// $maxPinned = 3;
		// foreach ( $this->pinnedResources as $pinnedSet ) {
			
			// print '<div id="setCol_'.$pinnedSet->set_id.'" class="col-lg-3 col-md-4 col-sm-6 col-xs-12 setCol ">';
			
			// $set = new Biodiv\ResourceSet ($pinnedSet->person_id, 
								// $pinnedSet->set_id,
								// $pinnedSet->resource_type,
								// $pinnedSet->set_name,
								// null,
								// $pinnedSet->school_id,
								// null,
								// $pinnedSet->tstamp,
								// $pinnedSet->num_in_set,
								// $pinnedSet->num_likes,
								// $pinnedSet->my_like,
								// $pinnedSet->my_fav,
								// $pinnedSet->tags,
								// $pinnedSet->resources
								// );
								
			// $set->printCard();
			
			// print '</div>';
			
			// $currPin += 1;
			// if ( $currPin > $maxPinned ) {
				
				// print '<a href="'.JText::_("COM_BIODIV_SEARCHRESOURCES_PAGE").'?filter=[\"pin\"]" class="btn btn-primary" >';
				// print JText::_("COM_BIODIV_SEARCHRESOURCES_MORE_PINS");
				// print '</a>';
				
				// break;
			// }

		// }
		
		// print '</div>'; // row
		// // print '</div>'; // panel-body
		// // print '</div>'; // panel
		
		// // print '</div>'; // col-12
		// // print '</div>'; // row
	// }
	
	print '<div class="row">';
	
	// if ( $this->displayPinned ) {
		// print '<div class="h4 panelHeading">';
		// print JText::_("COM_BIODIV_SEARCHRESOURCES_ALL");
		// print '</div>';
	// }
	
	foreach ( $this->resourceSets as $resourceSet ) {
		
		print '<div id="setCol_'.$resourceSet->set_id.'" class="col-lg-3 col-md-4 col-sm-6 col-xs-12 setCol ">';
		
		$set = new Biodiv\ResourceSet ($resourceSet->person_id, 
								$resourceSet->set_id,
								$resourceSet->resource_type,
								$resourceSet->set_name,
								null,
								$resourceSet->school_id,
								null,
								$resourceSet->tstamp,
								$resourceSet->num_in_set,
								$resourceSet->num_likes,
								$resourceSet->my_like,
								$resourceSet->my_fav,
								$resourceSet->is_pin,
								$resourceSet->tags,
								$resourceSet->resources,
								$resourceSet->badges
								);
								
		$set->printCard();
		
		print '</div>';
		
	}
	
	print '</div>'; // row
	
	
	
	
	
	if ( $this->numPages > 1 ) {
		
		$numPerPage = Biodiv\ResourceSet::NUM_PER_PAGE;
		$firstPageButton = intval(($this->page - 1)/$this->pageLabelsShown) * $this->pageLabelsShown + 1;
		$lastPageButton = min($this->numPages, $firstPageButton + $this->pageLabelsShown - 1);
		
		
		print '<div class="row">';
		print '<div class="col-md-12">';
		print '<ul class="pagination pagination-lg">';
		if ( $firstPageButton > 1 ) {
			$i = $firstPageButton - 1;
			print '<li><button id="page_'.$i.'" class="btn btn-lg btn-default newPage" ><i class="fa fa-backward"></i></button></li>';
		}
		for ( $i=$firstPageButton; $i <= $lastPageButton; $i++ ) {
			$btnClass = "btn-default";
			$activeClass = "";
			if ( $this->page == $i ){
				$btnClass = "btn-primary";
				$activeClass = "active";
			}
			print '<li class="'.$activeClass.'"><button id="page_'.$i.'" class="btn btn-lg '.$btnClass.' newPage" >'.JText::_("COM_BIODIV_SEARCHRESOURCES_PAGE").' '.$i.'</button></li>';
			
		}
		if ( $this->numPages > $lastPageButton ) {
			$i = $lastPageButton + 1;
			print '<li><button id="page_'.$i.'" class="btn btn-lg btn-default newPage" ><i class="fa fa-forward"></i></button></li>';
		}
		
		print '</ul>';
		print '</div>';
		print '</div>';
	}
	
	print '</div>'; // col-12
	print '</div>'; // row

}


// ----------------------- Help modal

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


// --------------------------- Upload modal 

print '<div id="uploadModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="uploadArea" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_SEARCHRESOURCES_CANCEL").'</button>';
print '      </div>';
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // uploadModal



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
print '        <a href="'.$this->badgeSchemeLink.'"><button type="button" class="btn btn-primary">'.JText::_("COM_BIODIV_SEARCHRESOURCES_VIEW_SCHEME").'</button></a>';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SEARCHRESOURCES_CLOSE").'</button>';
print '      </div>';	  	  
print '    </div>';

print '  </div>';
print '</div>';


// -------------------------- Filter modal
print '<div id="filterModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog modal-lg"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';

print '     <div class="h3 spaced">'.JText::_("COM_BIODIV_SEARCHRESOURCES_SELECT_INTEREST").'</div>';
print '     <div class="h4 spaced">'.JText::_("COM_BIODIV_SEARCHRESOURCES_MATCHING_ALL").'</div>';
print '	    <div id="filterArea" >';


// ------------------------------- Filter pop up menu
	
print '<div id="filterMenu" >';
	
print '<div class="row">';

print '<div class="col-md-4">';
foreach ( $this->resourceTypes as $type ) {
	$typeId = $type->type_id;
	$typeName = $type->name;
	$colorClass = $type->class_stem . 'Color';
	print '<div>';
	$checked = "";
	if ( $this->filter && in_array ( "type_".$typeId, $this->filter )  ) {
		$checked = 'checked';
	}
	print '<input type="checkbox" id="resourceType_'.$typeId.'" name="resourceType_'.$typeId.'" value="type_'.$typeId.'" '.$checked.' class="resourceTypeCheckbox">';
	print '<label for="resourceType_'.$typeId.'" class="uploadLabel '.$colorClass.'">'.$typeName.'</label>';
	print '</div>';
}
print '</div>'; // col-4

print '<div class="col-md-4">';
foreach ( $this->allTags as $groupId=>$tagGroup ) {
	foreach ( $tagGroup->tags as $tagId=>$tag ) {
		print '<div>';
		$checked = "";
		if ( $this->filter && in_array ( "tag_".$tag->tag_id, $this->filter )  ) {
			$checked = 'checked';
		}
		print '<input type="checkbox" id="tag_'.$tag->tag_id.'" name="tag[]" value="tag_'.$tag->tag_id.'" '.$checked.'>';
		print '<label for="tag_'.$tag->tag_id.'" class="uploadLabel '.$tag->color_class.'">'.$tag->name.'</label>';
		print '</div>';
	}
}
print '</div>'; // col-4

print '<div class="col-md-4">';
print '<div>';
$checked = "";
if ( $this->filter && in_array ( "fav", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_fav" name="filter_fav" value="fav" '.$checked.'>';
print '<label for="filter_fav" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_BOOKMARKED").'</button>'.'</label>';
print '</div>';

print '<div>';
$checked = "";
if ( $this->filter && in_array ( "like", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_like" name="filter_like" value="like" '.$checked.'>';
print '<label for="filter_like" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_LIKES").'</button>'.'</label>';
print '</div>';

print '<div>';
$checked = "";
if ( $this->filter && in_array ( "badges", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_badges" name="filter_badges" value="badges" '.$checked.'>';
print '<label for="filter_badges" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_BADGES").'</button>'.'</label>';
print '</div>';

print '<div>';
$checked = "";
if ( $this->filter && in_array ( "mine", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_mine" name="filter_mine" value="mine" '.$checked.'>';
print '<label for="filter_mine" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_MY_UPLOADS").'</button>'.'</label>';
print '</div>';

print '<div>';
$checked = "";
if ( $this->filter && in_array ( "pin", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_pin" name="filter_pin" value="pin" '.$checked.'>';
print '<label for="filter_pin" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_FEATURED").'</button>'.'</label>';
print '</div>';

print '<div>';
$checked = "";
if ( $this->filter && in_array ( "new", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_new" name="filter_new" value="new" '.$checked.'>';
print '<label for="filter_new" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_RECENT").'</button>'.'</label>';
print '</div>';

print '<div>';
$checked = "";
if ( $this->filter && in_array ( "community", $this->filter )  ) {
	$checked = 'checked';
}
print '<input type="checkbox" id="filter_community" name="filter_community" value="community" '.$checked.'>';
print '<label for="filter_community" class="uploadLabel ">'.JText::_("COM_BIODIV_SEARCHRESOURCES_COMMUNITY").'</button>'.'</label>';
print '</div>';



print '</div>'; // col-4

print '</div>'; // row


print '<button id="applyFiltersSearch" type="button" class="btn btn-primary spaced" data-dismiss="modal">'.JText::_("COM_BIODIV_SEARCHRESOURCES_APPLY_ANY").'</button>';


print '<button id="hide_filterMenu" type="button" class="btn btn-default spaced" data-dismiss="modal">'.JText::_("COM_BIODIV_SEARCHRESOURCES_CANCEL").'</button>';

print '</div>'; //filterMenu


print '     </div>';
print '      </div>';


print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // filterModal


JHtml::_('script', 'com_biodiv/commonbiodiv.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/commondashboard.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/resourceupload.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/resourcelist.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/pdfjs/pdf.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'jquery-upload-file/jquery.uploadfile.min.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/pdfjs/pdf.worker.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/searchresources.js', array('version' => 'auto', 'relative' => true), array());
if ( $this->help ) {
	JHtml::_('script', 'com_biodiv/help.js', array('version' => 'auto', 'relative' => true), array());
}


?>