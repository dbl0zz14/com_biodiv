<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLSADMIN_LOGIN").'</div>';
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "admindashboard");
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		
		print '<div id="helpButton_badges" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	

	// -------------------------------  Main page content
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_HEADING");
	print '</div>'; // col-12
	print '</div>'; // row
	print '</h2>';  
	
	
	print '<div id="displayArea">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12">';
	
	print '<div class="panel">';
	print '<div class="panel-body">';
	
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_SCHOOLSADMIN_UNAPPROVED").'</div>';
	
	print '<div class="unapprovedSchoolGrid h4 vSpaced">';
		
	print '<div class="unapprovedPersonName">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_PERSON");
	print '</div>';
	
	print '<div class="unapprovedSchoolName">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_SCHOOL");
	print '</div>';
	
	print '<div class="unapprovedSchoolPostcode">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_POSTCODE");
	print '</div>';
	
	print '<div class="unapprovedWebsite">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_WEBSITE");
	print '</div>';
	
	print '<div class="unapprovedTerms text-center">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_TERMS");
	print '</div>';
	
	print '</div>'; // unapprovedSchoolGrid
		
	// Add the rows of data  
	foreach ( $this->newSchools as $newSchool ) {
		
		print '<div class="unapprovedSchoolGrid vSpaced">';
		
		print '<div class="unapprovedPersonName">';
		print $newSchool->person_name;
		print '</div>';
		
		print '<div class="unapprovedSchoolName">';
		print $newSchool->school_name;
		print '</div>';
		
		print '<div class="unapprovedSchoolPostcode">';
		print $newSchool->postcode;
		print '</div>';
		
		print '<div class="unapprovedWebsite">';
		print $newSchool->website;
		print '</div>';
		
		print '<div class="unapprovedTerms text-center">';
		if ( $newSchool->terms_agreed ) {
			print '<i class="fa fa-check"></i>';
		}
		else {
			print '<i class="fa fa-times"></i>';
		}
		print '</div>';
		
		print '<div class="approveBtn">';
		print '<button id="approveSchool_'.$newSchool->signup_id.'"class="btn btn-info approveSchool" data-toggle="modal" data-target="#approveModal">'.JText::_("COM_BIODIV_SCHOOLSADMIN_APPROVE_REJECT").'</button>';
		print '</div>';
		
		print '</div>'; // unapprovedSchoolGrid
		
	}
		
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_SCHOOLSADMIN_APPROVED").'</div>';
	
	print '<div class="approvedSchoolGrid h4 vSpaced">';
		
	print '<div class="approvedSchoolName">';
	print JText::_("COM_BIODIV_SCHOOLSADMIN_SCHOOL");
	print '</div>';
	
	print '</div>'; // approvedSchoolGrid
		
	// Add the rows of data  
	foreach ( $this->allSchools as $existingSchool ) {
		
		print '<div class="approvedSchoolGrid vSpaced">';
		
		print '<div class="approvedSchoolName">';
		print $existingSchool->name;
		print '</div>';
		
		print '</div>'; // approvedSchoolGrid
		
	}

	print '</div>'; // panel-body
	print '</div>'; // panel
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	
	
	
	
	print '</div>'; // display area 
	
	
	
	print '</div>'; // col-12
	
	print '</div>'; // row // summary row
	
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
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_CANCEL").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



print '<div id="approveModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '<form id="approveSchoolForm" action = "'.BIODIV_ROOT.'" method = "POST">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '      </div>';
print '     <div class="modal-body">';
print '<h3 class="vSpaced">'.JText::_("COM_BIODIV_SCHOOLSADMIN_APPROVE_HEADING").'</h3>';
print '<form id="approveSchoolForm" >';
print '<input type="hidden" name="signUpId" value=""/>';

print '<div class="form-group">';
print '<label for="comment" class="h4">'.JText::_("COM_BIODIV_SCHOOLSADMIN_COMMENT").'</label>';
print '<textarea id="comment" name="comment" class="form-control"></textarea>';
print '</div>';

print '<div class="radio">';
print '<label>';
print '<input type="radio" name="approve" value="1"  >';
print JText::_("COM_BIODIV_SCHOOLSADMIN_APPROVE");
print '</label>';
print '</div>';

print '<div class="radio">';
print '<label>';
print '<input type="radio" name="approve" value="-1" >';
print JText::_("COM_BIODIV_SCHOOLSADMIN_REJECT");
print '</label>';
print '</div>';


print '      </div>';
print '	  <div class="modal-footer">';
print '        <button id="approveSchoolBtn" type="submit" class="btn btn-primary btn-lg">'.JText::_("COM_BIODIV_SCHOOLSADMIN_SAVE").'</button>';
print '        <button type="button" class="btn btn-info btn-lg" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLSADMIN_CANCEL").'</button>';
print '      </div>';

print '</form>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';





JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schoolsadmin.js", true, true);

?>



