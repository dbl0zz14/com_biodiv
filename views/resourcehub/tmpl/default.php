<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	//print '<a type="button" href="'.JURI::root().'/'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
}
else if ( $this->mySchoolRole == 0 or $this->mySchoolRole == Biodiv\SchoolCommunity::STUDENT_ROLE ) {
	print '<h2>'.$this->translations['no_access']['translation_text'].'</h2>';
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['student_link']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['student_dash']['translation_text'].'</a>';
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav("resourcehub");
	
	print '</div>';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	
	// --------------------- Main content
	
	
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
	
	
	print '<div class="row fullPageHeight">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	
	
	// ---------------------------- Filter buttons, search and new upload
	

	
	
	
	print '<div class="row searchRow">';
	
	print '<div class="col-md-10 col-sm-10 col-xs-12">';

	print '<div class="searchRes">';

	$searchPage = JURI::root() . "/" . $this->translations['search_page']['translation_text'];

	print '<form class="form-inline hidden-xs" action="'.$searchPage.'" method = "GET">';
	
	print '    <input type="search" name="search" class="form-control" id="searchResources" placeholder="Search..." style="border-right: 0px;border-bottom-left-radius:25px; border-top-left-radius:25px;">';
	
	print '<button class="btn btn-info searchResourcesBtn" type="submit" ><span class="glyphicon glyphicon-search" ></span></button>';
	
	
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
	
	print '<div class="row findRow">';
	
	print '<div class="col-md-12">';
	
	// ------------------------ Various quick find options plus featured resources

	print '<div class="findResourcesGrid">';
	
	
	
	print '<div class="findByGroup">';
	
	print '<div class="panel panel-default findByGroupPanel">';
	print '<div class="panel-body">';
	
	//print '<button type="button" class="btn btn-primary resourceUpload" data-toggle="modal" data-target="#uploadModal" >'.$this->translations['upload']['translation_text'].'</button>';
	
	print '<div class="findByGroupGrid">';
	
	print '<div class="uploadNew">';
	//print '<a href="'.$searchPage.'?fav=1">';
	print '<div class="panel panel-default actionPanel resourceUpload" role="button" data-toggle="modal" data-target="#uploadModal">';
	print '<div class="panel-body">';
	
	print '<div class="hidden-sm hidden-md hidden-lg hidden-xl">';
	print '<div class="row small-gutter">';
	print '<div class="col-xs-3">';
	print '<i class="fa fa-files-o fa-2x"></i>';
	print '</div>'; // col-3
	print '<div class="col-xs-9">';
	print '<div class="h4 panelHeading">';
	print $this->translations['upload']['translation_text'];
	print '</div>';
	print '</div>'; // col-9
	print '</div>'; // row
	print '</div>'; // hidden-md etc
	
	print '<div class="hidden-xs">';
	print '<div class="h5 panelHeading">';
	print $this->translations['upload']['translation_text'];
	print '</div>';
	print '<div class="findByGroupIcon text-center hidden-xs"><i class="fa fa-files-o fa-2x"></i></div>';
	print '</div>';
	
	
	
	// print '<div class="h5 panelHeading">'.'<i class="fa fa-files-o fa-2x hidden-sm hidden-md hidden-lg hidden-xl"></i> '.$this->translations['upload']['translation_text'].'</div>';
	// print '<div class="findByGroupIcon text-center hidden-xs"><i class="fa fa-files-o fa-2x"></i></div>';
	
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	//print '</a>';
	print '</div>';
	
	print '<div class="findBookmarked">';
	print '<a href="'.$searchPage.'?fav=1">';
	print '<div class="panel panel-default actionPanel defaultColor">';
	print '<div class="panel-body">';
	
	print '<div class="hidden-sm hidden-md hidden-lg hidden-xl">';
	print '<div class="row small-gutter">';
	print '<div class="col-xs-3">';
	print '<img src="'.$this->bookmarkedImg.'"  class="img-responsive" alt="Find bookmarked icon" />';
	print '</div>'; // col-3
	print '<div class="col-xs-9">';
	print '<div class="h4 panelHeading">';
	print $this->translations['find_bookmarked']['translation_text'];
	print '</div>';
	print '</div>'; // col-9
	print '</div>'; // row
	print '</div>'; // hidden-md etc
	
	print '<div class="hidden-xs">';
	print '<div class="h5 panelHeading">';
	print $this->translations['find_bookmarked']['translation_text'];
	print '</div>';
	print '<div class="findByGroupIcon text-center hidden-xs"><img src="'.$this->bookmarkedImg.'"  class="img-responsive" alt="Find bookmarked icon" /></div>';
	print '</div>';
	
	// print '<div class="h5 panelHeading">';
	// print '<div class="findByGroupIconSmall hidden-sm hidden-md hidden-lg hidden-xl"><img src="'.$this->bookmarkedImg.'"  class="img-responsive" alt="Find bookmarked icon" /></div> ';
	// print $this->translations['find_bookmarked']['translation_text'];
	// print '</div>';
	// print '<div class="findByGroupIcon text-center hidden-xs"><img src="'.$this->bookmarkedImg.'"  class="img-responsive" alt="Find bookmarked icon" /></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	print '</div>';
	
	print '<div class="findOwnResources">';
	print '<a href="'.$searchPage.'?mine=1">';
	print '<div class="panel panel-default actionPanel defaultColor">';
	print '<div class="panel-body">';
	
	print '<div class="hidden-sm hidden-md hidden-lg hidden-xl">';
	print '<div class="row small-gutter">';
	print '<div class="col-xs-3">';
	print '<img src="'.$this->myUploadsImg.'"  class="img-responsive" alt="Find my uploads icon" />';
	print '</div>'; // col-3
	print '<div class="col-xs-9">';
	print '<div class="h4 panelHeading">';
	print $this->translations['find_my_uploads']['translation_text'];
	print '</div>';
	print '</div>'; // col-9
	print '</div>'; // row
	print '</div>'; // hidden-md etc
	
	print '<div class="hidden-xs">';
	print '<div class="h5 panelHeading">';
	print $this->translations['find_my_uploads']['translation_text'];
	print '</div>';
	print '<div class="findByGroupIcon text-center hidden-xs"><img src="'.$this->myUploadsImg.'"  class="img-responsive" alt="Find bookmarked icon" /></div>';
	print '</div>';
	
	
	// print '<div class="h5 panelHeading">'.$this->translations['find_my_uploads']['translation_text'].'</div>';
	// print '<div class="findByGroupIcon text-center hidden-xs"><img src="'.$this->myUploadsImg.'"  class="img-responsive" alt="Find my uploads icon" /></div>';
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	print '</div>';
	
	print '<div class="findNewResources">';
	print '<a href="'.$searchPage.'?new=1">';
	print '<div class="panel panel-default actionPanel defaultColor">';
	print '<div class="panel-body">';
	
	print '<div class="hidden-sm hidden-md hidden-lg hidden-xl">';
	print '<div class="row small-gutter">';
	print '<div class="col-xs-3">';
	print '<img src="'.$this->newImg.'"  class="img-responsive" alt="Find new resources icon" />';
	print '</div>'; // col-3
	print '<div class="col-xs-9">';
	print '<div class="h4 panelHeading">';
	print $this->translations['find_new']['translation_text'];
	print '</div>';
	print '</div>'; // col-9
	print '</div>'; // row
	print '</div>'; // hidden-md etc
	
	print '<div class="hidden-xs">';
	print '<div class="h5 panelHeading">';
	print $this->translations['find_new']['translation_text'];
	print '</div>';
	print '<div class="findByGroupIcon text-center hidden-xs"><img src="'.$this->newImg.'"  class="img-responsive" alt="Find bookmarked icon" /></div>';
	print '</div>';
	
	// print '<div class="h5 panelHeading">'.$this->translations['find_new']['translation_text'].'</div>';
	// print '<div class="findByGroupIcon text-center hidden-xs"><img src="'.$this->newImg.'"  class="img-responsive" alt="Find new resources icon" /></div>';
	
	
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</a>';
	print '</div>';
	
	print '</div>'; // findByGroupGrid
	
	print '</div>'; // panel-body
	
	print '</div>'; // findByGroupPanel
	
	print '</div>'; // findByGroup
	
	
	print '<div class="findByType">';
	
	print '<div class="panel panel-default findByTypePanel">';
	print '<div class="panel-body">';
	
	//print '<div class="h4 panelHeading">'.$this->translations['find_by_type']['translation_text'].'</div>';
	
	print '<div class="findByTypeGrid">';
	
	foreach ( $this->resourceTypes as $type ) {
		print '<div class="'.$type->class_stem.'">';
		print '<a href="'.$searchPage.'?type='.$type->type_id.'">';
		//print '<div id="'.$type->class_stem.'Panel" class="panel panel-default actionPanel ">';
		print '<div class="panel panel-default actionPanel '.$type->class_stem.'Color">';
		print '<div class="panel-body">';
		print '<div class="h3 panelHeading"><h3>'.$type->name.'</h3></div>';
		print '<div class="text-center"><img src="'.$type->icon.'"  class="img-responsive '.$type->class_stem.'Img" alt="Find '.strtolower($type->name).' icon" /></div>';
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</a>';
		print '</div>';
	}
	
	print '</div>'; // findByTypeGrid
	
	print '</div>'; // panel-body
	
	print '</div>'; // findByTypePanel
	
	print '</div>'; // findByType



	print '<div class="featuredResources">';
	print '<div class="panel panel-default findByGroupPanel">';
	print '<div class="panel-body">';
	
	print '<div class="h4 panelHeading">'.$this->translations['featured']['translation_text'].'</div>';
	
	$maxResources = 2;
	$featuredCount = 0;
	foreach ( $this->featured as $resourceData ) {
		if ( $featuredCount < $maxResources ) {
			$resource = new Biodiv\ResourceFile ( $resourceData["resource_id"], 
												$resourceData["resource_type"],
												$resourceData["person_id"],
												$resourceData["school_id"],
												$resourceData["access_level"],
												$resourceData["set_id"],
												$resourceData["upload_filename"],
												$resourceData["title"],
												$resourceData["description"],
												$resourceData["source"],
												$resourceData["external_text"],
												$resourceData["filetype"],
												$resourceData["is_pin"],
												$resourceData["is_fav"],
												$resourceData["is_like"],
												$resourceData["num_likes"],
												$resourceData["num_in_set"],
												$resourceData["s3_status"],
												$resourceData["url"]);
		
			$resourceId = $resourceData["resource_id"];
		
			$resourcePage = $this->translations['resource_page']['translation_text'];
			print '<a href="'.$resourcePage.'?id='.$resourceId.'">';
		
			$resource->printCard();
			
			print '</a>';
			
			$featuredCount += 1;
		}
	}
	
	print '<div class="text-center">';
	print '<a href="'.$searchPage.'" class="btn btn-primary">';
	print '<div class="h4 panelHeading">'.$this->translations['more']['translation_text'].'</div>';
	print '</a>';
	print '</div>';
	
	print '</div>'; // panel-body
	
	print '</div>'; // findByGroupPanel
	
	print '</div>'; // featuredResources


	print '</div>'; // findResourcesGrid

	print '</div>'; // col-12
	
	print '</div>'; // row findRow
	
	print '</div>'; // col-12 
	
	print '</div>'; // row fullPageHeight
	
	print '</div>'; // col-12 end of main content
	
	print '</div>'; // row
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
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


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
print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
print '      </div>';
	  	  
print '    </div>'; // modal-content

print '  </div>'; // modal dialog
print '</div>'; // uploadModal



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourcehub.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);

?>



