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
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['trapper_stats']['translation_text'].'</h3>';
	
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
	
	print '<p class="text-center"><a class="btn btn-success btn-lg" href="'.$this->translations['trapper_page']['translation_text'].'" >'. $this->translations['man_sites']['translation_text'] .'</a></p>';
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['upl_class']['translation_text'].'</h3>';
	
	if ( $this->numSites > 0 ) {
		print "<canvas id='userProgressChart' ></canvas>";
	}
	else {
		print '<p>'.$this->translations['sites_none']['translation_text'].'</p>';
	}
		
	
	print '</div>'; // col-6
	

}


?>






