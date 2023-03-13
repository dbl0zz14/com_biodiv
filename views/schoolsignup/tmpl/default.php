<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLSIGNUP_LOGIN").'</div>';
}
else if ( $this->schoolUser ) {
	print '<a href="'.$this->besHome.'"><div type="button" class="list-group-item btn btn-block " >'.JText::_("COM_BIODIV_SCHOOLSIGNUP_SCH_USER").'</div></a>';
}
else {
	
	Biodiv\SchoolCommunity::generateNonUserHeader ( $this->helpOption );
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLSIGNUP_WELCOME").'</h2>';
	print '<h3>'.JText::_("COM_BIODIV_SCHOOLSIGNUP_EXPLAIN").'</h3>';
	print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_NEXT").'</h4>';
	
	print '</div>';
	
	print '<div class="col-md-8 col-sm-10 col-xs-12">';
	
	print '<form id="signupForm">';
	
	print '<input type="hidden" name="personId" value="' . $this->personId . '"/>';
	
	print '<div class="form-group">';
	print '<label for="name" class="h4">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_NAME").'</label>';
	print '<input type="text" id="name" name="name" value = "" class="form-control schoolSetupInput">';
	print '</div>';
	
	print '<div class="form-group">';
	print '<label for="schoolName" class="h4">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_SCHOOL_NAME").'</label>';
	print '<input type="text" id="schoolName" name="schoolName" value = "" class="form-control schoolSetupInput">';
	print '</div>';
		
	print '<div class="form-group">';
	print '<label for="postcode" class="h4">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_POSTCODE").'</label>';
	print '<input type="text" id="postcode" name="postcode" value = "" class="form-control schoolSetupInput">';
	print '</div>';
		
	print '<div class="form-group">';
	print '<label for="website" class="h4">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_WEBSITE").'</label>';
	print '<input type="url" id="website" name="website" value = "" class="form-control schoolSetupInput">';
	print '</div>';
	
	print '<h4>'.JText::_("COM_BIODIV_SCHOOLSIGNUP_PLEASE_AGREE").'</h4>';
	print '<div class="checkbox">';
	print '<label class="h4">';
    print '<input type="checkbox" id="terms" name="terms" value="1">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_TERMS");
    print '</label>';
	print '</div>';
	
	print '<div></div>';
	print '<div class="vSpaced">';
	print '<button type="submit" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_SCHOOLSIGNUP_SIGN_UP").'</button>';
	print '</div>';
	
	print '</form>';
	
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
print '      <div class="modal-body">';
print '	        <div id="helpArticle" ></div>';
print '       </div>';
print '	      <div class="modal-footer">';
print '         <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>';
print '       </div>'; // modal-footer  	  
print '    </div>'; // modal-content
print '  </div>'; // modal-dialog
print '</div>';





// JHTML::script("com_biodiv/commonbiodiv.js", true, true);
// JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schoolsignup.js", true, true);

?>





