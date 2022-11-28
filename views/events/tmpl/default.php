<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

print '<div class="row">';

print '<div class="col-md-12">';

$currEventNum = 0;

if ( $this->today ) {
	print '<h5>' . JText::_("COM_BIODIV_EVENTS_TODAY") . '</h5>';
	print '<table class="table table-condensed">';
	foreach ( $this->today as $ev ) {
		
		if ( $currEventNum < $this->displayNum ) {
			print '<tr>';
			//print '<td><div class="row row-no-gutters">';
			if ( $ev->school_event ) {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->school_image.'" /></td>';
				print '<td>'.$ev->school_name.' '.$ev->message.'</td>';
			
			}
			else {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->avatar.'" /></td>';
				print '<td>'.$ev->username.' '.$ev->message.'</td>';
			
			}
			//print '</div>'; // row
			print '</tr>';
		
			$currEventNum += 1;
		}
	}
	print '</table>';
	
	
}

if ( $currEventNum < $this->displayNum and $this->yesterday ) {
	print '<h5>' . JText::_("COM_BIODIV_EVENTS_YESTERDAY") . '</h5>';
	print '<table class="table table-condensed">';
	foreach ( $this->yesterday as $ev ) {
		
		if ( $currEventNum < $this->displayNum ) {
			print '<tr>';
			//print '<td><div class="row row-no-gutters">';
			if ( $ev->school_event ) {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->school_image.'" /></td>';
				print '<td>'.$ev->school_name.' '.$ev->message.'</td>';
			
			}
			else {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->avatar.'" /></td>';
				print '<td>'.$ev->username.' '.$ev->message.'</td>';
			
			}
			//print '</div>'; // row
			print '</tr>';
		
			$currEventNum += 1;
		}
		
	}
	print '</table>';
}

if ( $currEventNum < $this->displayNum and $this->thisWeek ) {
	print '<h5>' . JText::_("COM_BIODIV_EVENTS_THIS_WEEK") . '</h5>';
	print '<table class="table table-condensed">';
	foreach ( $this->thisWeek as $ev ) {
		
		if ( $currEventNum < $this->displayNum ) {
			print '<tr>';
			//print '<td><div class="row row-no-gutters">';
			if ( $ev->school_event ) {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->school_image.'" /></td>';
				print '<td>'.$ev->school_name.' '.$ev->message.'</td>';
			
			}
			else {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->avatar.'" /></td>';
				print '<td>'.$ev->username.' '.$ev->message.'</td>';
			
			}
			//print '</div>'; // row
			print '</tr>';
		
			$currEventNum += 1;
		}
		
		
	}
	print '</table>';
}


if ( $currEventNum < $this->displayNum and $this->earlier ) {
	print '<h5>' . JText::_("COM_BIODIV_EVENTS_EARLIER") . '</h5>';
	print '<table class="table table-condensed">';
	foreach ( $this->earlier as $ev ) {
		
		if ( $currEventNum < $this->displayNum ) {
			print '<tr>';
			//print '<td><div class="row row-no-gutters">';
			if ( $ev->school_event ) {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->school_image.'" /></td>';
				print '<td>'.$ev->school_name.' '.$ev->message.'</td>';
			
			}
			else {
				print '<td width="20%"><img class="img-responsive avatar" src="'.$ev->avatar.'" /></td>';
				print '<td>'.$ev->username.' '.$ev->message.'</td>';
			
			}
			//print '</div>'; // row
			print '</tr>';
		
			$currEventNum += 1;
		}
		
		
	}
	print '</table>';
}


print '</div>'; // col-12


print '</div>';

?>