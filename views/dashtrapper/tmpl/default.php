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
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
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
	
	print '<p class="text-center"><a class="btn btn-danger btn-lg" href="index.php/trapper-status">'. $this->translations['man_sites']['translation_text'] .'</a></p>';
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['upl_class']['translation_text'].'</h3>';
	
	print "<canvas id='userProgressChart' ></canvas>";
		
	
	print '</div>'; // col-6
	

}


?>






