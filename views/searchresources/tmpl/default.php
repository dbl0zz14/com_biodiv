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
	//print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
	
}

else {
	
	print '<div class="row">';
	
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("resourcehub");
		
		print '</div>';
		
		 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateStudentMasthead ( 0, null, 0, 0, 0, true, true );
		
		print '</div>';
	}
	
	print '</div>'; // row
	
	print '<div class="row">';
				
	print '<div class="col-md-2 col-sm-4 col-xs-4">';

	print '<a href="'.$this->translations['hub_page']['translation_text'].'" class="btn btn-primary homeBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . $this->translations['resources_home']['translation_text'];
	print '</a>';
	
	print '</div>'; // col-1

	print '</div>'; // row
				
				
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.$this->translations['heading']['translation_text'].'</span> <small class="hidden-xs hidden-sm">'.$this->translations['subheading']['translation_text'].'</small> ';
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
	

	$currSetId = 0;
	
	// Filters here
	
	print '<div class="row largeFilterButtons">';
	
	print '<div class="col-md-12">';
	
	/*
	print '<div class="btn-group filterBtnGroup hidden-xs" role="group" aria-label="Resource type filter buttons">';
	
	foreach ( $this->resourceTypes as $type ) {
		
		$typeId = $type->type_id;
		$typeName = $type->name;
		$colorClass = $type->class_stem . 'Color';
		$image = $type->icon;
		
		
		print '<div id="filterButton_'.$typeId.'" class="btn filterBtn filterByType text-center" style="padding:0">';
		
		print '<div class="panel panel-default '. $colorClass .'">';
		print '<div class="panel-body filterPanelBody">';
		
		
		// -------------------------------------- reource type icon
		print '<div class="row">';
		
		$imageSrc = JURI::root().$image;
		
		print '<div class="col-md-12 '.$colorClass.'_text"><img src="'.$imageSrc.'" class="img-responsive" alt="resource type icon" width="60px"/></div>';

		print '</div>'; // row
		
		
		// --------------------------------------- resource type name
		print '<div class="row">';
		print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$typeName.'</div>';
		print '</div>'; // row
		
		
		
		print '</div>'; // panel-body
		
		print '</div>'; // panel
		
		print '</div>'; // filterButton
		
		//print '</div>'; // col-1
	}
	
	print '</div>'; // btnGroup
	
	
	print '<div class="btn-group filterBtnGroup hidden-xs" role="group" aria-label="More resource filter buttons">';
	
	// ---------------------------------- bookmarked
	$image = $this->bookmarkedImg;
	$colorClass = "";
	
	print '<div id="filter_Fav" class="btn filterBtn filterByGroup '. $colorClass . ' text-center" style="padding:0">';
	
	print '<div class="panel panel-default">';
	print '<div class="panel-body filterPanelBody">';
	
	print '<div class="row">';
	$imageSrc = JURI::root().$image;
	print '<div class="col-md-12"><img src="'.$imageSrc.'" class="img-responsive" alt="bookmarked resources icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$this->translations['bookmarked']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	
	// ---------------------------------- my uploads
	$image = $this->myUploadsImg;
	$colorClass = "";
	
	print '<div id="filter_Mine" class="btn filterBtn filterByGroup '. $colorClass . ' text-center" style="padding:0">';
	
	print '<div class="panel panel-default">';
	print '<div class="panel-body filterPanelBody">';
	
	print '<div class="row">';
	$imageSrc = JURI::root().$image;
	print '<div class="col-md-12"><img src="'.$imageSrc.'" class="img-responsive" alt="my uploads icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$this->translations['my_uploads']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	
	// ---------------------------------- recently added
	$image = $this->newResourcesImg;
	$colorClass = "";
	
	print '<div id="filter_New" class="btn filterBtn filterByGroup '. $colorClass . ' text-center" style="padding:0">';
	
	print '<div class="panel panel-default">';
	print '<div class="panel-body filterPanelBody">';
	
	print '<div class="row">';
	$imageSrc = JURI::root().$image;
	print '<div class="col-md-12"><img src="'.$imageSrc.'" class="img-responsive" alt="new resources icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$this->translations['recent']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	
	// ---------------------------------- featured
	$image = $this->featuredImg;
	$colorClass = "";
	
	print '<div id="filter_Pin" class="btn filterBtn filterByGroup '. $colorClass . ' text-center" style="padding:0">';
	
	print '<div class="panel panel-default">';
	print '<div class="panel-body filterPanelBody">';
	
	print '<div class="row">';
	$imageSrc = JURI::root().$image;
	print '<div class="col-md-12"><img src="'.$imageSrc.'" class="img-responsive" alt="featured resources icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$this->translations['featured']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	print '</div>'; // btnGroup
	
	*/
	
	print '<div class="btn-group" role="group" aria-label="More resource filter buttons">';
	
	// ---------------------------------- more filters
	$image = $this->moreFiltersImg;
	$colorClass = "";
	
	print '<div id="moreFilters" class="btn filterBtn '. $colorClass . ' text-center" style="padding:0" data-toggle="modal" data-target="#filterModal">';
	
	print '<div class="panel panel-default">';
	print '<div class="panel-body filterPanelBody">';
	
	print '<div class="row">';
	$imageSrc = JURI::root().$image;
	print '<div class="col-md-12"><img src="'.$imageSrc.'" class="img-responsive" alt="more filters icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$this->translations['more_filters']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	
	// ---------------------------------- clear filters
	$image = $this->clearFiltersImg;
	$colorClass = "";
	
	print '<div id="clearFilters" class="btn filterBtn'. $colorClass . ' text-center" style="padding:0">';
	
	print '<div class="panel panel-default">';
	print '<div class="panel-body filterPanelBody">';
	
	print '<div class="row">';
	$imageSrc = JURI::root().$image;
	print '<div class="col-md-12"><img src="'.$imageSrc.'" class="img-responsive" alt="featured resources icon" width="60px"/></div>';
	print '</div>'; // row
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12 text-center filterText">'.$this->translations['clear_filters']['translation_text'].'</div>';
	print '</div>'; // row
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // filterButton
	
	
	print '</div>'; // btnGroup
	
	
	print '</div>'; // col-12
	
	print '</div>'; //largeFilterButtons row
	
	
	
	
	// Resource files rows
		
	$i = 0;
	$numFiles = count($this->resourceFiles);
	//print '<div class="row">';
	foreach ( $this->resourceFiles as $resourceFile ) {
		
		if ( $i % 4 == 0 ) {
			print '<div class="row">';
		}
		
		print '<div class="col-md-3 col-sm-6 col-xs-12 resourceCardColumn">';
		
		print '<div class="resourceCardContainer">';
		
		$resourceId = $resourceFile["resource_id"];
		$setId = $resourceFile["set_id"];
		
		$resourceSetPage = $this->translations['set_page']['translation_text'];
		print '<a href="'.$resourceSetPage.'?set_id='.$setId.'">';
		
		
		$resourceFile = new Biodiv\ResourceFile ( $resourceId, 
												$resourceFile["resource_type"],
												$resourceFile["person_id"],
												$resourceFile["school_id"],
												$resourceFile["access_level"],
												$resourceFile["set_id"],
												$resourceFile["upload_filename"],
												$resourceFile["title"],
												$resourceFile["description"],
												$resourceFile["source"],
												$resourceFile["external_text"],
												$resourceFile["filetype"],
												$resourceFile["is_pin"],
												$resourceFile["is_fav"],
												$resourceFile["is_like"],
												$resourceFile["num_likes"],
												$resourceFile["num_in_set"],
												$resourceFile["s3_status"],
												$resourceFile["url"]);
		
		$resourceFile->printCard();
		
		print '</a>';
		print '</div>'; // resourceCardContainer
		print '</div>'; // col-3
		
		
		$i++;
		
		if ( ( $i % 4 == 0 )  or ( $i == $numFiles ) ) {
			print '</div>'; // row
		}
		
	}
	
	//print '</div>'; // row
	
	
	if ( $this->numPages > 1 ) {
		print '<div class="row">';
		print '<div class="col-md-10 col-md-offset-1">';
		print '<ul class="pagination pagination-lg">';
		for ( $i=1; $i <= $this->numPages; $i++ ) {
			$activeClass = "";
			if ( $this->page == $i ){
				$activeClass = "active";
			}
			if ( $this->noArgs ) {
				print '<li class="'.$activeClass.'"><a href="'.$this->href.'?page='.$i.'">'.$this->translations['page']['translation_text'].' '.$i.'</a></li>';
			}
			else {
				print '<li class="'.$activeClass.'"><a href="'.$this->href.'&page='.$i.'">'.$this->translations['page']['translation_text'].' '.$i.'</a></li>';
			}
		}
		print '</ul>';
		print '</div>';
		print '</div>';
	}
	
	/*
	if ( $this->numPages > 1 ) {
		print '<div class="text-center">';
		print '<div class="btn btn-primary btn-lg" role="button">'.$this->translations['more']['translation_text'].'</div>';
		print '</div>';
	}
	*/

}

