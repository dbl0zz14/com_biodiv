<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ("Area covered = " . $this->areaCovered );

$document = JFactory::getDocument();
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
?>


<div class='row'>
<div class='col-md-3 col-sm-3 col-xs-6'>
<?php
  print "<div data-toggle='tooltip' title='".$this->translations['areas_tooltip']['translation_text']."'><button type='button' class='btn btn-warning' id='discover_areas' disabled>".$this->translations['show_areas']['translation_text']."</button></div>";
?>
</div>

<div class='col-md-4 col-sm-9 col-xs-6'>
<?php
  print '<div class="btn-group btn-toggle" data-toggle="tooltip" title="'.$this->translations['sites_tooltip']['translation_text'].'"> ';
  print '  <button class="btn btn-warning" id="discover_sites" >'.$this->translations['toggle_sites']['translation_text'].'</button> ';
  print '  <button class="btn btn-warning active disabled" id="hide_sites" >'.$this->translations['toggle_sites_off']['translation_text'].'</button> ';
  print '</div>';

?>

</div>

<?php
print "<div class='col-md-5 col-sm-12 col-xs-12'>";
print "<div class='input-group' data-toggle='tooltip' title='".$this->translations['species_tooltip']['translation_text']."' >";
//print "<div style='display:inline-block; '>";
print "<select class='form-control form-control-sm' name = 'species_id' id='species_select'>";
print "  <option value='' disabled selected hidden>" . $this->translations['sel_sp']['translation_text'] . "...</option>";


	foreach($this->speciesList as $id=>$species){
	  
	  print "<option value='$id'>$species</option>";
	}

print "</select>";
//print "</div>";
print "<span class='input-group-btn'>";
//print "<div style='display:inline-block; '>";
print "<button  class='btn btn-warning' id='discover_species'>" . $this->translations['show_sp']['translation_text'] . "</button>";
print "</span>";
//print "</div>";
print "</div>";
print "</div>";


?>
</div> <!-- row -->


<div class="row">
<div class='col-xs-12 col-sm-12 col-md-8'>
<div id="discovermap" style="width:100%; height:500px;"></div>
<?php print "<h5 class='bg-warning highlighted add-padding-all'>".$this->translations['data_warn']['translation_text']."</h5>";?>
</div>
<div class='col-xs-12 col-sm-12 col-md-4'>
<?php print '<div id="howto_message"><h3>'.$this->translations['areas_tooltip']['translation_text'].'<h3></div>'; ?>
<div id="sightingschart_message"></div>
<div id="uploadschart_message"></div>
<div class="table-responsive discover-chart" style="padding: 0; height:340px; width:310px; overflow:hidden;">
  <canvas id="sightingschart" class="table"></canvas>
</div>

<div class="table-responsive discover-chart" style="padding: 0; height:230px; overflow:hidden;">
<canvas id="uploadschart" class="table"></canvas>
</div>

</div> <!-- col-md-4 -->

</div> <!-- row -->

<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", array('cross_origin' => ''));
JHTML::stylesheet("https://unpkg.com/leaflet@1.6.0/dist/leaflet.css", array('integrity' => 'sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==', 'cross_origin' => ''));
JHTML::script("https://unpkg.com/leaflet@1.6.0/dist/leaflet.js", array('integrity' => 'sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==', 'cross_origin' => ''));
JHTML::script("com_biodiv/discover.js", true, true);

?>


