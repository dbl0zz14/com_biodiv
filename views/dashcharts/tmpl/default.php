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
	print '<a type="button" href="'.JText::_("COM_BIODIV_DASHCHARTS_DASH_PAGE").'" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_DASHCHARTS_LOGIN").'</a>';
}
else {
	
	if ( $this->numSites > 0 ) {
		print '<div class="row">';
		
		// ---------------------- Filter by
		print '<div class="col-xs-12 col-sm-12 col-md-3">';
		
		// Create 3 dropdowns of filter options: site, species and year
		
		print "<label for='site_filter'>".JText::_("COM_BIODIV_DASHCHARTS_SITE_FILTER")."</label>";
			
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
	
	print '<h3>'.JText::_("COM_BIODIV_DASHCHARTS_UPL_CLASS").'</h3>';
		
	if ( $this->numSites > 0 ) {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_UPL_CLASS_HELP").'</p>';
		
		print '<div class="user_chart">';
		print '<canvas id="userProgressChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_UPL_CLASS_NONE").'</p>';
		print '<p class="text-center"><a class="btn btn-danger btn-lg" href="'.JText::_("COM_BIODIV_DASHCHARTS_TRAPPER_PAGE").'" >'. JText::_("COM_BIODIV_DASHCHARTS_MAN_SITES") .'</a></p>';
	}
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHCHARTS_TOP_SPECIES").'</h3>';
	
	if ( $this->numAnimals > 0 ) {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_TOP_SPECIES_HELP").'</p>';
		
		print '<div class="user_chart">';
		print '<canvas id="topSpeciesChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_TOP_SPECIES_NONE").'</p>';
		
		print '<p class="text-center"><a class="btn btn-danger btn-lg" href="'. JText::_("COM_BIODIV_DASHCHARTS_SPOTTER_PAGE") .'">'. JText::_("COM_BIODIV_DASHCHARTS_CLASS_NOW") .'</a></p>';
	}
		
	print '</div>'; // col-6
	
	print '</div>'; // row
	
	
	print '<div class="row">';
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHCHARTS_RARE_SPECIES").'</h3>';
	
	if ( $this->numAnimals > 0 ) {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_RARE_SPECIES_HELP").'</p>';
	
		print '<div class="user_chart">';
		print '<canvas id="rareSpeciesChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_RARE_SPECIES_NONE").'</p>';
		
		
	}
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHCHARTS_SEQ_SPECIES").'</h3>';
	
	if ( $this->numAnimals > 0 ) {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_SEQ_SPECIES_HELP").'</p>';
	
		print '<div class="user_chart center-block">';
		print '<canvas id="topSeqSpeciesChart" ></canvas>';
		print '</div>'; // user_chart	
	}
	else {
		
		print '<p>'.JText::_("COM_BIODIV_DASHCHARTS_SEQ_SPECIES_NONE").'</p>';
	}
		
	
	print '</div>'; // col-6
	
	print '</div>'; // row
	

}


?>