print '<div id="filterModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog modal-lg"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';

print '     <div class="h3 spaced">'.$this->translations['select_interest']['translation_text'].'</div>';
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
	//print '<input type="checkbox" id="resourceType_'.$typeId.'" name="resourceType_'.$typeId.'" value="isType_'.$typeId.'">';
	print '<input type="checkbox" id="resourceType_'.$typeId.'" name="resourceType_'.$typeId.'" value="type_'.$typeId.'">';
	print '<label for="resourceType_'.$typeId.'" class="uploadLabel '.$colorClass.'">'.$typeName.'</label>';
	print '</div>';
}
print '</div>';

print '<div class="col-md-4">';
foreach ( $this->allTags as $groupId=>$tagGroup ) {
	foreach ( $tagGroup->tags as $tagId=>$tag ) {
		print '<div>';
		//print '<input type="checkbox" id="tag_'.$tag->tag_id.'" name="tag[]" value="isTag_'.$tag->tag_id.'">';
		print '<input type="checkbox" id="tag_'.$tag->tag_id.'" name="tag[]" value="tag_'.$tag->tag_id.'">';
		print '<label for="tag_'.$tag->tag_id.'" class="uploadLabel '.$tag->color_class.'">'.$tag->name.'</label>';
		print '</div>';
	}
}
print '</div>';

