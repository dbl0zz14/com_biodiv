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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCESET_LOGIN").'</div>';
}

else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 

	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "teacherzone");
	
	print '</div>';
		
	print '</div>'; // row
	
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_resourcehub" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	// --------------------- Main content
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">';

	print '<a href="'.JText::_("COM_BIODIV_RESOURCESET_HUB_PAGE").'" class="btn btn-success homeBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_RESOURCESET_RESOURCES_HOME");
	print '</a>';
	
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_RESOURCESET_HEADING").'</span> <small class="hidden-xs hidden-sm">'.JText::_("COM_BIODIV_RESOURCESET_SUBHEADING").'</small> ';
	print '</div>'; // col-12
	print '</div>'; // row
	print '</h2>'; 
	

	print '<div class="panel">';
	print '<div class="panel-body">';
	
	$this->resourceSet->printFullHeader();
	
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
		
		$resourcePage = JText::_("COM_BIODIV_RESOURCESET_RESOURCE_PAGE");
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
			
			print '<button id="editResource_'.$resourceId.'" class="btn btn-default edit_resource edit_resource_in_set" role="button" data-toggle="modal" data-target="#editModal">'.JText::_("COM_BIODIV_RESOURCESET_EDIT").'</button>';
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
	
	print '</div>'; // col-12
	
	print '</div>'; // row

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
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_RESOURCESET_CANCEL").'</button>';
	print '      </div>';
			  
	print '    </div>'; // modal-content

	print '  </div>'; // modal dialog
	print '</div>'; // editModal
	
	
	print '<div id="editSetModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header text-right">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '	    <div id="editSetArea" ></div>';
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_RESOURCESET_CANCEL").'</button>';
	print '      </div>';
			  
	print '    </div>'; // modal-content

	print '  </div>'; // modal dialog
	print '</div>'; // editTextModal


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
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_RESOURCESET_CANCEL").'</button>';
	print '      </div>';
			  
	print '    </div>'; // modal-content

	print '  </div>'; // modal dialog
	print '</div>'; // addFilesModal


	print '<div id="addBadgeModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header text-right">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '	    <div id="addBadgeArea" ></div>';
	print '      </div>';			  
	print '    </div>'; // modal-content

	print '  </div>'; // modal dialog
	print '</div>'; // addBadgeModal


	if ( $this->gotMessages > 0 ) {
		print '<div id="errorsModal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog"  >';

		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		print '      <div class="modal-header text-right">';
		print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
		print '        <h4 class="modal-title text-left">'.JText::_("COM_BIODIV_RESOURCESET_UPLOAD_ERRORS").'</h4>';
		print '      </div>';
		print '     <div class="modal-body">';
		showUploadMessages();
		print '      </div>';
		print '	  <div class="modal-footer">';
		print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_RESOURCESET_CANCEL").'</button>';
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
	print '        <a href="'.$this->badgeSchemeLink.'"><button type="button" class="btn btn-primary">'.JText::_("COM_BIODIV_RESOURCESET_VIEW_SCHEME").'</button></a>';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_RESOURCESET_CLOSE").'</button>';
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
JHTML::script("com_biodiv/pdfjs/pdf.worker.js", true, true);


?>