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

// Just for testing!!!!!!!!!!!!!!!!!
//ini_set('memory_limit', '256M');

if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {
	
	if ( $this->numSites > 0 ) {
		print '<div class="row">';
		
		//print '<h3>'.$this->translations['chart_page']['translation_text'].'</h3>';
		
		
		// ---------------------- Filter by
		print '<div class="col-xs-12 col-sm-12 col-md-3">';
		
		// Create 3 dropdowns of filter options: site, species and year
		
		print "<label for='site_filter'>".$this->translations['site_filter']['translation_text']."</label>";
			
		print "<select id = 'site_select' name = 'site_filter' class = 'form-control charts_select'>";
		
		foreach( $this->siteSelect as $selVal=>$selStr ){
			if ( $selVal == $this->siteId ) {
				// Show this as selected
				print "<option value='".$selVal."' selected>".$selStr."</option>";
			}
			else {
				print "<option value='".$selVal."'>".$selStr."</option>";
			}
		}

		print "</select>";
		
		print '</div>'; // col-3

		
		print '</div>'; // row
	}
	
	print '<div class="row">';
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['upl_class']['translation_text'].'</h3>';
		
	if ( $this->numSites > 0 ) {
		
		print '<p>'.$this->translations['upl_class_help']['translation_text'].'</p>';
		
		print '<div class="user_chart">';
		print '<canvas id="userProgressChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.$this->translations['upl_class_none']['translation_text'].'</p>';
		print '<p class="text-center"><a class="btn btn-danger btn-lg" href="'.JURI::root().'/'.$this->translations['trapper_page']['translation_text'].'" >'. $this->translations['man_sites']['translation_text'] .'</a></p>';
	}
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['top_species']['translation_text'].'</h3>';
	
	if ( $this->numAnimals > 0 ) {
		
		print '<p>'.$this->translations['top_species_help']['translation_text'].'</p>';
		
		print '<div class="user_chart">';
		print '<canvas id="topSpeciesChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.$this->translations['top_species_none']['translation_text'].'</p>';
		
		print '<p class="text-center"><a class="btn btn-danger btn-lg" href="index.php/'. $this->translations['spotter_page']['translation_text'] .'">'. $this->translations['class_now']['translation_text'] .'</a></p>';
	}
		
	print '</div>'; // col-6
	
	print '</div>'; // row
	
	
	print '<div class="row">';
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['rare_species']['translation_text'].'</h3>';
	
	if ( $this->numAnimals > 0 ) {
		
		print '<p>'.$this->translations['rare_species_help']['translation_text'].'</p>';
	
		print '<div class="user_chart">';
		print '<canvas id="rareSpeciesChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.$this->translations['rare_species_none']['translation_text'].'</p>';
		
		
	}
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['seq_species']['translation_text'].'</h3>';
	
	if ( $this->numAnimals > 0 ) {
		
		print '<p>'.$this->translations['seq_species_help']['translation_text'].'</p>';
	
		print '<div class="user_chart center-block">';
		print '<canvas id="topSeqSpeciesChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.$this->translations['seq_species_none']['translation_text'].'</p>';
	}
		
	
	print '</div>'; // col-6
	
	print '</div>'; // row
	

}


?>






