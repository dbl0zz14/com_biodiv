<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_HELPPAGE_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_HELPPAGE_NOT_SCH_USER").'</h2>';
}
else {
	
	$document = JFactory::getDocument();
	$document->addScriptDeclaration("BioDiv.helptype = '".$this->type."';");
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "help");
	
	print '</div>'; // col-12
	
	// --------------------- Main content
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<a href="'.$this->schoolHelpLink.'" class="btn btn-success homeBtn vSpaced" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_HELPPAGE_BACK");
	print '</a>';
	
	// print '<h2>';
	// print '<div class="row">';
	// print '<div class="col-md-10 col-sm-10 col-xs-10">';
	// print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLHELP_HEADING").'</span> <small class="hidden-xs">'.JText::_("COM_BIODIV_SCHOOLHELP_SUBHEADING").'</small>';
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
	
	print '<div class="panel helpPagePanel">';
	
	print '<div class="panel-body">';
		
	print '<div id="displayArea"></div>';
	
	print '</div>'; // panel-body
	
	print '</div>'; // panel
	
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





JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/helppage.js", true, true);



?>





