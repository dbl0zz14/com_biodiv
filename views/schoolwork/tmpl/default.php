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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLWORK_LOGIN").'</div>';
	
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLWORK_NO_ACCESS").'</h2>';
	
}
else {
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "teacherzone");
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_schoolwork" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
	print '<a href="'.$this->educatorPage.'" class="btn btn-success homeBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_SCHOOLWORK_EDUCATOR_ZONE");
	print '</a>';
	
				
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLWORK_HEADING").'</span> <small class="hidden-xs hidden-sm">'.JText::_("COM_BIODIV_SCHOOLWORK_SUBHEADING").'</small> ';
	print '</div>'; // col-12
	print '</div>'; // row
	print '</h2>'; 
	
	
	$currSetId = 0;
	
	// Filters here
	$searchPage = JText::_("COM_BIODIV_SCHOOLWORK_SEARCH_PAGE");
	
	print '<div class="row largeFilterButtons">';
	
	print '<div class="col-md-10 col-sm-10 col-xs-9">';
	
	
	print '<div class="btn-group" role="group" aria-label="Show all work buttons">';
	
	// ---------------------------------- clear filters
	// $image = $this->showAllImg;
	// $colorClass = "";
	// $activeClass = "";
	// if ( $this->activeBtn == "all" ) {
		// $activeClass = "activeFilterPanel";
	// }
	$image = $this->showAllImg;
	$activeImage = $this->showAllActiveImg;
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
	print '<div id="showAllWork" class="btn filterBtn text-center showAllWork" style="padding:0">';
	
	print '<div class="panel panel-default filterPanel '. $activeClass .'">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	//$imageSrc = JURI::root().$image;
	//print '<div class="col-md-12"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="clear filters icon" width="60px"/></div>';
	print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="clear filters icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SCHOOLWORK_ALL").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterBtn
	
	print '</div>'; // btnGroup
	
	
	print '<div class="btn-group" role="group" aria-label="Open search button">';
	
	// ---------------------------------- show the text search row
	// $image = $this->textSearchImg;
	// $colorClass = "";
	// $activeClass = "";
	// if ( $this->activeBtn == "search" ) {
		// $activeClass = "activeFilterPanel";
	// }
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
	//$imageSrc = JURI::root().$image;
	//print '<div class="col-md-12"><img src="'.$imageSrc.'" class="filterBadgesIcon" alt="text search icon" width="60px"/></div>';
	print '<div class="col-md-12"><img src="'.$currImageSrc.'" class="filterBadgesIcon" alt="search icon" data-icon="'.$imageSrc.'" data-activeicon="'.$activeImageSrc.'"/></div>';
	print '</div>'; // row
	
	print '<div class="row hidden-xs">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.JText::_("COM_BIODIV_SCHOOLWORK_SEARCH").'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterBtn
	
	
	print '</div>'; // btnGroup
	
	print '</div>'; // col-12
	
	
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
		print '    <input type="search" name="search" class="form-control" id="searchWork" value="'.$this->searchStr.'" >';
		print '<button id="clearWorkSearch" class="btn btn-default clearFilters showAllWork" type="button" title="Empty search"><span class="glyphicon glyphicon-remove" ></span></button>';
	
	}
	else {
		//print '    <input type="search" name="search" class="form-control" id="searchWork" placeholder="'.JText::_("COM_BIODIV_SCHOOLWORK_SEARCH").'" style="border-right: 0px;border-bottom-left-radius:25px; border-top-left-radius:25px;">';
		print '    <input type="search" name="search" class="form-control" id="searchWork" placeholder="'.JText::_("COM_BIODIV_SCHOOLWORK_SEARCH").'" >';
	}
	
	print '<button class="btn btn-default searchResourcesBtn" type="submit" title="Submit search"><span class="glyphicon glyphicon-search" ></span></button>';
	
	
	print '</form>';
	
	print '<form class="vSpaced hidden-lg hidden-sm" action="'.$searchPage.'" method = "GET">';
	
	print '  <div class="input-group">';
	print '    <span class="input-group-addon" style="background-color:#FFFFFF; border-bottom-left-radius:25px; border-top-left-radius:25px;"><span class="glyphicon glyphicon-search"></span></span>';
	print '    <input type="search" name="search" class="form-control" id="searchResourcesSmall" placeholder="'.JText::_("COM_BIODIV_SCHOOLWORK_SEARCH").'" style="border-top-right-radius:25px;border-bottom-right-radius:25px;">';
	print '</div>'; // input-group
		
	print '</form>'; 

	print '</div>'; // searchRes
	
	print '</div>'; // col-8
	
	print '</div>'; // searchRow 
	
	print '<div class="row">';
	
	foreach ( $this->workSets as $workSet ) {
		
		print '<div id="setCol_'.$workSet->set_id.'" class="col-lg-3 col-md-4 col-sm-6 col-xs-12 setCol ">';
		
		$avatarImg = $workSet->image;
		$name = $workSet->name;
		$set = new Biodiv\ResourceSet ($workSet->person_id, 
								$workSet->set_id,
								$workSet->resource_type,
								$workSet->set_name,
								$workSet->description,
								$workSet->school_id,
								null,
								$workSet->tstamp,
								$workSet->num_in_set,
								null,
								null,
								null,
								null,
								null,
								$workSet->resources,
								null
								);
								
		$set->printWork( $this->schoolUser, $avatarImg, $name );
		
		print '</div>';
		
	}
	
	print '</div>'; // workSets row
	
	print '<div class="row">';
	
	print '<div class="col-md-12">';
	
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
			print '<li class="'.$activeClass.'"><button id="page_'.$i.'" class="btn btn-lg '.$btnClass.' newPage" >'.JText::_("COM_BIODIV_SCHOOLWORK_PAGE").' '.$i.'</button></li>';
			
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
	
	print '</div>'; // col-12
	
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

JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schoolwork.js", true, true);
JHTML::script("com_biodiv/pdfjs/pdf.js", true, true);


?>