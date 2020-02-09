<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>


<div class="row">
<div class='col-md-3 cls-xs-12 map-column'>
<?php
  print "<p data-toggle='tooltip' title='".$this->translations['areas_tooltip']['translation_text']."'><button type='button' class='btn btn-warning' id='discover_areas' disabled>".$this->translations['show_areas']['translation_text']."</button></p>";
?>
</div>

<div class='col-md-3 cls-xs-12 map-column'>
<?php
  print '<div class="btn-group btn-toggle" data-toggle="tooltip" title="'.$this->translations['sites_tooltip']['translation_text'].'"> ';
  print '  <button class="btn btn-warning" id="discover_sites">'.$this->translations['toggle_sites']['translation_text'].'</button> ';
  print '  <button class="btn btn-warning active disabled" id="hide_sites">'.$this->translations['toggle_sites_off']['translation_text'].'</button> ';
  print '</div>';

?>


</div>
<div class='col-md-4 cls-xs-12 map-column'>
<div class="input-group" <?php print "data-toggle='tooltip' title='".$this->translations['species_tooltip']['translation_text']."'"?> >
<select name = 'species_id' id="species_select">
  <option value="" disabled selected hidden><?php print $this->translations['sel_sp']['translation_text']?>...</option>

  <?php
	foreach($this->speciesList as $id=>$species){
	  
	  print "<option value='$id'>$species</option>";
	}
  ?>
</select>
<span class="input-group-btn">
<button  class='btn btn-warning' id='discover_species'><?php print $this->translations['show_sp']['translation_text']?></button>
</span>
</div>
</div>
</div>
<div class="row">
<div class='col-md-8 cls-xs-12 map-column'>
<div id="discovermap" style="height:500px;"></div>
<?php print "<h5 class='bg-warning highlighted add-padding-all'>".$this->translations['data_warn']['translation_text']."</h5>";?>
</div>
<div class='col-md-4 cls-xs-12'>
<div id="sightingschart_message"></div>
<div id="uploadschart_message"></div>
<div class="table-responsive discover-chart" style="padding: 0; height:340px; width:310px; overflow:hidden;">
  <canvas id="sightingschart" class="table"></canvas>
</div>

<div class="table-responsive discover-chart" style="padding: 0; height:230px; overflow:hidden;">
<canvas id="uploadschart" class="table"></canvas>
</div>

</div>

</div>

<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", array('cross_origin' => ''));
JHTML::stylesheet("https://unpkg.com/leaflet@1.6.0/dist/leaflet.css", array('integrity' => 'sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==', 'cross_origin' => ''));
JHTML::script("https://unpkg.com/leaflet@1.6.0/dist/leaflet.js", array('integrity' => 'sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==', 'cross_origin' => ''));
JHTML::script("com_biodiv/discover.js", true, true);

?>


