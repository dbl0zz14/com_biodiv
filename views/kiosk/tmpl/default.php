<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// Add variables for use by the Javascript
$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.kiosk = '".$this->kiosk."';");

print '	<button id="home_button" class="btn btn-success h2" data-dismiss="modal"> <span class="fa fa-3x fa-home"> </button>';

print '<div id="kiosk"></div>';

//print '<div id="discovermap" style="width:100%; height:500px;"></div>';


JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/mediacarousel.js", true, true);
JHTML::script("com_biodiv/kiosk.js", true, true);
JHTML::script("com_biodiv/kioskclassify.js", true, true);
JHTML::script("com_biodiv/kioskquiz.js", true, true);
JHTML::script("com_biodiv/kiosklearn.js", true, true);
JHTML::script("com_biodiv/kiosktutorial.js", true, true);
JHTML::script("com_biodiv/kioskabout.js", true, true);

JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", array('cross_origin' => ''));
JHTML::stylesheet("https://unpkg.com/leaflet@1.6.0/dist/leaflet.css", array('integrity' => 'sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==', 'cross_origin' => ''));
//JHTML::script("https://unpkg.com/leaflet@1.6.0/dist/leaflet.js", array('integrity' => 'sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==', 'cross_origin' => ''));
JHTML::script("https://unpkg.com/leaflet@1.7.1/dist/leaflet.js", array('integrity' => 'sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==', 'cross_origin' => ''));
JHTML::script("com_biodiv/kioskmap.js", true, true);
?>


