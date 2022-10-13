<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	Biodiv\SchoolCommunity::generateNav("admindashboard");
	
	print '</div>';
		
	

	// -------------------------------  Main page content
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print $this->translations['heading']['translation_text'].' <small class="hidden-xs">'.$this->translations['subheading']['translation_text'].'</small>';
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
	
	
	print '<div id="displayArea">';
	
	print '<div class="row">';
	
	print '<div class="col-md-12">';
	
	print '<div class="panel">';
	print '<div class="panel-body">';
	
	print '<div class="h3 panelHeading">'.$this->translations['ecologists']['translation_text'].'</div>';
	
	
	print  '<table class="table" style="white-space:nowrap">  <thead>	<tr>';
			
	print '<th scope="col" class="align-top">' . $this->translations['name']['translation_text'] . '</th>';
	print '<th scope="col" class="align-top">' . $this->translations['schools']['translation_text'] . '</th>';
	print '<th scope="col" class="align-top"></th>';

	print '</tr>  </thead>  <tbody>';

	// Add the rows of data  
	foreach ( $this->ecologists as $ecol ) {
		print '<tr id="ecolRow_'.$ecol->personId.'">';
		
		print '<td id="ecolName_'.$ecol->personId.'">'.$ecol->name.'</td>';
		
		print '<td>';
		foreach ( $ecol->schools as $schoolId=>$school ) {
			print '<p id="currSchool_'.$schoolId.'" class="currSchool_'.$ecol->personId.'">'.$school.'</p>';
		}
		print '</td>';
		
		print '<td>';
		print '<div id="addSchools_'.$ecol->personId.'" class="btn btn-info vSpaced addSchools" role="button" data-toggle="modal" data-target="#pairEcolModal">'.$this->translations['edit']['translation_text'].'</div>';
		print '</td>';
		
		print '</tr>';
	}

	print '</tbody>';
	print '</table>';
	
	
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
//print '        <h4 class="modal-title">'.$this->translations['review']['translation_text'].'</h4>';
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



print '<div id="pairEcolModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';

print '    <form id="pairForm" action="'. BIODIV_ROOT . '&task=pair_ecologist" method="post">';

print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.$this->translations['add_school']['translation_text'].' <span id="ecolName"></span></h4>';
print '      </div>';
print '     <div class="modal-body">';

print '<input id="ecol" type="hidden" name="ecol" value="0"/>';


foreach( $this->allSchools as $school ){
	print '<div>';
	print '<input type="checkbox" id="school_'.$school->id.'" class="schoolCheckbox" name="school[]" value="'.$school->id.'">';
	print '<label for="school_'.$school->id.'"> '.$school->name.'</label>';
	print '</div>';
}
  

print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="submit" class="btn btn-primary" >'.$this->translations['save']['translation_text'].'</button>';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.$this->translations['cancel']['translation_text'].'</button>';
print '      </div>';

print '</form>';
	  	  
print '    </div>'; // modalContent

print '  </div>';
print '</div>';


	

JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schoolusers.js", true, true);

?>



