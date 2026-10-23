<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;


$document = Factory::getDocument();
if ( $this->areaCovered == "uk" ) {
	$document->addScriptDeclaration("BioDiv.areaCovered = {min_lat:45, max_lat:65, min_lon:-25, max_lon:15, lat_spacing:5, lon_spacing:8, high_zoom:9, min_zoom:4};");
	$document->addScriptDeclaration("BioDiv.mapCentre = [55,-5]");
	$document->addScriptDeclaration("BioDiv.initialZoom = 5;");
}
else {
	$document->addScriptDeclaration("BioDiv.areaCovered = {min_lat:35, max_lat:65, min_lon:-15, max_lon:35, lat_spacing:5, lon_spacing:8, high_zoom:9, min_zoom:4};");
	$document->addScriptDeclaration("BioDiv.mapCentre = [51,10]");
	$document->addScriptDeclaration("BioDiv.initialZoom = 4;");
}

if ( $this->showSitesOnLoad ) {
	$document->addScriptDeclaration("BioDiv.showSitesOnLoad = true;");
}
else {
	$document->addScriptDeclaration("BioDiv.showSitesOnLoad = false;");
}



// print '<div class="row">';


// print "<div class='form-inline col-md-12 col-sm-12 col-xs-12' >";
// print "<div class='btn-group' data-toggle='tooltip' title='".Text::_("COM_BIODIV_DISCOVER_AREAS_TOOLTIP")."' style='margin-bottom:4px;'><button type='button' class='btn btn-warning' id='discover_areas' disabled>".Text::_("COM_BIODIV_DISCOVER_SHOW_AREAS")."</button></div>";

// print ' <div class="btn-group" data-toggle="tooltip" title="'.Text::_("COM_BIODIV_DISCOVER_SITES_TOOLTIP").'"  style="margin-bottom:4px;"> ';
// print '  <button class="btn btn-warning" id="discover_sites" >'.Text::_("COM_BIODIV_DISCOVER_TOGGLE_SITES").'</button> ';
// print '  <button class="btn btn-warning d-none" id="hide_sites" >'.Text::_("COM_BIODIV_DISCOVER_TOGGLE_SITES_OFF").'</button> ';
// print '</div>';


// print " <div class='input-group' data-toggle='tooltip' title='".Text::_("COM_BIODIV_DISCOVER_SPECIES_TOOLTIP")."'  style='margin-bottom:4px;'>";
// print "<select class='form-control form-control-sm' name = 'species_id' id='species_select'>";
// print "  <option value='' disabled selected d-none>" . Text::_("COM_BIODIV_DISCOVER_SEL_SP") . "...</option>";


	// foreach($this->speciesList as $id=>$species){
	  
	  // print "<option value='$id'>$species</option>";
	// }

// print "</select>";
// print "<span class='input-group-btn'>";
// print "<button  class='btn btn-warning' id='discover_species'>" . Text::_("COM_BIODIV_DISCOVER_SHOW_SP") . "</button>";
// print "</span>";
// print "</div>"; // input-group

// print "</div>"; // col-12


// print '</div> <!-- row -->';

print '<div class="row mt-4">';


print "<div class='col-md-12 col-sm-12 col-xs-12' >";
print '<div class="d-inline" data-toggle="tooltip" title="'.Text::_("COM_BIODIV_DISCOVER_AREAS_TOOLTIP").'"> ';
print "<button type='button' class='btn btn-outline-primary d-inline' id='discover_areas' ".
		" disabled>".Text::_("COM_BIODIV_DISCOVER_SHOW_AREAS")."</button>";
print '</div>';

print '<div class="d-inline" data-toggle="tooltip" title="'.Text::_("COM_BIODIV_DISCOVER_SITES_TOOLTIP").'"> ';
print '  <button class="btn btn-outline-primary" id="discover_sites" >'.Text::_("COM_BIODIV_DISCOVER_TOGGLE_SITES").'</button> ';
print '  <button class="btn btn-outline-primary d-none" id="hide_sites" >'.Text::_("COM_BIODIV_DISCOVER_TOGGLE_SITES_OFF").'</button> ';
print '</div>';


//print " <div class='input-group d-inline' data-toggle='tooltip' title='".Text::_("COM_BIODIV_DISCOVER_SPECIES_TOOLTIP")."'  style='margin-bottom:4px;'>";
print "<select class='form-select d-inline' name = 'species_id' id='species_select'>";
print "  <option value='' disabled selected d-none>" . Text::_("COM_BIODIV_DISCOVER_SEL_SP") . "...</option>";


	foreach($this->speciesList as $id=>$species){
	  
	  print "<option value='$id'>$species</option>";
	}

print "</select>";
//print "<span class='input-group-btn d-inline'>";
print "<button  class='btn btn-primary d-inline' id='discover_species'>" . Text::_("COM_BIODIV_DISCOVER_SHOW_SP") . "</button>";
//print "</span>";
//print "</div>"; // input-group

print "</div>"; // col-12


print '</div> <!-- row -->';


print '<div class="row">';
print '<div class="col-xs-12 col-sm-12 col-md-8">';
print '<div id="discovermap" style="width:100%; height:500px;"></div>';
print '<h5 class="highlighted add-padding-all">'.Text::_("COM_BIODIV_DISCOVER_DATA_WARN").'</h5>';
print '</div>';
print '<div class="col-xs-12 col-sm-12 col-md-4">';
print '<div id="howto_message"><h5 class="highlighted add-padding-all">'.Text::_("COM_BIODIV_DISCOVER_AREAS_TOOLTIP").'<h5></div>'; 
print '<div id="sightingschart_message"></div>';
print '<div id="uploadschart_message"></div>';
print '<div class="table-responsive discover-chart" style="padding: 0; height:340px; width:310px; overflow:hidden;">';
print '  <canvas id="sightingschart" class="table"></canvas>';
print '</div>';

print '<div class="table-responsive discover-chart" style="padding: 0; height:230px; overflow:hidden;">';
print '<canvas id="uploadschart" class="table"></canvas>';
print '</div>';

print '</div> <!-- col-md-4 -->';

print '</div> <!-- row -->';


$this->document->getWebAssetManager()->usePreset('com_biodiv.discover');


?>


