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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_STUDENTACCOUNTS_LOGIN").'</div>';
	
}

else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12">';

	print '<div class="panel">';
	print '<div class="panel-body">';
	
	print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_STUDENTACCOUNTS_ACCOUNTS").'</div>';
	
	print '<div class="table-responsive">';
	print  '<table class="table" style="white-space:nowrap">  <thead>	<tr>';
		
	print '<th scope="col" class="align-top">' . JText::_("COM_BIODIV_STUDENTACCOUNTS_AVATAR") . '</th>';
	print '<th scope="col" class="align-top">' . JText::_("COM_BIODIV_STUDENTACCOUNTS_NAME") . '</th>';
	print '<th scope="col" class="align-top">' . JText::_("COM_BIODIV_STUDENTACCOUNTS_USERNAME") . '</th>';
	print '<th scope="col" class="align-top">' . JText::_("COM_BIODIV_STUDENTACCOUNTS_INCLUDE_POINTS") . '</th>';
	print '<th scope="col" class="align-top"></th>';

	print '</tr>  </thead>  <tbody>';

	// Add the rows of data  
	foreach ( $this->students as $studentId=>$student ) {
		print '<tr id="accountRow_'.$studentId.'">';
		
		print '<td>';
		print '<img class="img-responsive avatar progressAvatar" src="'.$student->image.'" />';
		print '</td>';
		
		print '<td id="studentName_'.$studentId.'" class="align-middle">'.$student->name.'</td>';
		
		print '<td id="studentUsername_'.$studentId.'" class="align-middle">'.$student->username.'</td>';
		
		$icon = '';
		$isActive = 0;
		if ( $student->include_points == 1 ) {
			$icon = '<i class="fa fa-check"></i>';
			$isActive = 1;
		}
		print '<td id="studentActive_'.$studentId.'" class="align-middle" data-isActive="'.$isActive.'">'.$icon.'</td>';
		
		
		print '<td class="align-middle">';
		print '<div id="editAccount_'.$studentId.'" class="btn btn-info editStudent" role="button" data-toggle="modal" data-target="#editStudentModal">'.JText::_("COM_BIODIV_STUDENTACCOUNTS_EDIT").'</div>';
		print '</td>';
		
		print '</tr>';
	}

	print '</tbody>';
	print '</table>';
	
	print '</div>'; // table-responsive
	
	
	print '</div>'; // panel-body
	print '</div>'; // panel
		
	print '</div>'; // col-12

	print '</div>'; // row

}


print '<div id="editStudentModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';

print '    <form id="editStudentForm" action="'. BIODIV_ROOT . '&task=edit_student" method="post">';

print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_STUDENTACCOUNTS_EDIT_STUDENT").' <span id="studentUsername"></span></h4>';
print '      </div>';
print '     <div class="modal-body">';

print '<input id="studentId" type="hidden" name="studentId" value="0"/>';

print '<div>';
print '<label for="studentName"> '.JText::_("COM_BIODIV_STUDENTACCOUNTS_NAME").'</label>';
print '<input type="text" id="studentName"  name="studentName">';
print '</div>';

print '<div class="vSpaced">';
print '<div><label for="studentActive"> '.JText::_("COM_BIODIV_STUDENTACCOUNTS_INCLUDE_POINTS").'</label></div>';
print '<input type="checkbox" id="studentActive" name="studentActive" value="1">';
print '</div>';


print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_STUDENTACCOUNTS_SAVE").'</button>';
print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_STUDENTACCOUNTS_CANCEL").'</button>';
print '      </div>';

print '</form>';
	  	  
print '    </div>'; // modalContent

print '  </div>';
print '</div>';



?>