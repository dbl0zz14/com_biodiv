<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>
<h1><?php print JText::_("COM_BIODIV_MAPSELECT_SEL_LOC") . ' ' . $this->site_name ?></h1>
<div id="map_canvas" style="width:400px;height:400px;"></div>
<script type='text/javascript'>
    BioDiv.grid_ref = '<?php print $this->grid_ref;?>';
	BioDiv.latitude = '<?php print $this->latitude;?>';
	BioDiv.longitude = '<?php print $this->longitude;?>';
</script>
<form id='map_select_form' action='<?php print BIODIV_ROOT . "&task=set_site_grid_reference"; ?>' method='post'>
<input type='hidden' name='site_id' value='<?php print $this->site_id;?>'/>
<div class='input-group' style='width:400px'>
    <span class="input-group-addon" id="basic-addon2" style='width:80px'><?php print JText::_("COM_BIODIV_MAPSELECT_LAT") ?></span>
    <input type="text" class='form-control' id='latitude' name='latitude'/>
    <span class="input-group-addon" id="basic-addon2" style='width:80px'><?php print JText::_("COM_BIODIV_MAPSELECT_LON") ?></span>
    <input type="text" class='form-control' id='longitude' name='longitude'/>
</div>
<div class='input-group' style='width:400px'> 
    <span class="input-group-addon" id="basic-addon1" style='width:100px'><?php JText::_("COM_BIODIV_MAPSELECT_GRID") ?></span>
    <input type="text" class='form-control' id='grid_ref' name='grid_ref'/>
    <span class="input-group-btn"><button class='btn btn-primary' type='submit'><?php print JText::_("COM_BIODIV_MAPSELECT_SELECT") ?></button></span>
</div>
</form>
<?php

$mapOptions = mapOptions();
$key = $mapOptions['key'];


JHtml::_('script', 'com_biodiv/mapinit.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/mapupdate.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/mapselect.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'https://maps.googleapis.com/maps/api/js?key='.$key.'&loading=async&callback=initMap', array(), array());
JHtml::_('script', 'com_biodiv/geodesy-master/vector3d.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/geodesy-master/latlon-ellipsoidal.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/geodesy-master/osgridref.js', array('version' => 'auto', 'relative' => true), array());


?>


