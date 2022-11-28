<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>

<?php

if ( !$this->personId ) {
	print '<a type="button" href="'.JText::_("COM_BIODIV_DASHTRAPPER_DASH_PAGE").'" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_DASHTRAPPER_LOGIN").'</a>';
}
else {
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHTRAPPER_TRAPPER_STATS").'</h3>';
	
	// Add the table and headings
	print  '<div class="table-responsive">';
	print  '<table class="table"><tbody>';

	// Add the rows of data  
	foreach ( $this->statRows as $row ) {
		
		print '<tr>';
		
		foreach ( $row as $rowField ) {
			print '<td>'.$rowField.'</td>';
		}
		
		print '</tr>';
	}

	print '</tbody></table>';
	
	print '</div>'; // table responsive
	
	print '<p class="text-center"><a class="btn btn-success btn-lg" href="'.JText::_("COM_BIODIV_DASHTRAPPER_TRAPPER_PAGE").'" >'. JText::_("COM_BIODIV_DASHTRAPPER_MAN_SITES") .'</a></p>';
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHTRAPPER_UPL_CLASS").'</h3>';
	
	if ( $this->numSites > 0 ) {
		print "<canvas id='userProgressChart' ></canvas>";
	}
	else {
		print '<p>'.JText::_("COM_BIODIV_DASHTRAPPER_SITES_NONE").'</p>';
	}
		
	
	print '</div>'; // col-6
	

}


?>