print '<div class="col-md-4">';
print '<div>';
print '<input type="checkbox" id="filter_fav" name="filter_fav" value="fav">';
print '<label for="filter_fav" class="uploadLabel ">'.$this->translations['bookmarked']['translation_text'].'</button>'.'</label>';
print '</div>';

print '<div>';
print '<input type="checkbox" id="filter_mine" name="filter_mine" value="mine">';
print '<label for="filter_mine" class="uploadLabel ">'.$this->translations['my_uploads']['translation_text'].'</button>'.'</label>';
print '</div>';

print '<div>';
print '<input type="checkbox" id="filter_pin" name="filter_pin" value="pin">';
print '<label for="filter_pin" class="uploadLabel ">'.$this->translations['featured']['translation_text'].'</button>'.'</label>';
print '</div>';

print '<div>';
print '<input type="checkbox" id="filter_new" name="filter_new" value="new">';
print '<label for="filter_new" class="uploadLabel ">'.$this->translations['recent']['translation_text'].'</button>'.'</label>';
print '</div>';


print '</div>';

print '</div>'; // row

//print '<button id="applyFiltersAll" type="button" class="btn btn-primary spaced" data-dismiss="modal">'.$this->translations['apply_all']['translation_text'].'</button>';

print '<button id="applyFiltersSearch" type="button" class="btn btn-primary spaced" data-dismiss="modal">'.$this->translations['apply_any']['translation_text'].'</button>';

//print '<button id="applyFiltersAny" type="button" class="btn btn-primary spaced" data-dismiss="modal">'.$this->translations['apply_any']['translation_text'].'</button>';

 
print '<button id="hide_filterMenu" type="button" class="btn btn-default spaced" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';

print '</div>'; //filterMenu


print '     </div>';
print '      </div>';
/*
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
print '      </div>';
*/
print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // filterModal



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/pdfjs/pdf.js", true, true);
// JHTML::script("com_biodiv/pdfjs/pdf.worker.js", true, true);

JHTML::script("com_biodiv/searchresources.js", true, true);




?>