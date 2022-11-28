<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


$document = JFactory::getDocument();


// Use map settings for initial state of map but cover area of Europe
// Zoom level determines spacing of lat and lon grid
$zoom = $this->initialMapSettings['mapzoom'];
$latSpacing = 4;
$lonSpacing = 8;
if ( $zoom > 5 && $zoom <= 6 ) {
	$latSpacing = 2;
	$lonSpacing = 4;
}
else if ( $zoom > 6 && $zoom <= 7) {
	$latSpacing = 1;
	$lonSpacing = 2;
}
else if ( $zoom > 7 && $zoom <= 9 ) {
	$latSpacing = 0.5;
	$lonSpacing = 1;
}
else if ($zoom > 9 ) {
	$latSpacing = 0.1;
	$lonSpacing = 0.2;
}
		
print '<div id="areaCovered" data-area-covered=\'{"min_lat":35, "max_lat":65, "min_lon":-15, "max_lon":35, "lat_spacing":'.$latSpacing.', "lon_spacing":'.$lonSpacing.', "high_zoom":9, "min_zoom":4}\'></div>';
		
print '<div id="mapCentre" data-map-centre="'.$this->initialMapSettings['mapcentre'].'"></div>';

print '<div id="initialZoom" data-initial-zoom="'.$this->initialMapSettings['mapzoom'].'"></div>';
	
print '<div id="projectCentre" data-project-centre="'.$this->projectMapSettings['mapcentre'].'"></div>';

print '<div id="projectZoom" data-project-zoom="'.$this->projectMapSettings['mapzoom'].'"></div>';
	

if ( $this->showSitesOnLoad ) {
	print '<div id="showSitesOnLoad" data-show-sites="true"></div>';
}
else {
	print '<div id="showSitesOnLoad" data-show-sites="false"></div>';
}


print '  <div class="col-md-12">';
print '  <div class="col-md-12">';
print '  <div class="col-md-10 col-md-offset-1 h1 text-center slight_lower">'.JText::_("COM_BIODIV_KIOSKMAP_MAP_HEADING").'</div>';  
print ' <div>'; // col-12
print ' <div>'; // col-12



print '<div class="row">';
print '<div id="map_buttons" class="col-md-12 text-center">';



print '<div class="form-inline col-md-12 col-sm-12 col-xs-12 " >';

print '<div class="col-md-2">';

print ' <div class="btn-group" style="margin-bottom:4px;"> ';
print '  <button class="btn btn-success small_btn" id="project_area" >'.JText::_("COM_BIODIV_KIOSKMAP_ZOOM_PROJECT").'</button> ';
print '</div>'; // btn-group

print '</div>'; // col-2


print '<div class="col-md-2">';

print '<div class="btn-group"  style="margin-bottom:4px;"><button type="button" class="btn btn-success small_btn" id="discover_areas" disabled>'.JText::_("COM_BIODIV_KIOSKMAP_SHOW_AREAS").'</button></div>';

print '</div>'; // col-2


print '<div class="col-md-2">';

print ' <div class="btn-group" style="margin-bottom:4px;"> ';
print '  <button class="btn btn-success small_btn" id="discover_sites" >'.JText::_("COM_BIODIV_KIOSKMAP_TOGGLE_SITES").'</button> ';
print '  <button class="btn btn-success small_btn hidden" id="hide_sites" >'.JText::_("COM_BIODIV_KIOSKMAP_TOGGLE_SITES_OFF").'</button> ';
print '</div>'; // btn-group

print '</div>'; // col-2


print '<div class="col-md-6">';

print ' <div class="input-group" style="margin-bottom:4px;">';
//print '<select class="form-control form-control-sm" name = "species_id" id="species_select">';
print '<select class="form-control" name = "species_id" id="species_select" >';
print '  <option value="" disabled selected hidden>' . JText::_("COM_BIODIV_KIOSKMAP_SEL_SP") . '...</option>';


foreach($this->speciesList as $id=>$species){
  
  print '<option value="'.$id.'">'.$species.'</option>';
}

print '</select>';
print '<span class="input-group-btn">';
print '<button  class="btn btn-success small_btn" id="discover_species">' . JText::_("COM_BIODIV_KIOSKMAP_SHOW_SP") . '</button>';
print '</span>';
print '</div>'; // input-group


print '</div>'; // col-8

print '</div>'; // col-12


print '</div>'; // col-8
print '</div>'; // row

print '<div class="row">';
print '<div class="col-md-12">';
print '<div class="col-md-12">';

// -------------- LHS ------------------------
print '<div class="col-xs-12 col-sm-12 col-md-8">';

print '<div id="discovermap" style="width:100%; height:70vh;" class="leaflet-container leaflet-touch leaflet-retina leaflet-fade-anim leaflet-grab leaflet-touch-drag leaflet-touch-zoom"></div>';


print '<h5 class="">'.JText::_("COM_BIODIV_KIOSKMAP_DATA_WARN").'</h5>';
print '</div>'; // col-8


// ------------- RHS --------------------------
print '<div class="col-xs-12 col-sm-12 col-md-4">';



print '<div id="sightingschart_message"></div>';
print '<div id="uploadschart_message"></div>';
print '<div class="table-responsive discover-chart" style="padding: 0; height:37vh; width:25vw; overflow:hidden;">';
print '  <canvas id="sightingschart" class="table"></canvas>';
print '</div>';

print '<div class="table-responsive discover-chart" style="padding: 0; height:32vh; overflow:hidden;">';
print '<canvas id="uploadschart" class="table"></canvas>';
print '</div>';

print '</div>'; // col-4

print '</div>'; // col-12
print '</div>'; // col-12

print '</div>'; // row

?>


