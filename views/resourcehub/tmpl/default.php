<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
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

	
	// ---------------------------- Filter buttons, search and new upload
	
	print '<div class="col-md-5">';
	
	print '<div class="row filterRow">';
	
	print '<div class="col-md-12">';
	
	print '<div class="btn-group" role="group" aria-label="resource filters">';
  

	print '<div class="btn btn-info pinned filterButton active ">';
	print $this->translations['pinned_resources']['translation_text'];
	print '</div>';
	
	print '<div class="btn btn-info favourites filterButton ">';
	print $this->translations['favourites']['translation_text'];
	print '</div>';
	
	print '<div class="btn btn-info latestUpload filterButton ">';
	print $this->translations['latest_upload']['translation_text'];
	print '</div>';
	
	print '<div class="btn-group">';
	print '<button type="button" class="btn btn-info dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    '.$this->translations['choose_type']['translation_text'].' <span class="caret"></span>';
	print '  </button>';
	print '  <ul class="dropdown-menu">';
	foreach($this->resourceTypes as $resType){
		
		$resTypeId = $resType[0];
		$resTypeName = $resType[1];
		
		print '<li><button type="button" class="btn btn-info btn-block resource-btn" data-resource-type="'.$resTypeId.'" >';
		
		print $resTypeName;
		
		print '</button></li>';
	}
	print '  </ul>';
	print '</div>'; // btn-group
	
	print '</div>'; // btn-group
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	print '</div>'; // col-5
	
	print '<div class="col-md-5">';

	// ----------------- Search and upload button-------------------- 
	
	print '<div class="row searchResourcesRow">';
	
	print '<div class="form-group col-md-12 col-sm-12 col-xs-12">';
	print '<div class="form-group has-feedback">';
	print '  <div class="input-group">';
	print '    <span class="input-group-addon" style="background-color:#FFFFFF; border-bottom-left-radius:25px; border-top-left-radius:25px;"><span class="glyphicon glyphicon-search"></span></span>';
	print '    <input type="search" class="form-control" id="searchResources" placeholder="Search..." style="border-left: 0px;border-bottom-right-radius:25px; border-top-right-radius:25px;">';
	print '  </div>'; // input-group
	print '</div>	'; // form-group
	print '</div>'; // col-8
	
	// print '<div class="col-md-2 text-right">';
	// print '<button class="btn btn-secondary btn-lg resourceUpload" style="background-color:#8e288c;color:white">'.$this->translations['upload']['translation_text'].'</button>';
	// print '</div>'; // col-2
	
	print '</div>'; // row
	
	print '</div>'; // col-5
	
	print '<div class="col-md-2 col-sm-4 col-xs-4 text-right">';
	print '<button class="btn btn-primary resourceUpload" >'.$this->translations['upload']['translation_text'].'</button>';
	print '</div>'; // col-2
	
	// print '</div>'; // row
	
	// print '<div class="row">';
	
	print '<div class="col-md-12">';
	
	// ------------------------ Where resources are displayed

	print '<div id="displayArea">';

	print '</div>';

	//print '</div>'; // col-12
	
	print '</div>'; // row
	
	print '</div>'; // col-12
	
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



JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourcehub.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);

?>



