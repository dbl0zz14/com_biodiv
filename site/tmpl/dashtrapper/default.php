<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


if ( !$this->personId ) {
	print '<a type="button" href="'.Text::_("COM_BIODIV_DASHTRAPPER_DASH_PAGE").'" class="list-group-item btn btn-block" >'.Text::_("COM_BIODIV_DASHTRAPPER_LOGIN").'</a>';
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-6">';
	
	print '<h3>'.Text::_("COM_BIODIV_DASHTRAPPER_TRAPPER_STATS").'</h3>';
	
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
	
	print '<p class="text-center"><a class="btn btn-primary btn-lg" href="'.Text::_("COM_BIODIV_DASHTRAPPER_TRAPPER_PAGE").'" >'. Text::_("COM_BIODIV_DASHTRAPPER_MAN_SITES") .'</a></p>';
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.Text::_("COM_BIODIV_DASHTRAPPER_UPL_CLASS").'</h3>';
	
	if ( $this->numSites > 0 ) {
		print "<canvas id='userProgressChart' ></canvas>";
	}
	else {
		print '<p>'.Text::_("COM_BIODIV_DASHTRAPPER_SITES_NONE").'</p>';
	}
		
	
	print '</div>'; // col-6
	
	print '</div>'; // row
	

}


?>






