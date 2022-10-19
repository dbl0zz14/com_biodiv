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
	

	print '<div class="panel">';
	print '<div class="panel-body">';
	
	print '<div class="row">';
	print '<div class="col-md-10">';
	print '<div class="h3">'.$this->resourceSet->getSetName().'</div>';
	print '</div>'; // col-10
	print '<div class="col-md-2">';
	
	if ( $this->canEdit ) {
		print '<div id="addFilesToSet_'.$this->setId.'" class="btn btn-primary addFilesToSet" data-toggle="modal" data-target="#addFilesModal">'.$this->translations['add_files']['translation_text'].'</div>';
	}
	
	print '</div>'; // col-2
	print '</div>'; // row
	
	
	// Resource files rows
	$i = 0;
	foreach ( $this->resourceFiles as $resourceFile ) {
		
		if ( $i%4 == 0 ) {
			print '<div class="row">';
		}
	
		
		print '<div class="col-md-3 col-sm-4 col-xs-12">';
		
		print '<div class="resourceInSet">';
		
		$resourceId = $resourceFile["resource_id"];
		$resourcePerson = $resourceFile["person_id"];
		
		$resourcePage = $this->translations['resource_page']['translation_text'];
		print '<a href="'.$resourcePage.'?id='.$resourceId.'">';
		
		
		$resourceFile = new Biodiv\ResourceFile ( $resourceId, 
												$resourceFile["resource_type"],
												$resourcePerson,
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
		
		if ( $this->personId == $resourcePerson ) {
			
			print '<button id="editResource_'.$resourceId.'" class="btn btn-default edit_resource edit_resource_in_set" role="button" data-toggle="modal" data-target="#editModal">'.$this->translations['edit']['translation_text'].'</button>';
		}
		
		print '</div>'; // resourceInSet
		
		print '</div>'; // col-3
		
		if ( $i%4 == 3 ) {
			print '</div>'; // row
		}
		$i++;
	}

	if ( $i%4 != 0 ) {
		print '</div>'; // row
	}
	
	print '</div>'; // panel-body
	print '</div>'; // panel

	print '<div id="editModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '	    <div id="editArea" ></div>';
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
	print '      </div>';
			  
	print '    </div>'; // modal-content

	print '  </div>'; // modal dialog
	print '</div>'; // editModal
	
	
	print '<div id="addFilesModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header text-right">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '	    <div id="addArea" ></div>';
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
	print '      </div>';
			  
	print '    </div>'; // modal-content

	print '  </div>'; // modal dialog
	print '</div>'; // addFilesModal


	if ( $this->gotMessages > 0 ) {
		print '<div id="errorsModal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog"  >';

		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		print '      <div class="modal-header text-right">';
		print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
		print '        <h4 class="modal-title text-left">'.$this->translations['upload_errors']['translation_text'].'</h4>';
		print '      </div>';
		print '     <div class="modal-body">';
		showUploadMessages();
		print '      </div>';
		print '	  <div class="modal-footer">';
		print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
		print '      </div>';
				  
		print '    </div>'; // modal-content

		print '  </div>'; // modal dialog
		print '</div>'; // errorsModal
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


}



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/resourceupload.js", true, true);
JHTML::script("com_biodiv/resourcelist.js", true, true);
JHTML::script("com_biodiv/resourceset.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("com_biodiv/pdfjs/pdf.js", true, true);


?>